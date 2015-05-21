<fieldset class="permission">
	<legend>İletişim İzni Ayarları</legend>
	<div class="item">
		<input type="checkbox" name="authentication[<?=$method?>][contact][gsm]" id="<?=$method?>_contact_gsm" value="1"<? if (isset($settings['authentication'][$method]['contact']['gsm']) || $method == 'sms') echo ' checked="checked"'; if ($method == 'sms') echo ' disabled'; ?>>
		<label for="<?=$method?>_contact_gsm">Cep telefonu giriş alanı ekle</label>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][gsm_permission]" id="<?=$method?>_contact_gsm_permission" value="1"<?= isset($settings['authentication'][$method]['contact']['gsm_permission']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission">Cep telefonu için iletişim izni iste</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][gsm_permission_checked]" id="<?=$method?>_contact_gsm_permission_checked" value="1"<?= isset($settings['authentication'][$method]['contact']['gsm_permission_checked']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission_checked">İletişim izni seçeneği işaretli gelsin</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][gsm_permission_required]" id="<?=$method?>_contact_gsm_permission_required" value="1"<?= isset($settings['authentication'][$method]['contact']['gsm_permission_required']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_gsm_permission_required">İletişim izni almadan girişe izin verme</label>
		</div>
	</div>
	<div class="item">
		<input type="checkbox" name="authentication[<?=$method?>][contact][email]" id="<?=$method?>_contact_email" value="1"<?= isset($settings['authentication'][$method]['contact']['email']) ? ' checked="checked"' : ''; ?>>
		<label for="<?=$method?>_contact_email">E-posta giriş alanı ekle</label>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][email_permission]" id="<?=$method?>_contact_email_permission" value="1"<?= isset($settings['authentication'][$method]['contact']['email_permission']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission">E-posta için iletişim izni iste</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][email_permission_checked]" id="<?=$method?>_contact_email_permission_checked" value="1"<?= isset($settings['authentication'][$method]['contact']['email_permission_checked']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission_checked">İletişim izni seçeneği işaretli gelsin</label>
		</div>
		<div class="secondary-item">
			<input type="checkbox" name="authentication[<?=$method?>][contact][email_permission_required]" id="<?=$method?>_contact_email_permission_required" value="1"<?= isset($settings['authentication'][$method]['contact']['email_permission_required']) ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_contact_email_permission_required">İletişim izni almadan girişe izin verme</label>
		</div>
	</div>
</fieldset>