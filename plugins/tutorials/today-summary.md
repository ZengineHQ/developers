---
layout: plugin-nav-bar
group: tutorials
subgroup: today-summary
---


<h1 id="building-a-today-plugin">Building a &quot;Today&quot; plugin <a href="https://github.com/ZengineHQ/labs/tree/master/plugins/today-summary" target="_blank">
        <span class="btn btn-primary btn-sm">
            <i class="fa fa-github fa-lg"></i> View on Github
        </span>
    </a>
</h1>

The Today plugin provides an example of a user-level, inline plugin that presents the user with a simple list of tasks and events due or taking place on the current day.  This allows the user to get a quick glance at their most important action items.  This plugin demonstrates how simple it is to develop a custom interaction with data from the {{site.productName}} API using the plugin system and basic AngularJS code.

## Prerequisites

If you haven't yet done so, we recommended first reading about [data access]({{site.baseurl}}/plugins/api/services/#zndata) before starting this tutorial.

In order for the plugin to show output, you should also create a few sample tasks and events taking place today.  You can create these right in the web app interface.

## Base Plugin Setup

When you first create a new plugin, you are provided with a template for a basic full-page plugin.  In this case, we want to make an inline plugin that will appear in the top nav bar, so that the user can quickly glance at it regardless of where they are within the app.  You can read more about full-page and inline plugins [here]({{site.baseurl}}/plugins/getting-started/full-page-inline.html).

As described in the link above, we need to make a few changes to our base registration options.  Namely, set `fullpage: false` and `location: 'zn-top-nav'` to make our plugin inline and give it a [location]({{site.baseurl}}/plugins/registration/#plugin-locations). We can delete `route`, `topNav`, and `pageTitle`, since they don't apply for inline plugins.

We also want to make this a user-level plugin, rather than the default of workspace-level.  This means that a user can install the plugin on an account level and use it regardless of which workspace they're in - in fact, it can even be used on the home screen, where no workspace is selected.  Be sure to go into the plugin's Publishing Settings in the app and change the Plugin Activation Level to User.

## Getting Started

The zn-top-nav injection point doesn't have enough room to show actual content, so let's start by setting up a basic dropdown menu to be used with our plugin.  If we look at the Components section of the [{{site.productName}} Design Patterns site]({{site.clientDomain}}/patterns/components){:target="_blank"}, we find an example of a menu under the "Inline Menus" header.  By inspecting the sample menu element in the browser, we can see the markup that will get us there.  This dropdown is inherited from Bootstrap, upon which the {{site.productName}} layout is built.

We set up our template shell like so:

{% highlight html %}
{% raw %}

<script type="text/ng-template" id="today-summary-main">
	<div class="dropdown">
    	<a class="topnav-section-link" data-toggle="dropdown" href="javascript:void(0)">
    		<i class="icon-th-list"></i>
    	</a>
    	<div id="today-summary-menu" class="dropdown-menu" role="menu">
            {{text}}
    	</div>
    </div>
</script>

{% endraw %}
{% endhighlight %}

While we're at it, let's add some basic styles for the dropdown to our CSS file:

{% highlight css %}
.dropdown-menu {
    color: black;
    padding: 10px;
    width: 400px;
    max-height: 500px;
}
{% endhighlight %}

The menu isn't very useful yet, but at least we've got it showing up!

## Fetching API data

Let's get a little more practical and try to display some task and event data from the {{site.productName}} API.  At the same time, let's add the dependencies we'll need to our controller.  We'll explain these as we go along.  For our first attempt, let's just fetch whatever tasks and events we can find.  We'll do this fetch whenever the `$scope.update` function is triggered:

{% highlight js %}
plugin.controller('todaySummaryCntl', ['$scope', 'znData', '$routeParams', '$q', function ($scope, znData, $routeParams, $q) {
	
	/*
	 * Load up-to-date task & event data from the API
	 */
	$scope.update = function() {
		znData('Tasks').query(
			{}, // params - leaving empty for now
			function success(response) {
				$scope.tasks = response;
			},
			function error(response) {
				console.log('Error loading tasks!');
			}
		);
		
		znData('Events').query(
			{}, // params - leaving empty for now
			function success(response) {
				$scope.events = response;
			},
			function error(response) {
				console.log('Error loading events!');
			}
		);
	};
	
	// Stop click event propagation - keeps the menu open on internal clicks
	$('#today-summary-menu').on('click', function (event) {
		event.stopPropagation();
	});
}])
{% endhighlight %}

Note the bit we snuck in at the end about stopping click event propagation.  This basic popup closes any time you click anywhere on the page, even if it's within the menu itself.  We want to be able to click around within the popup, so we override this behavior by stopping clicks inside the menu from propagating and hence closing the popup.

In order to invoke this function, we'll set `ng-click="update()"` on the dropdown menu icon in our template.  This way the content will be updated from the API every time the menu is opened.  Let's also output some very basic data so we can see that our function is working:

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="today-summary-main">
	<div class="dropdown">
		<a class="topnav-section-link" data-toggle="dropdown" ng-click="update()" href="javascript:void(0)">
			<i class="icon-th-list"></i>
		</a>
		<div id="today-summary-menu" class="dropdown-menu" role="menu">
			<span class="throbber" ng-show="loading"></span>
			<h2>Today's Tasks</h2>
			<ul>
				<li ng-repeat="task in tasks">{{task.task}}</li>
			</ul>
			
			<br />
			
			<h2>Today's Events</h2>
			<ul>
				<li ng-repeat="event in events">{{event.event}}</li>
			</ul>
    	</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}

Any property on the `$scope` item in our controller is available on the template.  This includes the function `update` as well as the data arrays `tasks` and `events`.  The `znData` service returns arrays of simple JavaScript data objects.  We can place those on the scope and then loop over them quite simply.

## Querying appropriate data

In our first iteration, we've simply asked the API for any data it can find.  However, the purpose of our plugin is to fetch tasks and events that are due/occurring today.  Let's start filtering down the results by adding some parameters to our queries.

In the case of tasks, there is a `due` date field.  The API can take a date in yyyy-mm-dd format.

In the case of events, there are `start` and `end` datetime fields, which potentially span across multiple days.  The API takes these datetime fields in ISO 8601 format.  To get events that are ongoing today, we want a time range from 00:00:00 to 23:59:59 on today's date.  More specifically, our criteria are events with a minimum `end` time of 00:00:00 and a maximum `start` time of 23:59:59.  It seems a little counterintuitive at first, but these criteria will include any events that started in the past and end today, events that start today and end in the future, and events that fall entirely within today.  If you imagine a typical calendar application, this is the subset of all events which would cross over today's calendar box.

Dates can be a little tricky in the browser, so let's add some helper functions:
{% highlight js %}
/*
 * Current date at 00:00:00 local time
 */
function getTodayBeginning() {
	var startTime = new Date();
	startTime.setHours(0);
	startTime.setMinutes(0);
	startTime.setSeconds(0);
	startTime.setMilliseconds(0);

	return startTime.toISOString();
}

/*
 * Current date at 23:59:59 local time
 */
function getTodayEnd() {
	var endTime = new Date();
	endTime.setHours(23);
	endTime.setMinutes(59);
	endTime.setSeconds(59);
	endTime.setMilliseconds(999);

	return endTime.toISOString();
}

/*
 * Current local date, yyyy-MM-dd string
 */
function getCurrentDate() {
	return new Date().toString('yyyy-MM-dd');
}
{% endhighlight %}

With these helper functions available, let's add some parameters to our `update` function.  While we're at it we can also specify sorts. We will sort events by `start`, since they may start at different points during the day or in the past.  For tasks, sorting by date doesn't make sense within one day because the `due` date doesn't have a time - for tasks due today, the `due` date is always the same.  Instead we'll sort by `priority`.

{% highlight js %}
$scope.update = function() {

	var eventParams = {
		// Property names with hyphens/dots need to go in quotes (standard JS stuff)
		'min-end': getTodayBeginning(),
		'max-start': getTodayEnd(),
		sort: 'start'
	};

	var taskParams = {
		due: getCurrentDate(),
		sort: 'priority'
	};

	znData('Tasks').query(
		taskParams,
		function success(response) {
			$scope.tasks = response;
		},
		function error(response) {
			console.log('Error loading tasks!');
		}
	);

	znData('Events').query(
		eventParams,
		function success(response) {
			$scope.events = response;
		},
		function error(response) {
			console.log('Error loading events!');
		}
	);
};
{% endhighlight %}

## Formatting output

At this point we're starting to get meaningful sets of data back.  Let's update our template to display more than just the items' names.

To keep things clean, we'll specify new templates for tasks and events, and then use them for each respective item.  Returning to the Patterns site, there are some nice [lists]({{site.clientDomain}}/patterns/lists){:target="_blank"} patterns we can use to quickly get well-styled lists.  Let's use the "condensed" list.  Here's a template for a task:

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="today-summary-task">
	<div class="list-item-wrapper">
		<div class="list-item-info-wrapper">
			<div class="list-item-headline">
				<span class="headline">{{task.task}}</span>
				<span class="pill pill-primary priority">Priority {{task.priority}}</span>
			</div>
			<div class="list-item-subline">
				<span class="subline">
					<span class="record" ng-show="task.record">{{task.record.name}} | </span>
					<span>{{task.status}}</span>
				</span>
			</div>
			<div class="list-item-right-block">
				<div class="list-item-right-icon">
					<i class="icon-check"></i>
				</div>
			</div>
		</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}

Note that we look for `task.record` and show the record name if it is available.  Tasks and events can be associated with a record, which comes back as a nested sub-object from the API.  However, it's not always set, so we use `ng-show` to display the record name span only if it is.

There is also a `body` property, which is free-form and can contain a lot of text.  It won't fit neatly within the summary list item, so let's allow the user to click on the list item and expand the body separately:

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="today-summary-task">
	<div class="list-item-wrapper" ng-click="task.showDetail = !task.showDetail">
		<div class="list-item-info-wrapper">
			<div class="list-item-headline">
				<span class="headline">{{task.task}}</span>
				<span class="pill pill-primary priority">Priority {{task.priority}}</span>
			</div>
			<div class="list-item-subline">
				<span class="subline">
					<span class="record" ng-show="task.record">{{task.record.name}} | </span>
					<span>{{task.status}}</span>
				</span>
			</div>
			<div class="list-item-right-block">
				<div class="list-item-right-icon">
					<i class="icon-check"></i>
				</div>
			</div>
		</div>
    </div>
    <div class="well detail" ng-show="task.showDetail">
		<div ng-show="task.body">{{task.body}}</div>
		<div ng-hide="task.body">[No details provided]</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}

`ng-click="task.showDetail = !task.showDetail"` is the crucial bit here.  Purely within the template, we're using AngularJS to flip a boolean switch for each task when clicking.  We then reference this property in the detail `div` with `ng-show="task.showDetail"`, so it will automatically show/hide as the item is clicked.

We add a template for events as well.  The layout is almost identical, but events have different data fields on them so a separate template is warranted:

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="today-summary-event">
    <div class="list-item-wrapper" ng-click="event.showDetail = !event.showDetail">
		<div class="list-item-info-wrapper">
			<div class="list-item-headline">
				<span class="headline">{{event.event}}</span>
			</div>
			<div class="list-item-subline">
				<span class="subline">
					<span class="record" ng-show="event.record">{{event.record.name}} | </span>
					<span>{{event.start | date:'short'}} - {{event.end | date:'short'}}</span>
				</span>
			</div>
			<div class="list-item-right-block">
				<div class="list-item-right-icon">
					<i class="icon-calendar"></i>
				</div>
			</div>
		</div>
    </div>
    <div class="well detail" ng-show="event.showDetail">
		<div ng-show="event.body">{{event.body}}</div>
		<div ng-hide="event.body">[No details provided]</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}

For the event template we're including the `start` and `end` dates.  The pipe notation passes them to AngularJS's `date` filter so that they're output in a compact, readable form.

Now that we have these templates set up, we need to update our primary template to actually use them for each task and event.  At the same time, let's show a bit of help text if there are no tasks or events to display:

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="today-summary-main">
	<div class="dropdown">
    	<a class="topnav-section-link" data-toggle="dropdown" ng-click="update()" href="javascript:void(0)">
    		<i class="icon-th-list"></i>
    	</a>
    	<div id="today-summary-menu" class="dropdown-menu" role="menu">
            <h2>Today's Tasks</h2>
    	    <ul class="list condensed">
                <li ng-repeat="task in tasks" ng-include src="'today-summary-task'"></li>
            </ul>
    		<h3 ng-hide="tasks.length">No tasks.</h3>
    		
    		<br />
    		
    		<h2>Today's Events</h2>
    		<ul class="list condensed">
    		    <li ng-repeat="event in events" ng-include src="'today-summary-event'"></li>
    		</ul>
            <h3 ng-hide="events.length">No events.</h3>
    	</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}

`ng-include` is set on an element in our main template along with a `src` attribute containing a string which corresponds to the id we set for our template in each `<script type="text/ng-template">` tag.

## User data

We're well on our way, but we missed an important part of the spec - our plugin should only be showing tasks that are assigned to the current user.  Tasks have an `assignedToUser` sub-object.  If we specify the `id` of that user, we can filter down what we want.  Furthermore, we only want to display tasks that have a status of either 'open' or 'in-progress'.

In order to specify the user ID, we need to fetch information about the current user.  We can get that from the API using the znData factory for 'Users' with the special `id` key `me`.  Once the user data is available on the scope, using it for our tasks request is as simple as adding a property to our params.  Limiting by status is a straightforward affair:

{% highlight js %}
znData('Users').get({id: 'me'}, function (response) {
	$scope.user = response;
});

// ... in $scope.update()
var taskParams = {
	due: getCurrentDate(),
	status: 'open|in-progress', // note pipe for specifying multiple options
	'assignedToUser.id': $scope.user.id,
	sort: 'priority'
};
{% endhighlight %}

### Race Conditions

That was nice and simple, but we need to remember that fetching anything from the API is asynchronous and takes some amount of time.  What will happen if a quick user clicks on the dropdown before the user data comes back?  Our `update` function will try to fetch tasks with user data that isn't set yet, and we'll have an error.

There are a number of potential strategies for working around this issue.  To keep it simple for this tutorial, let's go with the quick-and-dirty strategy of skipping the update if user data isn't available.  To ensure that our user doesn't get stuck with a menu that never loads, we'll automatically fetch task and event data one time after the user data comes in:

{% highlight js %}
$scope.update = function () {
	if (!$scope.user) {
	// Need user data to continue. Stop for now
		return;
	}

	var eventParams = {

. . .

znData('Users').get({id: 'me'}, function (response) {
	$scope.user = response;
	// Initial task/event data fetch now that user data is available
	$scope.update();
});
{% endhighlight %}

A more elegant but more complex solution could involve watching the `user` property and queuing up `update` requests until it becomes available, or using promise chaining to sequentially fetch user and then task/event data.

## Showing a Loading indicator

Now that we're more aware of loading time issues, let's add a loading indicator to the menu while updates are occurring.  The menu will continue the existing set of data while it fetches new data from the server.  We can communicate to the user that an update is ongoing by showing the loading indicator.  This will help avoid surprises if the data has updated and the menu contents change before the user's eyes.

Returning to the [Design Patterns components page]({{site.clientDomain}}/patterns/components){:target="_blank"}, there is a nifty "throbber" we can show simply by adding an element with the appropriate class.  By setting `ng-show="loading"` on this element, we can control when the throbber displays by setting the `loading` scope property as we work with the API in our controller:

{% highlight html %}
{% raw %}
<span class="throbber" ng-show="loading"></span>
{% endraw %}
{% endhighlight %}

On the controller side, our approach will be to set `$scope.loading = true` as soon as `$scope.update()` begins, and then set it back to `false` when we're done.  Again, though, we run into async complications - we are doing two separate API calls, one for tasks and one for events, each with their own set of callbacks.  Only when both are finished can we say that we are finished loading.

Again, there are multiple strategies for dealing with such a situation.  One approach might be to set a flag within each request's callback, and check in each callback if both flags have been set.  However, if we add more requests this approach will quickly become cumbersome.

Instead, we will use [Angular's `$q` service]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$q){:target="_blank"}, which provides a promise API.  You can read more about $q and promises at the link above.  In short, it provides a simple way for chaining together asynchronous events so that you can perform logic in the necessary order.  We will use $q's `all` function, which takes an array of multiple promises and lets you run a callback only when *all* of them are complete.  Since the `znData` functions always return promises, this is simple to use:

{% highlight js %}
$scope.update = function() {
	...
	$scope.loading = true; // display throbber
	...
	$q.all([
		znData('Tasks').query(
			taskParams,
			function success(response) {
				$scope.tasks = response;
			},
			function error(response) {
				console.log('Error loading tasks!');
			}
		),
		znData('Events').query(
			eventParams,
			function success(response) {
				$scope.events = response;
			},
			function error(response) {
				console.log('Error loading events!');
			}
		)
	]).then(function () {
		// runs when both Tasks and Events queries are done - hide throbber
		$scope.loading = false;
	});
{% endhighlight %}

Aside from `$scope.loading`, all we have added is the $q.all wrapper and the `then` callback at the end.  Now we can reliably tell when all requests have completed.

## Accounting for different workspaces

Our plugin remains present as a user switches workspaces, or goes to the home screen, where there is no specific workspace.  Let's update the plugin to load tasks/events only for the current workspace, if applicable.  As you can see when navigating around the app, the workspace ID is part of the URL.  We can fetch it easily using the `$routeProvider` service, which we passed into our controller earlier:

{% highlight js %}
$scope.update = function () {
	if (!$scope.user) {
	// Need user data to continue. Stop for now
		return;
	}

	var eventParams = {

. . .

znData('Users').get({id: 'me'}, function (response) {
$scope.update = function() {
. . . 
	if ($routeParams.workspace_id) {
		taskParams['workspace.id'] = eventParams['workspace.id'] = $routeParams.workspace_id;
	}
	
. . .
});
{% endhighlight %}

That's it!  Now the data in our plugin has a lot more context.

## Wrapping Up

The code for the entire Today plugin can be found below and also on [Github](https://github.com/ZengineHQ/labs/tree/master/plugins/today-summary){:target="_blank"}. In this case, the plugin namespace is 'todaySummary'. To make it work as your own, you will need to replace all instances of the text 'todaySummary' in JavaScript and 'today-summary' in HTML with your namespace.

If you have improvements to the plugin, feel free to make pull requests to the code repository and update the documentation for it [here]({{site.developerDomain}}/edit/gh-pages/plugins/tutorials/today-summary.md).

<ul class="nav nav-tabs" role="tablist" id="myTab">
  <li class="active"><a href="#plugin-js" role="tab" data-toggle="tab">plugin.js</a></li>
  <li><a href="#plugin-html" role="tab" data-toggle="tab">plugin.html</a></li>
  <li><a href="#plugin-css" role="tab" data-toggle="tab">plugin.css</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade in active" id="plugin-js">
{% highlight js %}
/**
 * Plugin Today Summary Controller
 */
plugin.controller('todaySummaryCntl', ['$scope', 'znData', '$routeParams', '$q', function ($scope, znData, $routeParams, $q) {

	/*
	 * Load up-to-date task & event data from the API
	 */
	$scope.update = function () {
	    if (!$scope.user) {
	        // Need user data to continue. Stop for now
	        return;
	    }
	    
		$scope.loading = true; // display throbber

		var eventParams = {
			// Property names with hyphens/dots need to go in quotes (standard JS stuff)
			'min-end': getTodayBeginning(),
			'max-start': getTodayEnd(),
			sort: 'start'
		};

		var taskParams = {
			due: getCurrentDate(),
			status: 'open|in-progress',
			'assignedToUser.id': $scope.user.id,
			sort: 'priority'
		};

		if ($routeParams.workspace_id) {
			taskParams['workspace.id'] = eventParams['workspace.id'] = $routeParams.workspace_id;
		}

		$q.all([
			znData('Tasks').query(
			    taskParams,
				function success(response) {
					$scope.tasks = response;
				},
				function error(response) {
					console.log('Error loading tasks!');
				}
			),
			znData('Events').query(
			    eventParams,
				function success(response) {
					$scope.events = response;
				},
				function error(response) {
					console.log('Error loading events!');
				}
			)
		]).then(function () {
			// runs when both Tasks and Events queries are done - hide throbber
			$scope.loading = false;
		});
	};
	
	znData('Users').get({id: 'me'}, function (response) {
		$scope.user = response;
	});
	
	/*
	 * Current date at 00:00:00 local time
	 */
	function getTodayBeginning() {
		var startTime = new Date();
		startTime.setHours(0);
		startTime.setMinutes(0);
		startTime.setSeconds(0);
		startTime.setMilliseconds(0);

		return startTime.toISOString();
	}

	/*
	 * Current date at 23:59:59 local time
	 */
	function getTodayEnd() {
		var endTime = new Date();
		endTime.setHours(23);
		endTime.setMinutes(59);
		endTime.setSeconds(59);
		endTime.setMilliseconds(999);

		return endTime.toISOString();
	}

	/*
	 * Current local date, yyyy-MM-dd string
	 */
	function getCurrentDate() {
		return new Date().toString('yyyy-MM-dd');
	}

	// Stop click event propagation - keeps the menu open on internal clicks
	// Important to run this within the controller code, since our plugin is loaded dynamically by the front end
	$('#today-summary-menu').on('click', function (event) {
		event.stopPropagation();
	});

}])
/**
 * Plugin Registration
 */
.register('todaySummary', {
	controller: 'todaySummaryCntl',
	template: 'today-summary-main',
	title: 'Today Summary Plugin',
	type: 'inline',
	icon: 'icon-th-list',
	location: 'zn-top-nav'
});
{% endhighlight %}
  </div>
    <div class="tab-pane fade" id="plugin-html">
{% highlight html %}
{% raw %}
<!-- Primary template -->
<script type="text/ng-template" id="today-summary-main">
	<div class="dropdown">
    	<a class="topnav-section-link" data-toggle="dropdown" ng-click="update()" href="javascript:void(0)">
    		<i class="icon-th-list"></i>
    	</a>
    	<div id="today-summary-menu" class="dropdown-menu" role="menu">
            <span class="throbber" ng-show="loading"></span>
            <h2>Today's Tasks</h2>
    	    <ul class="list condensed">
                <li ng-repeat="task in tasks" ng-include src="'today-summary-task'"></li>
            </ul>
    		<h3 ng-hide="tasks.length">No tasks.</h3>
    		
    		<br />
    		
    		<h2>Today's Events</h2>
    		<ul class="list condensed">
    		    <li ng-repeat="event in events" ng-include src="'today-summary-event'"></li>
    		</ul>
            <h3 ng-hide="events.length">No events.</h3>
    	</div>
    </div>
</script>

<!-- Template for each task -->
<script type="text/ng-template" id="today-summary-task">
	<div class="list-item-wrapper" ng-click="task.showDetail = !task.showDetail">
		<div class="list-item-info-wrapper">
			<div class="list-item-headline">
				<span class="headline">{{task.task}}</span>
				<span class="pill pill-primary priority">Priority {{task.priority}}</span>
			</div>
			<div class="list-item-subline">
				<span class="subline">
					<span class="record" ng-show="task.record">{{task.record.name}} | </span>
					<span>{{task.status}}</span>
				</span>
			</div>
			<div class="list-item-right-block">
				<div class="list-item-right-icon">
					<i class="icon-check"></i>
				</div>
			</div>
		</div>
    </div>
    <div class="well detail" ng-show="task.showDetail">
		<div ng-show="task.body">{{task.body}}</div>
		<div ng-hide="task.body">[No details provided]</div>
    </div>
</script>

<!-- Template for each event -->
<script type="text/ng-template" id="today-summary-event">
    <div class="list-item-wrapper" ng-click="event.showDetail = !event.showDetail">
		<div class="list-item-info-wrapper">
			<div class="list-item-headline">
				<span class="headline">{{event.event}}</span>
			</div>
			<div class="list-item-subline">
				<span class="subline">
					<span class="record" ng-show="event.record">{{event.record.name}} | </span>
					<span>{{event.start | date:'short'}} - {{event.end | date:'short'}}</span>
				</span>
			</div>
			<div class="list-item-right-block">
				<div class="list-item-right-icon">
					<i class="icon-calendar"></i>
				</div>
			</div>
		</div>
    </div>
    <div class="well detail" ng-show="event.showDetail">
		<div ng-show="event.body">{{event.body}}</div>
		<div ng-hide="event.body">[No details provided]</div>
    </div>
</script>
{% endraw %}
{% endhighlight %}
    </div>
  <div class="tab-pane fade" id="plugin-css">
{% highlight css %}
/**
 * Plugin Today Summary CSS
 *
 * We are using just a few CSS rules to customize the plugin look.
 * This is because most of the layout is using the {{site.productName}} Patterns.
 */

.throbber {
    float: right;
}

.dropdown-menu {
    color: black;
    padding: 10px;
    width: 400px;
    max-height: 500px;
}

h2 {
    padding-bottom: 5px;
    border-bottom: 1px solid #ccc;
}

.priority {
    float: right;
    margin: 3px;
}

.list-item-wrapper {
    cursor: pointer;
}

.detail {
    width: 96%;
    font-size: 0.9em;
}

{% endhighlight %}
  </div>
</div>
