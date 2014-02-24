<?
	$action = empty($user->id) ? url_for('user/add') : url_for('user', $user->id, 'update');
?>

<script>
	$(function() {
		$("#expires").datetimepicker({
			dateFormat: "dd.mm.yy -",
			regional: "tr",
		});
	});
</script>

<h2><?= empty($user->id) ? "Kullanıcı Ekle" : "Kullanıcı Düzenle ID:".$user->id ?></h2>

<? if (!empty($errors)): ?>
<ul class="errors">
<? foreach($errors as $error): ?>
	<li><?=$error?>
<? endforeach; ?>
</ul>
<? endif; ?>

<form action="<?=$action?>" method="POST">
	<div id="user">
		<p>
			<div class="label">TC Kimlik No</div>
			<input type="text" class="small<?=isset($errors['id_number']) ? ' error' : ''?>" name="user[id_number]" maxlength="11" value="<?=$user->id_number?>">
		</p>
		<p>
			<div class="label">Ad</div>
			<input type="text" class="small" name="user[name]" value="<?=$user->name?>">
		</p>
		<p>
			<div class="label">Soyad</div>
			<input type="text" class="small" name="user[surname]" value="<?=$user->surname?>">
		</p>
		<p>
			<div class="label">E-posta</div>
			<input type="text" class="small" name="user[email]" value="<?=$user->email?>">
		</p>
		<p>
			<div class="label">GSM</div>
			<input type="text" class="small<?=isset($errors['gsm']) ? ' error' : ''?>" name="user[gsm]" value="<?=$user->gsm?>">
		</p>
	<? if (isset($settings['authentication']['sms'])): ?>
		<p>
			<div class="label">Şifre</div>
			<input type="text" class="small<?=isset($errors['password']) ? ' error' : ''?>" name="user[password]" value="<?=$user->password?>">
		</p>
		<p>
			<div class="label">Günlük SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['daily_limit']) ? ' error' : ''?>" name="user[daily_limit]" value="<?=$user->daily_limit?>">
		</p>
		<p>
			<div class="label">Haftalık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['weekly_limit']) ? ' error' : ''?>" name="user[weekly_limit]" value="<?=$user->weekly_limit?>">
		</p>
		<p>
			<div class="label">Aylık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['monthly_limit']) ? ' error' : ''?>" name="user[monthly_limit]" value="<?=$user->monthly_limit?>">
		</p>
		<p>
			<div class="label">Yıllık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['yearly_limit']) ? ' error' : ''?>" name="user[yearly_limit]" value="<?=$user->yearly_limit?>">
		</p>
	<? endif; ?>
	<? if (isset($settings['authentication']['manual_user'])): ?>
		<p>
			<div class="label">Kullanıcı adı</div>
			<input type="text" class="small" name="user[username]" value="<?=$user->username?>">
		</p>
		<p>
			<div class="label">Şifre</div>
			<input type="text" class="small" name="user[password]" value="<?=$user->password?>">
		</p>
	<? endif; ?>
	<? if (isset($settings['authentication']['sms']) || isset($settings['authentication']['manual_user'])): ?>
		<p>
			<div class="label">Şifre Son Geçerlilik Tarihi</div>
			<input type="text" class="medium<?=isset($errors['expires']) ? ' error' : ''?>" name="user[expires]" id="expires" value="<?=format_date($user->expires)?>">
		</p>
	<? endif; ?>

		<p class="actions">
			<input type="submit" value="<?= empty($user->id) ? "Ekle" : "Güncelle" ?>">
			<a href="<?= empty($user->id) ? url_for('user') : url_for('user', $user->id) ?>">İptal</a>
		</p>
	</div>
</form>