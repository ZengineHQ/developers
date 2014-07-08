<?php
	$stage = $_SERVER['PARAM1'] == "stage" ? "stage-" : "";
	file_put_contents("/var/app/ondeck/rest-api/resources/config.local.php","<?php
		\$discoveryUrl = 'https://" . $stage . "api.wizehive.com/api-docs/resources.json';
?>
	");
	file_put_contents("/var/app/ondeck/rest-api/config.local.php","<?php
		\$apiDomain = 'https://" . $stage . "api.wizehive.com';
                \$authDomain = 'https://" . $stage . "auth.wizehive.com';
?>
        ");
?>
