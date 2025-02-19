			<button id="custom_login"<?= $form == 'custom_login' ? ' class="active"' : '' ?>><?=t('custom_login')?></button>
			<div class="form<?= $form == 'custom_login' ? ' active' : '' ?>" id="form_custom_login">
				<div class="content">
				<form method="post" onSubmit="return validateForm(this)" action="">
				<div class="item">
				<label><?=t('pasaport_no')?></label>
				<input name="user[pasaport_no]" type="text" value="<?=$user->pasaport_no?>">
				</div>
				<div class="item">
				<label><?=t('hasta_no')?></label>
				<input name="user[hasta_no]" type="text" value="<?=$user->hasta_no?>">
				</div>
				<input name="form_id" type="hidden" value="custom_login">
				<input class="submit" name="submit" type="submit" value="<?=t('login')?> &#187;">
				</form>
				</div>
			</div>
