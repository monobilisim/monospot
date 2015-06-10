<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?></title>
<link href="<?=$hotspot['_marka']?>/assets/css/captiveportal-style.css" rel="stylesheet">
<script src="<?=$hotspot['_marka']?>/assets/js/captiveportal-jquery-1.9.1.js"></script>
<script>
<? include $hotspot['_marka'] . '/assets/js/captiveportal.js'; ?>
<? $settings = include $hotspot['_marka'] . '/settings.inc'; ?>
</script>
<style>
#title {background-color: <?=$color?>}
#form-selection .active {background-color: <?=$color?>}
<? if (isset($settings['authentication']['sms']) && isset($settings['sms']['simple_screen'])): ?>
button#sms_register, button#sms_login {display: none}
.form .content {border-width: 1px}
.form .description {display: none}
<? endif; ?>
</style>
</head>

<body>

	<div id="title"><?=$title?></div>

	<div id="container">
		<div id='logo'>
			<img src="<?=$hotspot['_marka']?>/assets/img/captiveportal-logo.png">
		</div>

		<? if (isset($message)): ?>
		<div class="message <?= $message == 'password_sent' ? 'success' : 'error'?>"><?=t($message, $arg)?></div>
		<? endif; ?>

	<? if (isset($settings['authentication']['sms'])): ?>
		<button id="sms_register"<?= $form == 'sms_register' ? ' class="active"' : '' ?>><?=t('sms_register')?></button>
		<div class="form<?= $form == 'sms_register' ? ' active' : '' ?>" id="form_sms_register">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
			<p class="description"><?=t('sms_register_desc')?></p>
			<div class="item">
			<label><?=t('gsm')?>:</label>
			<input name="user[gsm]" class="text" type="text" maxlength="10" value="<?=$user->gsm?>" onkeypress="checkphone(this, event)">
			<div class="item-description"><?=t('gsm_desc')?></div>
			</div>
		<? if (isset($settings['sms']['id_number']) && $lang == 'tr'): ?>
			<div class="item">
			<label><?=t('name')?>:</label>
			<input name="user[name]" class="text" type="text" maxlength="40" value="<?=$user->name?>">
			</div>
			<div class="item">
			<label><?=t('surname')?>:</label>
			<input name="user[surname]" class="text" type="text" maxlength="40" value="<?=$user->surname?>">
			</div>
			<div class="item">
			<label><?=t('birthyear')?>:</label>
			<input name="birthyear" class="text" type="text" maxlength="4" value="<?=$_POST['birthyear']?>">
			</div>
			<div class="item">
			<label><?=t('id_number')?>:</label>
			<input name="user[id_number]" class="text" type="text" maxlength="11" value="<?=$user->id_number?>">
			</div>
		<? endif; ?>

		<?php $method = 'sms'; $_form = 'sms_register'; include 'captiveportal_permissions.html.php'; ?>

			<input name="form_id" type="hidden" value="sms_register">
			<input class="submit" name="submit" type="submit" value="<?=t('register')?> &#187;">
			</form>
			</div>
		</div>

		<button id="sms_login"<?= $form == 'sms_login' ? ' class="active"' : '' ?>><?=t('sms_login')?></button>
		<div class="form<?= $form == 'sms_login' ? ' active' : '' ?>" id="form_sms_login">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
			<p class="description"><?=t('login_desc_sms')?></p>
			<div class="item">
			<label><?=t('gsm')?>:</label>
			<input name="user[gsm]" class="text" type="text" maxlength="10" value="<?=$user->gsm?>" onkeypress="checkphone(this, event)"/>
			<div class="item-description"><?=t('gsm_desc')?></div>
			</div>
			<div class="item">
			<label><?=t('password')?>:</label>
			<input name="password" class="text" type="password">
			<div class="item-description"><?=t('password_desc_gsm')?></div>
			</div>
			<input name="form_id" type="hidden" value="sms_login">
			<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
			</form>
			</div>
		</div>
	<? endif; ?>

	<? if (isset($settings['authentication']['id_number']) && $lang == 'tr'): ?>
		<button id="id_number_login"<?= $form == 'id_number_login' ? ' class="active"' : '' ?>><?=t('id_number_login')?></button>
		<div class="form<?= $form == 'id_number_login' ? ' active' : '' ?>" id="form_id_number_login">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
			<p class="description"><?=t('login_desc_id_number')?></p>
			<div class="item">
			<label><?=t('name')?>:</label>
			<input name="user[name]" class="text" type="text" maxlength="40" value="<?=$user->name?>">
			</div>
			<div class="item">
			<label><?=t('surname')?>:</label>
			<input name="user[surname]" class="text" type="text" maxlength="40" value="<?=$user->surname?>">
			</div>
			<div class="item">
			<label><?=t('birthyear')?>:</label>
			<input name="birthyear" class="text" type="text" maxlength="4" value="<?=$_POST['birthyear']?>">
			</div>
			<div class="item">
			<label><?=t('id_number')?>:</label>
			<input name="user[id_number]" class="text" type="text" maxlength="11" value="<?=$user->id_number?>">
			</div>

			<?php $method = 'id_number'; $_form = 'id_number_login'; include 'captiveportal_permissions.html.php'; ?>

			<input name="form_id" type="hidden" value="id_number_login">
			<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
			</form>
			</div>
		</div>
	<? endif; ?>

	<? if (isset($settings['authentication']['manual_user'])): ?>
		<button id="manual_user_login"<?= $form == 'manual_user_login' ? ' class="active"' : '' ?>><?=t('manual_user_login')?></button>
		<div class="form<?= $form == 'manual_user_login' ? ' active' : '' ?>" id="form_manual_user_login">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
			<div class="description"><?=t('login_desc')?></div>
			<div class="item">
			<label><?=t('manual_user_name')?>:</label>
			<input name="user[username]" class="text" type="text" maxlength="11" value="<?=$user->username?>">
			</div>
			<div class="item">
			<label><?=t('password')?>:</label>
			<input name="password" class="text" type="password">
			</div>

			<?php $method = 'manual_user'; $_form = 'manual_user_login'; include 'captiveportal_permissions.html.php'; ?>

			<input name="form_id" type="hidden" value="manual_user_login">
			<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
			</form>
			</div>
		</div>
	<? endif; ?>

	<? include dirname(__FILE__) . '/captiveportal_custom.html.php'; ?>

		<div id="lang">
			<form method="post" action="">
				<input type="submit" name="lang" value="TR"> |
				<input type="submit" name="lang" value="EN">
			</form>
		</div>

	</div>

	<div id="wifi-logo">
		<img src="<?=$hotspot['_marka']?>/assets/img/captiveportal-wifi.png">
	</div>

</body>
</html>