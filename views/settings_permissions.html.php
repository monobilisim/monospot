<fieldset class="permission">
	<legend>İletişim İzni Ayarları</legend>
	<div class="item">
		<input type="checkbox" name="contact[<?=$method?>][gsm]" id="<?=$method?>_contact_gsm" value="1"<? if (isset($settings['contact'][$method]['gsm']) || $method == 'sms') echo ' checked="checked"'; if ($method == 'sms' || !$authentication_settings_enabled) echo ' disabled'; ?>>
		<label for="<?=$method?>_contact_gsm">Cep telefonu giriş alanı ekle</label>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][gsm_required]" id="<?=$method?>_contact_gsm_required" value="1"<? if (isset($settings['contact'][$method]['gsm_required']) || $method == 'sms') echo ' checked="checked"'; if ($method == 'sms' || !$authentication_settings_enabled) echo ' disabled'; ?>>
			<label for="<?=$method?>_contact_gsm_required">Cep telefonu girişi zorunlu olsun</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][gsm_permission]" id="<?=$method?>_contact_gsm_permission" value="1"<?= isset($settings['contact'][$method]['gsm_permission']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission">Cep telefonu için iletişim izni iste</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][gsm_permission_checked]" id="<?=$method?>_contact_gsm_permission_checked" value="1"<?= isset($settings['contact'][$method]['gsm_permission_checked']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission_checked">İletişim izni seçeneği işaretli gelsin</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][gsm_permission_required]" id="<?=$method?>_contact_gsm_permission_required" value="1"<?= isset($settings['contact'][$method]['gsm_permission_required']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission_required">İletişim izni almadan girişe izin verme</label>
		</div>
	</div>
	<div class="item">
		<input type="checkbox" name="contact[<?=$method?>][email]" id="<?=$method?>_contact_email" value="1"<?= isset($settings['contact'][$method]['email']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
		<label for="<?=$method?>_contact_email">E-posta giriş alanı ekle</label>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][email_required]" id="<?=$method?>_contact_email_required" value="1"<?= isset($settings['contact'][$method]['email_required']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_email_required">E-posta girişi zorunlu olsun</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][email_permission]" id="<?=$method?>_contact_email_permission" value="1"<?= isset($settings['contact'][$method]['email_permission']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission">E-posta için iletişim izni iste</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][email_permission_checked]" id="<?=$method?>_contact_email_permission_checked" value="1"<?= isset($settings['contact'][$method]['email_permission_checked']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission_checked">İletişim izni seçeneği işaretli gelsin</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="contact[<?=$method?>][email_permission_required]" id="<?=$method?>_contact_email_permission_required" value="1"<?= isset($settings['contact'][$method]['email_permission_required']) ? ' checked="checked"' : ''; ?><?= !$authentication_settings_enabled ? ' disabled' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission_required">İletişim izni almadan girişe izin verme</label>
		</div>
	</div>
</fieldset>
