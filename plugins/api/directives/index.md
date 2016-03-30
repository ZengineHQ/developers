---
layout: plugin-nav-bar
group: api
subgroup: directives
---

# {{site.productName}} Directives

{{site.productName}} provides several directives for you to use in your plugin html.

# znInlineFilter

The `zn-inline-filter` directive allows you to insert a <a href="{{site.baseurl}}/rest-api/conventions/data-filters/">data filter</a> builder directly into your plugin page. This is different from the <a href="{{site.baseurl}}/plugins/api/services/#znfilterspanel">znFiltersPanel service</a>, which opens the filter builder in a modal. The filter returned can be used to query <a href="{{site.baseurl}}/rest-api/resources/#!/forms-form.id-records">records</a>, save to a <a href="{{site.baseurl}}/rest-api/resources/#!/data_views">data view</a>, and build and run <a href="{{site.baseurl}}/rest-api/resources/#!/calculation_settings">calculations</a>.

The directive has 2 required parameters: `zn-inline-filter-options` and `zn-inline-filter-model`. `zn-inline-filter-options` is an object matching the options of the <a href="{{site.baseurl}}/plugins/api/services/#znfilterspanel">znFiltersPanel</a>, excluding `filter` and `onSave` options.

`zn-inline-filter-model` should represent the `$scope` property for the filter.

{% highlight javascript %}
{% raw %}
plugin.controller('MyController', function($scope) {

	// Must Match All Conditions, Disable Linked Fields and Nested Conditions
	$scope.inlineOptions = {
		formId: 123,
		operators: ['and'],
		subfilters: false,
		groups: false,
		fieldTypeBlacklist: ['linked']
	};

	// Data Filter
	$scope.filter = {};

	$scope.logFilter = function() {
		console.log($scope.filter);
	};

});
{% endraw %}
{% endhighlight %}

{% highlight html %}
{% raw %}
<div zn-inline-filter zn-inline-filter-options="inlineOptions" zn-inline-filter-model="filter"></div>
<a href="#" ng-click="logFilter();">Log Filter</a>
{% endraw %}
{% endhighlight %}

# znDatetimepickerWrapper

The `zn-datetimepicker-wrapper` directive is a wrapper for the Angular bootstrap [datepicker](http://angular-ui.github.io/bootstrap/#/datepicker){:target="_blank"} and [timepicker](http://angular-ui.github.io/bootstrap/#/timepicker){:target="_blank"}.
Both directives work perfectly fine on their own, but there are two main downsides:

1. There isn't a way to have a single datetime that is synced with both the datepicker and the timepicker.
2. The model for the pickers needs to be a Date object. This is a problem if your model needs to be a string, which is likely if the date is being retrieved and/or saved via the {{site.productName}} API.

 This directive solves both issues. You can use the directive on an element with model that's a string type, and put a bootstrap datepicker (and optionally a timepicker) as children of the directive element. Then, changes in the directive model are reflected in the picker(s), and vice versa.

A few things to note:

- The datepicker model must have a model called `date`.
- The timepicker (if present) must have a model called `time`.
- If you want the directive model to also sync with the timepicker, the directive element needs the attribute `sync-time` with a value that evaluates to true.

In addition, the directive provides the following useful scope properties:

- `format` - the preferred user date format, which can be passed on as the display format of the datepicker
- `open` - a function for opening the datepicker popup
- `opened` - a boolean state of whether the datepicker popup is open or not
- `user` - the entire User object.
- `dateOptions` - a set of default datepicker settings used for all datepickers within the app.

{% highlight html%}
{% raw %}
<div ng-model="myDate" zn-datetimepicker-wrapper sync-time="true">
    <input type="text" placeholder="{{user.settings.dateFormat}}" ng-model="date" ng-focus="open($event)" is-open="opened" datepicker-popup="{{format}}" datepicker-options="dateOptions"/>
    <timepicker class="timepicker" ng-model="time" minute-step="1"></timepicker>
</div>
{% endraw %}
{% endhighlight %}

# uiDraggable

The `ui-draggable` directive is an AngularJS wrapper for jQuery UI draggable.

All the jQuery UI draggable options can be defined in your controller.

{% highlight js%}
plugin.controller('MyController', function($scope) {
    $scope.items = ["One", "Two", "Three"];

    $scope.draggableOptions = {
        start: function(e, ui) { ... },
        stop: function(e, ui) { ... },
        connectToSortable: "#my-sortable",
        delay: 300
    };
});
{% endhighlight %}

Apply the directive to your elements:

{% highlight html%}
<ul ng-model="items">
    <li ng-repeat="item in items" ui-draggable="draggableOptions">{{ item }}</li>
</ul>
{% endhighlight %}

# validate

The `validate` directive works in conjunction with the [Angular form directive]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/form){:target="_blank"} to add an `error` class to the element when it has any invalid child inputs. The directive only checks for invalid inputs when `formName.submitted` is true. An input is considered invalid if `inputName.$valid` is false. In the example below, after clicking <input type="button"  class="btn btn-sm btn-primary" value="Save" />, this directive will add an `error` class to the control-group div if `myFormInput` is invalid. In this case, the validity of `myFormInput` will depend on the `ng-maxlength` and `required` directives. Check out the Angular docs for more info on [input validation]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/input){:target="_blank"}.

{% highlight html%}
<form name="myForm">
    <div class="control-group" validate="myFormInput">
        <label class="form-label">My Input</label>
        <div class="controls">
            <input type="text" name="myFormInput" required ng-maxlength="50"/>
        </div>
    </div>
    <input type="button"  class="btn btn-small btn-primary" ng-click="myForm.submitted = true" value="Save" />
</form>
{% endhighlight %}
