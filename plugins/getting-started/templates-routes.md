---
layout: plugin-nav-bar
group: intro
subgroup: templates-routes
---
# Templates and Routes

With more complex plugins, you may find a need for multiple pages. The plugin editor provides just one HTML setting, but it can be used to define multiple AngularJS script templates. The initial plugin HTML will start with one template, similar to the following.

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="my-plugin-main">
    <div class="title">
        <h1>{{text}}</h1>
    </div>
</script>
{% endraw %}
{% endhighlight%}

You can add additional templates, as seen below. Note that you need to use a dash-delimited version of your namespace as a prefix to your template IDs. Ex: `myPlugin` becomes `my-plugin`. This is used for consistency with the AngularJS HTML attribute format.

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="my-plugin-main">
    <div class="title">
        <h1>{{text}}</h1>
    </div>
</script>
<script type="text/ng-template" id="my-plugin-index">
    <div class="title">
        <h1>index</h1>
    </div>
</script>
<script type="text/ng-template" id="my-plugin-view">
    <div class="title">
        <h1>view</h1>
    </div>
</script>
{% endraw %}
{% endhighlight%}

The main plugin template will need a way to toggle the other templates. This can be achieved using an `ng-include`. Here we will use an expression to append a dynamic value called `currentPage` that will be set later in the plugin JavaScript.

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="my-plugin-main">
    <div ng-include="'my-plugin-' + currentPage"></div>
</script>
{% endraw %}
{% endhighlight%}

This example uses `ng-include` to display the template inline, but you can also display templates as a modal using the modal factory.

## Routes

We will add support for additional routes to specify the page in the plugin JavaScript. The `$routeParams` dependency is needed to read the params. We also need to add the additional route param to the plugin registration under `routes`.

{% highlight javascript %}
{% raw %}
plugin.controller('myPluginCntl', ['$scope', '$routeParams', function ($scope, $routeParams) {
    
    // Default page
    $scope.currentPage = 'index';
    
    // Read page from $routeParams
    if ($routeParams.page) {
        $scope.currentPage = $routeParams.page;
    }

}])
/**
 * Plugin Registration
 */
.register('myPlugin', {
    route: '/myplugin',
    // Define additional routes
    routes: [
        "/:page"
    ],
    controller: 'myPluginCntl',
    template: 'my-plugin-main',
    title: 'My Plugin',
    pageTitle: false,
    fullPage: true,
    topNav: true,
    order: 300,
    icon: 'icon-emo-beer'
});

{% endraw %}
{% endhighlight %}

For this example, the main plugin route would be similar to `{{ site.clientDomain }}/workspaces/123/myplugin/`. The added route for the templates with a dynamic `page` param would be `{{ site.clientDomain }}/workspaces/123/myplugin/index` or `{{ site.clientDomain }}/workspaces/123/myplugin/view`