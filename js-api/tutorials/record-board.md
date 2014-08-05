---
layout: plugin-nav-bar
group: tutorials
subgroup: record-board
---

# Creating a Record Board Plugin

Forms are like a table or spreadsheet. Form records are the submissions collected from people filling out the form. The standard app Data tab displays this data as a spreadsheet, with the form fields as columns and the form records as the rows.

The idea behind the Record Board plugin is to offer a different visualization and way of working with this form data. The Record Board plugin will display this data similar to kanban boards as columns of lists. Form folders will be used as columns containing lists of records.

## Prerequisites

Before developing the plugin, we will want to start off with a form and some record data. For the purposes of this guide, the form only needs 1 field, which will represent the record name. For this plugin, the form records should be things that can be categorized into lists.

For this example, let's create a form called Universities. The record name field will represent the name of the university. The following list of universities would be a good sample of records to create:

Georgetown University
Butler University
Xavier
University of Kentucky
Auburn
Texas A&M
Boston College
Virginia Tech
Florida State
University of Illinois
Michigan State University
The Ohio State University
Arizona State University
University of California, Los Angeles
University of Southern California


## Current Workspace

This is a workspace specific plugin, so the first thing the plugin needs to be aware of is which workspace is selected. This can be determined from the route using `$routeParams`. The following code injects `$routeParams` as a dependency, initializes a `workspaceId` `$scope` property, then uses `$routeParams` to set this property.

{% highlight js %}
/**
 * My Plugin Controller
 */
plugin.controller('myPluginCntl', ['$scope', '$routeParams', function ($scope, $routeParams) {

	// Current Workspace ID from Route
	$scope.workspaceId = null;
	
	// Initialize for Workspace ID
	if ($routeParams.workspace_id) {
		// Set Selected Workspace ID
		$scope.workspaceId = $routeParams.workspace_id;
	}

}])
{% endhighlight %}

## Workspace Forms

Now that the workspace ID is known, we want to query the workspace forms. For this example, we are only working with one form, but a workspace can have multiple forms, so we need to query for all the forms available.

To query the forms, we need to use the `Data` service. The `Data` service provides access to our REST API and has built-in functionality to make requests as the logged-in plugin user. We will need to include it as a dependency, like we did with `$routeParams`.

{% highlight js %}
plugin.controller('myPluginCntl', ['$scope', '$routeParams', 'Data', function ($scope, $routeParams, Data) {
{% endhighlight %}

We will use the `Data` service to query the `Forms` endpoint, passing the workspace ID from above as a query parameter and `folders` as related data we want included in the response.  

{% highlight js %}

	// Workspace Forms
	$scope.forms = [];
	
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
{% endhighlight %}

We need to trigger the loadForms function to be called, so we will add that to the workspace detection code after it sets the workspace ID.

{% highlight js %}

	// Initialize for Workspace ID
	if ($routeParams.workspace_id) {
		// Set Selected Workspace ID
		$scope.workspaceId = $routeParams.workspace_id;
		
		// Load Workspace Forms
		$scope.loadForms();
	}
{% endhighlight %}

Now the plugin javascript should be loading the workspace forms into `$scope.forms`. We need to add some HTML to display this list when the plugin runs. Click over to the plugin.html editor and add the following code into your main template.

{% highlight html %}
{% raw %}
<!-- form tabs -->
<div>
	<ul class="tabs">
		<li ng-repeat="form in forms">{{form.name}}</li>
	</ul>
</div>
{% endraw %}
{% endhighlight %}

This will cause the forms to appear as tabs. Tabs are a pattern of the app, so this HTML will display tabs the same way the app displays tabs.

At this point you could run the plugin. After clicking into a workspace and picking your plugin, you should see the list of forms in the workspace. Click back over to the plugin editor to continue.

## Selecting a Form

The plugin user will want to do more than just see a list of forms in the workspace. We need to add a way to select a form. When a form is selected, it should keep track of the form ID in a similar way to the workspace ID. We also want to keep track of the form's `folders` that we requested when we queried the `Forms` endpoint so that we can access them later.

{% highlight js %}

	// Selected Form ID
    $scope.formId = null;
	
	// Selected Form Folders
	$scope.folders = [];
	
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
        
	};
{% endhighlight %}

Now we can update the plugin HTML to allow for selecting forms by calling the `pickForm` function. Since we have the `$scope.formId` set, we can also visually indicate which form is currently selected by applying the active class. 

{% highlight html %}
{% raw %}
<!-- form tabs -->
<div>
	<ul class="tabs">
		<li ng-repeat="form in forms" ng-class="{active: formId == form.id}"><a href="#" ng-click="pickForm(form.id)">{{form.name}}</a></li>
	</ul>
</div>
{% endraw %}
{% endhighlight %}

## Loading Records

Picking a form should also trigger the records of that form to load. We will be displaying these records in lists by folder, so we will loop through the `folders` and query records by folder ID. The `folderRecords` property will hold these records. After querying the `Records` endpoint, it will store the records returned in `folderRecords`, indexed by folder ID.

{% highlight js %}

	// Records Indexed by Folder
    $scope.folderRecords = {};
	
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
{% endhighlight %}

To trigger loading the records, we will update the `pickForm` function to load the records after setting the folders.

{% highlight js %}

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
{% endhighlight %}

At this point, the plugin should be loading forms, folders, and form records into various `$scope` properties. Now that we have this data, we can use it in the plugin interface. Going back to the plugin HTML, we need to add a the folders as columns and records as lists under those columns.

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
            
    </div>
{% endraw %}
{% endhighlight %}

To make the divs appear as columns, we can add the following to plugin CSS.

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

.wrapper {
    width: 100%;
    margin-top: 10px;
}
{% endhighlight %}

## Wrapping Up

Your plugin javascript should now look something like this (with your own plugin namespace and registration options):

{% highlight js %}
/**
 * My Plugin Controller
 */
plugin.controller('myPluginCntl', ['$scope', '$routeParams', 'Data', function ($scope, $routeParams, Data) {

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
{% raw %}
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
			<ul class="record-list">
				<li ng-repeat="record in folderRecords[folder.id]" class="record">{{record.name}}</li>
			</ul>
		</div>
            
    </div>
    
</script>
{% endraw %}
{% endhighlight %}

## Part 2

At this point you should have a functional plugin that will display form folders as columns listing form records. If you don't have any folders, you may only see one column. In <a href="{{site.baseurl}}/js-api/tutorials/record-board-2.html">part 2</a>, we will work on making the plugin more useful by adding the ability to add folders and move records between lists.