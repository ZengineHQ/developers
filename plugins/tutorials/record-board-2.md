---
layout: plugin-nav-bar
group: tutorials
subgroup: record-board-2
---

<h1 id="creating-a-record-board-plugin-part-2">Creating a Record Board Plugin (Part 2) <a href="https://github.com/ZengineHQ/labs/tree/master/plugins/record-kanban-board" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

In [part 1]({{site.baseurl}}/plugins/tutorials/record-board), we created a workspace plugin that would get a list of forms, folders, and records. The plugin would display the forms as tabs, the folders as columns, and the records in a list, by column.

At this point you probably only have 1 column and you want to do more than just see a list of records. In this guide we will work on adding new folders/columns and moving records from one column to another.

## Adding Folders

Let's start by adding a column to the board that prompts for a folder name. You may recall some of this code from [part 1]({{site.baseurl}}/plugins/tutorials/record-board), as a reference where to put the add folder HTML.

{% highlight html %}
{% raw %}
<!-- Board Canvas -->
<div class="wrapper">

    <!-- Folder Column -->
    <div ng-repeat="folder in folders" class="column">
        <!-- Display Folder Name -->
        <div class="name" ng-hide="editFolder.id==folder.id">
            <span ng-show="folder.id==0">{{folder.name}}</span>
            <a href="#" ng-click="toggleEditFolder(folder.id)" ng-show="folder.id!=0">{{folder.name}}</a>
        </div>
        <!-- Edit Folder Name -->
        <div ng-show="editFolder.id==folder.id">
            <div class="control-group">
                <input type="text" ng-model="editFolder.name" class="input-large">
            </div>
            <div class="form-actions">
                <a href="#" ng-click="saveFolder()" class="btn btn-primary">Save</a>
                <a href="#" ng-click="toggleEditFolder(folder.id)" class="secondary">Cancel</a>
            </div>
        </div>

        <!-- Folder Records List -->
        <ul id="{{folder.id}}" data-id="{{folder.id}}" class="records-container">
            <li data-id="{{record.id}}" ng-repeat="record in folderRecords[folder.id]" class="record">{{record.name}}</li>
        </ul>
    </div>

    <!-- Add Folder Column -->
    <div ng-show="showAddFolder" class="column">
        <!-- Folder Name -->
        <div class="control-group">
            <input type="text" ng-model="addFolderName" placeholder="New Folder Name" class="input-large">
        </div>
        <!-- Add Folder Actions -->
        <div class="form-actions">
            <a href="#" ng-click="addFolder()" class="btn btn-primary">Add</a>
            <a href="#" ng-click="openAddFolder(false)" class="secondary">Cancel</a>
        </div>
    </div>

</div>
{% endraw %}
{% endhighlight %}

This represents patterns for forms and buttons that will match the app. The `ng-show` for `formId` will make sure the column only appears when a form has been selected, since a form is required to create a form folder. The `ng-model` makes the new folder name accessible from the `$scope` as the property `addFolderName`. We will write the `addFolder` function below in the plugin JavaScript to make it work.

The following function will post data to the `FormFolders` endpoint to create a new folder using the name from above. After successfully creating a new folder, it will update the list of folders and initialize an empty record list for the folder. With AngularJS 2-way data binding, updating the folders property will automatically make a new column appear in the interface.

A new service is also introduced, called `znMessage`, so be sure to add that to the dependencies in a similar way to `$routeParams` and `znData`. The `znMessage` service is used here to indicate success or failure to the user.

{% highlight js %}
// Add Folder Name
$scope.addFolderName = null;

/**
 * Add Folder
 */
$scope.addFolder = function() {

    var params = {
            formId: $scope.formId
    };

    var data = {
        name: $scope.addFolderName,
        form: {
            id: $scope.formId
        }
    };

    // Reset Folder Name
    $scope.addFolderName = '';

    // Save New Folder
    return znData('FormFolders').save(params, data, function (folder) {
        // Initialize New Folder Record List
        $scope.folderRecords[folder.id] = [];

        // Append New Folder to Folders List
        $scope.folders.push(folder);

        znMessage('New folder created', 'saved');

        return folder;
    }, function (e) {
        znMessage('Error creating folder', 'error');
    });
};
{% endhighlight %}

![Record Board Add Folder]({{ site.baseurl }}/img/plugins/tutorials/record-board-add-folder.png)

## Moving Records

Users can now add folders, but without a way to change the record folder from this screen, the new columns are probably empty. Let's add the ability to move the records between lists using the `ui-sortable` directive.

In the plugin JavaScript, we need to add some sortable options to the `$scope`. This will connect the record lists and allow you to drag records from one folder to another.

{% highlight js %}
// Sortable Options
$scope.sortableOptions = {
    connectWith: 'ul.records-container',
    items: 'li.record'
};
{% endhighlight %}

Next, in the plugin HTML, add the directive `ui-sortable` to the record list as seen below. As you can see, it should reference the `sortableOptions` from above. We also need to add `ng-model` referencing the list of records for `ui-sortable` to work.

{% highlight html %}
{% raw %}
<!-- Folder Column -->
<div ng-repeat="folder in folders" class="column">
    <!-- Display Folder Name -->
    <div class="name">{{folder.name}}</div>

    <!-- Folder Records List -->
    <ul class="record-list" ui-sortable="sortableOptions" ng-model="folderRecords[folder.id]">
        <li ng-repeat="record in folderRecords[folder.id]" class="record">{{record.name}}</li>
    </ul>
</div>
{% endraw %}
{% endhighlight %}

One more small, but important, addition is to update the CSS to add some height to empty lists. This is necessary to be able to drag items onto empty lists. Add the following to the plugin CSS.

{% highlight css %}
.column ul {
    min-height: 30px;
}
{% endhighlight %}

![Record Board Move Records]({{ site.baseurl }}/img/plugins/tutorials/record-board-folders.png)

## Saving Record Folders

Now that users can move records into different folders, let's add a way to save the changes. Starting with the plugin HTML, we will need to add a way to identify the record being moved. We can do this by adding a `data-id` attribute to the record item.

{% highlight html %}
{% raw %}
<!-- Folder Column -->
<div ng-repeat="folder in folders" class="column">
    <!-- Display Folder Name -->
    <div class="name">{{folder.name}}</div>

    <!-- Folder Records List -->
    <ul class="record-list" ui-sortable="sortableOptions" ng-model="folderRecords[folder.id]">
        <li ng-repeat="record in folderRecords[folder.id]" data-id="{{record.id}}" class="record">{{record.name}}</li>
    </ul>
</div>
{% endraw %}
{% endhighlight %}

Next, we need to update the sortable options to trigger a save when a record is moved. Sortable provides several callbacks when lists are updated. Here we can take advantage of the `stop` callback with the function you see below.

{% highlight js %}
// Sortable Options
$scope.sortableOptions = {
    connectWith: "ul.records-container",
    items: "li.record",
    stop: function(event, ui) {

        // Traverse Records by Folder
        angular.forEach($scope.folders, function(folder) {
            angular.forEach($scope.folderRecords[folder.id], function(record, index) {
                // Record Found and Folder Changed
                if (record.id == ui.item.data('id') &&
                    record.folder.id != folder.id) {

                    // Update Record Folder ID
                    znData('FormRecords').save({ formId: $scope.formId, id: record.id}, { folder: { id: folder.id }}, function(response) {
                        // Update Folder Records with Response
                        $scope.folderRecords[folder.id].splice(index, 1, response);
                    }, function(e) {
                        znMessage('Error moving record', 'error');
                    });
                }
            });
        });
    }
};
{% endhighlight %}

When the sorting has stopped, we traverse the known folders and records to find the where the record was moved. When the record is found and the new folder is different from the current folder, it uses the `znData` service to save the new folder ID. Once the save is complete, it updates the folder record list with the response.

![Record Board Plugin]({{ site.baseurl }}/img/plugins/tutorials/record-board-part2.png)

## Wrapping Up

Your plugin should now be able to display folders as columns, create folders, drag records from one folder to another, and save the results.

The code for the entire chat plugin can be found below and also on [Github](https://github.com/ZengineHQ/labs/tree/master/plugins/record-kanban-board){:target="_blank"}. In this case, the plugin namespace is 'namespaced', so to make it work as your own, you will need to replace all instances of the word 'namespaced' with your namespace.

If you have improvements to the plugin, feel free to make pull requests to the code repository and update the documentation for it [here]({{site.developerDomain}}/edit/gh-pages/plugins/tutorials/record-board-2.md).

<ul class="nav nav-tabs" role="tablist" id="myTab">
  <li class="active"><a href="#plugin-js" role="tab" data-toggle="tab">plugin.js</a></li>
  <li><a href="#plugin-html" role="tab" data-toggle="tab">plugin.html</a></li>
  <li><a href="#plugin-css" role="tab" data-toggle="tab">plugin.css</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade in active" id="plugin-js">
{% highlight js %}
/**
 * Plugin Record Board Controller
 */
plugin.controller('namespacedRecordBoardCntl', ['$scope', '$routeParams', 'znData', 'znMessage', function ($scope, $routeParams, znData, znMessage) {

    // Current Workspace ID from Route
    $scope.workspaceId = null;

    // Selected Form ID
    $scope.formId = null;

    // Workspace Forms
    $scope.forms = [];

    // Selected Form Folders
    $scope.folders = [];

    // Records Indexed by Folder
    $scope.folderRecords = {};

    // Show Add Folder Flag
    $scope.showAddFolder = false;

    // Add Folder Name
    $scope.addFolderName = null;

    // Selected Folder to Edit
    $scope.editFolder = {
        id: null,
        name: null
    };

    // Sortable Options
    $scope.sortableOptions = {
        connectWith: "ul.records-container",
        items: "li.record",
        stop: function(event, ui) {

            // Traverse Records by Folder
            angular.forEach($scope.folders, function(folder) {
                angular.forEach($scope.folderRecords[folder.id], function(record, index) {
                    // Record Found and Folder Changed
                    if (record.id == ui.item.data('id') &&
                        record.folder.id != folder.id) {

                        // Update Record Folder ID
                        znData('FormRecords').save({ formId: $scope.formId, id: record.id}, { folder: { id: folder.id }}, function(response) {
                            // Update Folder Records with Response
                            $scope.folderRecords[folder.id].splice(index, 1, response);
                        }, function(e) {
                            znMessage('Error moving record', 'error');
                        });
                    }
                });
            });
        }
    };

    /**
     * Load Forms for Workspace
     */
    $scope.loadForms = function() {
        // Reset Workspace Forms
        $scope.forms = [];

        var params = {
            workspace: { id: $scope.workspaceId },
            related: 'folders'
        };

        // Query Forms by Workspae ID and Return Loading Promise
        return znData('Forms').query(params).then(function(response){
            // Set Workspace Forms from Response
            $scope.forms = response;
        });
    };

    /**
     * Load Records by Form Folders
     */
    $scope.loadRecords = function() {
        // Reset Folder Records
        $scope.folderRecords = {};

        var queue = [];

        var params = {
            formId: $scope.formId,
            folder: {}
        };

        // Get Records by Folder
        angular.forEach($scope.folders, function(folder) {
            // Initialize Folder Record List
            $scope.folderRecords[folder.id] = [];

            params.folder.id = folder.id;

            // Query and Index Records by Folder
            var request = znData('FormRecords').query(params).then(function(response) {
                    $scope.folderRecords[folder.id] = response;
                }
            );

            queue.push(request);
        });

    };

    /**
     * Pick Selected Form
     */
    $scope.pickForm = function(formId) {
        // Reset Form Folders
        $scope.folders = [];

        // Set Selected Form ID
        $scope.formId = formId;

        // Find Form and Set Selected Form Folders
        angular.forEach($scope.forms, function(form) {
            if (form.id == formId) {
                $scope.folders = form.folders;
            }
        });

        // Load Records for Selected Form Folders
        $scope.loadRecords();

    };

    /**
     * Open or Close Add Folder Column
     */
    $scope.openAddFolder = function(show) {
        $scope.showAddFolder = show;
    };

    /**
     * Add Folder
     */
    $scope.addFolder = function() {

        var params = {
            formId: $scope.formId
        };

        var data = {
            name: $scope.addFolderName,
            form: {
                id: $scope.formId
            }
        };

        // Save New Folder
        return znData('FormFolders').save(params, data, function (folder) {
            // Close Add Column
            $scope.openAddFolder(false);

            // Initialize New Folder Record List
            $scope.folderRecords[folder.id] = [];

            // Append New Folder to Folders List
            $scope.folders.push(folder);

            return folder;
        }, function (e) {
            znMessage('Error creating folder', 'error');
        });
    };

    /**
     * Toggle Edit Folder
     */
    $scope.toggleEditFolder = function(folderId) {
        if ($scope.editFolder.id == folderId) {
            // Close Edit Folder
            $scope.editFolder.id = null;
            $scope.editFolder.name = null;
        }
        else {
            // Open Edit Folder for Folder ID
            $scope.editFolder.id = folderId;

            // Find Folder Name by ID
            angular.forEach($scope.folders, function(folder)  {
                if (folder.id == folderId) {
                    $scope.editFolder.name = folder.name;
                }
            });
        }
    };

    /**
     * Save Edit Folder
     */
    $scope.saveFolder = function() {

        var params = {
            formId: $scope.formId,
            id: $scope.editFolder.id
        };

        var data = {
            name: $scope.editFolder.name,
            form: {
                id: $scope.formId
            }
        };

        // Save Folder
        return znData('FormFolders').save(params, data, function (response) {
            // Update Folder in Folders List
            angular.forEach($scope.folders, function(folder, index)  {
                if (folder.id == $scope.editFolder.id) {
                    $scope.folders.splice(index, 1, response);
                }
            });

            // Close Edit Folder
            $scope.toggleEditFolder();

            return response;
        }, function (e) {
                znMessage('Error saving folder', 'error');
        });

    };

    // Initialize for Workspace ID
    if ($routeParams.workspace_id) {
        // Set Selected Workspace ID
        $scope.workspaceId = $routeParams.workspace_id;

        // Load Workspace Forms, then Pick First Form
        $scope.loadForms().then(function() {
            if ($scope.forms) {
                $scope.pickForm($scope.forms[0].id);
            }
        });
    }

}])
/**
 * Plugin Registration
 */
.register('namespacedRecordBoard', {
    route: '/namespaced',
    controller: 'namespacedRecordBoardCntl',
    template: 'namespaced-record-board-main',
    title: 'Record Board',
    pageTitle: false,
    fullPage: true,
    topNav: true,
    order: 300,
    icon: 'icon-th-large'
});
{% endhighlight %}
    </div>
    <div class="tab-pane fade" id="plugin-html">
{% highlight html %}
{% raw %}
<script type="text/ng-template" id="namespaced-record-board-main">

    <!-- form tabs -->
    <div>
        <ul class="tabs">
            <li ng-repeat="form in forms" ng-class="{active: formId == form.id}">
                <a href="#" ng-click="pickForm(form.id)">{{form.name}}</a>
            </li>
        </ul>
    </div>

    <!-- Header Actions -->
    <div ng-show="formId">
        <span class="btn" ng-click="openAddFolder(true)"><i class="icon-plus"></i> Add Folder</span>
    </div>

    <!-- Board Canvas -->
    <div class="wrapper">

        <!-- Folder Column -->
        <div ng-repeat="folder in folders" class="column">
            <!-- Display Folder Name -->
            <div class="name" ng-hide="editFolder.id==folder.id">
                <span ng-show="folder.id==0">{{folder.name}}</span>
                <a href="#" ng-click="toggleEditFolder(folder.id)" ng-show="folder.id!=0">{{folder.name}}</a>
            </div>
            <!-- Edit Folder Name -->
            <div ng-show="editFolder.id==folder.id">
                <div class="control-group">
                    <input type="text" ng-model="editFolder.name" class="input-large">
                </div>
                <div class="form-actions">
                    <a href="#" ng-click="saveFolder()" class="btn btn-primary">Save</a>
                    <a href="#" ng-click="toggleEditFolder(folder.id)" class="secondary">Cancel</a>
                </div>
            </div>

            <!-- Folder Records List -->
            <ul id="{{folder.id}}" data-id="{{folder.id}}" ui-sortable="sortableOptions" ng-model="folderRecords[folder.id]" class="records-container">
                <li data-id="{{record.id}}" ng-repeat="record in folderRecords[folder.id]" class="record">{{record.name}}</li>
            </ul>
        </div>

        <!-- Add Folder Column -->
        <div ng-show="showAddFolder" class="column">
            <!-- Folder Name -->
            <div class="control-group">
                <input type="text" ng-model="addFolderName" placeholder="New Folder Name" class="input-large">
            </div>
            <!-- Add Folder Actions -->
            <div class="form-actions">
                <a href="#" ng-click="addFolder()" class="btn btn-primary">Add</a>
                <a href="#" ng-click="openAddFolder(false)" class="secondary">Cancel</a>
            </div>
        </div>

    </div>

</script>
{% endraw %}
{% endhighlight %}
    </div>
    <div class="tab-pane fade" id="plugin-css">
{% highlight css %}
/**
 * Plugin Record Board CSS
 */

.title {
    color: purple;
}
.column {
    float: left;
    width: 200px;
    background-color: #fff;
    box-shadow: 0px 2px 2px rgba(136, 136, 136, 0.43);
    padding: 5px;
    margin: 0px 10px 20px 0px;
}

.column li {
    background-color: #fff;
    padding: 10px 5px 10px 5px;
    margin: 10px 0px 10px 0px;
    border: 1px solid #e3e3e3;
    border-radius: 3px;
}

.column .name {
    font-weight: bold;
}

.column ul {
    min-height: 30px;
}

.record {
    cursor: move;
}

.wrapper {
    width: 100%;
    margin-top: 10px;
}
{% endhighlight %}
    </div>
</div>
