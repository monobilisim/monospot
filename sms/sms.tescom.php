<?php

function send_sms($user, $password)
{
    global $settings;

    // Tescom SMS kullanıcı bilgileri
    $api_username = '';
    $api_password = '';
    $api_originator = '';

    $api_url = 'http://api.tescom.com.tr:8080/api/smspost/v1';

    $message = $settings['name'] . ' WiFi hizmeti icin sifreniz ' . $password . ' olarak tanimlanmistir. ';
    $gsm = '90' . $user->gsm;

    $post_data = '' .
        '<sms>'.
        "<username>$api_username</username>".
        "<password>$api_password</password>".
        "<header>$api_originator</header>".
        '<validity>2880</validity>'.
        '<message>'.
        '<gsm>'.
        "<no>$gsm</no>".
        '</gsm>'.
        "<msg><![CDATA[$message]]></msg>".
        '</message>'.
        '</sms>';

    $result = HTTPPoster($api_url, $post_data);
    $result = explode(' ', $result);

    if ($result[0] === '00') {
        return true;
    }
    return false;
}

function HTTPPoster($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=UTF-8"));
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}