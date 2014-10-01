---
layout: plugin-nav-bar
group: development
---

# Angular Services

This is a whitelist of the Angular services that you can inject as dependencies into your components (controller, services, etc.).

<div>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<a target="_blank" target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$anchorScroll">$anchorScroll</a>
				</td>
				<td>
					<p>When called, it checks current value of <code>$location.hash</code> and scrolls to the related element, according to rules specified in
					<a target="_blank" target="_blank" href="http://dev.w3.org/html5/spec/Overview.html#the-indicated-part-of-the-document">Html5 spec</a>.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ngCookies/service/$cookies">$cookies</a>
				</td>
				<td>
					<p>Provides read/write access to browser's cookies.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ngCookies/service/$cookieStore">$cookieStore</a>
				</td>
				<td>
					<p>Provides a key-value (string-object) storage, that is backed by session cookies. Objects put or retrieved from this storage are automatically serialized or deserialized by angular's toJson/fromJson.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$filter">$filter</a>
				</td>
				<td>
					<p>Filters are used for formatting data displayed to the user.</p>
				</td>
			</tr>
	  
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$http">$http</a>
				</td>
				<td>
					<p>The <code>$http</code> service is a core Angular service that facilitates communication with the remote HTTP servers via the browser's <a target="_blank" href="https://developer.mozilla.org/en/xmlhttprequest">XMLHttpRequest</a> object or via <a target="_blank" href="http://en.wikipedia.org/wiki/JSONP">JSONP</a>.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$interpolate">$interpolate</a>
				</td>
				<td>
					<p>Compiles a string with markup into an interpolation function. This service is used by the HTML <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$compile">$compile</a> service for data binding. See <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/provider/$interpolateProvider">$interpolateProvider</a> for configuring the interpolation markup.</p>
				</td>
			</tr>

			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$interval">$interval</a>
				</td>
				<td>
					<p>Angular's wrapper for <code>window.setInterval</code>. The <code>fn</code> function is executed every <code>delay</code>milliseconds.</p>
				</td>
			</tr>

			<tr>
				<td><a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$locale">$locale</a></td>
				<td>
					<p>$locale service provides localization rules for various Angular components.
					</p>
				</td>
			</tr>
		  
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$location">$location</a>
				</td>
				<td>
					<p>The $location service parses the URL in the browser address bar (based on the
					<a target="_blank" href="https://developer.mozilla.org/en/window.location">window.location</a>) and makes the URL
					available to your application. Changes to the URL in the address bar are reflected into
					$location service and changes to $location are reflected into the browser address bar.</p>
				</td>
			</tr>
			<tr>
				<td><a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$log">$log</a></td>
				<td><p>Simple service for logging. Default implementation safely writes the message into the browser's console (if present).</p>
				</td>
			</tr>

			<tr>
				<td><a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$parse">$parse</a></td>
				<td><p>Converts Angular <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/guide/expression">expression</a> into a function.</p></td>
			</tr>

			<tr>
				<td><a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$q">$q</a></td>
				<td><p>A promise/deferred implementation inspired by <a target="_blank" href="https://github.com/kriskowal/q">Kris Kowal's Q</a>.</p>
				</td>
			</tr>
			<tr>
				<td><a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ngRoute/service/$routeParams">$routeParams</a></td>
				<td>
					<p>The <code>$routeParams</code> service allows you to retrieve the current set of route parameters.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$timeout">$timeout</a>
				</td>
				<td>
					<p>Angular's wrapper for <code>window.setTimeout</code>. The <code>fn</code> function is wrapped into a try/catch block and delegates any exceptions to <a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$exceptionHandler">$exceptionHandler</a> service.</p>
				</td>
			 </tr>
		</tbody>
	</table>
</div>

## Partially Supported Services

This is a list of partially supported angular services that can be injected into plugins. Only the methods listed here are allowed. All other methods and properties are not implemented.

<div>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$templateCache">$templateCache</a>
				</td>
				<td>
					<p>The way to access angular templates programmatically. The <code>get</code> method is supported to read your plugin templates out of the $templateCache.</p>
				</td>
			</tr>
			<tr>
				<td>
					<a target="_blank" href="{{site.angularDomain}}/{{site.angularVersion}}/docs/api/ng/service/$window">$window</a>
				</td>
				<td>
					<p>The only way to access the browser <code>window</code> object. Only the <code>open</code> method is supported.</p>
				</td>
			</tr>
		</tbody>
	</table>
</div>
