---
layout: plugin-nav-bar
group: third-party-services
subgroup: ui-sortable
---

# UI.Sortable directive [![Build Status](https://travis-ci.org/angular-ui/ui-sortable.png)](https://travis-ci.org/angular-ui/ui-sortable)

This [directive](https://github.com/Wizehive/ui-sortable) allows you to sort an array with drag & drop.

## Usage

All the jQueryUI Sortable options can be defined in your controller.

{% highlight js%}
plugin.controller('MyController', function($scope) {
    $scope.items = ["One", "Two", "Three"];

    $scope.sortableOptions = {
        update: function(e, ui) { ... },
        axis: 'x'
    };
});
{% endhighlight %}

Apply the directive to your form elements:
{% highlight html%}
<ul ui-sortable="sortableOptions" ng-model="items">
    <li ng-repeat="item in items">{{ item }}</li>
</ul>
{% endhighlight %}