---
layout: plugin-nav-bar
group: development
---

# Using AngularJS in your plugin code

If you already know AngularJS, developing {{site.productName}} plugins is 99% like developing any other Angular app, and this page will highlight the differences. If you don't know Angular, this page will bring you up to speed. In your JavaScript, you can register components (controllers, services, factories, directives, and filters) the same way you would in Angular. The main difference is you need to use a special `plugin`object (instead of an Angular module) to call any component registration functions. You also need to prepend all component names with your registered namespace.

{% highlight js %}

plugin
    .controller('namespacedCntl', ['$scope', 'depService', function($scope, depService) {
        ...
    }])
    .service('namespacedService', ['depService', function(depService) {
        ...
    }])
    .factory('namespacedFactory', ['depService', function(depService) {
        ...
    }])
    .directive('namespacedDirective', ['depService', function(depService) {
        ...
    }])
    .filter('namespacedFilter', ['depService', function(depService) {
        ...
    }])
    .constant('namespacedConstant', value);

{% endhighlight %}

# Scopes and data binding

One of the most important and productive features of AngularJS is [two-way data binding]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/databinding).  This gives you a way to connect data in your controller JavaScript code with your view HTML code.  Every controller is passed a special $scope object.  Any properties that are set on this object are automatically available in HTML templates by using the property name within double curly brace notation, as in the example below.  $scope properties are also used by core AngularJS directives like [ng-model]({{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/directive/ngModel).

It is highly suggested to read the AngularJS documentation on [scopes]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/scope) and [templates]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/templates) to get started quickly with your plugin.

Controller code (JavaScript):
{% highlight js %}

plugin
    .controller('namespacedCntl', ['$scope', 'depService', function($scope, depService) {
        var privateMessage = 'This is not available in the template';
        $scope.greeting = 'Hello'; // This is available in the template because it's set on $scope
    }]);

{% endhighlight %}

Template code (HTML):
{% highlight html %}
{% raw %}
<script type="text/ng-template" id="paul-time-main">
    <h1>{{greeting}} world!</h1>
</script>

{% endraw %}
{% endhighlight %}

# Dependency Injection

In order for components to get ahold of their dependencies, Angular wires everything using a [dependency injection]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/di) subsystem. All of the components above (except for constants) were registered using [inline array annotation]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/di#inline-array-annotation); you pass an array, whose elements consist of a list of strings (the names of the dependencies), followed by the factory function taking a set of dependency arguments that match the list of string names.

The list of strings tell Angular which dependencies are required.  Angular then passes those dependencies into the function you define - then you can use them inside of your code however you like.

Dependency injection is how you can wire your own components together, as well as how you can include external dependencies, such as services written by [AngularJS](angular-services.html), [{{site.productName}}]({{site.baseurl}}/plugins/api/services), and few [third parties]({{site.baseurl}}/plugins/third-party). 

{% highlight js %}

plugin.controller('namespacedCntl', ['$scope', '$someAngularService', 'znSomeZengineService', 'someThirdParty', 
    function ($scope, $someAngularService, znSomeZengineService, someThirdPartyService) {
    
        ...

    }

}

{% endhighlight %}

# Services

If you want to write reusable business logic independent of views, you can create what's known in Angular as a [service]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/services). Any component that uses the service will get a reference to the same singleton instance. There are two recipes for creating services: **Service** and **Factory**. The only difference between them is that **Service** recipe works better for objects of custom type, while **Factory** can produce JavaScript primitives and functions.

The first way to create a service is through the **Service** recipe. When you’re using a **Service**, it’s instantiated with the `new` keyword, so you can add properties to `this` and the service will return `this`. When you inject the service as a dependency into a component, those properties on `this` will now be available on that component through your service.

{% highlight js %}

plugin.service('namespacedService', ['depService', function (depService) {
    
    var service = this;

    this.foo = function() {
       ...
    };

    return service;

};

{% endhighlight %}

The second way to create a service is through the **Factory** recipe. Unlike the **Service** recipe, it's not instantiated with the `new` keyword. When you’re using **Factory**, you create an object, add properties to it, then return that same object. 

{% highlight js %}

plugin.factory('namespacedFactory', ['depService', function (depService) {

    var factory = {};

    factory.foo = function() {
       ...
    };

    return factory;
};

{% endhighlight %}

In additional to creating your own services, you can also use the [services provided by {{site.productName}}]({{site.baseurl}}/plugins/api/services).

# Directives

Sometimes you want to attach a specified behavior to a DOM element. To do this, you can create [directives]({{site.angularDomain}}/{{site.angularVersion}}/docs/guide/directive). Just register the directive in your plugin.js code the same way you would in Angular, except with the plugin object. 

{% highlight js %}
plugin.directive('myCustomer', ['depService', function(depService) {
        return {
            restrict: 'E', // only match directive to element name
            scope: { // create an isolate scope
                firstName: '=first', // data-bind to the the 'first' attribute
                lastName: '=last'
            },{% raw %}
            template: 'Name: {{lastName}}, {{firstName}}', // can also use templateUrl {% endraw %}
            link: function(scope, element, attrs) {

                // 'scope' is an Angular scope object.
                console.log(scope.firstName);

                // 'element' is the jqLite-wrapped element that this directive matches.
                // {{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/function/angular.element
                console.log(element.contents());

                // 'attrs' is a hash object with key-value pairs
                // of normalized attribute names and 
                // their corresponding attribute values.
                console.log(attrs.first);

            }
        };
    }]);
{% endhighlight %}

Use the directive in your template by using the dash-delimited version of the camelCase directive name registered in your JavaScript.

{% highlight html %}
    <my-customer first="naomi" last="watson"></my-customer>
{% endhighlight %}

The result will look like this:
<pre>Name: Watson, Naomi</pre>

In additional to creating your own directives, you can also use the [directives provided by Zengine]({{site.baseurl}}/plugins/api/directives). Check out the [chat tutorial]({{site.baseurl}}/plugins/tutorials/building-a-chat-plugin.html#using-a-directive-to-display-each-message) for some more useful directive examples. 
