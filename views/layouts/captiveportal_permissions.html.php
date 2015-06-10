<? if (isset($settings['contact'][$method]['gsm'])): ?>
	<div class="item">
		<label><?=t('gsm')?>:</label>
		<input name="user[gsm]" class="text" type="text" maxlength="10" value="<?=$user->gsm?>" onkeypress="checkphone(this, event)">
		<div class="item-description"><?=t('gsm_desc')?></div>
	</div>
<? endif; ?>
<? if (isset($settings['contact'][$method]['gsm_permission'])): ?>
	<div class="item">
		<div class="permission">
			<input type="hidden" name="gsm_permission_asked">
			<input type="checkbox" name="gsm_permission" id="<?=$method?>_gsm_permission" value="1"<?= permission_checked($method, $_form, 'gsm') ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_gsm_permission"><?=t('gsm_permission')?></label>
		</div>
	</div>
<? endif; ?>
<? if (isset($settings['contact'][$method]['gsm_permission_required'])): ?>
	<input type="hidden" name="gsm_permission_required">
<? endif; ?>

<? if (isset($settings['contact'][$method]['email'])): ?>
	<div class="item">
		<label><?=t('email')?>:</label>
		<input name="user[email]" class="text" type="text" maxlength="40" value="<?=$user->email?>">
	</div>
<? endif; ?>
<? if (isset($settings['contact'][$method]['email_permission'])): ?>
	<div class="item">
		<div class="permission">
			<input type="hidden" name="email_permission_asked">
			<input type="checkbox" name="email_permission" id="<?=$method?>_email_permission" value="1"<?= permission_checked($method, $_form, 'email') ? ' checked="checked"' : ''; ?>>
			<label for="<?=$method?>_email_permission"><?=t('email_permission')?></label>
		</div>
	</div>
<? endif; ?>
<? if (isset($settings['contact'][$method]['email_permission_required'])): ?>
	<input type="hidden" name="email_permission_required">
<? endif; ?>