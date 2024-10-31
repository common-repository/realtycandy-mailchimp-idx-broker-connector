<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h1>Idx MailChimp Sync</h1>
	<p id="sync-image">Sync &nbsp<img src="<?php echo get_home_url(); ?>/wp-includes/js/tinymce/skins/lightgray/img/loader.gif" height="23px" alt=""></p>
	<form method="post" action="options.php">
		<?php settings_fields( 'wpps_settings' ); ?>

		<?php do_settings_sections( 'wpps_settings' );	 ?>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</p>
	</form>
</div> <!-- .wrap -->
