---
layout: plugin-nav-bar
group: registration
---

## Plugin Registration Options

There are various plugin options you can change through the registration options. Some of the options only make sense for a particular plugin type, like full page or inline. 

The following is a full set of possible options for a full page plugins.

{% highlight javascript %}
{% raw %}
/**
 * Plugin Registration
 */
.register('myPlugin', {
    route: '/myplugin',
    routes: [
        '/:page'
    ],
    controller: 'myPluginCntl',
    template: 'my-plugin-main',
    title: 'My Plugin',
    pageTitle: false,
    type: 'fullPage',
    topNav: true,
    order: 300,
    icon: 'icon-emo-beer'
});

{% endraw %}
{% endhighlight %}

This is a full set of options for an inline plugin: 

{% highlight javascript %}
{% raw %}
/**
 * Plugin Registration
 */
.register('myPlugin', {
    controller: 'myPluginCntl',
    template: 'my-plugin-main',
    location: 'zn-plugin-data-subheader'
});

{% endraw %}
{% endhighlight %}

The following table presents the registration options and their purpose.

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Param</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>route</td>
            <td>The URI path to run your plugin. If your route is <code>/myplugin</code>, then the full URI to your plugin might be <code>{{ site.clientDomain }}/workspaces/123/plugin/myplugin</code></td>
        </tr>
        <tr>
            <td>routes</td>
            <td>Specify additional routes to sub pages of your plugin. Will appear under your main <code>route</code> param. Value should be defined as a JSON array. Ex: <code>routes: ['/:id']</code> translates to <code>{{ site.clientDomain }}/workspaces/123/plugin/myplugin/456</code>, where 456 will be available as a <code>$routeParam</code> named <code>id</code>.</td>
        </tr>
        <tr>
            <td>controller</td>
            <td>You can have multiple controllers in your plugin JavaScript. This param represents the main controller name. Note that all controller names are prefixed with your namepsace, like <code>myPluginCntl</code>.</td>
        </tr>
        <tr>
            <td>template</td>
            <td>Similar to controller, your plugin HTML can have multiple templates. The <code>template</code> parameter corresponds to the template associated with the main controller. This value represents a template ID in the plugin HTML. The template ID must be prefixed with a dash-delimited version of your namespace, like <code>my-plugin-main</code>. This is in keeping with the AngularJS HTML attribute style.</td>
        </tr>
        <tr>
            <td>title</td>
            <td>The tooltip text to appear on hover of your plugin icon.</td>
        </tr>
        <tr>
            <td>pageTitle</td>
            <td>Heading text to appear below the app header and above your plugin template. If not provided, will use value of <code>title</code>. If false, won't prepend the header at all.</td>
        </tr>
        <tr>
            <td>type</td>
            <td>The type of your plugin, can be one of: fullPage, inline or recordOverlay. Checkout <a href="{{site.baseurl}}/plugins/getting-started/plugin-types.html">plugin types</a> for details of each plugin type option.</td>
        </tr>
        <tr>
            <td>topNav</td>
            <td>Whether to display your plugin icon in the app header.</td>
        </tr>
        <tr>
            <td>order</td>
            <td>The order to display the plugin icon in <code>topNav</code>, in relation to the other icons. A higher number moves the icon towards the left and a lower number towards the right.</td>
        </tr>
        <tr>
            <td>icon</td>
            <td>Icon to represent your plugin in the app header and the marketplace (if the plugin is public). Must be an icon name seen here: <a href="{{ site.clientDomain }}/patterns/icons">{{ site.clientDomain }}/patterns/icons</a></td>
        </tr>
        <tr>
            <td>location</td>
            <td>Location to load plugin if it's type is "inline". See <a href="#locations">plugin locations</a>.</td>
        </tr>
    </tbody>
</table>

### Plugin Locations
<a name="locations"></a>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Location</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
		    <tr>
            <td>zn-top-nav</td>
            <td>In the nav bar at the top of the app, next to the Notifications bell.</td>
        </tr>
        <tr>
            <td>zn-plugin-data-subheader</td>
            <td>Above the data grid. Only available when there are records to display in the grid./td>
        </tr>
        <tr>
            <td>zn-plugin-panel-header</td>
            <td>Above the data panel.</td>
        </tr>
        <tr>
            <td>zn-plugin-panel-footer</td>
            <td>Below the data panel.</td>
        </tr>
        <tr>
            <td>zn-plugin-form-top</td>
            <td>Above the form in the data panel.</td>
        </tr>
        <tr>
            <td>zn-plugin-form-bottom</td>
            <td>Below the form in the data panel.</td>
        </tr>
    </tbody>
</table>
