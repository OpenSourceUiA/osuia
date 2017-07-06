<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>
<table class="form-table">
	<tr valign="top" class="wp_cron">
		<th scope="row"><?php esc_html_e( 'Interval for sending emails', 'mymail' )?></th>
		<td><p><?php printf( __( 'Send emails at most every %1$s minutes', 'mymail' ), '<input type="text" name="mymail_options[interval]" value="' . mymail_option( 'interval' ) . '" class="small-text">' ) ?></p><p class="description"><?php esc_html_e( 'Optional if a real cron service is used', 'mymail' );?></p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Service', 'mymail' )?></th>
		<td>
			<?php $cron = mymail_option( 'cron_service' );?>
			<label><input type="radio" class="cron_radio" name="mymail_options[cron_service]" value="wp_cron" <?php checked( $cron == 'wp_cron' );?> > <?php esc_html_e( 'Use the wp_cron function to send newsletters', 'mymail' )?></label><br>
			<?php if ( ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ): ?>
			<div class="error inline"><p><strong><?php printf( __( 'WordPress cron is disabled! Uncomment the %s constant in the wp-config.php or use a real cron instead', 'mymail' ), '<code>DISABLE_WP_CRON</code>' ); ?></strong></p></div>
			<?php endif;?>
			<label><input type="radio" class="cron_radio" name="mymail_options[cron_service]" value="cron" <?php checked( $cron == 'cron' )?> > <?php esc_html_e( 'Use a real cron to send newsletters', 'mymail' )?></label> <span class="description"><?php esc_html_e( 'reccomended for many subscribers', 'mymail' )?></span>
			<?php if ( file_exists( MYMAIL_UPLOAD_DIR . '/CRON_LOCK' ) && ( time() - filemtime( MYMAIL_UPLOAD_DIR . '/CRON_LOCK' ) ) < 10 ): ?>
			<div class="error inline"><p><strong><?php esc_html_e( 'Cron is currently running!', 'mymail' );?></strong></p></div>
			<?php endif;?>
		</td>
	</tr>
	<tr valign="top" class="cron_opts cron" <?php if ( $cron != 'cron' ) {
	echo ' style="display:none"';
}
?>>
		<th scope="row"><?php esc_html_e( 'Cron Settings', 'mymail' )?></th>
		<td>
			<p>
			<input type="text" name="mymail_options[cron_secret]" value="<?php echo esc_attr( mymail_option( 'cron_secret' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'a secret hash which is required to execute the cron', 'mymail' )?></span>
			</p>
			<?php $cron_url = defined( 'MYMAIL_MU_CRON' )
	? add_query_arg( array( 'action' => 'mymail_cron_worker', 'secret' => mymail_option( 'cron_secret' ) ), admin_url( 'admin-ajax.php' ) )
	: MYMAIL_URI . 'cron.php?' . mymail_option( 'cron_secret' );
?>
			<p><?php esc_html_e( 'You can keep a browser window open with following URL', 'mymail' )?><br>
			<a href="<?php echo $cron_url ?>" class="external"><code><?php echo $cron_url ?></code></a><br>
			<?php esc_html_e( 'call it directly', 'mymail' )?><br>
			<code>curl --silent <?php echo $cron_url ?></code><br>
			<?php esc_html_e( 'or set up a cron', 'mymail' )?><br>
			<code>*/<?php echo mymail_option( 'interval' ) ?> * * * * GET <?php echo $cron_url ?> > /dev/null</code></p>
			<p class="description"><?php esc_html_e( 'You can setup an interval as low as one minute, but should consider a reasonable value of 5-15 minutes as well.', 'mymail' );?></p>
			<p class="description"><?php esc_html_e( 'If you need help setting up a cron job please refer to the documentation that your provider offers.', 'mymail' );?></p>
			<p class="description"><?php printf( __( 'Anyway, chances are high that either %1$s, %2$s or %3$s  documentation will help you.', 'mymail' ), '<a href="http://docs.cpanel.net/twiki/bin/view/AllDocumentation/CpanelDocs/CronJobs#Adding a cron job" class="external">the CPanel</a>', '<a href="http://download1.parallels.com/Plesk/PP10/10.3.1/Doc/en-US/online/plesk-administrator-guide/plesk-control-panel-user-guide/index.htm?fileName=65208.htm" class="external">Plesk</a>', '<a href="http://www.thegeekstuff.com/2011/07/php-cron-job/" class="external">the crontab</a>' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Cron Lock', 'mymail' )?></th>
		<td>

			<?php $cron_lock = mymail_option('cron_lock'); ?>
			<select name="mymail_options[cron_lock]">
				<option value="file" <?php selected( $cron_lock, 'file' ); ?>><?php _e('File based', 'mymail') ?></option>
				<option value="db" <?php selected( $cron_lock, 'db' ); ?>><?php _e('Database based', 'mymail') ?></option>
			</select>
			<p class="description"><?php esc_html_e( 'A Cron Lock ensures your cron is not overlapping and causing duplicate emails. Select which method you like to use.', 'mymail' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Last hit', 'mymail' )?></th>
		<td>
		<?php $last_hit = get_option( 'mymail_cron_lasthit' );
			if($last_hit && time()-$last_hit['timestamp'] > 720 && mymail('cron')->is_locked()) : ?>
			<div class="error inline">
			<p><?php printf( __('Looks like your Cron Lock is still in place after %1$s! Read more about why this can happen %2$s.', 'mymail' ), '<strong>'.human_time_diff( $last_hit['timestamp'] ).'</strong>', '<a href="https://help.revaxarts.com/what-is-a-cron-lock/" class="external">'.__('here', 'mymail').'</a>'); ?></p>
			</div>
			<?php endif; ?>

		<ul class="lasthit highlight">
		<?php if($last_hit) :
				$interv = round( ( $last_hit['timestamp'] - $last_hit['oldtimestamp'] ) / 60 );
		 ?>
			<li>IP: <?php echo $last_hit['ip']; if($last_hit['ip'] == mymail_get_ip()) echo ' ('.__('probably you', 'mymail').')' ?></li>
			<li><?php echo $last_hit['user'] ?></li>
			<li><?php echo date( $timeformat, $last_hit['timestamp'] + $timeoffset ).', <strong>'.sprintf( __( '%s ago', 'mymail' ), human_time_diff( $last_hit['timestamp'] )).'</strong>' ?></li>
			<?php if($interv): ?>
			<li><?php echo __( 'Interval', 'mymail' ).': <strong>'.$interv.' '._x( 'min', 'short for minute', 'mymail' ).'</strong>'; ?></li>
			<?php endif; ?>
			<?php if($last_hit['timemax']): ?>
			<li><?php echo __( 'Max Execution Time', 'mymail' ).': '.round( $last_hit['timemax'], 3 ) . ' '._x( 'sec', 'short for second', 'mymail' ); ?></li>
			<?php endif; ?>
		<?php else: ?>
			<li><strong><?php esc_html_e( 'never', 'mymail' ) ?></strong>
			(<a href="https://help.revaxarts.com/how-do-i-know-if-my-cron-is-working-correctly/" class="external"><?php esc_html_e( 'why?', 'mymail' ) ?></a>)</li>
		<?php endif; ?>

		</ul>

		</td>
	</tr>
</table>
