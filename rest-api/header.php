<?php
require_once("config.php");
?>
<div class="row">
	
	<div class="large-2 columns">
		
		<?php
		
		$request_uri = array_shift(explode('?', $_SERVER['REQUEST_URI']));
		$strlen = strlen($request_uri);
		if (substr($request_uri, -1) === '/') {
			$request_uri = substr($request_uri, 0, ($strlen - 1));
		}
		$_SERVER['X_REQUEST_URI'] = $request_uri;
		
		function pathMatch($path = '') {
			$this_strlen = strlen($path);
			$request_strlen = strlen($_SERVER['X_REQUEST_URI']);
			if (substr($path, 0, $this_strlen) === substr($_SERVER['X_REQUEST_URI'], 0, $request_strlen)) {
				return true;
			}
			return false;
		}
		function getNav($array = array(), $parent_path = '/rest-api') {
			$return = '';
			foreach ($array as $name => $settings) {
				if (is_string($settings) && $settings === '---') {
					$return .= '<li class="divider"></li>';
					continue;
				}
				$this_path = $parent_path;
				if (strpos($parent_path, '#') !== false) {
					$this_path .= '-';
				} elseif (!empty($settings['path'])) {
					$this_path .= '/';
				} else {
					$this_path .= '';
				}
				$this_path .= $settings['path'];
				$return .= '<li';
				$path_match = pathMatch($this_path);
				if ($path_match) {
					$return .= ' class="active"';
					$settings['final'] = null;
				}
				$return .= '><a href="' . $this_path . '">' . $name;
				if (empty($settings['final'])) {
					$sub_nav = array_diff_key($settings, array('path' => true, 'final' => true));
					if (!empty($sub_nav)) {
						$return .= '<ul>' . getNav($sub_nav, $this_path) . '</ul>';
					}
				}
				$return .= '</a></li>';
			}
			return $return;
		}
		$nav = array(
			'Introduction' => array(
				'path' => '',
//				'Audience' => array(
//					'path' => '#audience'
//				),
//				'Restrictions' => array(
//					'path' => '#restrictions'
//				)
			),
			'---',
			'Quick Start' => array(
				'path' => 'quick-start',
				'Basics' => array(
					'path' => '#basics',
				),
				'Auth' => array(
					'path' => '#auth'
				),
				'Getting an API Key' => array(
					'path' => '#getting-an-api-key'
				),
				'Getting an Access Token' => array(
					'path' => '#getting-an-access-token'
				),
				'Example Request' => array(
					'path' => '#example-request'
				)
			),
			'---',
			'Resources' => array(
				'path' => 'resources'
			),
			'---',
			'Conventions' => array(
				'path' => 'conventions',
				'---',
				'General' => array(
					'path' => 'general',
					'final' => true,
					'Base Path' => array(
						'path' => '#base-path'
					),
					'HTTPS Required' => array(
						'path' => '#https-required'
					),
					'Timestamp Format' => array(
						'path' => '#timestamp-format'
					),
					'Default Timezone' => array(
						'path' => '#default-timezone'
					),
					'Media Type Support' => array(
						'path' => '#media-type-support',
						'JSONP' => array(
							'path' => 'jsonp'
						),
						'JSON Pretty Printing' => array(
							'path' => 'json-pretty-printing'
						)
					)
				),
				'---',
				'Passing Parameters' => array(
					'path' => 'passing-parameters',
					'final' => true,
					'URI-based' => array(
						'path' => '#uri-based'
					),
					'Query String' => array(
						'path' => '#query-string'
					),
					'POST, PUT' => array(
						'path' => '#post-put'
					)
				),
				'---',
				'Querying Options' => array(
					'path' => 'querying-options',
					'final' => true,
					'Pagination' => array(
						'path' => '#pagination'
					),
					'Sorting' => array(
						'path' => '#sorting'
					),
					'Operator Prefixes' => array(
						'path' => '#operator-prefixes'
					),
					'Multiple Conditions' => array(
						'path' => '#multiple-conditions'
					),
					'Limiting Returned Attributes' => array(
						'path' => '#limiting-returned-attributes'
					),
					'Relational Data' => array(
						'path' => '#relational-data'
					),
					'Timezone Conversion' => array(
						'path' => '#timezone-conversion'
					)
				),
				'---',
				'Response Format' => array(
					'path' => 'response-format',
					'final' => true,
					'HTTP Status' => array(
						'path' => '#http-status'
					),
					'Success' => array(
						'path' => '#success',
						'GET, POST and PUT' => array(
							'path' => 'get-post-put'
						)
					),
					'Failure' => array(
						'path' => '#failure',
						'POST and PUT: User Error' => array(
							'path' => 'post-and-put-user-error'
						)
					)
				),
				'---',
				'HTTP Verbs' => array(
					'path' => 'http-verbs',
					'final' => true,
					'GET' => array(
						'path' => '#get'
					),
					'POST' => array(
						'path' => '#post'
					),
					'PUT' => array(
						'path' => '#put'
					),
					'DELETE' => array(
						'path' => '#delete'
					),
					'OPTIONS' => array(
						'path' => '#options'
					)
				),
			),
			'---',
			'Auth' => array(
				'path' => 'auth',
				'final' => true,
				'OAuth 2 Overview' => array(
					'path' => '#oauth2-overview'
				),
				'Registering an Application' => array(
					'path' => '#registering-an-application',
					'Redirect URI' => array(
						'path' => 'redirect-uri'
					)
				),
				'Public vs. Private Resources' => array(
					'path' => '#public-vs-private-resources'
				),
				'Passing Credentials' => array(
					'path' => '#passing-credentials'
				),
				'3-Legged Flows' => array(
					'path' => '#3-legged-flows',
					'Server' => array(
						'path' => 'server'
					),
					'Client' => array(
						'path' => 'client'
					)
				),
				'Authorizations API' => array(
					'path' => '#authorizations-api'
				)
			),
			'---',
			'Cross-Origin Resource Sharing (CORS)' => array(
				'path' => 'cross-origin-resource-sharing'
			)
		);
		
		
		?>
		
		<ul class="side-nav">
			<?php
			echo getNav($nav);
			?>
		</ul>
		
	</div>
	
	<div class="large-10 columns">
		