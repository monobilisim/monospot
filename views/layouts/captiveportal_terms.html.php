<div class="item">
	<div class="checkbox">
		<input type="hidden" name="terms_asked">
		<input type="checkbox" name="terms" id="<?=$method?>_terms" value="1"<?= isset($settings['terms_checked']) ? ' checked="checked"' : ''; ?>>
		<label for="<?=$method?>_terms"><?=t('terms')?></label>
		<div><a href="#terms-text" class="popup terms-link"><?=t('terms_link')?></a></div>
	</div>
</div>