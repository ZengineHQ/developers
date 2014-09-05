---
layout: plugin-nav-bar
group: tutorials
subgroup: chat
---


<h1 id="building-a-chat-plugin">Building a Chat plugin <a href="https://github.com/ZengineHQ/labs/tree/master/plugins/chat-room" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

This tutorial guides you through the process of building a basic workspace-level chat plugin using Firebase to store the chat messages.

If you haven't yet done so, we recommended first reading about [data access]({{site.baseurl}}/plugins/api/services/#zndata) and [developing plugins with Firebase]({{site.baseurl}}/plugins/third-party/developing-plugins-with-firebase.html) before starting this tutorial.

## Get API data

 The first thing you need to do is get some data from the API. Using the znData factory, we can get the currently logged-in user, metadata about this plugin (namely Firebase authentication info), and the members of the workspace.

{% highlight js %}

/**
 * Chat Controller
 */
plugin.controller('namespacedChatCntl', ['$scope', '$routeParams', 'znData', function ($scope, $routeParams, znData) {

    /**
     * Load indicator
     */
    $scope.loading = true;

    /**
     * Get all members in a workspace
     *
     * equivalent to: GET {{site.apiDomain}}/v1/workspaces/{workspaceId}/members
     */
    znData('WorkspaceMembers').query(
        // Params
        {
            workspaceId: $routeParams.workspace_id
        },
        // Success
        function(resp) {
            $scope.members = resp;
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

    /**
     * Get plugin data
     *
     * equivalent to: GET {{site.apiDomain}}/v1/plugins/?namespace=chat
     */
    znData('Plugins').get(
        // Params
        {
            namespace: 'namespaced'
        },
        // Success
        function(resp) {
            // Note: the response comes back as an array, but because namespaces are unique
            // this request will contain just one element, for convenience let assign the
            // first element to `$scope.plugin` to save us the need to refer to it as `$scope.plugin[0]`
            // to read plugin properties
            $scope.plugin = resp[0];
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

    /**
     * Get currently logged-in user
     *
     * equivalent to: GET {{site.apiDomain}}/v1/users/me
     */
    znData('Users').get(
        // Params
        {
            id: 'me'
        },
        // Success
        function(resp) {
            $scope.me = resp;
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

}])

{% endhighlight %}
If you run this code and check the browser network requests you can find the three requests against the {{site.productName}} API.

You can also add a call to `console.log(res)` in each request success function to dump the response data in your browser console.

## Wait for API responses

After making the API requests, we need to wait for the success callbacks to finish before connecting to Firebase. Depending on how familiar you are with AngularJS, you may know the concept of [watchers]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope#$watchCollection){:target="_blank"}. In short, you can set a watcher for one or more properties on an object and be notified when a change occurs. In this use case, we want to know when the following properties are loaded: `$scope.members`, `$scope.plugin` and `$scope.me`.


{% highlight js %}

    /**
     * Wait for members, plugin and current user data to be loaded before connect with Firebase
     */
    var unbindInitialDataFetch = $scope.$watchCollection('[members, plugin, me]', function() {

        // If there is an err in the scope:
        // 1. Change the state of the loading indicator to false
        // 2. Remove the watcher
        // 3. Return (the plugin.html should contain logic to show the error message)
        if ($scope.err) {
            $scope.loading = false;
            unbindInitialDataFetch();
            return;
        }

        // Check if all of the three `$scope` properties have been defined
        // 1. Remove the watcher
        // 2. Call `$scope.connect` to connect with Firebase
        if ($scope.members !== undefined && $scope.plugin !== undefined && $scope.me !== undefined) {
            unbindInitialDataFetch();
            $scope.connect();
        }

    });

{% endhighlight %}

The method `$watchCollection` returns a function that can be called to dispose/remove the watcher. In this case the data is fetched once when the plugin loads into the workspace without further need to call `$scope.connect()`.

## Connecting to Firebase

We want to restrict the Firebase data to {{site.productName}} authenticated users, so we take advantage of the `firebaseAuthToken` returned from the plugin API response to connect to Firebase. After sucessful authentication, the `$scope.connect()` method will set presence using the Firebase low level API, and then assign a few scope properties for convenience and turn off the loading indicator.

Note that you need to inject the `$firebase` service in your controller signature.

{% highlight js %}

    /**
     * Load indicator
     */
    $scope.loading = true;

    /**
     * Connect with Firebase
     */
    $scope.connect = function() {

        // Room reference
        var ref = new Firebase($scope.plugin.firebaseUrl + '/rooms/' + $routeParams.workspace_id);

        // Authenticate user and set presence
        ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {

            // Set error if present and returns
            if (err) {
                $scope.err = err;
                $scope.$apply();
                return;
            }

            // Set presence using the Firebase low level API
            var session = new Firebase($scope.plugin.firebaseUrl + '/rooms/' + $routeParams.workspace_id + '/sessions/' + $scope.me.id);
            var connection = new Firebase($scope.plugin.firebaseUrl + '/.info/connected');

            // Will set an element in the session list when the user is connected and
            // automatically remove it when the user disconnects
            connection.on('value', function(snapshot) {

                if (snapshot.val() === true) {

                    // Add current user to the room sessions
                    session.set(true);

                    // Remove on disconnect
                    session.onDisconnect().remove();

                }

            });

            // Remove the user from the active sessions list when the plugin is closed
            $scope.$on('$destroy', function() {
                session.remove();
            });

            // Set sessions
            $scope.sessions = $firebase(ref.child('sessions')).$asObject();

            // Set messages
            $scope.messages = $firebase(ref.child('/messages')).$asArray();

            // Set loading
            $scope.loading = false;

            // Apply changes to the scope
            $scope.$apply();

        });

    };

{% endhighlight %}

## Adding chat messages to Firebase

This method adds a new message to the chat room with three properties:

* `userId`: the user ID of the current logged in user
* `text`: the message text
* `timestamp`: the unix timestamp that the message is posted

Note that we are using a special Firebase variable `Firebase.ServerValue.TIMESTAMP` to set the `timestamp`.

{% highlight js %}

    /**
     * Add a new message
     */
    $scope.addMessage = function() {

        if (!$scope.form || !$scope.form.message) {
            return;
        }

        $scope.messages.$add({
            userId: $scope.me.id,
            text: $scope.form.message,
            timestamp: Firebase.ServerValue.TIMESTAMP
        });

        $scope.form.message = null;

    };

{% endhighlight %}

## Using a directive to display each message

This directive will use two scope properties: `message`, the message to be parsed, and  `members`, an array with workspace members data (ex: display name and avatar image url).

The `templateUrl` property value is `namespaced-chat-message` a HTML template that will be put in the `plugin.html` later in this tutorial.

The directive uses a scope watcher in the members property, because the members takes a few milliseconds to be available so it's need to wait before start parsing the message, when available it's loops the `members` and finds the member that posted the message and assign it to `scope.member` to be used in the template.

It also emits an event `chatAutoscroll` to trigger a scroll to the new added message.

{% highlight js %}

/**
 * Messages Directive
 */
.directive('chatMessage', [function() {
    return {
        scope: {
            message: '=',
            members: '='
        },
        templateUrl: 'namespaced-chat-message',
        link: function postLink(scope, element, attrs) {
            var unbind = scope.$watch('members', function(members) {
                if (!members) {
                    return;
                }
                angular.forEach(members, function(member) {
                    if (member.user.id === scope.message.userId) {
                        scope.member = member;
                    }
                });
                unbind();
                scope.$emit('chatAutoscroll');
            });
        }
    };
}])

{% endhighlight %}

The `namespaced-chat-message` template used in conjection with the `namespacedChatMessage` directive that parses each messages.

{% highlight html %}
{% raw %}
<!-- Chat message template -->
<script type="text/ng-template" id="namespaced-chat-message">
    <div class="message-left">
        <img ng-src="{{member.user.settings.avatarUrl}}" alt="{{member.user.displayName || member.user.username || member.user.email}}" class="avatar avatar-small">
    </div>
    <div class="message-right">
        <p>{{member.user.displayName || member.user.username || member.user.email}} <span class="message-time">{{message.timestamp | date:'shortTime' || ''}}</span></p>
        <p>{{message.text}}</p>
    </div>
</script>
{% endraw %}
{% endhighlight %}

## Add a directive to auto scroll messages

All messages in the room will be displayed inside a `div` container with a fixed height and a vertical scroll for better user experience, in order to keep the last message visible this directive will scroll to the contents every time it's receives the `chatAutoscroll` event.

{% highlight js %}
/**
 * Autoscroll Directive
 */
.directive('chatAutoscroll', ['$timeout', function($timeout) {
    return {
        link: function postLink(scope, element, attrs) {
            scope.$on('chatAutoscroll', function() {
                $timeout(function() {
                    element.scrollTop(element[0].scrollHeight);
                });
            });
        }
    };
}])

{% endhighlight %}

## The HTML markup for the chat room

The `chat-main` template uses the grid from the {{site.productName}} patterns to render a two column layout, where the left column displays the messages and the right column the member list.

{% highlight html %}
{% raw %}
<!-- Chat main template -->
<script type="text/ng-template" id="chat-main">
    <div ng-show="loading">
        <span class="throbber"></span>
    </div>
    <div ng-hide="loading" class="row">
        <div class="col-md-10">
            <div class="main-white">
                <div class="messages" namespaced-chat-autoscroll>
                    <div ng-repeat="message in messages">
                        <div namespaced-chat-message message="message" members="members"></div>
                        <hr ng-if="!$last">
                    </div>
                </div>
                <div class="">
                    <form ng-submit="addMessage()">
                        <input type="text" ng-model="form.message" placeholder="Type a message and press enter" class="message-box">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="main-white">
                <div class="members">
                    <p ng-repeat="member in members" ng-class="{'online': sessions[member.user.id], 'offline': !sessions[member.user.id]}">{{member.user.displayName || member.user.username}}</p>
                </div>
            </div>
        </div>
  </div>
</script>
{% endraw %}
{% endhighlight %}

## Security rules

To restrict the access to only authenticated users in Firebase and only to members in the workspace, setup the follow security rules in Firebase dashboard.

{% highlight json %}
{% raw %}
{
  "rules": {
    "rooms": {
      "$workspace": {
        ".read": "auth.workspaces[$workspace] != null",
        ".write": "auth.workspaces[$workspace] != null"
      }
    }
  }
}
{% endraw %}
{% endhighlight %}

## Wrapping Up

The code for the entire chat plugin can be found below and also on [Github](https://github.com/ZengineHQ/labs/tree/master/plugins/chat-room){:target="_blank"}. In this case, the plugin namespace is 'namespaced', so to make it work as your own, you will need to replace all instances of the word 'namespaced' with your namespace.

If you have improvements to the plugin, feel free to make pull requests to the code repository and update the documentation for it [here]({{site.developerDomain}}/edit/gh-pages/plugins/tutorials/building-a-chat-plugin.md).

<ul class="nav nav-tabs" role="tablist" id="myTab">
  <li class="active"><a href="#plugin-js" role="tab" data-toggle="tab">plugin.js</a></li>
  <li><a href="#plugin-html" role="tab" data-toggle="tab">plugin.html</a></li>
  <li><a href="#plugin-css" role="tab" data-toggle="tab">plugin.css</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade in active" id="plugin-js">
{% highlight js %}
/**
 * Chat Controller
 */
plugin.controller('namespacedChatCntl', ['$scope', '$routeParams', 'znData', '$firebase', function ($scope, $routeParams, znData, $firebase) {

    /**
     * Load indicator
     */
    $scope.loading = true;

    /**
     * Connect with Firebase
     */
    $scope.connect = function() {

        // Room reference
        var ref = new Firebase($scope.plugin.firebaseUrl + '/rooms/' + $routeParams.workspace_id);

        // Authenticate user and set presence
        ref.auth($scope.plugin.firebaseAuthToken, function(err, res) {

            // Set error if present and returns
            if (err) {
                $scope.err = err;
                $scope.$apply();
                return;
            }

            // Set presence using the Firebase low level API
            var session = new Firebase($scope.plugin.firebaseUrl + '/rooms/' + $routeParams.workspace_id + '/sessions/' + $scope.me.id);
            var connection = new Firebase($scope.plugin.firebaseUrl + '/.info/connected');

            // Will set an element in the session list when the user is connected and
            // automatically remove it when the user disconnects
            connection.on('value', function(snapshot) {

                if (snapshot.val() === true) {

                    // Add current user to the room sessions
                    session.set(true);

                    // Remove on disconnect
                    session.onDisconnect().remove();

                }

            });

            // Remove the user from the active sessions list when the plugin is closed
            $scope.$on('$destroy', function() {
                session.remove();
            });

            // Set sessions
            $scope.sessions = $firebase(ref.child('sessions')).$asObject();

            // Set messages
            $scope.messages = $firebase(ref.child('/messages')).$asArray();

            // Set loading
            $scope.loading = false;

            // Apply changes to the scope
            $scope.$apply();

        });

    };

    /**
     * Get all members in a workspace
     *
     * equivalent to: GET {{site.apiDomain}}/v1/workspaces/{workspaceId}/members
     */
    znData('WorkspaceMembers').query(
        // Params
        {
            workspaceId: $routeParams.workspace_id
        },
        // Success
        function(resp) {
            $scope.members = resp;
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

    /**
     * Get plugin data
     *
     * equivalent to: GET {{site.apiDomain}}/v1/plugins/?namespace=namespaced
     */
    znData('Plugins').get(
        // Params
        {
            namespace: 'namespaced'
        },
        // Success
        function(resp) {
            // Note: the response comes back as an array, but because namespaces are unique
            // this request will contain just one element, for convenience let assign the
            // first element to `$scope.plugin` to save us the need to refer to it as `$scope.plugin[0]`
            // to read plugin properties
            $scope.plugin = resp[0];
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

    /**
     * Get current logged user in {{site.productName}}
     *
     * equivalent to: GET {{site.apiDomain}}/v1/users/me
     */
    znData('Users').get(
        // Params
        {
            id: 'me'
        },
        // Success
        function(resp) {
            $scope.me = resp;
        },
        // Error
        function(resp) {
            $scope.err = resp;
        }
    );

    /**
     * Wait for members, plugin and current user data to be loaded before connect with Firebase
     */
    var unbindInitialDataFetch = $scope.$watchCollection('[members, plugin, me]', function() {

        // If there is an err in the scope:
        // 1. Change the state of the loading indicator to false
        // 2. Remove the watcher
        // 3. Return (the plugin.html should contain logic to show the error message)
        if ($scope.err) {
            $scope.loading = false;
            unbindInitialDataFetch();
            return;
        }

        // Check if all of the three `$scope` properties have been defined
        // 1. Remove the watcher
        // 2. Call `$scope.connect` to connect with Firebase
        if ($scope.members !== undefined && $scope.plugin !== undefined && $scope.me !== undefined) {
            unbindInitialDataFetch();
            $scope.connect();
        }

    });

    /**
     * Add a new message
     */
    $scope.addMessage = function() {

        if (!$scope.form || !$scope.form.message) {
            return;
        }

        $scope.messages.$add({
            userId: $scope.me.id,
            text: $scope.form.message,
            timestamp: Firebase.ServerValue.TIMESTAMP
        });

        $scope.form.message = null;

    };

}])


/**
 * Messages Directive
 */
.directive('namespacedChatMessage', [function() {
    return {
        scope: {
            message: '=',
            members: '='
        },
        templateUrl: 'namespaced-chat-message',
        link: function postLink(scope, element, attrs) {
            var unbind = scope.$watch('members', function(members) {
                if (!members) {
                    return;
                }
                angular.forEach(members, function(member) {
                    if (member.user.id === scope.message.userId) {
                        scope.member = member;
                    }
                });
                unbind();
                scope.$emit('chatAutoscroll');
            });
        }
    };
}])

/**
 * Autoscroll Directive
 */
.directive('namespacedChatAutoscroll', ['$timeout', function($timeout) {
    return {
        link: function postLink(scope, element, attrs) {
            scope.$on('chatAutoscroll', function() {
                $timeout(function() {
                    element.scrollTop(element[0].scrollHeight);
                });
            });
        }
    };
}])

/**
 * Registration Settings
 */
.register('namespacedChat', {
  route: '/namespacedchat',
  controller: 'namespacedChatCntl',
  template: 'namespaced-chat-main',
  title: 'Chat',
  pageTitle: false,
  fullPage: true,
  topNav: true,
  order: 300,
  icon: 'icon-chat'
});
{% endhighlight %}
  </div>
    <div class="tab-pane fade" id="plugin-html">
{% highlight html %}
{% raw %}
<!-- Chat main template -->
<script type="text/ng-template" id="namespaced-chat-main">
    <div ng-show="loading">
        <span class="throbber"></span>
    </div>
    <div ng-hide="loading" class="row">
        <div class="col-md-10">
            <div class="main-white">
                <div class="messages" namespaced-chat-autoscroll>
                    <div ng-repeat="message in messages">
                        <div namespaced-chat-message message="message" members="members"></div>
                        <hr ng-if="!$last">
                    </div>
                </div>
                <div class="">
                    <form ng-submit="addMessage()">
                        <input type="text" ng-model="form.message" placeholder="Type a message and press enter" class="message-box">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="main-white">
                <div class="members">
                    <p ng-repeat="member in members" ng-class="{'online': sessions[member.user.id], 'offline': !sessions[member.user.id]}">{{member.user.displayName || member.user.username}}</p>
                </div>
            </div>
        </div>
  </div>
</script>

<!-- Chat message template -->
<script type="text/ng-template" id="namespaced-chat-message">
    <div class="message-left">
        <img ng-src="{{member.user.settings.avatarUrl}}" alt="{{member.user.displayName || member.user.username || member.user.email}}" class="avatar avatar-small">
    </div>
    <div class="message-right">
        <p>{{member.user.displayName || member.user.username || member.user.email}} <span class="message-time">{{message.timestamp | date:'shortTime' || ''}}</span></p>
        <p>{{message.text}}</p>
    </div>
</script>
{% endraw %}
{% endhighlight %}
    </div>
  <div class="tab-pane fade" id="plugin-css">
{% highlight css %}
/**
 * We are using just a few CSS rules to customize the plugin look.
 * This is because most of the layout is using the {{site.productName}} Patterns.
 */

.offline {
    color: #ccc;
}

.online {
    color: #000;

}

.messages {
    overflow:scroll;
    height:500px;
    padding-right: 15px;
}

.members {
    overflow:scroll;
    height:550px;
}

.message-time {
    color: #ccc;
}

.message-box {
    margin-top: 10px;
    width:97%;
    padding:10px;
}

.message-left {
    width: 40px;
    float: left;
}

{% endhighlight %}
  </div>
</div>
