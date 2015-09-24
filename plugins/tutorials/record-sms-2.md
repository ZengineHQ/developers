---
layout: plugin-nav-bar
group: tutorials
subgroup: record-sms-2
---

<h1 id="creating-a-record-sms-plugin">Creating a Record SMS Plugin (Part 2) <a href="https://github.com/ZengineHQ/labs/tree/master/plugins/record-sms" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

In [part 1]({{site.baseurl}}/plugins/tutorials/record-sms), we created a plugin that sends a text message whenever a record is created.

At this point, the text message is hard-coded to say something like 'Record 123 was created!' and is sent to some hard-coded phone number. The plugin would be more useful if the workspace admins could have more control over this. In this part of the tutorial, we will add in the ability for users to specify the message body and **To** number, as well as connect their own Twillio accounts, so they can specify their own **From** number.

## Adding SMS Settings

First let's add a section to the settings interface: SMS Settings.

This section will allow workspace admins to specify three things, the **To** number, the **From** number, and the content of the text message. Currently, the webhook settings are stored in `https://<your-firebase>.firebaseio.com/<workspaceId>/settings/webhook`. Let's store an `sms` object under the settings path as well, with the following properties: `to`, `from`, and `body`. 

Use the html below to add SMS Settings as a section in the `pluginSettings` form. Note the required validation on the two numbers, and some basic phone number validation on the **To** phone number. (The ***From** number can either be a phone number or an alphanumeric sender ID, so we skip validation for it.)

{% highlight html %}
{% raw %}
<h2>
    <i class="icon-mobile"></i> SMS Settings
</h2>
<div class="control-group">
    <label for="form-label" class="form-label">To Number</label>
    <div class="controls">
        <input type="text" name="to-phone-number" class="input-xxlarge"
            ng-model="settings.sms.to"
            ng-required="true" ng-pattern="/^\+?[0-9-()\s]+$/">
        <span class="help-inline required">required</span>
    </div>
</div>
<div class="control-group">
    <label for="form-label" class="form-label">From Number</label>
    <div class="controls">
        <input type="text" name="from-phone-number" class="input-xxlarge"
            ng-model="settings.sms.from"
            ng-required="true">
        <span class="help-inline required">required</span>
    </div>
</div>
<div class="control-group">
    <label for="form-label" class="form-label">Message</label>
    <div class="controls">
        <textarea ng-model="settings.sms.body" name="body" class="input-xxlarge"/>
    </div>
</div>
{% endraw %}
{% endhighlight %}


Note that since we are putting this new `sms` object under settings, and we are already sending the entire settings object to Firebase by calling `$scope.settings.$save()`, no additional JavaScript is needed.

## Adding Twillio Credentials

When you specify a **From** number, it needs to be a phone number or [alphanumeric sender ID](https://www.twilio.com/help/faq/sms/what-is-alphanumeric-sender-id-and-how-do-i-get-started){:target="_blank"} that has been purchased from Twillio and configured to send sms. Most likely workspace admins will try to use **From** numbers connected to their Twillio account, which means we need a way for them to provide the credentials for their account. So let's add a third section to the settings interface: Twillio Credentials.

Twilio uses two credentials to determine which account an API request is coming from. The **Account SID**, which acts as a username, and the **Auth Token** which acts as a password.

Since the **Auth Token** should be kept private, we don't want it to be displayed in the settings interface. So we need to update the Firebase security rules to accomodate that constraint. So far, everything has been saved in `$scope.settings`. These properties, such as the **To** and **From** numbers, need to be displayed by the frontend, and later accessed by the backend service, so it knows how to send the text message. So the rule to reflect that is to set the `.read` property to:

{% highlight json %}
"auth.workspaces[$workspace] === 'admin' ||
auth.workspaces[$workspace] === 'owner' ||
auth.workspaces[$workspace] === 'server'"
{% endhighlight %}

If `auth.workspaces[$workspace]` is `'admin'` or `'owner'` it means it is a {{site.productName}} authenticated user that can access the workspace settings section, which means they are either an admin or owner of the workspace. If the value of `auth.workspaces[$workspace]` is 'server', it means it is a backend service.

Since the **Auth Token** has slightly different read permissions, we can't store it in `settings`. Instead, we will store it under `secrets` with the following read rule:

{% highlight json %}
"auth.workspaces[$workspace] === 'server'"
{% endhighlight %}

Finally, the write permissions for these properties is the same, so we can put it at the parent level. Here is what the final JSON looks like for the Firebase security rules.

{% highlight json %}

{
  "rules": {
    "$workspace": {
      ".write": "auth.workspaces[$workspace] === 'admin' ||
                  auth.workspaces[$workspace] === 'owner'",
      "settings": {
        ".read": "auth.workspaces[$workspace] === 'admin' ||
                  auth.workspaces[$workspace] === 'owner' ||
                  auth.workspaces[$workspace] === 'server'"
      },
      "secrets": {
        ".read": "auth.workspaces[$workspace] === 'server'"
      }
    }
  }
}

{% endhighlight %}

Use the html below to add a Twillio Credentials section to your existing the `pluginSettings` form. Note the help text for the **Auth Token** reflects these new security rules.

{% highlight html%}
{% raw %}

<h2>
    <i class="icon-login"></i> Twillio Credentials
</h2>
<div class="control-group">
    <label for="form-label" class="form-label">Account SID</label>
    <div class="controls">
        <input ng-model="settings.twillio.accountSid" type="text" class="input-xxlarge">
        <span class="help-block">
            Log into your Twilio account and go to 
            <a target="_blank" href="https://www.twilio.com/user/account/settings">
                https://www.twilio.com/user/account/settings
            </a>
        </span>
    </div>
</div>
<div class="control-group">
    <label for="form-label" class="form-label">Auth Token</label>
    <div class="controls">
        <input type="text" ng-model="secrets.twillioAuthToken" class="input-xxlarge">
        <span class="help-block">
            Once submitted, your Twillio Auth Token 
            will be stored but not visible.
            You can use this input to edit it at any time.
        </span>
    </div>
</div>
{% endraw %}
{% endhighlight %}

Now that we are saving the **Auth Token** to `https://<your-firebase>.firebaseio.com/<workspaceId>/secrets`, we need to update our fronted plugin.js code.

First, we must modify the `$scope.connect` method to fetch a reference to the `secrets` path in Firebase and set it as a scope property. Note we can't use the `$asObject` method because we don't have read permission.

{% highlight js %}

/**
 * Connect to Firebase
 */
$scope.connect = function() {
    
    $scope.secrets = {};

    // Firebase reference
    var ref = new Firebase($scope.plugin.firebaseUrl + '/' + $routeParams.workspace_id);

    // Authenticate user
    ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {
        
        // Log error if present and return
        if (err) {
            console.log(err);
            return;
        }
        
        // Set reference to secrets (non-readable by the frontend)
        $scope.secretsSync = $firebase(ref.child('secrets'));
        
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
{% endhighlight %}

Now we need to modify the `$scope.updatedFirebaseData` method to use the `$update` method to save the **Auth Token** to Firebase if passed. For more information, check out the [AngularFire documention on $update]({{site.baseurl}}/libraries/angularfire/{{site.angularFireVersion}}/#angularfire-firebase-updatekey-data){:target="_blank"}.

{% highlight js %}
$scope.updateFirebaseData = function() {
    
    var secrets = angular.extend({}, $scope.secrets);
   
    // so we don't remove any existing auth token,
    // if it's missing from the form 
    if (!secrets.twillioAuthToken) {
        delete secrets.twillioAuthToken;
    }
    
    // Only passed properties will be updated
    $scope.secretsSync.$update(secrets);
    
    delete $scope.secrets.twillioAuthToken;
    
    $scope.settings.$save();

};
{% endhighlight %}

## Using Firebase in Your Backend Service

Now that we are saving these SMS and Twillio Settings in Firebase, we need to update our backend service code to use these new settings.

If a frontend component of the plugin was making requests to the backend service, we could send the Firebase settings as part of the payload. However, since a webhook is making the request to the backend service with a predetermined payload, the backend service needs to fetch these settings from Firebase directly.

In order to do this, we first need to include the `zn-firebase` library, which when invoked, returns a reference to a `Firebase` object constructed from the Firebase URL associated with your plugin. If you are developing locally, you will need to pass in this url as the header `X-Firebase-Url`.

{% highlight js %}

var znFirebase = require('./lib/zn-firebase');

{% endhighlight %}

After including the `zn-firebase` library, we can now fetch the data from Firebase. Even though the backend service has read access to both `/<workspaceId>/settings` and `/<workspaceId>/secrets`, we can't make a single request to the parent object `/<workspaceId>`. This is because of two facts about how Security and Firebase Rules work: 1.) [Rules are not filters](https://www.firebase.com/docs/security/guide/securing-data.html#section-filter){:target="_blank"} and 2.) [Rules cascade](https://www.firebase.com/docs/security/guide/securing-data.html#section-cascade){:target="_blank"}. In other words, since didn't define a read rule at the `/<workspaceId>` level (or any parent), we can't read at that location (rules are not filters). And we didn't define a read rule at the parent `/<workspaceId>` level because we have slightly different read rules for the two children, and any rules defined at the child level will be ignored if already defined at the parent level (rules cascade). Therefore, we need to make two separate Firebase requests: one for `/<workspaceId>/settings` and one for `/<workspaceId>/secrets`.

{% highlight js %}

znFirebase().child(workspaceId + '/secrets').once('value', function(secrets) {

    znFirebase().child(workspaceId + '/settings').once('value', function(settings) {
        sendSms(settings.val(), secrets.val());
    });

}, function (err) {
    eventData.response.status(500).send(err);
});

{% endhighlight %}

After fetching the data from Firebase, we can update the `sendSms` function by replacing the hard-coded values with the ones from Firebase. The full backend plugin.js code is below.

{% highlight js %}

exports.run = function(eventData) {

    var sendSms = function(settings, secrets) {

        if (eventData.request.body.data &&
            eventData.request.body.data[0].action === 'create') {

            var accountSid = settings.twillio.accountSid, 
                authToken = secrets.twillioAuthToken,
                client = require('twilio')(accountSid, authToken);

            var recordId = eventData.request.body.data[0].record.id;

            var message = settings.sms.body || 'Record' + recordId + ' was created!'; 

            var params = {
                body: message,
                to: settings.sms.to,
                from: settings.sms.from
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

    var workspaceId = eventData.request.params.workspaceId;

    znFirebase().child(workspaceId + '/secrets').once('value', function(secrets) {

        znFirebase().child(workspaceId + '/settings').once('value', function(settings) {
            sendSms(settings.val(), secrets.val());
        });

    }, function (err) {
        eventData.response.status(500).send(err);
    });

}

{% endhighlight %}

## Webhook Verification

Right now, your backend plugin endpoint is completely public, and anyone that makes a request to it can trigger sending of text messages. If you want to ensure that only the associated webhook can send text messages, we can take advantage of a webhook's unique `secretKey`, which is sent in the `X-Zengine-Webhook-Key` header for every request made by a webhook. The `secretKey` is a returned with the payload when you first create the webhook. So let's save this secret key to Firebase, and verify it against the header in the backend service code.

In the frontend plugin.js code below, we modified the `$scope.save` method slightly to save the `secretKey` to Firebase. We save it to the `secrets` object, so the frontend plugin code has permission to update it, but not read it.

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
            url: baseUrl + '/' + $scope.pluginName + '/sms'
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
        
        if (response && response.secretKey) {
            $scope.settings.webhook.id = response.id;
            $scope.secrets.webhookSecretKey = response.secretKey;    
        }
        
        $scope.updateFirebaseData();

        znMessage('Settings Updated', 'saved');

    };
    
    znData('Webhooks').save(data, success);
    
};

{% endhighlight %}

Now in our service code, we modified the `sendSms` method slightly to compare the header in the request with the `secretKey` in Firebase and return a 401 if they don't match.

{% highlight js %}

var znFirebase = require('./lib/zn-firebase');

exports.run = function(eventData) {

    var sendSms = function(settings, secrets) {

        // Verify The Request is Coming From A Zengine Webhook
        if (eventData.request.headers['x-zengine-webhook-key'] !==
            secrets.webhookSecretKey) {
            return eventData.response.status(401).send('Unauthorized');
        }

        if (eventData.request.body.data &&
            eventData.request.body.data[0].action === 'create') {

            var accountSid = settings.twillio.accountSid, 
                authToken = secrets.twillioAuthToken,
                client = require('twilio')(accountSid, authToken);

            var recordId = eventData.request.body.data[0].record.id;

            var message = settings.sms.body || 'Record' + recordId + ' was created!'; 

            var params = {
                body: message,
                to: settings.sms.to,
                from: settings.sms.from
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

    var workspaceId = eventData.request.params.workspaceId;

    znFirebase().child(workspaceId + '/secrets').once('value', function(secrets) {

        znFirebase().child(workspaceId + '/settings').once('value', function(settings) {
            sendSms(settings.val(), secrets.val());
        });

    }, function (err) {
        eventData.response.status(500).send(err);
    });

}

{% endhighlight %}

## Wrapping Up

Your plugin should now be able to allow workspace admins to connect their own Twillio accounts, and specify the text message body, and to/from numbers. The backend service  


The code for the entire record sms plugin can be found below and also on [Github](https://github.com/ZengineHQ/labs/tree/master/plugins/record-sms){:target="_blank"}. In this case, the plugin namespace is 'recordSms', so to make it work as your own, you will need to replace all instances of the word 'recordSms'/'record-sms' with your namespace.

If you have improvements to the plugin, feel free to make pull requests to the code repository and update the documentation for it [here]({{site.developerDomain}}/edit/gh-pages/plugins/tutorials/record-sms-2.md).

<ul class="nav nav-tabs" role="tablist" id="myTab">
  <li class="active"><a href="#plugin-js" role="tab" data-toggle="tab">plugin.js</a></li>
  <li><a href="#plugin-html" role="tab" data-toggle="tab">plugin.html</a></li>
  <li><a href="#plugin-css" role="tab" data-toggle="tab">plugin.css</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade in active" id="plugin-js">
{% highlight js %}
plugin.controller('recordSmsCntl', [
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
                
                if (response && response.id) {
                    $scope.settings.webhook.id = response.id;
                    $scope.secrets.webhookSecretKey = response.secretKey;    
                }
                
                $scope.updateFirebaseData();
                
                znMessage('Settings Updated', 'saved');

            };
            
            znData('Webhooks').save(data, success);
            

        };
        
        $scope.updateFirebaseData = function() {
            
            var secrets = angular.extend({}, $scope.secrets);
           
            // so we don't remove any existing auth token,
            // if it's missing from the form 
            if (!secrets.twillioAuthToken) {
                delete secrets.twillioAuthToken;
            }
            
            // Use an $update here for write-only properties
            $scope.secretsSync.$update(secrets);
            
            delete $scope.secrets.twillioAuthToken;
            
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
        
        /**
         * Connect to Firebase
         */
        $scope.connect = function() {
            
            $scope.secrets = {};
    
            // Firebase reference
            var ref = new Firebase($scope.plugin.firebaseUrl + '/' + $routeParams.workspace_id);
    
            // Authenticate user
            ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {
                
                // Log error if present and return
                if (err) {
                    console.log(err);
                    return;
                }
                
                // Set reference to secrets (non-readable by the frontend)
                $scope.secretsSync = $firebase(ref.child('secrets'));
                
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
         * equivalent to: GET {{site.apiDomain}}/v1/plugins/?namespace={pluginName}
         */
        $scope.loading = true;
        
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
            controller: 'recordSmsCntl',
            template: 'record-sms-settings',
            type: 'settings'
        }
    ]
});
{% endhighlight %}
    </div>
    <div class="tab-pane fade in active" id="plugin-html">
{% highlight html %}
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
                        <span ng-show="filterCount">&nbsp;</span>
                        <span class="badge badge-primary ng-binding" ng-show="filterCount">
                            {{filterCount}}
                        </span>
                    </a>
                </div>
            </div>
            <hr/>
            <h2>
                <i class="icon-login"></i> Twillio Credentials
            </h2>
            <div class="control-group">
                <label for="form-label" class="form-label">Account SID</label>
                <div class="controls">
                    <input ng-model="settings.twillio.accountSid" type="text" class="input-xxlarge">
                    <span class="help-block">
                        Log into your Twilio account and go to 
                        <a target="_blank" href="https://www.twilio.com/user/account/settings">
                            https://www.twilio.com/user/account/settings
                        </a>
                    </span>
                </div>
            </div>
            <div class="control-group">
                <label for="form-label" class="form-label">Auth Token</label>
                <div class="controls">
                    <input type="text" ng-model="secrets.twillioAuthToken" class="input-xxlarge">
                    <span class="help-block">
                        Once submitted, your Twillio Auth Token 
                        will be stored but not visible.
                        You can use this input to edit it at any time.
                    </span>
                </div>
            </div>
            <h2>
                <i class="icon-mobile"></i> SMS Settings
            </h2>
            <div class="control-group">
                <label for="form-label" class="form-label">To Number</label>
                <div class="controls">
                    <input type="text" name="to-phone-number" class="input-xxlarge"
                        ng-model="settings.sms.to"
                        ng-required="true" ng-pattern="/^\+?[0-9-()\s]+$/">
                    <span class="help-inline required">required</span>
                </div>
            </div>
            <div class="control-group">
                <label for="form-label" class="form-label">From Number</label>
                <div class="controls">
                    <input type="text" name="from-phone-number" class="input-xxlarge"
                        ng-model="settings.sms.from" ng-required="true">
                    <span class="help-inline required">required</span>
                </div>
            </div>
            <div class="control-group">
                <label for="form-label" class="form-label">Message</label>
                <div class="controls">
                    <textarea ng-model="settings.sms.body" name="body" class="input-xxlarge"/>
                </div>
            </div>
            <hr/>
            <div class="form-actions">
                <input type="submit" class="btn btn-primary" ng-disabled="pluginSettings.$invalid" value="Save">
            </div>
        </form>
    </div>
</script>
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