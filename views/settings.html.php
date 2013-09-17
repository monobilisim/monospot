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
			<input type="radio" name="authentication" id="sms" value="sms"<? echo $settings['authentication'] == 'sms' ? ' checked="checked"' : ''; ?>>
			<label for="sms">SMS ile şifre gönderimi</label>
		</p>
		<p>
			<input type="radio" name="authentication" id="id_number" value="id_number"<? echo $settings['authentication'] == 'id_number' ? ' checked="checked"' : ''; ?>>
			<label for="id_number">TC Kimlik No</label>
		</p>
		<p>
			<input type="radio" name="authentication" id="id_number_passport" value="id_number_passport"<? echo $settings['authentication'] == 'id_number_passport' ? ' checked="checked"' : ''; ?>>
			<label for="id_number_passport">TC Kimlik No veya Pasaport No</label>
		</p>
		<p>
			<input type="radio" name="authentication" id="manual_password" value="manual_password"<? echo $settings['authentication'] == 'manual_password' ? ' checked="checked"' : ''; ?>>
			<label for="manual_password">Elle kullanıcı açma</label>
			<p>
				<div class="label">Kullanıcı adı alanı</div>
				<input type="text" class="large" name="username" value="<?=$settings['username']?>">
			</p>
		</p>
	</fieldset>
	
	<fieldset>
		<legend>Girilmesi İstenen Alanlar</legend>
		<p>
			<input type="checkbox" name="fields[id_number]" id="field_id_number" value="1"<? echo isset($settings['fields']['id_number']) ? ' checked="checked"' : ''; ?>>
			<label for="field_id_number">TC Kimlik No (Giriş yöntemi TC Kimlik No seçilmemişse)</label>
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
	
	<fieldset<?=$settings['authentication'] == 'sms' ? '' : ' style="display:none"'?>>
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