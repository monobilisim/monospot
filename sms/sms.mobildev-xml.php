<?php

function send_sms($user, $password)
{
	// Mobildev kullanıcı bilgileri
	$api_username = '';
	$api_password = '';
	$api_originator = '';

	global $settings;
	$data = '<?xml version="1.0" encoding="utf-8" ?><MainmsgBody><UserName>' . $api_username . '</UserName><PassWord>' . $api_password . '</PassWord><Action>0</Action><Mesgbody>' . $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.</Mesgbody><Numbers>' . $user->gsm . '</Numbers><Originator>' . $api_originator . '</Originator><Encoding>0</Encoding><MessageType>N</MessageType></MainmsgBody>';
	$url = 'https://xmlapi.mobildev.com';
	$result = HTTPPoster($url, $data);

	if (substr($result, 0, 3) == 'ID:')
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
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
	$result = curl_exec($ch);
	return $result;
}

?>
