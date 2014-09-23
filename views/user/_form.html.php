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
	<? if ($settings['custom_fields']): ?>
		<? foreach (explode("\n", $settings['custom_fields']) as $field): ?>
			<? $field = explode('|', $field); ?>
			<? if ($field[0] != 'gsm'): ?>
			<div class="item">
				<div class="label"><?=$field[1]?></div>
				<input type="text" class="large" name="user[<?=$field[0]?>]" value="<?=$user->$field[0]?>">
			</div>
			<? endif; ?>
		<? endforeach; ?>
	<? endif; ?>
	<? if (isset($settings['authentication']['id_number']) || isset($settings['sms_fields']['id_number'])): ?>
		<div class="item">
			<div class="label">Ad</div>
			<input type="text" class="small" name="user[name]" value="<?=$user->name?>">
		</div>
		<div class="item">
			<div class="label">Soyad</div>
			<input type="text" class="small" name="user[surname]" value="<?=$user->surname?>">
		</div>
		<div class="item">
			<div class="label">TC Kimlik No</div>
			<input type="text" class="small<?=isset($errors['id_number']) ? ' error' : ''?>" name="user[id_number]" maxlength="11" value="<?=$user->id_number?>">
		</div>
	<? endif; ?>
		<div class="item">
			<div class="label">E-posta</div>
			<input type="text" class="xlarge" name="user[email]" value="<?=$user->email?>">
		</div>
	<? if (isset($settings['authentication']['sms']) || strpos($settings['custom_fields'], 'gsm') !== false): ?>
		<div class="item">
			<div class="label">GSM</div>
			<input type="text" class="small<?=isset($errors['gsm']) ? ' error' : ''?>" name="user[gsm]" value="<?=$user->gsm?>">
		</div>
		<div class="item">
			<div class="label">Günlük SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['daily_limit']) ? ' error' : ''?>" name="user[daily_limit]" value="<?=$user->daily_limit?>">
		</div>
		<div class="item">
			<div class="label">Haftalık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['weekly_limit']) ? ' error' : ''?>" name="user[weekly_limit]" value="<?=$user->weekly_limit?>">
		</div>
		<div class="item">
			<div class="label">Aylık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['monthly_limit']) ? ' error' : ''?>" name="user[monthly_limit]" value="<?=$user->monthly_limit?>">
		</div>
		<div class="item">
			<div class="label">Yıllık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['yearly_limit']) ? ' error' : ''?>" name="user[yearly_limit]" value="<?=$user->yearly_limit?>">
		</div>
	<? endif; ?>
	<? if (isset($settings['authentication']['manual_user'])): ?>
		<div class="item">
			<div class="label">Kullanıcı adı</div>
			<input type="text" class="small<?=isset($errors['username']) ? ' error' : ''?>" name="user[username]" value="<?=$user->username?>">
		</div>
	<? endif; ?>
		<div class="item">
			<div class="label">Şifre</div>
			<input type="text" class="small" name="user[password]" value="<?=$user->password?>">
		</div>
		<div class="item">
			<div class="label">Şifre Son Geçerlilik Tarihi</div>
			<input type="text" class="medium<?=isset($errors['expires']) ? ' error' : ''?>" name="user[expires]" id="expires" value="<?=format_date($user->expires)?>">
		</div>

		<div class="actions">
			<input type="submit" value="<?= empty($user->id) ? "Ekle" : "Güncelle" ?>">
			<a href="<?= empty($user->id) ? url_for('user') : url_for('user', $user->id) ?>">İptal</a>
		</div>
	</div>
</form>