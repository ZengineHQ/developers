---
layout: plugin-nav-bar
group: development
---

# Plugin Services

Plugin services are [Node.js](https://nodejs.org/){:target="_blank"} apps with a specific structure that will be explained below. They are written in Javascript and can take advantage of existing 3rd-party, Javascript based, node modules. They can use our included API library to communicate with our REST API or modules like `restify` to communicate with other APIs.

To get started, from the [Developer tools]({{site.clientDomain}}/account/developer){:target="_blank"}, create or edit a plugin, then add a new backend service. This will initialize your service with sample code that you should then download and unzip. The unzipped folder represents everything that makes your service.

## Service Structure

* **_runner**
	This is our code for executing your plugin locally. **Do not modify**.
* **lib**
	Used for organizing service code into modules. By default comes with our `znHttp` library for communicating with our REST API and our `znFirebase` library for communicating with [Firebase](https://www.firebase.com/){:target="_blank"}. You can add your own files, but **do not modify existing files**.
* **node_modules**
	Where 3rd party node modules live. Includes `restify` for making HTTP requests and `firebase` if you don't use our `znFirebase` library. You should use `npm install` to install additional node modules here. Pure Javascript modules are recommended. Native modules or dependencies must be built against [Amazon Linux libraries](https://aws.amazon.com/blogs/compute/nodejs-packages-in-lambda/){:target="_blank"}. Modules count against your service file size.
* **package.json**
	Node.js specific package file.
* **plugin.js**
	Finally, this is your plugin code.

---

## plugin.js

The sample plugin.js will take a form ID, then query the API and return the form data to the user.

The `eventData` object passed into your function is used to fetch request data from the user, including query string parameters or form post data. It is also used to send response data back to the user. In this example, `eventData.request.query.id` fetches an `id` paramter from the query string for the form ID. Using the included `znHttp` library, it makes a request to the REST API and fetches the form by that ID. Then it uses `eventData.response` to return HTTP 200 on success and sends the API response to the user.

You will also notice some error checking logic, in case the API returns an error or the user makes an invalid request.

{% highlight javascript %}
{% raw %}
// Plugin code goes here
var znHttp = require('./lib/zn-http');

exports.run = function(eventData) {

	if (eventData.request.method === 'GET') {

		var formId = eventData.request.query.id;

		znHttp().get('/forms/' + formId).then(function(response) {

			// return first form
			eventData.response.status(200).send(response.getBody());

		}, function(error) {

			eventData.response.status(404).send(error.getBody());
		});
		
	} else {
		eventData.response.status(404).send('Not found');
	}

}

{% endraw %}
{% endhighlight %}

## eventData

The `eventData` object contains the request and the response objects for getting data from the user and sending data to the user.

### eventData.request Properties

<div>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>headers</td>
				<td><code>Object<code></td>
				<td>HTTP request headers</td>
			</tr>
			<tr>
				<td>body</td>
				<td><code>Object</code></td>
				<td>HTTP POST data. Corresponds to <a href="http://expressjs.com/api.html#req.body" target="_blank">req.body</a></td>
			</tr>
			<tr>
				<td>method</td>
				<td><code>String</code></td>
				<td><code>GET</code>, <code>POST</code>, <code>PUT</code>, or <code>DELETE</code></td>
			</tr>
			<tr>
				<td>params</td>
				<td><code>Object</code></td>
				<td>Contains request's <code>workspaceId</code>, <code>pluginNamespace</code>, and <code>pluginRoute</code></td>
			</tr>
			<tr>
				<td>query</td>
				<td><code>Object</code></td>
				<td>Contains query string parameters. Corresponds to <a href="http://expressjs.com/api.html#req.query" target="_blank">req.query</a></td>
			</tr>
			<tr>
				<td>originalUrl</td>
				<td><code>String</code></td>
				<td>Request URL. Corresponds to <a href="http://expressjs.com/api.html#req.originalUrl" target="_blank">req.originalUrl</a></td>
			</tr>
		</tbody>
	</table>
</div>

### eventData.response Methods

<div>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>set</td>
				<td>Set response HTTP headers. Corresponds to <a href="http://expressjs.com/api.html#res.set" target="_blank">res.set</a></td>
			</tr>
			<tr>
				<td>status</td>
				<td>Set response HTTP status code. Corresponds to <a href="http://expressjs.com/api.html#res.status" target="_blank">res.status</a></td>
			</tr>
			<tr>
				<td>send</td>
				<td>Send response body. Corresponds to <a href="http://expressjs.com/api.html#res.send" target="_blank">res.send</a></td>
			</tr>
			<tr>
				<td>end</td>
				<td>Ends the response. Corresponds to <a href="http://expressjs.com/api.html#res.end" target="_blank">res.end</a></td>
			</tr>
		</tbody>
	</table>
</div>

## znHttp

znHttp is an included library for connecting to the {{site.productName}} REST API. API requests will be made as the user accessing the service. When used in conjunction with frontend plugin code, the user accessing your frontend plugin will be the user making the API requests. This means the plugin service can only access what the acting user has permission to access. When saving data, that user will be considered the "created by user."

When using offline services and executing them by a webhook instead of the {{site.productName}} app, the user making the request will be a special "integration" user with the same permissions as the workspace admin.

Note: Services shutdown immediately when the response is sent to the user, cancelling any open HTTP requests. Because of this, the response must be sent only when all HTTP requests are completed, inside the success or error callbacks.

{% highlight javascript %}
{% raw %}
// Good
znHttp().post('/forms/123/records', { field456: 'Name' }).then(function(response) {
	eventData.response.status(200).send(response.getBody());
}, function(error) {
	eventData.response.status(404).send(error.getBody());
});

// Bad
znHttp().post('/forms/123/records', { field456: 'Name' });
eventData.response.status(200).send();
{% endraw %}
{% endhighlight %}

## Executing Draft Services

When using the Developer tools and testing your plugin in draft mode, the `znPluginData` service will automatically make requests to your draft service. Your user will be making the requests to the service, so actions will be taken under your account. Keep in mind this will not be the case when you publish the plugin and other users are the ones accessing your plugin.

When working with your service locally or when accessing the service directly, to use the draft service, you will need to manually pass in the draft HTTP header `X-Plugin-Draft`. This header value must be a valid access token for the plugin developer. By default, this will make requests as the plugin developer. You can pas a second HTTP authorization header to specify a different, valid access token, if known. 

{% highlight %}
X-Plugin-Draft: 93c43cc17b5ecdb53b5b732247c99086
Authorization: Bearer 93c43cc17b5ecdb53b5b732247c99086
{% endhighlight %}


