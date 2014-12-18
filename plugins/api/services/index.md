---
layout: plugin-nav-bar
group: api
subgroup: services
---

# Zengine Services

{{site.productName}} provides several services for you to inject as dependencies when developing your plugin.

# znMessage

A service that displays a temporary alert message at the top of the page.

<h4><samp>znMessage(message, [type], [duration])</samp></h4>

{% highlight javascript %}
// Example controller including the 'znMessage' service.
plugin.controller('MyCntl', ['$scope', 'znMessage', function($scope, znMessage) {
    $scope.onSubmit = function() {
        znMessage('Your data was saved!', 'saved');
    };
}]);
{% endhighlight %}

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Param</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>message</td>
            <td><code>string</code></td>
            <td>
                The message to display.
            </td>
        </tr>
        <tr>
            <td>type (optional)</td>
            <td><code>string</code></td>
            <td>
                Determines the background color of the message. Valid types: 'info', 'saved', 'error', 'warning'. Default is 'info'.
            </td>
        </tr>
        <tr>
            <td>duration (optional)</td>
            <td><code>integer</code></td>
            <td>
                How long the message is displayed in milliseconds. Default is 4000.
            </td>
        </tr>
    </tbody>
</table>

Forces any open messages to close.

<h4><samp>znMessage(false);</samp></h4>


# znConfirm

A service that displays a confirmation dialog.

<h4><samp>znConfirm(message, [callback])</samp></h4>

{% highlight javascript %}
var close = function() {
    console.log('Closed');
};
znConfirm('Are you sure you want to close?', close);
{% endhighlight %}

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Param</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>message</td>
            <td><code>string</code></td>
            <td>
                The message to display in the dialog.
            </td>
        </tr>
        <tr>
            <td>callback (optional)</td>
            <td><code>function</code></td>
            <td>
                If defined, will add a 'Yes' button to the dialog, which when clicked, will execute the callback.
            </td>
        </tr>
    </tbody>
</table>

# znModal

A service that displays a [modal]({{site.clientDomain}}/patterns/modals) dialog. For an alternative modal service, try [Angular bootstrap's](http://angular-ui.github.io/bootstrap){:target="_blank"} `$modal`.

<h4><samp>znModal(options)</samp></h4>

{% highlight javascript %}
plugin.controller('myMainCntl', ['$scope', 'znModal', function($scope, znModal) {
    $scope.onSubmit = function() {
        znModal({
            title: 'My Modal Dialog',
            template: "<form ng-controller='myModalCntl' name='myForm'><input name='input' required></form>",
            classes: 'my-dialog',
            btns: {
                'Save': {
                    primary: true,
                    action: function(scope) {
                        // on Save click
                    }
                },
                'Delete': {
                    danger: true,
                    action: function() {
                        // on Delete click
                    }
                }
            }
        });
    };
}]);
/*
 * For enhanced control over modal buttons
*/
plugin.controller('myModalCntl', ['$scope', function($scope) {
    $scope.setBtnAction('Save', function (callback) {

        // See {{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/form for more details
        var form = $scope.myForm; // value of the name attribute on the form.

        if (!form.$valid) { // e.g. empty input
            return false; // do nothing
        }

        var keepOpen = false; // close the modal afterwards
        callback($scope, keepOpen); // call the original callback with $scope passed to it
    });
}]);
{% endhighlight %}


<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Param</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>options</td>
            <td><code>object</code></td>
            <td>
                <p>The object has following properties:</p>
                <ul>
                    <li><strong>title</strong> (string) - The dialog title. </li>
                    <li><strong>template</strong> (string) - Raw HTML to display as the dialog body. </li>
                    <li><strong>templateUrl</strong> (string) - Takes precedence over the template property. Works the same as the 'templateUrl' option when registering a directive. Corresponds to the id of the <code>script</code> tag that wraps the HTML to display as the dialog body. For more info, see the Angular docs on the <a href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/script" target="_blank">script directive</a>. </li>
                    <li><strong>classes</strong> (string) One or more (space-separated) CSS classes to add to the dialog. </li>
                    <li><strong>closeButton</strong> (bool) - A close button is included by default. Passing <code>false</code> won't include it. </li>
                    <li><strong>unique</strong> (bool | string) Whether to close any other open dialogs. <code>true</code> means close any other dialogs. Alternatively, a CSS class name can be passed to close related dialogs. </li>
                    <li><strong>btns</strong> (object) An object hash of buttons to include. Each button is a key-value pair, where the key is name used for the button text, and the value is a hash object that can include the following properties:
                        <ul>
                            <li>
                                one of three possible keys to determine background color: <code class="btn-success">success</code>, <code class="btn-danger">danger</code>, or <code class="btn-primary">primary</code>.
                            </li>
                            <li>
                                <code>action</code> callback to run when the button is clicked. By default, the action callback is called with no arguments, but this can be enhanced by calling the function <code>setBtnAction(name, onClick)</code>, which is available on the modal scope (more detail on modal scope below). The first argument, <code>name</code>, is the name of the button specified as the key in the <code>btns</code> hash. The second argument, <code>onClick</code>, is a function that is called on click of the button, instead of the original callback. When called, <code>onClick</code> is passed a wrapper function that takes two arguments: <code>data</code> and <code>keepOpen</code>. When the wrapper function is called within the <code>onClick</code> function, it calls the original <code>action</code>callback with its first argument <code>data</code>, and unless <code>keepOpen</code> is true, closes the modal.
                            </li>
                        </ul>
                    </li>
                    <li><strong>scope</strong> (object) - This property determines which scope to use in the modal template. (If you are familiar with Angular, this property is similar to how the scope property works with a directive.) There are three options for the scope to be used:
                        <ul>
                            <li>By default, the modal creates a child scope, which prototypically inherits from the $rootScope.</li>
                            <li>To create a child scope, which prototypically inherits from a different parent (i.e. the scope where the modal is being used), you can pass a reference like this: <code>scope: $scope</code>.</li>
                            <li>To create an isolated scope, which does not prototypically inherit, so that it is completely isolated from its parent, you can pass an object like this: <code>scope: { ... } </code>.
                            </li>
                        </ul>
                    In addition the scope associated with modal's content is augmented with the method <code>setBtnAction(name, onClick)</code>.
                    </li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>


# znPluginEvents

znPluginEvents is service that acts as a wrapper for the [Angular pub-sub system]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope){:target="_blank"}, and is meant for communication between plugins and the core app.

<h4><samp>znPluginEvents.$on(name, listener)</samp></h4>

Same as [Angular $on]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope#$on){:target="_blank"}. This method can used to listen for the following **broadcasted events**:

* zn-data-view-deleted
* zn-data-view-saved
* zn-data-view-loaded
* zn-data-panel-record-loaded


---

<h4><samp>znPluginEvents.$broadcast(name, args)</samp></h4>

Same as [Angular $broadcast]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope#$broadcast){:target="_blank"}. This method can used to broadcast data to  the following **event listeners**:

* zn-data-column-resize

# znData

The znData service provides a [collection of resources](#available_resources) that should be used for accessing data via the {{site.productName}} [REST API]({{site.baseurl}}/rest-api/resources). After passing the name of the resource to the service, you get back an object that can use the four methods described below: `get`, `query`, `delete`, and `save`. All four methods return a standard [Angular promise object]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$q){:target="_blank"}.


<h4 id="get"><samp>znData(resourceName).get(params, successCallback, errorCallback)</samp></h4>

Performs a `GET` request on a single object. The id param, if passed to `params`, will be interpreted as a url parameter.

{% highlight js %}

// Get single workspace member
// equivalent to: GET /workspaces/123/members/456
// response will be a JSON Object
znData('WorkspaceMembers').get({ workspaceId:123, id:456 }, function(member) {
    $scope.member = member;
});
{% endhighlight %}

---

<h4 id="query"><samp>znData(resourceName).query(params, successCallback, errorCallback)</samp></h4>

Performs a `GET` request on an array of objects. The id param, if passed to `params`, will be interpreted as a query parameter.

{% highlight js %}
// Get list of workspace members
// equivalent to: GET /workspaces/123/members?id=456
// response will be an array
znData('WorkspaceMembers').query({ workspaceId:123, id: 456 }, function(members) {
    $scope.member = members[0];
});
{% endhighlight %}

---

<h4 id="delete"><samp>znData(resourceName).delete(params, successCallback, errorCallback)</samp></h4>

Performs a `DELETE` request. Same as calling <samp>del(params, successCallback, errorCallback);</samp>

{% highlight js %}
// Delete a workspace member
// equivalent to: DELETE /workspaces/123/members/456
znData('WorkspaceMembers').delete({ workspaceId:123, id:456 }, function() {
    znMessage('Member removed from workspace', 'saved');
});
{% endhighlight %}

---

<h4 id="save"><samp>znData(resourceName).save([params], data, successCallback, errorCallback)</samp></h4>

If `params` is present and contains the id param, performs a `PUT`. Otherwise performs a `POST` request.

{% highlight js %}
// Add a workspace member
// equivalent to: POST /workspaces/123/members
znData('WorkspaceMembers').save({ workspaceId:123 }, { 'inviteCode' : 123456 }, function(member) {
    $scope.member = member;
});

// Update a workspace member to be an admin
// equivalent to: PUT /workspaces/123/members/456
znData('WorkspaceMembers').save({ workspaceId:123, id:456 }, { 'role.id' : 2 }, function(members) {
    $scope.members = members;
});

// Make all admins of workspace 123 owners
// equivalent to: PUT /workspaces/123/members?role.id=1
znData('WorkspaceMembers').save({ workspaceId:123, 'role.id': 2 } , { 'role.id' : 1 }, function(members) {
    $scope.members = members;
});

{% endhighlight %}

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Param</th>
            <th>Type</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>params (optional)</td>
            <td><code>object</code></td>
            <td>Optional if the resource has no required URL parameters and a <code>POST</code> is desired. Valid URL parameters are defined for each resource <a href="#available_resources">here</a>. If the <code>params</code>object is passed, any keys that aren't URL parameters are sent as <a href="{{site.baseurl}}/rest-api/conventions/querying-options/">query parameters</a> in the request. Some examples of valid query parameters are limit, related, sort, and attributes. If the id param is included in this object, a <code>PUT</code> is made. If not, a <code>POST</code> is made.
            </td>
        </tr>
        <tr>
            <td>data</td>
            <td><code>object</code></td>
            <td>Sent as the payload of the request.</td>
        </tr>
        <tr>
            <td>successCallback</td>
            <td><code>function(data, metaData, headers)</code></td>
            <td>
                <p>The function to execute if the request succeeds.</p>
                <ul>
                    <li><strong>data</strong> - The data of the response. </li>
                    <li><strong>metaData</strong> - An object containing the following info about the response:
                        <ul>
                            <li>status</li>
                            <li>code</li>
                            <li>totalCount</li>
                            <li>limit</li>
                            <li>offset</li>
                        </ul>
                        Click <a href="{{site.baseurl}}/rest-api/conventions/response-format">here</a> for more info about the response format.
                    </li>
                    <li><strong>headers</strong> - The HTTP response headers.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>errorCallback</td>
            <td><code>function(resp)</code></td>
            <td>
                <p>The function to execute if the request fails. Click <a href="{{site.baseurl}}/rest-api/conventions/response-format/#failure">here</a> for more info about the format of the failure response.</p>
            </td>
        </tr>
    </tbody>
</table>

#### Available Resources

The parameterized URL is used to query the {{site.productName}} [REST API]({{site.baseurl}}/rest-api/resources). For example, if the resource name is **FormFields**, the parameterized URL is **/forms/:formId/fields/:id**. In this case, `formId` is a required URL paramter, and must be passed to the `params` argument of any `get()`, `query()`, `delete()`, or `save()` called on `znData('FormFields')`. The `id` parameter on the other hand, may or may not be passed, depending on whether the request is intended for a single object or multiple objects. This is true for the `id` parameter of any resource URL.

<table class="table table-striped">
    <thead>
        <th>Resource Name</th>
        <th>Parameterized URL</th>
    </thead>
    <tbody>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/activities">Activities</a></td><td>/activities/:activityId</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/app_templates">AppTemplates</a></td><td>/app_templates/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/app_template_install_jobs">AppTemplateInstallJobs</a></td><td>/app_template_install_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/data_filters">DataFilters</a></td><td>/data_filters/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/data_views">DataViews</a></td><td>/data_views/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/events">Events</a></td><td>/events/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/files">Files</a></td><td>/files/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms">Forms</a></td><td>/forms/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-records-permissions">DefaultFormPermissions</a></td><td>/forms/permissions</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records-permissions">FormRecordPermissions</a></td><td>/forms/:formId/records/permissions</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-fields">FormFields</a></td><td>/forms/:formId/fields/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-folders">FormFolders</a></td><td>/forms/:formId/folders/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records">FormRecords</a></td><td>/forms/:formId/records/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-uploads">FormUploads</a></td><td>/forms/:id/uploads</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/notes">Notes</a></td><td>/notes/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/notes-note.id-replies">NoteReplies</a></td><td>/notes/:noteId/replies/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-notifications">Notifications</a></td><td>/notifications/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-notification_email">NotificationEmails</a></td><td>/notification_emails/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/plugins">Plugins</a></td><td>/plugins/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/user_plugin_links">UserPluginLinks</a></td><td>/user_plugin_links/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspace_plugin_links">WorkspacePluginLinks</a></td><td>/workspace_plugin_links/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_import_files">RecordImportFiles</a></td><td>/record_import_files/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_import_jobs">RecordImportJobs</a></td><td>/record_import_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_export_jobs">RecordExportJobs</a></td><td>/record_export_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/tasks">Tasks</a></td><td>/tasks/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_lists">TaskLists</a></td><td>/task_lists/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_priorities">TaskPriorities</a></td><td>/task_priorities</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_statuses">TaskStatuses</a></td><td>/task_statuses</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-task_preferences">TaskPreferences</a></td><td>/users/:userId/task_preferences</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users">Users</a></td><td>/users/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-roles">Roles</a></td><td>/workspaces/:workspaceId/roles/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces">Workspaces</a></td><td>/workspaces/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspace_copy_jobs">WorkspaceCopyJobs</a></td><td>/workspace_copy_jobs</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-invitees">WorkspaceInvitees</a></td><td>/workspaces/:workspaceId/invitees/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-members">WorkspaceMembers</a></td><td>/workspaces/:workspaceId/members/:id</td></tr>
		<tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-transfer_requests">WorkspaceTransferRequests</a></td><td>/workspaces/:workspaceId/transfer_requests/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-members-member.id-task_preferences">WorkspaceTaskPreferences</a></td><td>/workspaces/:workspaceId/members/:memberId/task_preferences</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-logo">WorkspaceLogo</a></td><td>/workspaces/:workspaceId/logo</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/countries">Countries</a></td><td>/countries</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/states">States</a></td><td>/states</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/subscriptions">Subscriptions</a></td><td>/subscriptions/:id</td></tr>
    </tbody>
</table>
