<?php

date_default_timezone_set('Europe/Istanbul');

require 'lib/limonade.php';
require 'lib/idiorm.php';
require 'lib/paris.php';
require 'models/user.php';
require 'sms.php';

$settings = include('settings.inc');

function configure()
{	
	$dir = dirname(__FILE__);

	ORM::configure('sqlite:'.$dir.'/db/hotspot.db');

	option('views_dir', $dir.'/views');
	
	error_layout('layouts/captiveportal.html.php');	
}

dispatch('', 'welcome');
function welcome()
{
	global $settings;
	$user = Model::factory('User')->create();
	$user->defaults();
	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('user', $user);
	return html('layouts/captiveportal.html.php');
}

dispatch_post('', 'welcome_post');
function welcome_post()
{	
	global $settings, $clientmac, $clientip;
	if (isset($_POST['lang']))
	{
		$_SESSION['lang'] = strtolower($_POST['lang']);
		redirect_to('');
	}
	
	$user = null;
	if ($settings['authentication'] == 'sms')
		$user = Model::factory('User')->where('gsm', $_POST['user']['gsm'])->find_one();
	if ($settings['authentication'] == 'id_number')
		$user = Model::factory('User')->where('id_number', $_POST['user']['id_number'])->find_one();
	if ($settings['authentication'] == 'id_number_passport')
	{
		if (isset($_POST['user']['id_number'])) $field = 'id_number';
		if (isset($_POST['user']['username'])) $field = 'username';
		$user = Model::factory('User')->where($field, $_POST['user'][$field])->find_one();
	}
	if ($settings['authentication'] == 'manual_password')
		$user = Model::factory('User')->where('username', $_POST['user']['username'])->find_one();
	
	$form = '';
	$arg = '';

	if (!isset($_POST['user']['password'])) # Yeni kayıt ya da TC Kimlik ile giriş
	{
		if ($user && $settings['authentication'] == 'sms')
		{
			$message = 'user_already_registered';
			$form = 'login';
		}
		else
		{
			// Geçersiz form gönderimlerine karşı önlem
			if (empty($_POST['user'])) redirect_to('');
			if ($settings['authentication'] == 'sms' && empty($_POST['user']['gsm'])) redirect_to('');
			
			if (!$user)
			{
				$user = Model::factory('User')->create();
				$user->defaults();
				$user->fill($_POST['user']);
			}
			
			if ($user->id_number)
			{
				$bilgiler = array(
					"isim"      => tr_toUpper($user->name),
					"soyisim"   => tr_toUpper($user->surname),
					"dogumyili" => $_POST['birthyear'],
					"tcno"      => $user->id_number
				);
				 
				$sonuc = tcno_dogrula($bilgiler);
				 
				if ($sonuc == 'true')
				{
					if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport')
					{
						login($user);
					}
				}
				else
				{
					if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport')
						captiveportal_logportalauth($user->id_number,$clientmac,$clientip,"FAILURE");
					$message = 'invalid_id_number';
					$form = 'register';
				}
			}
			
			if ($settings['authentication'] == 'sms' && !$form)
			{
				$message = password_request_check($user);
				if (!$message)
				{
					$password = generate_password();
					$user->password = $password;
					$user->save();
					if ($sent = send_sms($user, $password, $clientmac))
					{
						$message = 'password_sent';
					}
					else
					{
						$message = 'password_not_sent';
					}
					$form = 'login';
				}
				else
				{
					$form = 'register';
				}
			}
		}
	}
	else # Login
	{
		if (!$user)
		{
			$message = 'user_not_found';
			if ($settings['authentication'] == 'sms') $form = 'register';
			else $form = 'login';
			$user = Model::factory('User')->create();
			$user->defaults();
		}
		else
		{
			if (strlen($_POST['user']['password']) > 0) # login attempt
			{
				if ($user->password == $_POST['user']['password'])
				{
					if (password_expired($user))
					{
						if ($settings['authentication'] == 'sms') $message = 'password_expired_sms';
						else $message = 'password_expired';
						$form = 'login';
					}
					else
					{
						login($user);
					}
				}
				else
				{
					captiveportal_logportalauth($user->gsm,$clientmac,$clientip,"FAILURE");
					$user->password = '';
					$message = 'invalid_password';
					$form = 'login';
				}
			}
			else # password request
			{
				$message = password_request_check($user);
				if (!$message)
				{
					$password = generate_password();
					$user->password = $password;
					$user->save();
					if ($sent = send_sms($user, $password, $clientmac))
					{
						$message = 'password_sent';
					}
					else
					{
						$message = 'password_not_sent';
					}
				}
				if ($message == 'min_interval') $arg = $settings['min_interval'];
				$form = 'login';
			}
		}
	}
	set('title', $settings['name'] . ' ' . t('welcome'));
	set('color', $settings['color']);
	set('message', $message);
	set('arg', $arg);
	set('form', $form);
	set('user', $user);
	return html('layouts/captiveportal.html.php');
}

run();


/* Functions */

function login($user)
{
	global $settings, $clientmac, $clientip;
	// hangi alan username olarak alınacak?
	if ($settings['authentication'] == 'sms') $field = 'gsm';
	if ($settings['authentication'] == 'id_number') $field = 'id_number';
	if ($settings['authentication'] == 'id_number_passport')
	{
		if (!empty($user->id_number)) $field = 'id_number';
		if (!empty($user->username)) $field = 'username';
	}
	if ($settings['authentication'] == 'manual_password') $field = 'username';
	
	$user->last_login = time();
	$user->save();
	captiveportal_logportalauth($user->$field,$clientmac,$clientip,"LOGIN");
	$attributes['session_terminate_time'] = $user->expires;
	portal_allow($clientip, $clientmac, $user->$field, $password = null, $attributes);
}

function t($key, $arg = '')
{
	$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tr';
	$messages = include dirname(__FILE__).'/lang/'.$lang.'.php';
	return sprintf($messages[$key], $arg);
}

function generate_password()
{
	return $password = mt_rand(100000, 999999);
}

function password_expired($user)
{
	if ($user->expires < time()) return true;
	else return false;
}

function password_request_check($user)
{
	global $settings;
	
	# daily global limit
	$daily_global_count = ORM::for_table('sms')->where_raw("strftime('%Y-%m-%d', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m-%d', 'now')")->count();
	if ($daily_global_count >= $settings['daily_global_limit']) return 'daily_global_limit';
	
	# minimum interval
	$seconds = time() - $user->last_sms;
	$minutes = floor($seconds/60);
	if ($minutes < $settings['min_interval']) return 'min_interval';
	
	# daily limit
	$daily_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y-%m-%d', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m-%d', 'now')")->count();
	if ($daily_count >= $user->daily_limit) return 'daily_limit';
	
	# weekly limit
	$weekly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%W', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%W', 'now')")->count();
	if ($weekly_count >= $user->weekly_limit) return 'weekly_limit';
	
	# monthly limit
	$monthly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y-%m', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m', 'now')")->count();
	if ($monthly_count >= $user->monthly_limit) return 'monthly_limit';
	
	# yearly limit
	$yearly_count = ORM::for_table('sms')->where('user_id', $user->id)->where_raw("strftime('%Y', datetime(timestamp, 'unixepoch', 'localtime')) = strftime('%Y', 'now')")->count();
	if ($yearly_count >= $user->yearly_limit) return 'yearly_limit';
	
	return false;
}

function tcno_dogrula($bilgiler) {
 
	$gonder = '<?xml version="1.0" encoding="utf-8"?>
	<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	<TCKimlikNoDogrula xmlns="http://tckimlik.nvi.gov.tr/WS">
	<TCKimlikNo>'.$bilgiler["tcno"].'</TCKimlikNo>
	<Ad>'.$bilgiler["isim"].'</Ad>
	<Soyad>'.$bilgiler["soyisim"].'</Soyad>
	<DogumYili>'.$bilgiler["dogumyili"].'</DogumYili>
	</TCKimlikNoDogrula>
	</soap:Body>
	</soap:Envelope>';
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,            "https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($ch, CURLOPT_POST,           true );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS,    $gonder);
	curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
	'POST /Service/KPSPublic.asmx HTTP/1.1',
	'Host: tckimlik.nvi.gov.tr',
	'Content-Type: text/xml; charset=utf-8',
	'SOAPAction: "http://tckimlik.nvi.gov.tr/WS/TCKimlikNoDogrula"',
	'Content-Length: '.strlen($gonder)
	));
	$gelen = curl_exec($ch);
	curl_close($ch);
	 
	return strip_tags($gelen);
}

function tr_toUpper($veri) {
    return strtoupper (str_replace(array ('ı', 'i', 'ğ', 'ü', 'ş', 'ö', 'ç' ),array ('I', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç' ),$veri));
}