<?php

require 'autoload.php';

define('URL_SITIO', 'http://localhost:8080/paypal');

$apiContext = new \PayPal\Rest\ApiContext(
		new \PayPal\Auth\OAuthTokenCredential(
				//ClienteID
				'Aai-r5Y5Kw8xxTtx16fNlL6bTwnE9bXVCX5mpQh64y8lnGUvKSkIQaohaJa6vxLGZFQqN2-4pujtaF1r',
				//Secret
				'EPfT3X-FBKF6no3W97c0Yi3rCqcSTKFoB85oorhJLfQmIYRxk7dlECWLTZ0D99zeG1hEc_9YtU8v0bic'
		)
);

