---
layout: plugin-nav-bar
group: intro
subgroup: getting-started
---
# Creating Plugins

Plugins can be created and managed through the Zengine Developer screen, found under My Account. To start, there are two options -- name and namespace. Name will be used to publicly identify your plugin in the app. Namespace is a unique identifier to be used by your plugin code to distinguish it from other plugins. Namespace is not publicly displayed, but it will be used in several places in your plugin code. Name can be changed, but namespace cannot be changed after your plugin is created.

After you have provided a plugin name and namespace, you will be taken into the plugin developer console to edit your newly created plugin. Plugins consist of 3 pieces of data -- css, html, and javascript. The initial code is a sample Hello World plugin, populated with your specific plugin options. Notice that your namespace appears in the plugin HTML  template ID and in the javascript controller and registration parameters.

Plugins are written in AngularJS. If you open the initial plugin HTML, you will see that it contains an AngularJS template with a binding for `{% raw %}{{text}}{% endraw %}`.

{% highlight html %}
{% raw %}
<script type="text/ng-template" id="my-plugin-main">
	<div class="title">
		<h1>{{text}}</h1>
	</div>
</script>
{% endraw %}
{% endhighlight%}

If you click to the plugin javascript, you can see where it dynamically sets the `text` property to `Hello World!`

{% highlight javascript %}
{% raw %}
plugin.controller('myPluginCntl', ['$scope', function ($scope) {

	$scope.text = 'Hello World!';

}])
{% endraw %}
{% endhighlight %}

### Running Plugins

You can test your plugin as you develop by clicking Run in the header. When you run your plugin, you will be taken out of the editor and back to the app. Navigate in to one of your workspaces and you will notice a new icon for the plugin in the header. Hover over the icon and it should display the plugin name. Click on the icon and it will open the plugin to display the Hello World text.

At this point, your plugin is not published. Only you can access and run your plugin.

To get back to the plugin editor, hover over the Dev Mode menu in the right of the header and click View Editor.

## Registration Options

There are various plugin options you can change through the registration options. For example, the icon and hover text from above comes from these settings.

{% highlight javascript %}
{% raw %}
/**
 * Plugin Registration
 */
.register('myPlugin', {
	route: '/myplugin',
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
			<td>You can have multiple controllers in your plugin javascript. This param represents the main controller name. Note that all controller names are prefixed with your namepsace, like <code>myPluginCntl</code>.</td>
		</tr>
		<tr>
			<td>template</td>
			<td>Similar to controller, your plugin HTML can have multiple templates. This is the template that corresponds with the main controller. This value represents a template ID in the plugin HTML. The template ID must be prefixed with a dash-delimited version of your namespace, like <code>my-plugin-main</code>. This is in keeping with the AngularJS HTML attribute style.</td>
		</tr>
		<tr>
			<td>title</td>
			<td>The hover text for your plugin icon.</td>
		</tr>
		<tr>
			<td>pageTitle</td>
			<td>Heading text to appear below the app header and above your plugin template.</td>
		</tr>
		<tr>
			<td>fullPage</td>
			<td>If true, your plugin will appear on its own page. It can have an icon in the app header and its own URI defined by the <code>route</code> param. If false, the plugin will not appear on its own and should be written to appear in another app location using XYZ.</td>
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
			<td>Icon to represent your plugin in the app header. Must be an icon name seen here: <a href="{{ site.clientDomain }}/patterns/icons">{{ site.clientDomain }}/patterns/icons</a></td>
		</tr>
		<tr>
			<td>location</td>
			<td>Location to load plugin if not a full page plugin. See <a href="#locations">plugin locations</a>.</td>
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
			<td>zn-plugin-data-subheader</td>
			<td>Above the data grid.</td>
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

# Publishing Plugins

You can get to the publishing screen by clicking the Publishing Settings button in the plugin editor. From there you can confirm settings such as the name and description. You can also provide a Firebase URL and optionally a Firebase secret. The Firebase secret will allow a plugin to take advantage of Firebase authentication. 

Plugins are only available to the developer prior to publishing. Once a plugin is published, it can be shared with specific workspaces for all users to use in that workspace. After publishing, a list of workspaces where the plugin developer is a member will appear. The plugin developer can choose to add or remove access to the plugin by workspace.

