<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Bounce Address', 'mymail' )?></th>
		<td><input type="text" name="mymail_options[bounce]" value="<?php echo esc_attr( mymail_option( 'bounce' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'Undeliverable emails will return to this address', 'mymail' );?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row">&nbsp;</th>
		<td><label><input type="checkbox" name="mymail_options[bounce_active]" id="bounce_active" value="1" <?php checked( mymail_option( 'bounce_active' ) );?>> <?php esc_html_e( 'enable automatic bounce handling', 'mymail' )?></label>
		</td>
	</tr>

</table>
<div id="bounce-options" <?php if ( !mymail_option( 'bounce_active' ) ) { echo 'style="display:none"';} ?>>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php esc_html_e( 'If you would like to enable bouncing you have to setup a separate POP3 mail account', 'mymail' );?></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Server Address : Port', 'mymail' )?></th>
			<td><input type="text" name="mymail_options[bounce_server]" value="<?php echo esc_attr( mymail_option( 'bounce_server' ) ); ?>" class="regular-text">:<input type="text" name="mymail_options[bounce_port]" id="bounce_port" value="<?php echo mymail_option( 'bounce_port' ); ?>" class="small-text"></td>
		</tr>
		<tr valign="top">
			<th scope="row">SSL</th>
			<td><label><input type="checkbox" name="mymail_options[bounce_ssl]" id="bounce_ssl" value="1" <?php checked( mymail_option( 'bounce_ssl' ) );?>> <?php esc_html_e( 'Use SSL. Default port is 995', 'mymail' )?></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Username', 'mymail' )?></th>
			<td><input type="text" name="mymail_options[bounce_user]" value="<?php echo esc_attr( mymail_option( 'bounce_user' ) ); ?>" class="regular-text"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Password', 'mymail' )?></th>
			<td><input type="password" name="mymail_options[bounce_pwd]" value="<?php echo esc_attr( mymail_option( 'bounce_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
		<tr valign="top" class="wp_cron">
			<th scope="row"></th>
			<td><p><?php printf( __( 'Check bounce server every %s minutes for new messages', 'mymail' ), '<input type="text" name="mymail_options[bounce_check]" value="' . mymail_option( 'bounce_check' ) . '" class="small-text">' ) ?></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Delete messages', 'mymail' );?></th>
			<td><label><input type="checkbox" name="mymail_options[bounce_delete]" value="1" <?php checked( mymail_option( 'bounce_delete' ) )?>> <?php esc_html_e( 'Delete messages without tracking code to keep postbox clear (recommended)', 'mymail' )?></label>
			</td>
		</tr>
		<tr valign="top" class="wp_cron">
			<th scope="row"><?php esc_html_e( 'Soft Bounces', 'mymail' )?></th>
			<td><p><?php printf( __( 'Resend soft bounced mails after %s minutes', 'mymail' ), '<input type="text" name="mymail_options[bounce_delay]" value="' . mymail_option( 'bounce_delay' ) . '" class="small-text">' ) ?></p>
			<p><?php
$dropdown = '<select name="mymail_options[bounce_attempts]" class="postform">';
$value = mymail_option( 'bounce_attempts' );
for ( $i = 1; $i <= 10; $i++ ) {
	$selected = ( $value == $i ) ? ' selected' : '';
	$dropdown .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
}
$dropdown .= '</select>';

printf( __( '%s attempts to deliver message until hardbounce', 'mymail' ), $dropdown );

?></p></td>
		</tr>
	</table>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"></th>
			<td>
			<input type="button" value="<?php esc_html_e( 'Test bounce settings', 'mymail' )?>" class="button mymail_bouncetest">
			<div class="loading bounce-ajax-loading"></div>
			<span class="bouncetest_status"></span>
			</td>
		</tr>
	</table>
</div>
