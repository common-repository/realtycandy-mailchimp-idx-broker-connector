<?php
/*
 * Basic Section
 */	
//$settings = get_option( 'prowp_setting_values' );
//echo "<pre>"; var_dump($settings); echo "<pre>"; 
?>

<?php if ( 'enable_auto_sync' == $field['label_for'] ) : ?>
	<div class="form-group">		
		<div>
			<label class="radio inline">
				<input id="<?php esc_attr_e( 'wpps_settings[basic][enable_auto_sync]' ); ?>" 
				name="<?php esc_attr_e( 'wpps_settings[basic][enable_auto_sync]' ); ?>" 
				value="y" class="regular-text" <?php checked(  $settings['basic']['enable_auto_sync'], 'y'); ?>  type="radio">
				Yes
			</label>
			<label class="radio inline">
				<input id="<?php esc_attr_e( 'wpps_settings[basic][enable_auto_sync]' ); ?>" 
				name="<?php esc_attr_e( 'wpps_settings[basic][enable_auto_sync]' ); ?>" 
				value="n" class="regular-text" <?php checked(  $settings['basic']['enable_auto_sync'], 'n'); ?>  type="radio">
				No
			</label>
		</div>
	</div>

<?php endif; ?>

<?php if ( 'sync_list' == $field['label_for'] ) : ?>
	<div class="form-group">		
		<select name="<?php esc_attr_e( 'wpps_settings[basic][sync_list]' ); ?>" 
			id="<?php esc_attr_e( 'wpps_settings[basic][sync_list]' ); ?>" class="form-control">
			<option value="">Select a list..</option>
			<?php if (!empty($lists->lists)): ?>
				<?php foreach ($lists->lists as $list): ?>
					<option value="<?php echo $list->id;?>" <?php selected( $settings['basic']['sync_list'], $list->id ); ?> ><?php echo $list->name ?></option>
				<?php endforeach ?>
				
			<?php endif ?>
		</select>
	</div>


<?php endif; ?>

<?php
/*
 * Cron Section
 */
?>

<?php if ( 'enable_cron_idx_mailchimp' == $field['label_for'] ) : ?>	
	<div class="form-group">		
		<div>
			<label class="radio inline">
				<input id="<?php esc_attr_e( 'wpps_settings[cron][enable_cron_idx_mailchimp]' ); ?>" 
				name="<?php esc_attr_e( 'wpps_settings[cron][enable_cron_idx_mailchimp]' ); ?>" 
				value="y" class="regular-text" <?php checked(  $settings['cron']['enable_cron_idx_mailchimp'], 'y'); ?>  type="radio">
				Yes
			</label>
			<label class="radio inline">
				<input id="<?php esc_attr_e( 'wpps_settings[cron][enable_cron_idx_mailchimp]' ); ?>" 
				name="<?php esc_attr_e( 'wpps_settings[cron][enable_cron_idx_mailchimp]' ); ?>" 
				value="n" class="regular-text" <?php checked(  $settings['cron']['enable_cron_idx_mailchimp'], 'n'); ?>  type="radio">
				No
			</label>
		</div>
	</div>

<?php endif; ?>

<?php if ( 'cron_time' == $field['label_for'] ) : ?>
	<div class="form-group">
	<?php //echo "<pre>"; var_dump(wp_get_schedules()); echo "<pre>";  ?>		
		<select name="<?php esc_attr_e( 'wpps_settings[cron][cron_time]' ); ?>" 
			id="<?php esc_attr_e( 'wpps_settings[cron][cron_time]' ); ?>" class="form-control">			
			<?php if (!empty($cron_time_available)): ?>				
				<?php foreach ($cron_time_available as $key => $value): ?>
					<option value="<?php echo $key;?>" <?php selected( $settings['cron']['cron_time'], $key ); ?> ><?php echo $value ?></option>
				<?php endforeach ?>
				
			<?php endif ?>
		</select>
	</div>


<?php endif; ?>