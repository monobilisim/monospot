<?php

function send_sms($user, $password)
{
	// Kullanıcı bilgileri
	$api_usercode = '';
	$api_password = '';
	$api_header = '';

	global $settings;
	$message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
	$xml='<?xml version="1.0" encoding="iso-8859-9"?>
<mainbody>
	<header>
		<company>NETGSM</company>
        <usercode>' . $api_usercode . '</usercode>
        <password>' . $api_password . '</password>
		<startdate></startdate>
		<stopdate></stopdate>
	    <type>1:n</type>
        <msgheader>' . $api_header . '</msgheader>
        </header>
		<body>
		<msg><![CDATA[' . $message . ']]></msg>
		<no>' . $user->gsm . '</no>
		</body>
</mainbody>';
	$url = 'http://api.netgsm.com.tr/xmlbulkhttppost.asp';
	$result = HTTPPoster($url, $xml);
	if (substr($result, 0, 2) === '00')
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