<?php
require_once("../../header.php");
require_once("../header.php");
?>

<h1>Quick Start</h1>

<h2 id="basics">Basics</h2>

<p>The base API is accessible at:</p>

<p><pre>
<?php echo $apiDomain; ?>/v1/
</pre></p>

<p>Data can be both sent & received in JSON & XML formats.</p>

<h2 id="auth">Auth</h2>

<p>The <a href="/rest-api/auth/#oauth2-overview">OAuth 2 protocol</a> is used to authenticate and authorize 
every API request.</p>

<?php require_once("__auth_breakdown.php"); ?>

<h2 id="getting-an-api-key">Getting an API Key</h2>

<p>You can retrieve any API key by <a href="../resources/#!/users/add_users_post_2">creating a user</a>, and then using the access token returned to you to <a href="../resources/#!/users-user.id-clients/add_clients_Clients_post_2">create an API client</a>. Take note of the key that is returned to you. It cannot be retrieved again.</p>

<h2 id="getting-an-access-token">Getting an Access Token</h2>

<p>Access token flows can get quite complicated. They vary depending on who the user is, how your application 
interacts with that user, and other nuanced requirements.</p>

<p>To fully understand which flows apply to your use case and how to implement them, we suggest reading 
more about the API's <a href="/rest-api/auth">Auth</a> process at large.</p>

<p>For now, to get you started with a sample request, we will assume that you are trying to access data 
from your own account with a token that you manually acquire for the purposes of this demo. 
<span id="getting-an-access-token-login">When you are ready, 
<a href="<?php echo $authDomain; ?>/oauth2/v1/authorize?client_id=<?php echo $demoClientId; ?>&response_type=token&state=demo">proceed to login and retrieve your demo token</a>.</span></p>

<div id="getting-an-access-token-success" data-alert class="alert-box success round" style="display: none">
	Your token was successfully generated and will be valid for the next 1 hour: <br />
	<input id="getting-an-access-token-token-value" type="text" onclick="this.select();" value="" />
</div>
<div id="getting-an-access-token-error" data-alert class="alert-box alert round" style="display: none">
	There was an error. Please try again.
</div>

<script type="text/javascript">

	<?php if (!empty($_GET['error'])) { ?>
		alert('There was an error. Please try again.');
	<?php } ?>

	var params = {}, queryString = location.hash.substring(1),
		regex = /([^&=]+)=([^&]*)/g, m;
	while (m = regex.exec(queryString)) {
		params[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
	}

	if (Object.keys(params).length > 0) { // doesn't work in all browsers. just for demo purposes.

		if (params['state'] != 'demo') {

			document.getElementById('getting-an-access-token-error').style.display = 'block';

		} else {

			var req = new XMLHttpRequest();

			req.open('GET', '<?php echo $authDomain; ?>/oauth2/v1/token?access_token=' + params['access_token']);

			req.onreadystatechange = function (e) {
				if (req.readyState == 4) {
					if(req.status == 200){

						document.getElementById('getting-an-access-token-login').style.display = 'none';
						document.getElementById('getting-an-access-token-token-value').value = params['access_token'];
						document.getElementById('access-token-example').innerHTML = params['access_token'];
						document.getElementById('getting-an-access-token-success').style.display = 'block';

						window.location = '#getting-an-access-token';

					} else {

						document.getElementById('getting-an-access-token-error').style.display = 'block';

					}
				}
			};
			req.send(null);

		}

	}

</script>

<h2 id="example-request">Example Request</h2>

<p>Now that you have an API key and demo access token, you are ready to make both public and private API 
requests.</p>

<p>Let's start with a private request (the most common). For example, all of the 
<strong>Workspaces </strong>you are associated with in JSON format:</p>

<p><pre>
<?php echo $apiDomain; ?>/v1/workspaces.json?access_token=<span id="access-token-example">{your demo access token}</span>
</pre></p>

<p>For public requests, such as retrieving a list of <strong>AppTemplates</strong> from 
the marketplace, you must instead supply your API key. Simply point your browser to:</p>

<p><pre>
<?php echo $apiDomain; ?>/v1/app_templates.json?client_id={your API key}
</pre></p>

<p>That's all there is to it! From here on out, we suggest learning by doing. The 
<a href="/rest-api/resources">resources</a> are not only documented, but also interactive. You can peruse 
them to learn the API semantics and make real requests (take note of your API key and access token, 
because you'll need them!). As 
you want or need, the rest of the documentation is here to help you learn more about general API 
conventions, tools and details that may prove useful for specific application requirements.</p>

<?php
require_once("../footer.php");
require_once("../../footer.php");
?>
