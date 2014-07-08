<?php
require_once("../../../header.php");
require_once("../../header.php");
?>

<h1>Conventions</h1>

<h2>Passing Parameters</h2>

<h3 id="uri-based">URI-based</h3>

<p>Resource identifiers are typically passed in the URI itself. For example, if you wanted to 
<code>GET</code> a resource with a particular ID, you would pass the ID as a parameter after 
the resource name:</p>

<p><pre>
<?php echo $apiDomain; ?>/v1/{resource}/{RESOURCE_ID}
</pre></p>

<p>Sub-resource paths are also built using one or more URI parameters:</p>

<p><pre>
<?php echo $apiDomain; ?>/v1/{resource}/{RESOURCE_ID}/{sub-resource}
</pre></p>

<h3 id="query-string">Query String</h3>

<p>Parameters are used in the query string for two primary reasons:</p>

<ol>
	<li>Filter down the results retrieved by a <code>GET</code> request</li>
	<li>Format the data being received by any request</li>
</ol>

<p>For more information on query string parameters, see 
<a href="/rest-api/conventions/querying-options">Querying Options</a></p>

<h3 id="post-put">POST, PUT</h3>

<p>Parameters to save or update are sent in the body of <code>POST</code> or <code>PUT</code> 
requests, respectively. For more information, see 
<a href="/rest-api/conventions/general/#media-type-support">Media Type Support</a>.</p>

<?php
require_once("../../footer.php");
require_once("../../../footer.php");
?>