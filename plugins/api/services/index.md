---
layout: plugin-nav-bar
group: api
subgroup: services
---

# {{site.productName}} Services

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
            <td><code>Object</code></td>
            <td>
                <p>The object has the following properties:</p>
                <ul>
                    <li><strong>title</strong> - <code>{string}</code> - The dialog title. </li>
                    <li><strong>template</strong> - <code>{string}</code> - Raw HTML to display as the dialog body. </li>
                    <li><strong>templateUrl</strong> - <code>{string}</code> - Takes precedence over the template property. Works the same as the 'templateUrl' option when registering a directive. Corresponds to the id of the <code>script</code> tag that wraps the HTML to display as the dialog body. For more info, see the Angular docs on the <a href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/script" target="_blank">script directive</a>. </li>
                    <li><strong>classes</strong> - <code>{string}</code> - One or more (space-separated) CSS classes to add to the dialog. </li>
                    <li><strong>closeButton</strong> - <code>{boolean}</code> - A close button is included by default. Passing <code>false</code> won't include it. </li>
                    <li><strong>unique</strong> - <code>{boolean|string}</code> - Whether to close any other open dialogs. <code>true</code> means close any other dialogs. Alternatively, a CSS class name can be passed to close related dialogs. If you are using the plugin location <a href="http://{{site.baseurl}}/plugins/registration/#locations" target="_blank">zn-plugin-form-top</a>, in order for the modal to work this must be set to <code>true</code>. </li>
                    <li><strong>btns</strong> - <code>{Object}</code> - An object hash of buttons to include. Each button is a key-value pair, where the key is name used for the button text, and the value is a hash object that can include the following properties:
                        <ul>
                            <li>
                                one of three possible keys to determine background color: <code class="btn-success">success</code>, <code class="btn-danger">danger</code>, or <code class="btn-primary">primary</code>.
                            </li>
                            <li>
                                <code>action</code> callback to run when the button is clicked. By default, the action callback is called with no arguments, but this can be enhanced by calling the function <code>setBtnAction(name, onClick)</code>, which is available on the modal scope (more detail on modal scope below). The first argument, <code>name</code>, is the name of the button specified as the key in the <code>btns</code> hash. The second argument, <code>onClick</code>, is a function that is called on click of the button, instead of the original callback. When called, <code>onClick</code> is passed a wrapper function that takes two arguments: <code>data</code> and <code>keepOpen</code>. When the wrapper function is called within the <code>onClick</code> function, it calls the original <code>action</code>callback with its first argument <code>data</code>, and unless <code>keepOpen</code> is true, closes the modal.
                            </li>
                        </ul>
                    </li>
                    <li><strong>scope</strong> - <code>{Object}</code> - This property determines which scope to use in the modal template. (If you are familiar with Angular, this property is similar to how the scope property works with a directive.) There are three options for the scope to be used:
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

# znFiltersPanel

znFiltersPanel is a service that allows you to view and build a <a href="{{site.baseurl}}/rest-api/conventions/data-filters/">data filter</a> by opening a modal. This is different from the <a href="{{site.baseurl}}/plugins/api/directives/#zninlinefilter">znInlineFilter directive</a>, which displays the filter builder directly in the page. The filter returned from the panel can be used to query <a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records">records</a>, save to a <a href="{{site.baseurl}}/rest-api/resources/#!/data_views">data view</a>, and build and run <a href="{{site.baseurl}}/rest-api/resources/#!/calculation_settings">calculations</a>.

<h4><samp>znFiltersPanel.open(options)</samp></h4>

{% highlight js %}

plugin.controller('myMainCntl', ['$scope', 'znFiltersPanel', 'znData', function($scope, znFiltersPanel, znData) {

    $scope.formId = 123;

    $scope.openFiltersPanel = function() {

        znFiltersPanel.open({
            formId: formId,
            filter: {
                and: [{
                    prefix: '',
                    attribute: 'folder.id',
                    value: 0
                }]
            },
            onSave: function(filter) {
                // use filter to interact with API, e.g. query records

                znData('FormRecords').get(
                    {
                        formId: $scope.formId,
                        filter: JSON.stringify(filter)
                    }
                );
            },
            fieldTypeBlacklist: ['text-input', 'linked'],
            attributeBlacklist: ['field123', 'createdByUser.id']
        });
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
            <td>options</td>
            <td><code>Object</code></td>
            <td>
                <p>The object has the following properties:</p>
                <ul>
                    <li><strong>formId</strong> - <code>{integer}</code> - Form ID of the form you want to filter on.</li>
                    <li><strong>filter</strong> - <code>{Object}</code> - Existing filter to open with the panel. Does not apply to <code>znInlineFilter</code> directive.</li>
                    <li><strong>onSave</strong> - <code>{function(filter)}</code> - A callback executed when the filter panel is saved. Does not apply to <code>znInlineFilter</code> directive.</li>
                    <li><strong>subfilters</strong> - <code>{boolean}</code> - Whether to allow subfiltering on related fields. Defaults to <code>true</code>.</li>
                    <li><strong>groups</strong> - <code>{boolean}</code> - Whether to allow nested conditions. Defaults to <code>true</code>.</li>
                    <li><strong>dynamicValues</strong> - <code>{boolean}</code> - Whether to allow dynamic values such as <code>logged-in-user</code>. Defaults to <code>true</code>.</li>
                    <li><strong>operators</strong> - <code>{array}</code> - A list of operators to allow filtering on. Defaults to <code>['and', 'or']</code> but <code>['and']</code> or <code>['or']</code> can also be passed.
                    </li>
                    <li><strong>attributeBlacklist</strong> - <code>{array}</code> - A list of specific fields to prevent the user from filtering on. The list can contain an attribute like <code>'field123'</code>, where 123 is the ID of a field belonging to the form. The list can also contain the following attributes: <code>'folder.id'</code>, <code>'createdByUser.id'</code>, <code>'created'</code>, and <code>'modified'</code>. </li>
                    <li><strong>prefixBlacklist</strong> - <code>{array}</code> -  A list of prefixes to prevent the user from filtering on. The following is a list of valid prefixes:
                        <ul>
                            <li>""</li>
                            <li>"not"</li>
                            <li>"contains"</li>
                            <li>"not-contains"</li>
                            <li>"starts-with"</li>
                            <li>"ends-with"</li>
                            <li>"min"</li>
                            <li>"max"</li>
                            <li>"not-validates"</li>
                        </ul>
                    </li>
                    <li><strong>fieldTypeBlacklist</strong> - <code>{array}</code> -  A list of field types to prevent the user from filtering on. The following is a list of valid field types:
                        <ul>
                            <li>calculated-field</li>
                            <li>checkbox</li>
                            <li>country-select</li>
                            <li>date-picker</li>
                            <li>dropdown</li>
                            <li>file-upload</li>
                            <li>heading</li>
                            <li>hidden-field</li>
                            <li>html</li>
                            <li>link-counter</li>
                            <li>linked</li>
                            <li>member</li>
                            <li>numeric</li>
                            <li>page-break</li>
                            <li>radio</li>
                            <li>spacer</li>
                            <li>state-select</li>
                            <li>summary</li>
                            <li>text</li>
                            <li>text-area</li>
                            <li>text-input</li>
                            <li>year</li>
                        </ul>
                        For a more complete reference, see the API documention on <a href="{{site.baseurl}}/rest-api/resources/#!/form_field_taxonomy">form field taxonomy</a>.
                    </li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

# znFilterMatcher

The znFilterMatcher service lets your plugin compare a record against a filter and determine if it's a match. It uses the same matching as querying records using a filter or for conditional field rules. One case you might use this for is to filter down a list of records that have already been fetched without making an additional request to the API.

For the matching to work properly, the data you are filtering on must be present in the record. Additionally, subfilters of fields on other forms and dynamic conditions, such as `logged-in-user`, are not supported.

{% highlight js %}

var record = { 'field123': 'Chicago', 'field456': 2015 };

if (znFilterMatcher(record, { 'and': [{ 'prefix': '', 'attribute': 'field123', 'value': 'Chicago'}]})) {
	// Record Matches field123 = Chicago
}

if (znFilterMatcher(record, { 'and': [{ 'prefix': 'min', 'attribute': 'field456', 'value': 2015}]})) {
	// Record Matches field456 >= 2015
}

{% endhighlight %}

# znData

The znData service provides a [collection of resources](#available-resources) that should be used for accessing data via the {{site.productName}} [REST API]({{site.baseurl}}/rest-api/resources). After passing the name of the resource to the service, you get back an object that can use the methods described below: `get`, `query`, `save`, `update`, `delete`, `saveAll`, `updateAll` and `deleteAll`. All methods return a standard [Angular promise object]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$q){:target="_blank"}.


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

---

<h4 id="update"><samp>znData(resourceName).update([params], data, successCallback, errorCallback)</samp></h4>

Works the same as the `save` with the execption it will always performs a `PUT`.

{% highlight js %}
// Update a workspace member to be an admin
// equivalent to: PUT /workspaces/123/members/456
znData('WorkspaceMembers').update({ workspaceId:123, id:456 }, { 'role.id' : 2 }, function(members) {
    $scope.members = members;
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

<h4 id="saveAll"><samp>znData(resourceName).saveAll(params, data, successCallback, errorCallback)</samp></h4>

Performs a `POST` request with multiple objects to be created.

{% highlight js %}
// Create multiple tasks at once
// equivalent to: POST /tasks

$scope.tasks = [
    {
        "task":"Test 1",
        "workspace":{"id":62},
        "taskList":{"id":0},
        "order":1,
        "due":"2015-03-20",
        "assignedToUser":{"id":9},
        "priority":1,
        "status":"open"
    },
    {
        "task":"Test 2",
        "workspace":{"id":62},
        "taskList":{"id":0},
        "order":1,
        "due":"2015-03-20",
        "assignedToUser":{"id":9},
        "priority":1,
        "status":"open"
    },
    {
        "task":"Test 3",
        "workspace":{"id":62},
        "taskList":{"id":0},
        "order":1,
        "due":"2015-03-20",
        "assignedToUser":{"id":9},
        "priority":1,
        "status":"open"
    }
];

znData('Tasks').saveAll({}, $scope.tasks, function(data) {
    znMessage('All tasks saved.', 'saved');
    // `data` will contain IDs of created tasks
});
{% endhighlight %}

---

<h4 id="updateAll"><samp>znData(resourceName).updateAll(params, data, successCallback, errorCallback)</samp></h4>

Performs a `PUT` request.
The `data` param needs to be an object with the properties to be updated for all record that matches the params/conditions.

{% highlight js %}
// Update all tasks status to `closed` where the status is `in-progress`
// equivalent to: PUT /tasks/?status=in-progress

var params = { status: 'in-progress' };
var data = { status: 'closed' };

znData('Tasks').updateAll(params, data, function() {
    znMessage('All tasks updated.', 'saved');
});

// Update all tasks status to `archived` where the IDs are 1, 2, 3
// equivalent to: PUT /tasks/?id=1|2|3

var params = { id: '1|2|3' };
var data = { status: 'archived' };

znData('Tasks').updateAll(params, data, function() {
    znMessage('All tasks archived.', 'saved');
});
{% endhighlight %}

---

<h4 id="deleteAll"><samp>znData(resourceName).deleteAll(params, successCallback, errorCallback)</samp></h4>

Performs a `DELETE` request.

{% highlight js %}
// Delete all tasks with status `archived`
// equivalent to: DELETE /tasks/?status=archived

var params = { status: 'archived' };

znData('Tasks').deleteAll(params, function() {
    znMessage('All tasks deleted.', 'saved');
});

// Delete all tasks with IDs 1, 2, 3
// equivalent to: DELETE /tasks/?id=1|2|3

var params = { id: '1|2|3' };

znData('Tasks').deleteAll(params, function() {
    znMessage('All tasks deleted.', 'saved');
});
{% endhighlight %}

---

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
            <td><code>Object</code></td>
            <td>Optional if the resource has no required URL parameters and a <code>POST</code> is desired. Valid URL parameters are defined for each resource <a href="#available_resources">here</a>. If the <code>params</code>object is passed, any keys that aren't URL parameters are sent as <a href="{{site.baseurl}}/rest-api/conventions/querying-options/">query parameters</a> in the request. Some examples of valid query parameters are limit, related, sort, and attributes. If the id param is included in this object, a <code>PUT</code> is made. If not, a <code>POST</code> is made.
            </td>
        </tr>
        <tr>
            <td>data</td>
            <td><code>Object</code></td>
            <td>Sent as the payload of the request.</td>
        </tr>
        <tr>
            <td>successCallback</td>
            <td><code>function(data, metaData, headers)</code></td>
            <td>
                <p>The function to execute if the request succeeds.</p>
                <ul>
                    <li><strong>data</strong> - <code>{Object}</code> - The data of the response. </li>
                    <li><strong>metaData</strong> - <code>{Object}</code> - An object containing the following info about the response:
                        <ul>
                            <li>status</li>
                            <li>code</li>
                            <li>totalCount</li>
                            <li>limit</li>
                            <li>offset</li>
                        </ul>
                        Click <a href="{{site.baseurl}}/rest-api/conventions/response-format">here</a> for more info about the response format.
                    </li>
                    <li><strong>headers</strong> - <code>{function([headerName])}</code> – Getter function for the HTTP response headers.</li>
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

The {{site.productName}} REST API has more [querying options]({{site.baseurl}}/rest-api/conventions/querying-options) for pagination, sorting, filtering, and relational data.

<table class="table table-striped">
    <thead>
        <th>Resource Name</th>
        <th>Parameterized URL</th>
    </thead>
    <tbody>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/activities">Activities</a></td><td>/activities/:activityId</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/app_templates">AppTemplates</a></td><td>/app_templates/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/app_template_install_jobs">AppTemplateInstallJobs</a></td><td>/app_template_install_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/binary_export_jobs-binaryexportjob.id-batches">BinaryExportBatch</a></td><td>/binary_export_jobs/:binaryExportJobId/batches/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/binary_export_jobs">BinaryExportJob</a></td><td>/binary_export_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/calculate">Calculate</a></td><td>/calculate</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/calculation_settings">CalculationSettings</a></td><td>/calculation_settings/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/data_views">DataViews</a></td><td>/data_views/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/events">Events</a></td><td>/events/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/files">Files</a></td><td>/files/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms">Forms</a></td><td>/forms/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-records-permissions">DefaultFormPermissions</a></td><td>/forms/permissions</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-fields">FormFields</a></td><td>/forms/:formId/fields/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/form_field_taxonomy">FormFieldTaxonomy</a></td><td>/form_field_taxonomy</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-folders">FormFolders</a></td><td>/forms/:formId/folders/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/form_groups">FormGroups</a></td><td>/forms_groups/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records-permissions">FormRecordPermissions</a></td><td>/forms/:formId/records/permissions</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records">FormRecords</a></td><td>/forms/:formId/records/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-uploads">FormUploads</a></td><td>/forms/:id/uploads</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/notes">Notes</a></td><td>/notes/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/notes-note.id-replies">NoteReplies</a></td><td>/notes/:noteId/replies/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-notifications">Notifications</a></td><td>/notifications/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-notification_email">NotificationEmails</a></td><td>/notification_emails/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/plugins">Plugins</a></td><td>/plugins/:pluginId</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/plugins-plugin.id-screenshots">PluginScreenshots</a></td><td>/plugins/:pluginId/screenshots</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/plugins-plugin.id-services">PluginServices</a></td><td>/plugins/:pluginId/services/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/plugins-plugin.id-services-service.id-uploads">PluginServiceUploads</a></td><td>/plugins/:pluginId/services/:serviceId/uploads</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspace_plugin_links">WorkspacePluginLinks</a></td><td>/workspace_plugin_links/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_import_files">RecordImportFiles</a></td><td>/record_import_files/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_import_jobs">RecordImportJobs</a></td><td>/record_import_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/record_export_jobs">RecordExportJobs</a></td><td>/record_export_jobs/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/scheduled_webhooks">ScheduledWebhooks</a></td><td>/scheduled_webhooks/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/tasks">Tasks</a></td><td>/tasks/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_lists">TaskLists</a></td><td>/task_lists/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_priorities">TaskPriorities</a></td><td>/task_priorities</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/task_statuses">TaskStatuses</a></td><td>/task_statuses</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users-user.id-task_preferences">TaskPreferences</a></td><td>/users/:userId/task_preferences</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/users">Users</a></td><td>/users/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/workspaces-workspace.id-roles">Roles</a></td><td>/workspaces/:workspaceId/roles/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/webhook_events">WebhookEvents</a></td><td>/webhook_events/:id</td></tr>
        <tr><td><a href="{{site.baseurl}}/rest-api/resources/#!/webhooks">Webhooks</a></td><td>/webhooks/:id</td></tr>
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

# znPluginData

The znPluginData service is used to communicate with [Plugin Services]({{site.baseurl}}/plugins/development/services.html), similar to how znData makes requests to the REST API. Instead of passing a resource name, you pass the plugin namespace and service route. The methods available are: `get`, `post`, `put`, and `delete`. The methods return an Angular promise object.

The param `workspaceId` is always required and must be a workspace where the plugin is installed. Query string parameters should be passed as `params`.

---

<h4 id="pluginDataGet"><samp>znPluginData(namespace).get(route, params, successCallback, errorCallback)</samp></h4>

Performs a `GET` request.

{% highlight js %}
// equivalent to: GET {{site.pluginDomain}}/workspaces/123/myPlugin/my-route?id=456
znPluginData('myPlugin').get('/my-route', { workspaceId: 123, params: { id: 456 }}, function(results) {
    $scope.results = results;
});
{% endhighlight %}

---

<h4 id="pluginDataPost"><samp>znPluginData(namepsace).post(route, params, data, successCallback, errorCallback)</samp></h4>

Performs a `POST` request.

{% highlight js %}
// equivalent to: POST {{site.pluginDomain}}/workspaces/123/myPlugin/my-route
znPluginData('myPlugin').post('/my-route', { workspaceId: 123 }, { name: $scope.name }, function(result) {
    $scope.result = result;
});
{% endhighlight %}

---

<h4 id="pluginDataPut"><samp>znPluginData(namespace).put(route, params, data, successCallback, errorCallback)</samp></h4>

Performs a `PUT` request.

{% highlight js %}
// equivalent to: PUT {{site.pluginDomain}}/workspaces/123/myPlugin/my-route?id=456
znPluginData('myPlugin').put('/my-route', { workspaceId: 123, params: { id: 456 }}, { email: $scope.email }, function(result) {
    $scope.result = result;
});
{% endhighlight %}

---

<h4 id="pluginDataDelete"><samp>znPluginData(namespace).delete(route, params, successCallback, errorCallback)</samp></h4>

Performs a `DELETE` request.

{% highlight js %}
// equivalent to: DELETE {{site.pluginDomain}}/workspaces/123/myPlugin/my-route?id=456
znPluginData('myPlugin').delete('/my-route', { workspaceId: 123, params: { id: 456 }}, function(result) {
    // Deleted
});
{% endhighlight %}

---

# znPluginEvents

znPluginEvents is service that acts as a wrapper for the [Angular pub-sub system]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope){:target="_blank"}, and is meant for communication between plugins and the core app.

<h4><samp>znPluginEvents.$on(name, listener)</samp></h4>

Same as [Angular $on]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/type/$rootScope.Scope#$on){:target="_blank"}. This method can be used to listen for the following broadcasted events:

* zn-ui-record-overlay-record-loaded
* zn-data-<code class="btn-success">resource-name</code>-<code class="btn-primary">action</code>
    * Events in this format are triggered by a successful response to a call via the [znData service](#zndata). The <code class="btn-success">resource name</code> is the hyphenated version of the resource names listed [here](#available-resources). The <code class="btn-primary">action</code> can be one of the following: `read`, `saved`, `deleted`, `saved-all`, `updated-all` or `deleted-all`. For example, calling `znData('FormRecords').save()` will trigger the `'zn-data-form-records-saved'` event.

**Important:** Make sure to deregister your listeners when your plugin is destroyed. Not deregistering listeners will cause listeners to duplicate and pile up, which will degrade the performance of your plugin and the app. The code below shows listeners being registered and deregistered on $scope $destroy.

{% highlight js %}
/**
 * Plugin testPluginEvents Controller
 */
plugin.controller('testPluginEventsCntl', ['$scope', 'znPluginEvents', function ($scope, znPluginEvents) {

    // zn-ui-record-overlay-record-loaded
    var recordLoaded = znPluginEvents.$on('zn-ui-record-overlay-record-loaded', function(evt, record) {
        console.log(record);
    });

    // zn-data-[resource name]-saved
    var recordSaved = znPluginEvents.$on('zn-data-form-records-saved', function(evt, record, created, params) {
        if (created) {
            console.log('Record ' + record.id + ' was created in form ' + params.formId);
        } else {
            console.log('Record ' + record.id + ' was updated in form ' + params.formId);
        }
    });

    // zn-data-[resource name]-deleted
    var recordDeleted = znPluginEvents.$on('zn-data-form-records-deleted', function(evt, params) {
        console.log('Record ' + params.id + ' was deleted');
    });

    // zn-data-[resource name]-read
    var recordRead = znPluginEvents.$on('zn-data-form-records-read', function(evt, records, params) {
        angular.forEach(records, function(record) {
            console.log(record);
        });
    });

    // zn-data-[resource name]-saved-all
    var taskSaveAll = znPluginEvents.$on('zn-data-tasks-saved-all', function(evt, data, params) {
        console.log('Tasks IDs created: ' + data.join(','));
        // `data` will be an array of IDs
    });

    // zn-data-[resource name]-updated-all
    var taskUpdateAll = znPluginEvents.$on('zn-data-tasks-updated-all', function(evt, params) {
        console.log('Tasks was updated');
        // `params` will contain the path and query params used
    });

    // zn-data-[resource name]-deleted-all
    var taskDeleteAll = znPluginEvents.$on('zn-data-tasks-deleted-all', function(evt, params) {
        console.log('Tasks was deleted');
        // `params` will contain the path and query params used
    });

    // Deregister listeners
    $scope.$on("$destroy", function() {
        if (recordLoaded) recordLoaded();
        if (recordSaved) recordSaved();
        if (recordDeleted) recordDeleted();
        if (recordRead) recordRead();
        if (taskSaveAll) taskSaveAll();
        if (taskUpdateAll) taskUpdateAll();
        if (taskDeleteAll) taskDeleteAll();
    });

}]);
{% endhighlight %}

<!-- znLocalStorage -->

# znLocalStorage {#zn-local-storage}

This service gives you basic access to <a href="http://diveintohtml5.info/storage.html" target="_blank">browser local storage</a> and fallbacks to browser cookies if support is not available.

### znLocalStorage.set(key, value) {#zn-local-storage-set}

Set an item in browser local storage.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testLocalStorageCntl', ['$scope', 'znLocalStorage', function ($scope, znLocalStorage) {

  znLocalStorage.set('lastSaved', new Date());

}]);
{% endhighlight %}

### znLocalStorage.get(key) {#zn-local-storage-get}

Get an item in browser local storage.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testLocalStorageCntl', ['$scope', 'znLocalStorage', function ($scope, znLocalStorage) {

  znLocalStorage.get('lastSaved');

}]);
{% endhighlight %}

### znLocalStorage.remove(key) {#zn-local-storage-remove}

Remove an item in browser local storage.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testLocalStorageCntl', ['$scope', 'znLocalStorage', function ($scope, znLocalStorage) {

  znLocalStorage.remove('lastSaved');

}]);
{% endhighlight %}

### znLocalStorage.isSupported {#zn-local-storage-is-supported}

Checks if the browser support local storage.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testLocalStorageCntl', ['$scope', 'znLocalStorage', function ($scope, znLocalStorage) {

  if (znLocalStorage.isSupported) {
    // is supported
  } else {
    // not supported
  }

}]);
{% endhighlight %}

<!-- znCookies -->

# znCookies {#zn-cookies}

This service gives you basic access to browser cookies.

### znCookies.set(key, value) {#zn-cookies-set}

Set an item in browser cookies.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testCookiesCntl', ['$scope', 'znCookies', function ($scope, znCookies) {

  znCookies.set('lastSaved', new Date());

}]);
{% endhighlight %}

### znCookies.get(key) {#zn-cookies-get}

Get an item in browser cookies.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testCookiesCntl', ['$scope', 'znCookies', function ($scope, znCookies) {

  znCookies.get('lastSaved');

}]);
{% endhighlight %}

### znCookies.remove(key) {#zn-cookies-remove}

Remove an item in browser cookies.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testCookiesCntl', ['$scope', 'znCookies', function ($scope, znCookies) {

  znCookies.remove('lastSaved');

}]);
{% endhighlight %}

### znCookies.isSupported {#zn-cookies-is-supported}

Checks if the browser support cookies.

{% highlight js %}
/**
 * Plugin testLocalStorage Controller
 */
plugin.controller('testCookiesCntl', ['$scope', 'znCookies', function ($scope, znCookies) {

  if (znCookies.isSupported) {
    // is supported
  } else {
    // not supported
  }

}]);
{% endhighlight %}
