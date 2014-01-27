<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
	$api_customer_code = '';
	$api_alphanumeric = '';

	global $settings;
	$xml =
	'<packet version="1.0">
		<header>
			<auth userName="' . $api_customer_code . '-' . $api_username . '" password="' . $api_password . '" />
		</header>
		<body>
			<sendMessage>
				<type>Sms</type>
				<from>' . $api_alphanumeric . '</from>
				<to>' . $user->gsm . '</to>
				<sendDate></sendDate>
				<data>' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.</data>
				</sendMessage>
		</body>
	</packet>';
	$url = 'http://smsc.cmfcell.com/sections/service/api/xmlwebservice/xmlwebservice.aspx';
	$result = HTTPPoster($url, $xml);
	$result_xml = new SimpleXMLElement($result);

	if (!isset($result_xml->error))
		return true;
	else
		return false;
}

function HTTPPoster($url, $data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	return $result;
}
