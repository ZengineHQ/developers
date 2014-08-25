---
layout: plugin-nav-bar
group: intro
subgroup: getting-started
---
# Creating Plugins

Plugins can be created and managed through the Zengine Developer screen, found in the [Developer section of My Account]({{site.clientDomain}}/account/developer). To start, there are two options -- name and namespace. Name will be used to publicly identify your plugin in the app. Namespace is a unique identifier to be used by your plugin code to distinguish it from other plugins. Namespace is not publicly displayed, but it will be used in several places in your plugin code. Name can be changed, but namespace cannot be changed after your plugin is created.

After you have provided a plugin name and namespace, you will be taken into the plugin developer console to edit your newly created plugin. Plugins consist of 3 pieces of data -- CSS, HTML, and JavaScript. The initial code is a sample Hello World plugin, populated with your specific plugin options. Notice that your namespace appears in the plugin HTML template ID and in the JavaScript controller and registration parameters.

Plugins are written in [AngularJS](https://angularjs.org/). If you open the initial plugin HTML, you will see that it contains an AngularJS template with a binding for `{% raw %}{{text}}{% endraw %}`.

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="my-plugin-main">
    <div class="title">
        <h1>{{text}}</h1>
    </div>
</script>
{% endraw %}
{% endhighlight%}

If you click to the plugin JavaScript, you can see where it dynamically sets the `text` property to `Hello World!`

{% highlight javascript %}
{% raw %}
plugin.controller('myPluginCntl', ['$scope', function ($scope) {

    $scope.text = 'Hello World!';

}])
{% endraw %}
{% endhighlight %}


### Registration Options

There are various plugin options you can change through the registration options. The following options are the only ones you need to get a full-page plugin up and running. 

{% highlight javascript %}
{% raw %}
/**
 * Plugin Registration
 */
.register('myPlugin', {
    route: '/myplugin',
    controller: 'myPluginCntl',
    template: 'my-plugin-main'
});

{% endraw %}
{% endhighlight %}

The `route` parameter represents the URI path to run your plugin. If your route is `/myplugin`, then the full URI to your plugin might be `{{ site.clientDomain }}/workspaces/123/plugin/myplugin</`

You can have multiple controllers in your plugin JavaScript. The `controller` parameter represents the main controller name. Note that all controller names are prefixed with your namepsace, like `myPluginCntl`.

Similar to controller, your plugin HTML can have multiple templates. The `template` parameter corresponds to the template associated with the main controller. This value represents a template ID in the plugin HTML. The template ID must be prefixed with a dash-delimited version of your namespace, like `my-plugin-main`. This is in keeping with the AngularJS HTML attribute style.

# Running Plugins

You can test your plugin as you develop by clicking the **Run** button in the top right corner. When you run your plugin, you will be taken out of the editor and back to the app. Navigate to one of your workspaces and you will notice a new icon for the plugin in the header. Hover over the icon and it should display the plugin name. Click on the icon and it will open the plugin to display the Hello World text.

At this point, your plugin is not published. Only you can access and run your plugin.

To get back to the plugin editor, hover over the Dev Mode menu in the right of the header and click View Editor.

# Publishing Plugins

You can get to the publishing screen by clicking the Publishing Settings button in the plugin editor. From there you can confirm settings such as the name and description. You can also provide a Firebase URL and optionally a Firebase secret. The Firebase secret will allow a plugin to take advantage of Firebase authentication. 

Plugins are only available to the developer prior to publishing. Once a plugin is published, it can be shared with specific workspaces for all users to use in that workspace. After publishing, a list of workspaces where the plugin developer is a member will appear. The plugin developer can choose to add or remove access to the plugin by workspace.

