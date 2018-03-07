<?php

function send_sms($user, $password)
{
    // Kullanıcı bilgileri
    $api_username = '';
    $api_password = '';
    $api_originator = '';

    global $settings;
    require_once 'nusoap/lib/nusoap.php';
    $client = new nusoap_client("http://service.markamsms.com/Service/DeSMSService.asmx?wsdl", 'wsdl');
    $message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir.';
    $params = array(
        'username' => $api_username,
        'password' => $api_password,
        'originator' => $api_originator
        'sms_account' => 0,
        'message' => $message,
        'receipients' => '90' . $user->gsm,
        'division_time' => 0,
        'division_receiver_count' => 0,
        'turkish' => FALSE,
	);

	$response = $client->call('SendInstantSMS', $params);

	if ($response['SendInstantSMSResult']['result_code'] == '00') {
        return TRUE;
    } else {
        return FALSE;
    }
}
