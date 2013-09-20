<?php

/* pfSense login check */
include_once("auth.inc");
include_once("priv.inc");

/* Authenticate user - exit if failed */
if (!session_auth()) {
	require_once("authgui.inc");
	display_login_form();
	exit;
}
/* ------------------- */

error_reporting(E_ALL);
date_default_timezone_set('Europe/Istanbul');

require 'lib/limonade.php';
require 'lib/idiorm.php';
require 'lib/paris.php';
require 'lib/validate.class.php';
require 'models/user.php';

$settings = include 'settings.inc';

function configure()
{
	$dir = dirname(__FILE__);
	
	ORM::configure('sqlite:'.$dir.'/db/hotspot.db');

	option('views_dir', $dir.'/views');

	layout('layouts/admin.html.php');
	error_layout('layouts/admin.html.php');
}

dispatch('/', 'home');
function home()
{
	redirect_to('users');
}

dispatch('users*', 'admin_user_index');
function admin_user_index()
{
	global $settings;
	
	parse_str(params(0), $get);
	
	$users = ORM::for_table('user');
	if (isset($get['id_number'])) $users->where_like('id_number', '%'.$get['id_number'].'%');
	if (isset($get['name'])) $users->where_like('name', '%'.$get['name'].'%');
	if (isset($get['surname'])) $users->where_like('surname', '%'.$get['surname'].'%');
	if (isset($get['gsm'])) $users->where_like('gsm', '%'.$get['gsm'].'%');
	if (isset($get['last_sms'])) $users->where_raw("strftime('%d.%m.%Y', datetime(last_sms, 'unixepoch', 'localtime')) = ?", array($get['last_sms']));
	if (isset($get['expires'])) $users->where_raw("strftime('%d.%m.%Y', datetime(expires, 'unixepoch', 'localtime')) = ?", array($get['expires']));
	if (isset($get['username'])) $users->where_like('username', '%'.$get['username'].'%');
	$total = $users->count();
	
	$users->select('user.*');
	$users->select_expr("sum(case
		when strftime('%Y-%m-%d', datetime(sms.timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m-%d', 'now') then 1
		else 0
		end)", 'sms_day');
	$users->select_expr("sum(case
		when strftime('%W', datetime(sms.timestamp, 'unixepoch', 'localtime')) = strftime('%W', 'now') then 1
		else 0
		end)", 'sms_week');
	$users->select_expr("sum(case
		when strftime('%Y-%m', datetime(sms.timestamp, 'unixepoch', 'localtime')) = strftime('%Y-%m', 'now') then 1
		else 0
		end)", 'sms_month');
	$users->select_expr("sum(case
		when strftime('%Y', datetime(sms.timestamp, 'unixepoch', 'localtime')) = strftime('%Y', 'now') then 1
		else 0
		end)", 'sms_year');
	$users->left_outer_join('sms', array('sms.user_id', '=', 'user.id'));
	$users->group_by('user.id');
	
	$columns = array('id_number', 'gsm', 'last_sms', 'expires', 'daily_limit', 'weekly_limit', 'monthly_limit', 'yearly_limit');
	if (isset($get['order']) && in_array($get['order'], $columns)) $col = $get['order'];
	else $col = 'user.id';
	if (isset($get['dir']) && in_array($get['dir'], array('asc', 'desc'))) $dir = $get['dir'];
	else $dir = 'asc';
	
	$offset = (int)$settings['items_per_page'];
	if (!isset($get['page'])) $start = 0;
	else $start = $offset * ($get['page'] - 1);
	
	$order_by_method = 'order_by_' . $dir;
	$users->$order_by_method($col)->limit($start.','.$offset);
	$users = $users->find_many();
	
	set('users', $users);
	set('pager', pager($start, $offset, $total, 'users'));
	set('get', $get);
	set('settings', $settings);
	set('rowspan', $settings['authentication'] == 'sms' ? 2 : 1);
	return html('user/index.html.php');
}

dispatch('user/add', 'admin_user_add_page');
function admin_user_add_page()
{
	global $settings;
	$user = Model::factory('User')->create();
	$user->defaults();
	set('user', $user);
	set('settings', $settings);
	set('title', 'Kullanıcı ekle');
	return html('user/_form.html.php');
}

dispatch_post('user/add', 'admin_user_add');
function admin_user_add()
{
	global $settings;
	$user = Model::factory('User')->create();
	$user->fill($_POST['user']);

	if ($errors = $user->validate($_POST['user']))
	{
		set('errors', $errors);
		set('user', $user);
		set('settings', $settings);
		return html('user/_form.html.php');
	}
	if($user->save())
	{
		$_SESSION['message'] = 'Kullanıcı eklendi.';
		redirect_to('user', $user->id());
	}
	else
	{
		halt(SERVER_ERROR, "Kullanıcı oluşturulması sırasında bir hata oluştu.");
	}
}

dispatch('user/:id', 'admin_user_view');
function admin_user_view($id)
{
	global $settings;
	$user = Model::factory('User')->find_one($id);
	if ($user->id)
	{
		set('user', $user);
		set('settings', $settings);
		return html('user/view.html.php');
	}
	else
	{
		halt(NOT_FOUND, "Böyle bir kullanıcı yok.");
	}
}

dispatch('user/:id/update', 'admin_user_update_page');
function admin_user_update_page($id)
{
	global $settings;
	$user = Model::factory('User')->find_one($id);
	if ($user->id)
	{
		set('user', $user);
		set('settings', $settings);
		return html('user/_form.html.php');
	}
	else
	{
		halt(NOT_FOUND, 'Böyle bir kullanıcı yok.');
	}
}

dispatch_post('/user/:id/update', 'admin_user_update');
function admin_user_update($id)
{
	global $settings;
	$user = Model::factory('User')->find_one($id);
	$user->fill($_POST['user']);
	
	if ($errors = $user->validate($_POST['user']))
	{
		set('user', $user);
		set('errors', $errors);
		set('settings', $settings);
		return html('user/_form.html.php');
	}
	if($user->save())
	{
		$_SESSION['message'] = 'Kullanıcı bilgileri güncellendi.';
		redirect_to('user', $id);
	}
	else
	{
		halt(SERVER_ERROR, "Kullanıcı bilgilerinin güncellenmesi sırasında bir hata oluştu. (id:" . params('id') . ")");
	}
}

dispatch('user/:id/delete', 'admin_user_delete');
function admin_user_delete($id)
{
	$user = Model::factory('User')->find_one($id);
	$user->delete();
	$_SESSION['message'] = 'Kullanıcı silindi.';
	redirect_to('users');
}

dispatch('sms*', 'admin_sms_index');
function admin_sms_index()
{
	global $settings;
	
	parse_str(params(0), $get);
	
	$orm = ORM::for_table('sms')->join('user', array('user.id', '=', 'sms.user_id'));
	if (isset($get['gsm'])) $orm->where_like('user.gsm', '%'.$get['gsm'].'%');
	if (isset($get['mac'])) $orm->where_like('mac', '%'.$get['mac'].'%');
	if (isset($get['timestamp_1'])) $orm->where_raw("strftime('%d.%m.%Y', datetime(timestamp, 'unixepoch', 'localtime')) between ? and ?", array($get['timestamp_1'], $get['timestamp_2']));
	
	$total = $orm->count();
	
	$columns = array('gsm', 'timestamp');
	if (isset($get['order']) && in_array($get['order'], $columns)) $col = $get['order'];
	else $col = 'timestamp';
	if (isset($get['dir']) && in_array($get['dir'], array('asc', 'desc'))) $dir = $get['dir'];
	else $dir = 'desc';
	
	$offset = (int)$settings['items_per_page'];
	if (!isset($get['page'])) $start = 0;
	else $start = $offset * ($get['page'] - 1);
	
	$order_by_method = 'order_by_' . $dir;
	$smss = $orm->$order_by_method($col)->limit($start.','.$offset)->find_many();
	
	set('smss', $smss);
	set('pager', pager($start, $offset, $total, 'smss'));
	set('get', $get);
	set('settings', $settings);
	return html('sms/index.html.php');
}

dispatch('settings', 'admin_settings');
function admin_settings()
{
	global $HTTP_SERVER_VARS;
	$username = $HTTP_SERVER_VARS['AUTH_USER'];
	$local_user = getUserEntry($username);
	if ($local_user['scope'] != 'system')
	{
		$_SESSION['message'] = 'Bu sayfaya erişim yetkiniz yok.';
		redirect_to('users');
	}
	global $settings;
	set('settings', $settings);
	return html('settings.html.php');
}

dispatch_post('settings', 'admin_settings_save');
function admin_settings_save()
{
	unset($_POST['__csrf_magic']);
	if ($errors = validate_settings($_POST))
	{
		set('errors', $errors);
		set('settings', $_POST);
	}
	else
	{
		global $config;
		$config['captiveportal']['timeout'] = $_POST['session_timeout'];
		$config['dhcpd']['lan']['defaultleasetime'] = $_POST['session_timeout'] * 60;
		$config['dhcpd']['lan']['maxleasetime'] = $_POST['session_timeout'] * 60 + 60;
		write_config();
		
		file_put_contents('settings.inc', '<?php' . "\n\n" . 'return ' . var_export($_POST, true) . ';');
		$settings = include('settings.inc');
		$_SESSION['message'] = 'Ayarlar kaydedildi.';
		set('settings', $settings);
	}
	return html('settings.html.php');
}

run();


/* Functions */

function format_date($timestamp)
{
	return $timestamp ? date('d.m.Y - H:i', $timestamp) : '';
}

function pager($start, $offset, $total, $url)
{
	parse_str(params(0), $get);
	
	$html = '<div id="pager">';
	$html .= '<p>Toplam: ' . $total . '</p>';
	if ($offset < $total)
	{
		$n = ceil($total/$offset);
		for ($i=1; $i<=$n; $i++)
		{
			$get['page'] = $i;
			$html .= '<a href="' . url_for($url, $get) . '"' . ($start == ($i-1)*$offset ? ' class="disabled"' : '') . '>' . $i . '</a>';
		}
	}
	$html .= '</div>';
	return $html;
}

function order_link($url, $column, $label)
{
	$dir_reverse = array(
		'asc' => 'desc',
		'desc' => 'asc',
	);
	$dir_arrows = array(
		'asc' => ' &darr;',
		'desc' => ' &uarr;',
	);
	parse_str(params(0), $get);
	unset($get['page']);
	$dir = isset($get['order']) && $get['order'] == $column ? $dir_reverse[$get['dir']] : 'asc';
	$arrow = isset($get['order']) && $get['order'] == $column ? $dir_arrows[$dir] : '';
	$get['order'] = $column;
	$get['dir'] = $dir;
	$link = '<a href="' . url_for($url, $get) . '">' . $label . '</a>' . $arrow;
	return $link;
}

function validate_settings($post)
{
	global $settings;
	
	$val = new Validation;
	
	$fields = array(
		'valid_for' => array(
			'rules' => 'required|integer',
			'label' => 'Oturum Geçerlilik Süresi',
		),
	);
	
	if ($settings['authentication'] == 'sms')
	{
		$fields['daily_global_limit'] = array(
			'rules' => 'required|integer',
			'label' => 'Günlük SMS limiti',
		);
		$fields['daily_limit'] = array(
			'rules' => 'required|integer',
			'label' => 'Günlük SMS limiti',
		);
		$fields['weekly_limit'] = array(
			'rules' => 'required|integer',
			'label' => 'Haftalık SMS limiti',
		);
		$fields['monthly_limit'] = array(
			'rules' => 'required|integer',
			'label' => 'Aylık SMS limiti',
		);
		$fields['yearly_limit'] = array(
			'rules' => 'required|integer',
			'label' => 'Yıllık SMS limiti',
		);
		$fields['min_interval'] = array(
			'rules' => 'required|integer',
			'label' => 'İki SMS arası minimum süre',
		);
	}
	
	$errors = $val->validate($fields, $post);
	return $errors;
}