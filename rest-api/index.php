<?php
require_once("../header.php");
require_once("header.php");
?>

<h1>Introduction</h1>

<p>WizeHive provides a <a href="https://en.wikipedia.org/wiki/Representational_state_transfer">RESTful</a> 
API which maps virtually every <a href="/rest-api/resources">resource</a> in the platform to a list of 
URIs. These URIs can be retrieved and manipulated with 
<a href="/rest-api/conventions/http-verbs">HTTP verbs</a>.</p>

<h2 id="audience">Audience</h2>

<p>The API is made available for a wide variety of use cases, <strong>including</strong> commercial 
applications. Some possibilities include:</p>

<ul>
	<li><a href="/js-api">JavaScript plugins</a></li>
	<li>Mobile clients</li>
	<li>Integration with third party web & mobile applications</li>
	<li>Bulk fetching & processing of private data</li>
	<li>... and more</li>
</ul>

<p>The documentation herein is provided as a reference guide for third party developers who wish to learn 
more about the API's conventions and resources.</p>

<h2 id="restrictions">Restrictions</h2>

<p>The WizeHive API is in beta, which means it is still rapidly maturing. While we do not 
expect the conventions or resources to change in a way that prove destructive to third party integrations, 
we cannot makes any promises. A Release Candidate is expected by late 2013, at which point Version 1 of the
API will be "locked".</p>

<p>Because the API is still in beta, we have no rate limiting or abuse negation techniques in place.
We expect you to be kind - only make requests which are necessary, use caching techniques whenever 
possible, and use the API in a way that does not have a negative impact on WizeHive's business or 
infrastructure. We, of course, reserve the right to suspend API access to anybody acting against the 
spirit of these restrictions.</p>

<p>The API is currently provided free of charge. We expect for this to remain true indefinitely, as our 
intention is to charge the end users of WizeHive, not the developers who add value. But again, 
we reserve the right to reverse this decision in the future.</p>

<p>These restrictions will become more formally defined over time, but the purpose and intended audience of 
the API is a constant.</p>

<?php
require_once("footer.php");
require_once("../footer.php");
?>