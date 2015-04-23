---
layout: plugin-nav-bar
group: api
subgroup: directives
---

# {{site.productName}} Directives

{{site.productName}} provides several directives for you to use in your plugin html.

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
<div ng-model="myDate" zn-datetimepicker-wrapper sync-time="true">
    <input type="text" placeholder="{{user.settings.dateFormat}}" ng-model="date" ng-focus="open($event)" is-open="opened" datepicker-popup="{{format}}" datepicker-options="dateOptions"/>
    <timepicker class="timepicker" ng-model="time" minute-step="1"></timepicker>
</div>
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
