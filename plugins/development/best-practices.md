---
layout: plugin-nav-bar
group: development
---
# Best Practices

The following is a list of best practices for developing {{site.productName}} plugins. This list includes things specific to {{site.productName}}, as well as general AngularJS best practices compiled from various sources, including the AngularJS [Best Practices wiki page](https://github.com/angular/angular.js/wiki/Best-Practices){:target="_blank"}, the book [Mastering Web Application Development with AngularJs](http://www.amazon.com/Mastering-Web-Application-Development-AngularJS/dp/1782161821){:target="_blank"} by Pawel Kozlowski and Pete Bacon Darwin, and this Stack Overflow [post](http://stackoverflow.com/questions/14994391/how-do-i-think-in-angularjs-if-i-have-a-jquery-background){:target="_blank"} for AngularJS beginners with jQuery background.

* **Namespace your code**  
  Your component names and HTML template IDs should all be prefixed with the namespace you chose when you created your plugin. Your CSS will be automatically scoped by your namespace.
* **Follow {{site.productName}} Design Patterns for UI Guidelines**
  When designing your plugin, follow our [{{site.productName}} Design Patterns]({{site.clientDomain}}/patterns){:target="_blank"}, so that your plugin UI fits in with the rest of the app. This page contains information on:
  - global CSS settings (typography, colors, etc.)
  - {{site.productName}}-specific components (e.g. follow buttons, data screen, tasks, config lists)
  - {{site.productName}} extensions of Bootstrap components (e.g. tables, buttons, forms)
  - general design principles.
* **Use the `znData` service for accessing the {{site.productName}} API**
  The [znData service]({{site.baseurl}}/plugins/api/services/#zndata) is a useful AngularJS binding for accessing resources from the [{{site.productName}} API]({{site.baseurl}}/rest-api).  Using it will allow you to easily and immediately start interacting with the {{site.productName}} API without needing to write any boilerplate code.
* **Store any user preferences in Firebase**
  For any extraneous data (such as user preferences) that doesn't fit in with any {{site.productName}} API resources, you can use [AngularFire]({{site.baseurl}}/plugins/third-party/developing-plugins-with-firebase.html), the AngularJS binding for Firebase.
* **Only use `znPluginEvents.$broadcast()`, `znPluginEvents.$emit()` and `znPluginEvents.$on()` for documented events**  
  The `znPluginEvents` service is generally meant for events that are relevant globally across the entire app, and are documented [here]({{site.baseurl}}/plugins/api/services/#znpluginevents). If you want events for communication within your plugin code, you are probably better off trying the following tips:
  * `$scope.$watch()` should replace the need for events.
  * Injecting services and calling methods directly is also useful for direct communication (i.e. between controllers).
  * Directives are able to directly communicate with each other through directive-controllers.
* **Extend directives by using Directive Controllers**  
  You can place methods and properties into a directive-controller, and access that same controller from other directives. You can even override methods and properties through this relationship
* **Add teardown code to controllers and directives**  
  Controller and directives emit an event right before they are destroyed. This is where you are given the opportunity to tear down your plugins and listeners and pretty much perform garbage collection.
  * Subscribe to the `$scope.$on('$destroy', ...)` event
* **Avoid using jQuery / direct DOM manipulation**
  jQuery is available for use in your plugin code. However, before doing any DOM manipulation, here are a few things to keep in mind:
  - AngularJS heavily promotes a **declarative** style of programming for templates. Getting a reference to a DOM element and manipulating element's properties indicates an imperative approach to UI; this goes against the AngularJS way of building UIs.
  - Ask yourself if you really need DOM manipulation. AngularJS has many tools that reduce the need for any low-level, step-by-step instructions on how to change individual properties of DOM elements.  For example, `ngBind` and {% raw %}`{{}}`{% endraw %} allow two-way data binding. With `ngClass`, we can dynamically update the class, and `ngShow` and `ngHide` programmatically show or hide an element.
  - If you do need it, put it inside a directive. One case for this might be if you have a third party (e.g. jQuery) widget that is complex enough and it is not worth writing a pure AngularJS version of it in the short term. You can accelerate your development by wrapping such as widget in an AngularJS directive. 