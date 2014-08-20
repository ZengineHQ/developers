---
layout: plugin-nav-bar
group: api
---

# Services

To include external code, you must use what's known in Angular as a [service](https://code.angularjs.org/{{site.angularVersion}}/docs/guide/services). Plugins can inject many useful services, including some written by [AngularJS](angular), [{{site.productName}}](zengine), and few [third parties](third-party). 

To use a service, you add it as a dependency for the component (controller, service, filter or directive) that depends on the service. Angular's [dependency injection](https://code.angularjs.org/{{site.angularVersion}}/docs/guide/di) subsystem takes care of the rest.


{% highlight js %}

plugin.controller('nameSpacedControllerNameCntl', ['$scope', '$someAngularService', 'znSomeZengineService', 'someThirdParty', 
	function ($scope, $someAngularService, znSomeZengineService, someThirdPartyService) {
	
		...

	}

}

{% endhighlight %}

You can also create your own services. Doing so is almost exactly the same as creating a controller.

{% highlight js %}

plugin.service('nameSpacedService', ['$scope', '$someAngularService', 'znSomeZengineService', 'someThirdParty', 
	function ($scope, $someAngularService, znSomeZengineService, someThirdPartyService) {
	
		...

	}

}

{% endhighlight %}

# Directives
[directive](https://code.angularjs.org/{{site.angularVersion}}/docs/guide/directive)