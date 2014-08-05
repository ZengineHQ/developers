---
layout: plugin-nav-bar
group: tutorials
subgroup: record-board
---

# Creating a Record Board Plugin Part 2

In part 1, we created a workspace plugin that would get a list of forms, folders, and records. The plugin would display the forms as tabs, the folders as columns, and the records in a list, by column.

At this point you probably only have 1 column and you want to do more than just see a list of records. In this guide we will work on adding new folders/columns and moving records from one column to another.

## Adding Folders

Let's start by adding a column to the board that prompts for a folder name. You can recall some of this code from part 1, as a reference where to put the add folder HTML. 

{% highlight html %}
{% raw %}
	<!-- Board Canvas -->
	<div class="wrapper">
    
		<!-- Folder Column -->
		<div ng-repeat="folder in folders" class="column">
			<!-- Display Folder Name -->
			<div class="name">{{folder.name}}</div>
            
			<!-- Folder Records List -->
			<ul class="record-list">
				<li ng-repeat="record in folderRecords[folder.id]" class="record">{{record.name}}</li>
			</ul>
		</div>
		
		<!-- Add Folder Column -->
		<div ng-show="formId" class="column">
			<!-- Folder Name -->
			<div class="control-group">
				<input type="text" ng-model="addFolderName" placeholder="New Folder Name" class="input-large">
			</div>
			<!-- Add Folder Actions -->
			<div class="form-actions">
				<a href="#" ng-click="addFolder()" class="btn btn-primary">Add</a>
			</div>
		</div>
            
	</div>
{% endraw %}
{% endhighlight %}

This represents patterns for forms and buttons that will match the app. The `ng-show` for `formId` will make sure the column only appears when a form has been selected, since a form is required to create a form folder. The `ng-model` makes the new folder name accessible from the `$scope` as the property `addFolderName`. We will write the `addFolder` function below in the plugin javascript to make it work.

The following function will post data to the `FormFolders` endpoint to create a new folder using the name from above. After successfully creating a new folder, it will update the list of folders and initialize an empty record list for the folder. With AngularJS 2-way data binding, updating the folders property will automatically make a new column appear in the interface.

A new service is also introduced, called `message`, so be sure to add that to the dependencies in a similar way to `$routeParams` and `Data`. The `message` service is used here to indicate success or failure to the user.

{% highlight js %}
	// Add Folder Name 
    $scope.addFolderName = null;
    
	/**
	 * Add Folder
	 */
	$scope.addFolder = function() {
		var data = {
			name: $scope.addFolderName,
			form: {
				id: $scope.formId
			}
		};
		
		// Reset Folder Name
		$scope.addFolderName = '';
 
		// Save New Folder
		return Data('FormFolders').save({formId: $scope.formId}, data, function (folder) {
			// Initialize New Folder Record List
			$scope.folderRecords[folder.id] = [];
            
			// Append New Folder to Folders List
			$scope.folders.push(folder);
			
			message('New folder created', 'saved');
			
			return folder;
		}, function (e) {
			message('Error creating folder', 'error');
		});
	};
{% endhighlight %}

![Record Board Add Folder]({{ site.url }}/img/js-api/tutorials/record-board-add-folder.png)

## Moving Records

Users can now add folders, but without a way to change the record folder from this screen, the new columns are probably empty. Let's add the ability to move the records between lists using the `ui-sortable` directive.

In the plugin javascript, we need to add some sortable options to the `$scope`. This will connect the record lists and allow you to drag records from one folder to another.

{% highlight js %}
	// Sortable Options
	$scope.sortableOptions = {
		connectWith: 'ul.record-list',
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
.record-list {
    min-height: 15px;
}
{% endhighlight %}

![Record Board Move Records]({{ site.url }}/img/js-api/tutorials/record-board-folders.png)

## Saving Record Folders

Now that users can move records into different folders, let's add a way to save the changes. Starting with the plugin HTML, we will need to add a way to identify the record being moved. We can do this by adding a data-id attribute to the record item.

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

Next, we need to update the sortable options to trigger a save when a record is moved. Sortable provides several callbacks when lists are updated. Here we can take advantage of the `update` callback with the function you see below.

{% highlight js %}
	// Sortable Options
	$scope.sortableOptions = {
		connectWith: 'ul.record-list',
		items: 'li.record',
		update: function(event, ui) {
		
			// Ignore Reorder
			if (!ui.sender) {
				return;
			}
            
			// Traverse Records by Folder
			angular.forEach($scope.folders, function(folder) {
				angular.forEach($scope.folderRecords[folder.id], function(record, index) {
					// Record Found
					if (record.id == ui.item.data('id')) {
                        
						// Update Record Folder ID
						Data('FormRecords').save({ formId: $scope.formId, id: record.id}, { folder: { id: folder.id }}, function(response) {
							// Update Folder Records with Response
							$scope.folderRecords[folder.id].splice(index, 1, response);
							
							message('Record moved', 'saved');
						}, function(e) {
							message('Error moving record', 'error');
						});
					}
				});
			});
		}
	};
{% endhighlight %}

First, we ignore cases where `ui.sender` is empty, because those only represent reordering records in the same list. Then we traverse the known folders and records to find the where the record was moved. When the record is found it uses the `Data` service to save the new folder ID. One the save is complete, it updates the folder record list with the response.

![Record Board Plugin]({{ site.url }}/img/js-api/tutorials/record-board-part2.png)

## Wrapping Up

Your plugin should now be able to display folders as columns, create folders, drag records from one folder to another, and save the results.

Your plugin javascript should now look something like this (with your own plugin namespace and registration options):

{% highlight js %}
/**
 * My Plugin Controller
 */
plugin.controller('myPluginCntl', ['$scope', '$routeParams', 'Data', 'message', function ($scope, $routeParams, Data, message) {

	// Current Workspace ID from Route
	$scope.workspaceId = null;
	
	// Workspace Forms
	$scope.forms = [];
	
	// Selected Form ID
    $scope.formId = null;
	
	// Selected Form Folders
	$scope.folders = [];
	
	// Records Indexed by Folder
    $scope.folderRecords = {};
    
    // Add Folder Name 
    $scope.addFolderName = null;
    
    // Sortable Options
	$scope.sortableOptions = {
		connectWith: 'ul.record-list',
		items: 'li.record',
		update: function(event, ui) {
		
			// Ignore Reorder
			if (!ui.sender) {
				return;
			}
            
			// Traverse Records by Folder
			angular.forEach($scope.folders, function(folder) {
				angular.forEach($scope.folderRecords[folder.id], function(record, index) {
					// Record Found
					if (record.id == ui.item.data('id')) {
                        
						// Update Record Folder ID
						Data('FormRecords').save({ formId: $scope.formId, id: record.id}, { folder: { id: folder.id }}, function(response) {
							// Update Folder Records with Response
							$scope.folderRecords[folder.id].splice(index, 1, response);
							
							message('Record moved', 'saved');
						}, function(e) {
							message('Error moving record', 'error');
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
        
		// Query Forms by Workspae ID and Return Loading Promise
		return Data('Forms').query({workspace: { id: $scope.workspaceId }, related: 'folders'}, function(response){
			// Set Workspace Forms from Response
			$scope.forms = response;
		});
	};
	
	/**
	 * Pick Selected Form
	 */
	$scope.pickForm = function(formId) {
		// Set Selected Form ID
		$scope.formId = formId;
		
		// Reset Form Folders
		$scope.folders = [];
        
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
	 * Load Records by Form Folders
	 */
	$scope.loadRecords = function() {
		// Reset Folder Records
		$scope.folderRecords = {};
        
		var queue = [];
        
		// Get Records by Folder
		angular.forEach($scope.folders, function(folder) {
			// Initialize Folder Record List
			$scope.folderRecords[folder.id] = [];
            
			// Query and Index Records by Folder
			var request = Data('FormRecords').query({formId: $scope.formId, folder: { id: folder.id }}, function(response) {
				$scope.folderRecords[folder.id] = response;
			});
            
			queue.push(request);
		});
        
	};
	
	/**
	 * Add Folder
	 */
	$scope.addFolder = function() {
		var data = {
			name: $scope.addFolderName,
			form: {
				id: $scope.formId
			}
		};
		
		// Reset Folder Name
		$scope.addFolderName = '';
 
		// Save New Folder
		return Data('FormFolders').save({formId: $scope.formId}, data, function (folder) {
			// Initialize New Folder Record List
			$scope.folderRecords[folder.id] = [];
            
			// Append New Folder to Folders List
			$scope.folders.push(folder);
			
			message('New folder created', 'saved');
			
			return folder;
		}, function (e) {
			message('Error creating folder', 'error');
		});
	};
	
	// Initialize for Workspace ID
	if ($routeParams.workspace_id) {
		// Set Selected Workspace ID
		$scope.workspaceId = $routeParams.workspace_id;
		
		// Load Workspace Forms
		$scope.loadForms();
	}

}])
{% endhighlight %}

Your HTML should look similar to this (with your own plugin namespace in the template id):

{% highlight html %}
<script type="text/ng-template" id="my-plugin-main">
   
    <!-- form tabs -->
    <div>
        <ul class="tabs">
            <li ng-repeat="form in forms" ng-class="{active: formId == form.id}"><a href="#" ng-click="pickForm(form.id)">{{form.name}}</a></li>
        </ul>
    </div>
    
    <!-- Board Canvas -->
	<div class="wrapper">
    
		<!-- Folder Column -->
		<div ng-repeat="folder in folders" class="column">
			<!-- Display Folder Name -->
			<div class="name">{{folder.name}}</div>
            
			<!-- Folder Records List -->
			<ul class="record-list" ui-sortable="sortableOptions" ng-model="folderRecords[folder.id]">
				<li ng-repeat="record in folderRecords[folder.id]" data-id="{{record.id}}" class="record">{{record.name}}</li>
			</ul>
		</div>
		
		<!-- Add Folder Column -->
		<div ng-show="formId" class="column">
			<!-- Folder Name -->
			<div class="control-group">
				<input type="text" ng-model="addFolderName" placeholder="New Folder Name" class="input-large">
			</div>
			<!-- Add Folder Actions -->
			<div class="form-actions">
				<a href="#" ng-click="addFolder()" class="btn btn-primary">Add</a>
			</div>
		</div>
            
	</div>
    
</script>
{% endhighlight %}

Your plugin CSS should look like this:

{% highlight css %}
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

.record-list {
    min-height: 15px;
}

.wrapper {
    width: 100%;
    margin-top: 10px;
}
{% endhighlight %}