---
layout: plugin-nav-bar
group: tutorials
subgroup: record-sms
---

<h1 id="creating-a-record-sms-plugin">Creating a Record SMS Plugin (Part 1) <a href="https://github.com/ZengineHQ/labs/tree/{{site.githubBranch}}/plugins/record-sms" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

This tutorial guides you through the process of building a plugin that sends text messages when a record is created. We will use [Twillio](https://www.twilio.com){:target="_blank"} to send the text messages and {{site.productName}} webhooks to trigger the messages to sends on record creation.

If you haven't yet done so, we recommended first reading about [backend services]({{site.baseurl}}/plugins/development/services.html) and [developing plugins with Firebase]({{site.baseurl}}/plugins/third-party/developing-plugins-with-firebase.html) before starting this tutorial.

## Prerequisites

Before developing the plugin, you will need a [Twillio account](https://www.twilio.com/try-twilio){:target="_blank"}, which can be created for free, if you don't have one yet. Once you have an account, log into Twillio and go to the [account settings page](https://www.twilio.com/user/account/settings){:target="_blank"} to view your API Credentials. For the purposes of this tutorial, you can use your test **AccountSID** and **AuthToken**, so you don't need to pay for sent messages.

## Creating A Backend Plugin Service

When a {{site.productName}} webhook is triggered, it makes a POST request with a payload about the triggered data. We need to setup a service to receive these payloads and send the text messages. We can do this by adding a backend service to our plugin from the [Developer tools]({{site.clientDomain}}/account/developer){:target="_blank"}. After creating a backend service, downloading and unzipping the draft source code, go to the top-level of the code directory, and run the command below to install the [Twillio Node.js library](https://www.twilio.com/docs/node/install){:target="_blank"}:

{% highlight js %}
npm install twilio --save
{% endhighlight %}

Then in the plugin.js file, add the following code: 

{% highlight js %}
{% raw %}
exports.run = function(eventData) {

    var sendSms = function() {

        var accountSid = '{{your AccountSID goes here}}',
            authToken = '{{your AuthToken goes here}}',
            client = require('twilio')(accountSid, authToken);

        var params = {
            body: 'A record was created!',
            to: '{{ any valid mobile number}}',
            from: '+15005550006'
        };

        client.sms.messages.create(params, function(err, sms) {

            if (err) {
                eventData.response.status(404).send(err);
            } else {
                eventData.response.status(200).send(sms);
            }

        });

    };

    sendSms();

}
{% endraw %}
{% endhighlight %}

If you use your test API credentials, you can test sending a successful sms by using the magic number **+15005550006** as the **From** number, and a regular phone number for the **To** number. To generate failure cases, check out this [list of test numbers](https://www.twilio.com/docs/api/rest/test-credentials#test-sms-messages-parameters-From){:target="_blank"}. You can try this out locally by booting up the node app by running `npm start` and going to [localhost:3000/](localhost:3000/){:target="_blank"}. This won't actually send the text message, but you should get a successful response back. If you want to send real text messages, you must use your live API credentials. If you want to send text messages for free through your trial account, follow this [5-step process](https://www.twilio.com/help/faq/twilio-basics/how-does-twilios-free-trial-work){:target="_blank"}.

## Plugin Settings

Now we want to allow workspace administrators to customize what triggers these sms messages to be sent. We can do this by adding a settings interface to your frontend plugin code. We accomplish this when invoking the register function and passing the `interfaces` array containing an object of type `settings`. You might have noticed your default frontend plugin.js code already comes with this settings interface set up, but in case not, you can use the code below (replaced with your own plugin namespace):

{% highlight js %}

.register('namespaced-record-sms', {
    route: '/namespaced-record-sms',
    title: 'Record SMS Plugin',
    icon: 'icon-mobile',
    interfaces: [
        {
            controller: 'namespacedRecordSmsSettingsCntl',
            template: 'namespaced-record-sms-settings',
            type: 'settings'
        }
    ]
});


{% endhighlight js%}

Now workspaces admins can go to the workspace settings section in {{site.productName}}, and click on the "Record SMS Plugin" card to edit settings about this plugin.

![Record SMS Plugin]({{ site.baseurl }}/img/plugins/tutorials/record-sms-settings.png)

## Configuring the Webhook

Now that our plugin has a settings section, we can use it to create the webhooks that will make requests to the plugin service endpoint. For the purposes of this tutorial, assume the webhook should trigger for events about records, so we set the `resource` attribute to `'records'`. We also assume we only care about records in the current workspace, so we can get the workspace ID from the route using `$routeParams`. We don't want the webhook to trigger for any activity on tasks, events, or comments associated with the records, so we set `includeRelated` to `false`.

Below is the resulting base data we will use to create a webhook: 
{% highlight js %}
var baseUrl = '{{site.pluginDomain}}/workspaces/' + $routeParams.workspace_id,
    data = {
        resource: 'records',
        workspace: {
            id: $routeParams.workspace_id
        },
        includeRelated: false,
        url: baseUrl + '/' + $scope.pluginName + '/sms-messages'
    };
{% endhighlight %}

Now we want to allow users to further filter down which records trigger the webhook, so we allow users to choose a form from a dropdown list of forms in the workspace. If a form is chosen, we also provide the option to create a data filter.

Add the code below, replacing 'namespaced' with your namespace, and 'sms-messages' with the actual route of your backend service. so a webhook is created when a workspace admin clicks the "Save" button.

{% highlight js %}
plugin.controller('namespacedRecordSmsSettingsCntl', [
    '$scope',
    '$routeParams',
    '$firebase',
    'znMessage',
    'znData',
    'znFiltersPanel',
    function (
        $scope,
        $routeParams,
        $firebase,
        znMessage,
        znData,
        znFiltersPanel
    ) {

        /**
         * Save Plugin Settings
         */
        $scope.save = function() {

            var baseUrl = '{{site.pluginDomain}}/workspaces/' + $routeParams.workspace_id,
                data = {
                    resource: 'records',
                    workspace: {
                        id: $routeParams.workspace_id
                    },
                    includeRelated: false,
                    url: baseUrl + '/' + $scope.pluginName + '/sms-messages'
                };

            $scope.settings.webhook = $scope.settings.webhook || {};

            if ($scope.settings.webhook.form &&
                $scope.settings.webhook.form.id) {
                data['form.id'] = $scope.settings.webhook.form.id;
            }

            if ($scope.settings.webhook.filter) {
                data['filter'] =  $scope.settings.webhook.filter;
            }

            var success = function(response) {

                znMessage('Settings Updated', 'saved');

            };

            znData('Webhooks').save(data, success);

        };

        /**
         * Reset Filter
         */
        $scope.resetFilter = function() {
            delete $scope.settings.webhook.filter;
            $scope.filterCount = null;
        };


        /**
         * Open Filter Panel
         */
        $scope.openFiltersPanel = function() {

            var params = {
                formId: $scope.settings.webhook.form.id,
                subfilters: false,
                onSave: function(filter) {
                    $scope.settings.webhook.filter = filter;
                    $scope.filterCount = filter[Object.keys(filter)[0]].length;
                }
            };

            if ($scope.settings.webhook && $scope.settings.webhook.filter) {
                params.filter = $scope.settings.webhook.filter;
            }

            znFiltersPanel.open(params);
        };

        /**
         * Load Forms For Workspace
         *
         */
        znData('Forms').query(
            {
                workspace: { id: $routeParams.workspace_id },
                related: 'fields',
                attributes: 'id,name,singularName'
            },
            function(data) {
                $scope.forms = data;
            }
        );
    }
])
{% endhighlight %}

The html below creates a form called `pluginSettings` that allows users to choose a form ID and a [data filter]({{site.baseurl}}/rest-api/conventions/data-filters/).

{% highlight html %}
{% raw %}
<script id="record-sms-settings" type="text/ng-template">
    <div class="col-md-6 panel-white">
        <div ng-show="loading"><span class="throbber"></span></div>
        <form class="form" name="pluginSettings" ng-submit="save()" ng-show='!loading'>
            <h2><i class="icon-zengine"></i> Record Settings</h2>
            <div class="control-group">
                <label for="form-label" class="form-label">Form</label>
                <div class="controls">
                    <select ng-model="settings.webhook.form.id" name="formId" class="input-xxlarge" ng-options="form.id as form.name for form in forms">
                        <option value=""></option>
                    </select>
                    <a href="javascript:void(0)" class="btn btn-small"
                        ng-click="openFiltersPanel()" ng-disabled="!settings.webhook.form.id"
                        tooltip="Filter" tooltip-placement="right">
                        <i class="icon-filter"></i>
                        <span ng-show="filterCount">&nbsp;</span>
                        <span class="badge badge-primary ng-binding" ng-show="filterCount">
                            {{filterCount}}
                        </span>
                    </a>
                </div>
            </div>
            <hr/>
            <div class="form-actions">
                <input type="submit" class="btn btn-primary" ng-disabled="pluginSettings.$invalid" value="Save">
            </div>
        </form>
    </div>
</script>
{% endraw %}
{% endhighlight %}

## Using Webhook Payload Data

Now that the webhook is being created in the plugin settings, text messages will be sent anytime a record is created, updated, or deleted in the workspace. However, we only want to send messages when a record is created, so we need to update our service code to look at the payload data, and ignore non-creates.

Update your service code below to use the payload data to filter out updates and deletes, as well as send the record id in the message body.

{% highlight js %}
{% raw %}
exports.run = function(eventData) {

    var sendSms = function() {

        if (eventData.request.body.data &&
            eventData.request.body.data[0].action === 'create') {

            var accountSid = '{{your AccountSID goes here}}',
                authToken = '{{your AuthToken goes here}}',
                client = require('twilio')(accountSid, authToken);

            var recordId = eventData.request.body.data[0].record.id;

            var message = 'Record ' + recordId + ' was created!';

            var params = {
                body: message,
                to: '{{ any valid mobile number}}',
                from: '+15005550006'
            };

            client.sms.messages.create(params, function(err, sms) {

                if (err) {
                    eventData.response.status(404).send(err);
                } else {
                    eventData.response.status(200).send(sms);
                }

            });

        } else {
            eventData.response.status(403).send('Forbidden');
        }

    };

    sendSms();

}
{% endraw %}
{% endhighlight %}

Once you have updated your `plugin.js` file, zip the updated plugin folder and upload it back to your plugin service. In order to test it working via a webhook, you will need to publish your plugin. If you don't want it to show up in the marketplace, make sure your plugin is private.

After publishing your plugin, add it to a workspace, and create a record in that workspace. For record creation, a sample webhook payload looks like this:

{% highlight json %}
{
    "developerMessage": "The data in the payload is an activity.",
    "webhook": {
        "id": 26430
    },
    "webhookEvent": {
        "id": 1622
    },
    "data": [
        {
            "id": 1089360,
            "workspace": {
                "id": 237
            },
            "resource": "records",
            "action": "create",
            "createdByUser": {
                "id": 109
            },
            "record": {
                "id": 866944,
                "form": {
                    "id": 2237
                },
                "folder": {
                    "id": 0
                }
            },
            "created": "2015-09-23 18:02:29"
        }
    ]
}
{% endhighlight %}

You can see whether the webhook POST request was successful by querying the [webhook_events endpoint]({{site.baseurl}}/rest-api/resources/#!/webhook_events){:target="_blank"}. If you are using the live Twillio API credentials, you can also look at the [Twillio SMS logs](https://www.twilio.com/user/account/log/messages).

## Saving Webhook Settings to Firebase

In order for workspace administrators to be able to come back and edit these settings, we will use Firebase to store them.

We want to restrict updating the Firebase data to workspace admins, so we take advantage of the `firebaseAuthToken` returned from the plugin API response to connect to Firebase. After sucessful authentication, the `$scope.connect()` method will assign the settings data to a scope property and turn off the loading indicator.

Note that you need to inject the `$firebase` service in your controller signature.

{% highlight js %}

$scope.loading = true;

/**
 * Connect to Firebase
 */
$scope.connect = function() {
    
    // Firebase reference
    var ref = new Firebase($scope.plugin.firebaseUrl + '/' + $routeParams.workspace_id);

    // Authenticate user
    ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {

        // Log error if present and return
        if (err) {
            console.log(err);
            return;
        }

        // Fetch settings
        $scope.settings = $firebase(ref.child('settings')).$asObject();
        
        $scope.settings.$loaded().then(function(data) {
            $scope.loading = false;
            if ($scope.settings.webhook && $scope.settings.webhook.filter) {
                var filter = $scope.settings.webhook.filter;
                $scope.filterCount = filter[Object.keys(filter)[0]].length;
            }
        });

    });

};

/**
 * Get plugin data
 *
 * equivalent to: GET https://stage-api.zenginehq.com/v1/plugins/?namespace={pluginName}
 */
znData('Plugins').get(
    // Params
    {
        namespace: $scope.pluginName
    },
    // Success
    function(resp) {
        // Note: the response comes back as an array, but because namespaces are unique
        // this request will contain just one element, for convenience assign the
        // first element to `$scope.plugin`
        $scope.plugin = resp[0];
        $scope.connect();
    },
    // Error
    function(resp) {
        $scope.err = resp;
    }
);
{% endhighlight %}

Now that we are fetching data from Firebase, we need to update the `$scope.save` method to save the data to Firebase. We can do this with by calling `$scope.settings.$save()`. For more information, check out the [AngularFire documention on $save]({{site.baseurl}}/libraries/angularfire/{{site.angularFireVersion}}/#angularfire-firebaseobject-save){:target="_blank"}.

Since a new webhook is created each time the "Save" button is clicked, the code below also adds some logic to store the webhook id in Firebase and delete the old one before creating a new one.

{% highlight js %}

/**
 * Save Plugin Settings
 */
$scope.save = function() {

    var baseUrl = '{{site.pluginDomain}}/workspaces/' + $routeParams.workspace_id,
        data = {
            workspace: {
                id: $routeParams.workspace_id
            },
            resource: 'records',
            includeRelated: false,
            url: baseUrl + '/' + $scope.pluginName + '/sms-messages'
        };

    $scope.settings.webhook = $scope.settings.webhook || {};

    if ($scope.settings.webhook.id) {
        znData('Webhooks').delete({id: $scope.settings.webhook.id});
    }

    if ($scope.settings.webhook.form &&
        $scope.settings.webhook.form.id) {
        data['form.id'] = $scope.settings.webhook.form.id;
    }

    if ($scope.settings.webhook.filter) {
        data['filter'] =  $scope.settings.webhook.filter;
    }

    var success = function(response) {

        if (response && response.id) {
            $scope.settings.webhook.id = response.id;
        }

        $scope.updateFirebaseData();

        znMessage('Settings Updated', 'saved');

    };
    
    znData('Webhooks').save(data, success);
    
};

/**
 * Save Settings To Firebase
 */
$scope.updateFirebaseData = function() {
    
    $scope.settings.$save();

};

{% endhighlight %}

## Wrapping Up
At this point you should have a functional plugin that will send sms messages anytime a record matching certain user-defined conditions is created in the workspace. In [part 2]({{site.baseurl}}/plugins/tutorials/record-sms-2), we will work on making the plugin more customizable and secure.

Your plugin backend code should look like this:
{% highlight js %}
{% raw %}
exports.run = function(eventData) {

    var sendSms = function() {

        if (eventData.request.body.data &&
            eventData.request.body.data[0].action === 'create') {

            var accountSid = '{{your AccountSID goes here}}',
                authToken = '{{your AuthToken goes here}}',
                client = require('twilio')(accountSid, authToken);

            var recordId = eventData.request.body.data[0].record.id;

            var message = 'Record ' + recordId + ' was created!';

            var params = {
                body: message,
                to: '{{ any valid mobile number}}',
                from: '+15005550006'
            };

            client.sms.messages.create(params, function(err, sms) {

                if (err) {
                    eventData.response.status(404).send(err);
                } else {
                    eventData.response.status(200).send(sms);
                }

            });

        } else {
            eventData.response.status(403).send('Forbidden');
        }

    };

    sendSms();

}
{% endraw %}
{% endhighlight %}


Your plugin frontend code should now look something like this (with your own plugin namespace in the js registration options and html template id, and replace '/sms-messages' with your own plugin service route):

<ul class="nav nav-tabs" role="tablist" id="myTab">
  <li class="active"><a href="#plugin-js" role="tab" data-toggle="tab">plugin.js</a></li>
  <li><a href="#plugin-html" role="tab" data-toggle="tab">plugin.html</a></li>
  <li><a href="#plugin-css" role="tab" data-toggle="tab">plugin.css</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade in active" id="plugin-js">

{% highlight js %}
plugin.controller('namespacedRecordSmsSettingsCntl', [
    '$scope',
    '$routeParams',
    '$firebase',
    'znMessage',
    'znData',
    'znFiltersPanel',
    function (
        $scope,
        $routeParams,
        $firebase,
        znMessage,
        znData,
        znFiltersPanel
    ) {


        /**
         * Save Plugin Settings
         */
        $scope.save = function() {

            var baseUrl = '{{site.pluginDomain}}/workspaces/' + $routeParams.workspace_id,
                data = {
                    workspace: {
                        id: $routeParams.workspace_id
                    },
                    resource: 'records',
                    includeRelated: false,
                    url: baseUrl + '/' + $scope.pluginName + '/sms-messages'
                };

            $scope.settings.webhook = $scope.settings.webhook || {};

            if ($scope.settings.webhook.id) {
                znData('Webhooks').delete({id: $scope.settings.webhook.id});
            }

            if ($scope.settings.webhook.form &&
                $scope.settings.webhook.form.id) {
                data['form.id'] = $scope.settings.webhook.form.id;
            }

            if ($scope.settings.webhook.filter) {
                data['filter'] =  $scope.settings.webhook.filter;
            }

            var success = function(response) {

                $scope.updateFirebaseData();

                znMessage('Settings Updated', 'saved');

            };

            znData('Webhooks').save(data, success);

        };

        /**
         * Save Settings To Firebase
         */
        $scope.updateFirebaseData = function() {
            
            $scope.settings.$save();

        };

        /**
         * Reset Filter
         */
        $scope.resetFilter = function() {
            delete $scope.settings.webhook.filter;
            $scope.filterCount = null;   
        };

        /**
         * Open Filter Panel
         */
        $scope.openFiltersPanel = function() {

            var params = {
                formId: $scope.settings.webhook.form.id,
                subfilters: false,
                onSave: function(filter) {
                    $scope.settings.webhook.filter = filter;
                    $scope.filterCount = filter[Object.keys(filter)[0]].length;
                }
            };

            if ($scope.settings.webhook && $scope.settings.webhook.filter) {
                params.filter = $scope.settings.webhook.filter;
            }
            
            znFiltersPanel.open(params);
        };

        $scope.loading = true;

        /**
         * Connect to Firebase
         */
        $scope.connect = function() {
                
            // Firebase reference
            var ref = new Firebase($scope.plugin.firebaseUrl + '/' + $routeParams.workspace_id);

            // Authenticate user
            ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {

                // Log error if present and return
                if (err) {
                    console.log(err);
                    return;
                }

                // Fetch readable settings
                $scope.settings = $firebase(ref.child('settings')).$asObject();
                
                $scope.settings.$loaded().then(function(data) {
                    $scope.loading = false;
                    if ($scope.settings.webhook && $scope.settings.webhook.filter) {
                        var filter = $scope.settings.webhook.filter;
                        $scope.filterCount = filter[Object.keys(filter)[0]].length;
                    }
                });

            });

        };
        

        /**
         * Load Forms For Workspace
         *
         */
        znData('Forms').query(
            {
                workspace: { id: $routeParams.workspace_id },
                related: 'fields',
                attributes: 'id,name,singularName'
            },
            function(data) {
                $scope.forms = data;
            }
        );
    
        /**
         * Get plugin data
         *
         * equivalent to: GET https://stage-api.zenginehq.com/v1/plugins/?namespace={pluginName}
         */
        
        znData('Plugins').get(
            // Params
            {
                namespace: $scope.pluginName
            },
            // Success
            function(resp) {
                // Note: the response comes back as an array, but because namespaces are unique
                // this request will contain just one element, for convenience let assign the
                // first element to `$scope.plugin`
                $scope.plugin = resp[0];
                $scope.connect();
            },
            // Error
            function(resp) {
                $scope.err = resp;
            }
        );
        
    }  
])
.register('record-sms', {
    route: '/record-sms',
    title: 'Record SMS Plugin',
    icon: 'icon-mobile',
    interfaces: [
        {
            controller: 'namespacedRecordSmsSettingsCntl',
            template: 'record-sms-settings',
            type: 'settings'
        }
    ]
});
{% endhighlight %}
    </div>
    <div class="tab-pane fade" id="plugin-html">
{% highlight html %}
{% raw %}
<script id='record-sms-settings' type='text/ng-template'>
    <div class="col-md-6 panel-white">
        <div ng-show='loading'><span class="throbber"></span></div>
        <form class="form" name="pluginSettings" ng-submit="save()" ng-show='!loading'>
            <h2><i class="icon-zengine"></i> Record Settings</h2>
            <div class="control-group">
                <label for="form-label" class="form-label">Form</label>
                <div class="controls">
                    <select ng-model="settings.webhook.form.id" name="formId" class="input-xxlarge" ng-options="form.id as form.name for form in forms"
                        ng-change="resetFilter()">
                        <option value=""></option>
                    </select>
                    <a href="javascript:void(0)" class="btn btn-small" 
                        ng-click="openFiltersPanel()" ng-disabled="!settings.webhook.form.id" 
                        tooltip="Filter" tooltip-placement="right">
                        <i class="icon-filter"></i>
                        <span ng-show="filters.count">&nbsp;</span>
                        <span class="badge badge-primary ng-binding" ng-show="filterCount">
                            {{filterCount}}
                        </span>
                    </a>
                </div>
            </div>
            <hr/>
            <div class="form-actions">
                <input type="submit" class="btn btn-primary" ng-disabled="pluginSettings.$invalid" value="Save">
            </div>
        </form>
    </div>
</script>
{% endraw %}
{% endhighlight %}
</div>
<div class="tab-pane fade" id="plugin-css">
{% highlight css %}
.form select {
    height: 30px;
}
{% endhighlight %}
    </div>
</div>
