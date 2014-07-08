<p>To access <strong>public</strong> resources, you will need to provide an API key in the query string 
of your request:</p>

<p><pre>?client_id={your API key}</pre></p>

<p>To access <strong>private</strong> resources, you will need to provide an access token for the user 
you are acting on behalf of (even if that user is yourself).</p>

<p><pre>?access_token={user's access token}</pre></p>