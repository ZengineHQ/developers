---
layout: plugin-nav-bar
group: tutorials
subgroup: scheduled-service
---

<h1 id="creating-a-scheduled-plugin-service">Creating a Scheduled Plugin Service <a href="https://github.com/ZengineHQ/labs/tree/{{site.githubBranch}}/plugins/scheduled-service" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

Our goal -- in order to know which items are on the todo list for today, we will fetch records from {{site.productName}} and post them to HipChat once a day. We will accomplish this by combining a backend plugin service with scheduled webhooks. Our backend service will fetch records and post them to HipChat. Our scheduled webhook will then run once a day to execute the backend service. If you aren't yet familiar, we recommended first reading about [backend services]({{site.baseurl}}/plugins/development/services.html).

## Prerequisites

You will need a HipChat account with API access to send messages. [Create an API token](https://www.hipchat.com/account/api) for "Send Message".

## Creating the Plugin Service

If you haven't done so already, start by creating a plugin and adding a backend service using the [Developer tools]({{site.clientDomain}}/account/developer){:target="_blank"}. Next, download and extract the default draft source. Edit the `plugin.js` file and replace the contents with the following code:

{% highlight js %}
{% raw %}
// Plugin code goes here
var znHttp = require('./lib/zn-http'),
	requestify = require('requestify');

exports.run = function(eventData) {

	// Scheduled Webhooks use POST
	if (eventData.request.method === 'POST') {

		var formId = eventData.request.query.formId,
			folderId = eventData.request.query.folderId || 0,
			hipchatRoom = eventData.request.query.hipchatRoom,
			hipchatToken = eventData.request.query.hipchatToken,
			scheduledData = eventData.request.body.data;

		znHttp().get('/forms/' + formId + '/records?folder.id=' + folderId).then(function(response) {

			var body = response.getBody(),
				records = body.data,
				message = [];

			message.push('Scheduled @ ' + scheduledData.scheduled);

			records.forEach(function(record) {
				message.push(record.id + ' - ' + record.name);
			});

			message.push('Next Scheduled @ ' + scheduledData.nextScheduled);

			message = message.join("\n");

			requestify.post('https://api.hipchat.com/v2/room/' + hipchatRoom + '/message?auth_token=' + hipchatToken, {
				message: message
			}).then(function() {
				// return message
				eventData.response.status(200).send(message);
			}, function(error) {
				eventData.response.status(404).send(error);
			});

		}, function(error) {
			eventData.response.status(404).send(error);
		});
		
	} else {
		eventData.response.status(404).send('Not found');
	}

};
{% endraw %}
{% endhighlight %}

The above code will take several query string parameters to fetch records and then post them to HipChat. Scheduled webhooks will then send a POST to your service, containing data such as when the webhook was scheduled to run and when the webhook is next scheduled to run. The above code will look for this data and include it in the message it sends to HipChat.

Once you have updated `plugin.js`, zip the updated plugin folder and upload it back to your plugin service.

## Publish and Install

Once your plugin service has been updated, you will need to publish and install it into the workspace where it should run. When publishing your plugin, make sure to enable offline access so that it can fetch your records when the webhook executes.

After publishing, install and activate the plugin in your workspace.

## Creating a Scheduled Webhook

Now that you have a plugin service, you can [create a scheduled webhook]({{site.baseurl}}/rest-api/resources/#!/scheduled_webhooks/add_scheduled_webhooks_ScheduledWebhook_post_2) to execute it. Create a scheduled webhook with the `workspace.id` where you installed your plugin, a `frequency` of `daily`, and a URL in the format `{{site.pluginDomain}}/workspaces/{workspaceId}/{pluginNamespace}/{serviceRoute}?formId={formId}&folderId={folderID}&hipchatRoom={room}&hipchatToken={token}`

 You must specify a `formId`, `hipchatRoom`, and the `hipchatToken` that you created under prerequisites. You can optionally specify a `folderId` to fetch a subset of records, for instance, only records in a "To Do" folder, ignoring records in a "Completed" folder.

If you do not specify a `start` datetime, then it will start running immediately and will run daily at that time.

Once the scheduled webhook executes your plugin service, you should see results like this posted in your HipChat room:

	Scheduled @ 2015-09-23 21:00:00
	866936 - Frontend Interface
	866937 - Backend API
	866938 - Documentation
	Next Scheduled @ 2015-09-24 21:00:00
