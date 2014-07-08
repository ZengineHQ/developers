<?php
/**
 * Default configuration for swagger-ui
 *
 * @since   1.0
 * @author  Everton Yoshitani <everton@wizehive.com>
 */

/**
 * Discovey URL with the resources listing
 *
 * @since   1.0
 * @author  Everton Yoshitani <everton@wizehive.com>
 */
$discoveryUrl = 'https://api.wizehive.com/api-docs/resources.json';


/**
 * Default API Key (same as access_token)
 *
 * Set it for development mode
 *
 * @since   1.0
 * @author  Everton Yoshitani <everton@wizehive.com>
 */
$apiKey = false;

/**
 * Check for a local config file that overwrites this one for local development
 *
 * @since   1.0
 * @author  Everton Yoshitani <everton@wizehive.com>
 */
$localConfigFile = dirname(__FILE__) .'/config.local.php';
if (file_exists($localConfigFile) and is_readable($localConfigFile)) {
    require $localConfigFile;
}
