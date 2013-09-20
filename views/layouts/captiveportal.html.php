<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=$title?></title>
<link href="monospot/assets/css/captiveportal-style.css" rel="stylesheet">
<script src="monospot/assets/js/captiveportal-jquery-1.9.1.js"></script>
<script>
<? include 'monospot/assets/js/captiveportal.js'; ?>
<? $settings = include('monospot/settings.inc'); ?>
</script>
<style>
#title {background-color: <?=$color?>}
<? if ($form): ?>
#form-<?=$form?> {display: block}
<? endif; ?>
<? if ($settings['simple_screen']): ?>
#form-selection button {display: none}
.form .description {display: none}
form {padding-top: 20px}
<? endif; ?>
</style>
</head>

<body>
	
	<div id="container">
		<div id="title"><?=$title?></div>
		<div id='logo'>
			<img src="monospot/assets/img/captiveportal-logo.png">
		</div>

		<? if (isset($message)): ?>
		<div class="message <?= $message == 'password_sent' ? 'success' : 'error'?>"><?=t($message, $arg)?></div>
		<? endif; ?>

		<div id="form-selection">
		<? if ($settings['authentication'] == 'sms'): ?>
			<button id="register"<?= $form == 'register' ? ' class="active"' : '' ?>><?=t('register_select')?></button>
			<button id="login"<?= $form == 'login' ? ' class="active"' : '' ?>><?=t('login_select')?></button>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number'): ?>
			<button id="register"<?= $form == 'register' ? ' class="active"' : '' ?>><?=t('login_id_number')?></button>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number_passport'): ?>
			<button id="register"<?= $form == 'register' ? ' class="active"' : '' ?>><?=t('login_id_number')?></button>
			<button id="login"<?= $form == 'login' ? ' class="active"' : '' ?>><?=t('login_passport')?></button>
		<? endif; ?>
		<? if ($settings['authentication'] == 'manual_password'): ?>
			<button id="login"<?= $form == 'login' ? ' class="active"' : '' ?>><?=t('login_select')?></button>
		<? endif; ?>
		</div>
		
	<? if ($settings['authentication'] == 'sms' || $settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport'): ?>
		<div class="form" id="form-register">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
		<? if ($settings['authentication'] == 'sms'): ?>
			<p class="description"><?=t('register_desc')?></p>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport'): ?>
			<p class="description"><?=t('login_desc_id_number')?></p>
		<? endif; ?>
		<? if ($settings['authentication'] == 'sms'): ?>
			<div class="item">
			<label><?=t('gsm')?></label>
			<input name="user[gsm]" type="text" maxlength="10" value="<?=$user->gsm?>" onkeypress="checkphone(this, event)">
			<div class="item-description"><?=t('gsm_desc')?></div>
			</div>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number' || $settings['authentication'] == 'id_number_passport' || isset($settings['fields']['id_number'])): ?>
			<div class="item">
			<label><?=t('name')?></label>
			<input name="user[name]" type="text" maxlength="40" value="<?=$user->name?>">
			</div>
			<div class="item">
			<label><?=t('surname')?></label>
			<input name="user[surname]" type="text" maxlength="40" value="<?=$user->surname?>">
			</div>
			<div class="item">
			<label><?=t('birthyear')?></label>
			<input name="birthyear" type="text" maxlength="4" value="<?=$_POST['birthyear']?>">
			</div>
			<div class="item">
			<label><?=t('id_number')?></label>
			<input name="user[id_number]" type="text" maxlength="11" value="<?=$user->id_number?>">
			</div>
		<? endif; ?>
		<? if ($settings['authentication'] == 'sms'): ?>
			<input class="submit" name="submit" type="submit" value="<?=t('register')?> &#187;">
		<? else: ?>
			<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
		<? endif; ?>
			</form>
			</div>
		</div>
	<? endif; ?>
	
	<? if ($settings['authentication'] == 'sms' || $settings['authentication'] == 'id_number_passport' || $settings['authentication'] == 'manual_password'): ?>
		<div class="form" id="form-login">
			<div class="content">
			<form method="post" onSubmit="return validateForm(this)" action="">
		<? if ($settings['authentication'] == 'sms'): ?>
			<p class="description"><?=t('login_desc_gsm')?></p>
		<? else: ?>
			<p class="description"><?=t('login_desc')?></p>
		<? endif; ?>
		<? if ($settings['authentication'] == 'sms'): ?>
			<div class="item">
			<label><?=t('gsm')?></label>
			<input name="user[gsm]" type="text" maxlength="10" value="<?=$user->gsm?>" onkeypress="checkphone(this, event)"/>
			<div class="item-description"><?=t('gsm_desc')?></div>
			</div>
		<? endif; ?>
		<? if ($settings['authentication'] == 'manual_password'): ?>
			<div class="item">
			<label><?=$settings['username']?>:</label>
			<input name="user[username]" type="text" maxlength="11" value="<?=$user->username?>">
			</div>
		<? endif; ?>
		<? if ($settings['authentication'] == 'id_number_passport'): ?>
			<div class="item">
			<label><?=t('passport')?></label>
			<input name="user[username]" type="text" value="<?=$user->username?>">
			</div>
		<? endif; ?>
			<div class="item">
			<label><?=t('password')?></label>
			<input name="user[password]" type="password">
		<? if ($settings['authentication'] == 'gsm'): ?>
			<div class="item-description"><?=t('password_desc_gsm')?></div>
		<? endif; ?>
			</div>
			<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
			</form>
			</div>
		</div>
	<? endif; ?>
	
		<div id="lang">
			<form method="post" action="">
				<input type="submit" name="lang" value="TR"> | 
				<input type="submit" name="lang" value="EN">
			</form>
		</div>

	</div>

	<div id="wifi-logo">
		<img src="monospot/assets/img/captiveportal-wifi.png">
	</div>

</body>
</html>
