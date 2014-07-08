<div class="row">
	<div class="large-12 columns">
		<h1>WizeHive JavaScript API Documentation</h1>

		<h5 class="subheader">Core Development Docs</h5>
		<p>
			This page is primarily oriented toward 3rd-party developers building WizeHive plugins. 
			There are also more general WizeHive development docs and best practices for core feature development. 
			While they are oriented towards WizeHive core developers, they may be of use/interest to others. 
			You can find the core development docs <a href="https://github.com/Wizehive/anglerfish" target="_blank">here</a>.
		</p>

		<hr />

		<h2>About WizeHive Plugins</h2>

		<p>
			Plugins allow any developer to make and enable additional features and enhancements to the existing WizeHive product. 
			Plugins can span a wide variety types and complexity, and can be created by both casual and advanced developers. 
			All that's required is a little knowledge of HTML and JavaScript.
		</p>
		<p>
			WizeHive is built upon the <a href="http://angularjs.org/" target="_blank">AngularJS</a> framework. 
			As such, WizeHive plugins, are built as AngularJS <a href="http://docs.angularjs.org/guide/dev_guide.mvc.understanding_controller" target="_blank">Controllers</a>. 
			You can develop your plugin locally or dynamically on the live site. 
			Once it's completed, you can register it to our Plugins collection and enable it to any of your WizeHive workspaces.
		</p>

		<hr />

		<a name="develop-local"></a>
		<h3>Running WizeHive Locally</h3>

		<ol>
			<li>
				First, head on over to the <a href="https://github.com/Wizehive/anglerfish">WizeHive repo on Github</a> and fork a copy to your Github account.
			</li>
			<li>
				Create a new project directory and clone your new repo locally: <kbd>$ git clone git://github.com/[your-account]/anglerfish.git</kbd>
			</li>
			<li>
				Install <a href="http://gruntjs.com/">Grunt</a> if you haven't already. (This is not required, but is recommended.)<br />
				Run <kbd>$ grunt package</kbd>.
			</li>
			<li>
				Create a new git branch off master for your plugin development: <br /><kbd>$ git checkout -b my-new-plugin</kbd>
			</li>
			<li>
				In your local copy of WizeHive, you'll find the plugins directory at: <kbd>/app/plugins</kbd>. This contains existing plugins that have already been added to WizeHive. To create your own, add a new directory with your plugin name. Your plugin name:
				<ul>
					<li>Should be short and clear.</li>
					<li>Should use snake-case with dashes, <kbd>e.g. my-new-plugin</kbd></li>
					<li>Will match the name used in other aspects of the plugin, such as the default controller file, default template file, and URL slug name (if it is a full page plugin).</li>
				</ul>
			</li>
			<li>
				Continue on to the <a href="#develop-general">General Plugin Development Guide</a>
			</li>
		</ol>

		<!-- <hr />

		<a name="develop-live"></a>
		<h3>Developing on WizeHive.com</h3>

		<p>
			You don't have to download the whole WizeHive codebase to develop a plugin. 
			They can be developed right on the live site. 
			This means less to set up, and you can interact directly with live data.
		</p>

		<p>
			To get developing a plugin on the live site you just need to be able to run a simple HTTP server on your development computer.
		</p>

		<p>
			Then, open up your favorite browser developer tools. In the JavaScript console you can register your plugin with: <kbd>wizehive.register('my-plugin', options);</kbd>
		</p>

		<p>
			Available <code>wizehive.register</code> Options:
		</p>

		<ul>
			<li>
				<code>path</code> - Path to your local webserver plugins (e.g. 'http://localhost:8000/plugins')
			</li>
			<li>
				<code>debug</code> - Always set this to <strong>true</strong> for live site plugin development.
			</li>
			<li>
				<code>fullPage</code> - Whether this is a full-page plugin vs an inline widget. (defaults to <strong>false</strong>)
			</li>
			<li>
				<code>redirect</code> - Whether the app should automatically redirect to your plugin after it has been loaded. Only applicable to <code>fullPage</code> plugins.
			</li>
			<li>
				<code>location</code> - Location(s) where inline plugins should be included. Multiple locations can be comma-separated: <code>'app-top,app-bottom'</code>
			</li>
			<li>
				<code>success</code> - An optional callback that will be run once all the plugin files have been loaded.
			</li>
		</ul>

		<p>
			Complete examples:
		</p>

		<div class="code">
<pre>// Full-page Plugin
wizehive.register('my-fullpage-plugin', {
debug: true,
path: 'http://localhost:8000/plugins',
fullPage: true,
redirect: true
});

// Inline Plugin
wizehive.register('my-inline-plugin', {
debug: true,
path: 'http://localhost:8000/plugins',
location: 'app-bottom'
});</pre>
		</div>

		<p>
			Continue on to the <a href="#develop-general">General Plugin Development Guide</a>
		</p> -->

		<hr />

		<a name="develop-general"></a>
		<h3>Plugin Development Guide</h3>

		<h5>Creating Your New Plugin</h5>
		<p>
			Every plugin resides in its own directory underneath the <kbd>/app/plugins</kbd> directory and usually has at a minimum one JavaScript file and one HTML template file.
		</p>

		<p>
			Note: There is a helpful Grunt task you can use to create new plugins. It will create your plugin directory, all the standard sub-directories, and stub out files for JS, CSS, HTML, and tests. If you have Grunt installed, it will make your life easier.<br />
			Example: <kbd>$ grunt makeplugin:my-plugin[:true|false]</kbd> <br />
			Where &quot;my-plugin&quot; is your plugin name/slug. The third argument is optional and if <strong>true</strong> will set the plugin up to use Sass.
		</p>

		<p>
			 Example plugin directory:
		</p>

		<div class="code">
<pre>/app
/plugins
    /my-new-plugin
        /css
            my-new-plugin.css
        /js
            my-new-plugin.js
        /sass
            my-new-plugin.scss (optional)
        /templates
            my-new-plugin.html
        /tests
            my-new-plugin-controller-spec.js
        config.json</pre>
		</div>

		<p>
			Generally, there are two different types of plugins:
		</p>

		<h5>Full-page Plugins</h5>
		<p>
			Full-page plugins render as an entire &quot;page&quot; within the app, and are accessed by visiting a unqiue URL location <kbd>e.g. wizehive.com/my-new-plugin</kbd>.
		</p>

		<h5>Widget Plugins</h5>
		<p>
			Widget plugins may be embedded in various locations throughout the app by defining a &quot;location.&quot; For example, there is location at the bottom of the whole app, such that it will appear at the bottom of every page, called &quot;app-bottom.&quot; By defining &quot;app-bottom&quot; as your plugin's location, it will be injected there. You may specify multiple locations if needed. For example, you might want your plugin to run on the Data and Tasks pages, but nowhere else. In that case, you'd set your plugin's location to be: &quot;data-bottom,tasks-bottom&quot;. In many cases, a widget plugin might want to be absolutely positioned and not simply be placed in a particular order in the page DOM. That's okay, but you will still want to define your locations so the plugin knows when to be activated.
		</p>

		<h5>Registering Your Plugin</h5>

		<p>
			For your plugin to run, it needs to be register with the app. There are two steps you need to take to make that happen:

			<ol>
				<li>
					<p>
						Run the <kbd>wizehive.register('my-plugin')</kbd> function. 
						Generally, it makes sense to do this in the same place that your define your main controller. 
						So, if your plugin is called <strong>my-plugin</strong> then you should have a matching /js/my-plugin.js file. 
						In its most basic form, this file should define the main controller and register the plugin:
					</p>
					<p>
						<kbd>wizehive.controller('MyPluginCntl', [function() { ... }]).register('my-plugin', { ... });</kbd>
					</p>
					<p>
						The register function should always come second because it will immediately use your main controller, so it needs to be present.
					</p>
					<p>
						The second argument for <strong>register</strong> is the plugin options. Available options are:
					</p>

					<ul>
						<li><strong>fullPage</strong> (bool) - If true, plugin will be a &quot;Full Page&quot; plugin and respond to URL changes.</li>
						<li><strong>topNav</strong> (bool) - (fullPage only) If true, an icon will be added to the Top Nav to access the plugin.</li>
						<li><strong>title</strong> (string) - Used for a tooltip for the Top Nav icon.</li>
						<li><strong>rootOnly</strong> (bool) - (fullPage only) If true, the plugin will only be available when a Workspace has not been chosen.</li>
						<li><strong>requireWorkspace</strong> (bool) - (fullPage only)  If true, the plugin will only be available within Workspaces.</li>
						<li><strong>icon</strong> (string) - (fullPage only) An icon to use for the Top Nav menu.</li>
						<li><strong>nested</strong> (number) - (fullPage only) </li>
						<li><strong>location</strong> (string) - (widget only) </li>
					</ul>
				</li>
				<li>

				</li>
			</ol>
		</p>
	</div>
</div>
