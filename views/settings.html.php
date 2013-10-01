<h2>Ayarlar</h2>

<? if (!empty($errors)): ?>
<ul class="errors">
<? foreach($errors as $error): ?>
	<li><?=$error?>
<? endforeach; ?>
</ul>
<? endif; ?>

<form action="<?=url_for('settings')?>" method="POST">
	<fieldset>
		<legend>Giriş Yöntemi</legend>
		<p>
			<input type="checkbox" name="authentication[sms]" id="sms" value="1"<? echo isset($settings['authentication']['sms']) ? ' checked="checked"' : ''; ?>>
			<label for="sms">SMS ile şifre gönderimi</label>
				<div class="secondary-setting">
					<input type="checkbox" name="simple_screen" id="simple_screen" value="1"<? echo isset($settings['simple_screen']) ? ' checked="checked"' : ''; ?>>
					<label for="simple_screen">Basitleştirilmiş Ekran</label>
				</div>
				<div class="secondary-setting">
					<input type="checkbox" name="sms_fields[id_number]" id="sms_field_id_number" value="1"<? echo isset($settings['sms_fields']['id_number']) ? ' checked="checked"' : ''; ?>>
					<label for="sms_field_id_number">TC Kimlik No doğrulaması yap</label>
				</div>
		</p>
		<p>
			<input type="checkbox" name="authentication[id_number]" id="id_number" value="1"<? echo isset($settings['authentication']['id_number']) ? ' checked="checked"' : ''; ?>>
			<label for="id_number">TC Kimlik No</label>
		</p>
		<p>
			<input type="checkbox" name="authentication[manual_user]" id="manual_user" value="1"<? echo isset($settings['authentication']['manual_user']) ? ' checked="checked"' : ''; ?>>
			<label for="manual_user">Elle kullanıcı açma</label>
		</p>
	</fieldset>
	
	<fieldset>
		<legend>Genel Ayarlar</legend>
		<p>
			<div class="label">Oturum Geçerlilik Süresi</div>
			<input type="text" class="xsmall<?=isset($errors['session_timeout']) ? ' error' : ''?>" name="session_timeout" value="<?=$settings['session_timeout']?>"> dakika
		</p>
		<p>
			<div class="label">Şifre Geçerlilik Süresi</div>
			<input type="text" class="xsmall<?=isset($errors['valid_for']) ? ' error' : ''?>" name="valid_for" value="<?=$settings['valid_for']?>"> gün
		</p>
	</fieldset>
	
	<fieldset<?=isset($settings['authentication']['sms']) ? '' : ' style="display:none"'?>>
		<legend>SMS Ayarları</legend>
		<p>
			<div class="label">Günlük Toplam SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['daily_global_limit']) ? ' error' : ''?>" name="daily_global_limit" value="<?=$settings['daily_global_limit']?>">
		</p>
		Kullanıcı başına ayarlar:
		<p>
			<div class="label">Günlük SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['daily_limit']) ? ' error' : ''?>" name="daily_limit" value="<?=$settings['daily_limit']?>">
		</p>
		<p>
			<div class="label">Haftalık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['weekly_limit']) ? ' error' : ''?>" name="weekly_limit" value="<?=$settings['weekly_limit']?>">
		</p>
		<p>
			<div class="label">Aylık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['monthly_limit']) ? ' error' : ''?>" name="monthly_limit" value="<?=$settings['monthly_limit']?>">
		</p>
		<p>
			<div class="label">Yıllık SMS Limiti</div>
			<input type="text" class="xsmall<?=isset($errors['yearly_limit']) ? ' error' : ''?>" name="yearly_limit" value="<?=$settings['yearly_limit']?>">
		</p>
		<p>
			<div class="label">İki SMS arası minimum süre</div>
			<input type="text" class="xsmall<?=isset($errors['min_interval']) ? ' error' : ''?>" name="min_interval" value="<?=$settings['min_interval']?>"> dk.
		</p>
	</fieldset>

	<fieldset>
		<legend>Erişim Ekranı Ayarları</legend>
		<p>
			<div class="label">Kurum Adı</div>
			<input type="text" class="xxlarge<?=isset($errors['name']) ? ' error' : ''?>" name="name" value="<?=$settings['name']?>">
		</p>
		<p>
			<div class="label">Renk</div>
			<input type="text" class="xsmall<?=isset($errors['color']) ? ' error' : ''?>" name="color" value="<?=$settings['color']?>">
		</p>
	</fieldset>
	
	<fieldset>
		<legend>Yönetim Ekranı Ayarları</legend>
		<p>
			<div class="label">Sayfa Başına Adet</div>
			<input type="text" class="xsmall<?=isset($errors['items_per_page']) ? ' error' : ''?>" name="items_per_page" value="<?=$settings['items_per_page']?>">
		</p>
	</fieldset>
	
		<p>
			<input type="submit" value="Kaydet">
		</p>
	</div>
</form>