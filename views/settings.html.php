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
		<div class="item">
			<input type="checkbox" name="authentication[sms]" id="sms" value="1"<? echo isset($settings['authentication']['sms']) ? ' checked="checked"' : ''; ?>>
			<label for="sms">SMS ile şifre gönderimi</label>
				<div class="secondary-item">
					<input type="checkbox" name="sms[simple_screen]" id="sms_simple_screen" value="1"<? echo isset($settings['sms']['simple_screen']) ? ' checked="checked"' : ''; ?>>
					<label for="sms_simple_screen">Basitleştirilmiş ekran</label>
				</div>
				<div class="secondary-item">
					<input type="checkbox" name="sms[always_send_password]" id="sms_always_send_password" value="1"<? echo isset($settings['sms']['always_send_password']) ? ' checked="checked"' : ''; ?>>
					<label for="sms_always_send_password">Geçerli şifresi olsa bile yeni şifre gönder</label>
				</div>
				<div class="secondary-item">
					<input type="checkbox" name="sms[id_number]" id="sms_id_number" value="1"<? echo isset($settings['sms']['id_number']) ? ' checked="checked"' : ''; ?>>
					<label for="sms_id_number">TC Kimlik No doğrulaması yap</label>
				</div>
		<?php $method = 'sms'; include 'settings_permissions.html.php'; ?>
		</div>
		<div class="item">
			<input type="checkbox" name="authentication[id_number]" id="id_number" value="1"<? echo isset($settings['authentication']['id_number']) ? ' checked="checked"' : ''; ?>>
			<label for="id_number">TC Kimlik No</label>
		</div>
		<?php $method = 'id_number'; include 'settings_permissions.html.php'; ?>
		<div class="item">
			<input type="checkbox" name="authentication[manual_user]" id="manual_user" value="1"<? echo isset($settings['authentication']['manual_user']) ? ' checked="checked"' : ''; ?>>
			<label for="manual_user">Elle kullanıcı açma</label>
		</div>
		<?php $method = 'manual_user'; include 'settings_permissions.html.php'; ?>
		<div class="item">
			<input type="checkbox" name="terms" id="terms" value="1"<? echo isset($settings['terms']) ? ' checked="checked"' : ''; ?>>
			<label for="terms">Sözleşme onayı</label>
				<div class="secondary-item">
					<input type="checkbox" name="terms_checked" id="terms_checked" value="1"<? echo isset($settings['terms_checked']) ? ' checked="checked"' : ''; ?>>
					<label for="terms_checked">Sözleşme onayı işaretli gelsin</label>
				</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Genel Ayarlar</legend>
		<div class="item">
			<div class="label">Oturum Geçerlilik Süresi</div>
			<input type="text" class="numeric xsmall<?=isset($errors['session_timeout']) ? ' error' : ''?>" name="session_timeout" value="<?=$settings['session_timeout']?>"> dakika
		</div>
		<div class="item">
			<div class="label">Şifre Geçerlilik Süresi</div>
			<input type="text" class="numeric xsmall<?=isset($errors['valid_for']) ? ' error' : ''?>" name="valid_for" value="<?=$settings['valid_for']?>"> gün
		</div>
        <div class="item">
            <input type="checkbox" name="disallow_multiple_logins" id="disallow_multiple_logins" value="1"<? echo isset($settings['disallow_multiple_logins']) ? ' checked="checked"' : ''; ?>>
            <label for="disallow_multiple_logins">Aynı MAC adresi ile farklı kullanıcının giriş yapmasını engelle</label>
        </div>
        <div class="item">
            <div class="label">MAC Adresi Bazlı Engelleme Süresi</div>
            <input type="text" class="numeric xsmall<?=isset($errors['disallow_multiple_logins_for']) ? ' error' : ''?>" name="disallow_multiple_logins_for" value="<?=$settings['disallow_multiple_logins_for']?>"> gün
        </div>
	</fieldset>

	<fieldset<?=isset($settings['authentication']['sms']) ? '' : ' style="display:none"'?>>
		<legend>SMS Ayarları</legend>
		<div class="item">
			<div class="label">Günlük Toplam SMS Limiti</div>
			<input type="text" class="numeric xsmall<?=isset($errors['daily_global_limit']) ? ' error' : ''?>" name="daily_global_limit" value="<?=$settings['daily_global_limit']?>">
		</div>
		Kullanıcı başına ayarlar:
		<div class="item">
			<div class="label">Günlük SMS Limiti</div>
			<input type="text" class="numeric xsmall<?=isset($errors['daily_limit']) ? ' error' : ''?>" name="daily_limit" value="<?=$settings['daily_limit']?>">
		</div>
		<div class="item">
			<div class="label">Haftalık SMS Limiti</div>
			<input type="text" class="numeric xsmall<?=isset($errors['weekly_limit']) ? ' error' : ''?>" name="weekly_limit" value="<?=$settings['weekly_limit']?>">
		</div>
		<div class="item">
			<div class="label">Aylık SMS Limiti</div>
			<input type="text" class="numeric xsmall<?=isset($errors['monthly_limit']) ? ' error' : ''?>" name="monthly_limit" value="<?=$settings['monthly_limit']?>">
		</div>
		<div class="item">
			<div class="label">Yıllık SMS Limiti</div>
			<input type="text" class="numeric xsmall<?=isset($errors['yearly_limit']) ? ' error' : ''?>" name="yearly_limit" value="<?=$settings['yearly_limit']?>">
		</div>
		<div class="item">
			<div class="label">İki SMS arası minimum süre</div>
			<input type="text" class="numeric xsmall<?=isset($errors['min_interval']) ? ' error' : ''?>" name="min_interval" value="<?=$settings['min_interval']?>"> dk.
		</div>
	</fieldset>

	<fieldset>
		<legend>Erişim Ekranı Ayarları</legend>
		<div class="item">
			<div class="label">Kurum Adı</div>
			<input type="text" class="xxlarge<?=isset($errors['name']) ? ' error' : ''?>" name="name" value="<?=$settings['name']?>">
		</div>
		<div class="item">
			<div class="label">Renk</div>
			<input type="text" class="xsmall<?=isset($errors['color']) ? ' error' : ''?>" name="color" value="<?=$settings['color']?>">
		</div>
	</fieldset>

	<fieldset>
	<legend>Yönetim Ekranı Ayarları</legend>
		<div class="item">
			<div class="label">Sayfa Başına Adet</div>
			<input type="text" class="numeric xsmall<?=isset($errors['items_per_page']) ? ' error' : ''?>" name="items_per_page" value="<?=$settings['items_per_page']?>">
		</div>
		<div class="item">
			<div class="label">Özel Alanlar</div>
			<textarea name="custom_fields" rows="5" cols="40"><?=$settings['custom_fields']?></textarea>
		</div>
	</fieldset>

		<div class="item">
			<input type="submit" value="Kaydet">
		</div>
	</div>
</form>