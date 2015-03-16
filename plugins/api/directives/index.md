---
layout: plugin-nav-bar
group: api
subgroup: directives
---

# Zengine Directives

Zengine provides several directives for you to use in your plugin html.

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
