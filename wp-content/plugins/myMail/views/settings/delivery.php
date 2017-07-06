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
		<th scope="row"><?php esc_html_e( 'Number of mails sent', 'mymail' )?></th>
		<td><p><?php printf( __( 'Send max %1$s emails at once and max %2$s within %3$s hours', 'mymail' ), '<input type="text" name="mymail_options[send_at_once]" value="' . mymail_option( 'send_at_once' ) . '" class="small-text">', '<input type="text" name="mymail_options[send_limit]" value="' . mymail_option( 'send_limit' ) . '" class="small-text">', '<input type="text" name="mymail_options[send_period]" value="' . mymail_option( 'send_period' ) . '" class="small-text">' ) ?></p>
		<p class="description"><?php esc_html_e( 'Depending on your hosting provider you can increase these values', 'mymail' )?></p>
		<?php
$sent_this_period = get_transient( '_mymail_send_period', 0 );
$mails_left = max( 0, mymail_option( 'send_limit' ) - $sent_this_period );
$next_reset = get_option( '_transient_timeout__mymail_send_period_timeout' );

if ( !$next_reset || $next_reset < time() ) {
	$next_reset = time() + mymail_option( 'send_period' ) * 3600;
	$mails_left = mymail_option( 'send_limit' );
}
?>
		<p class="description"><?php printf( __( 'You can still send %1$s mails within the next %2$s', 'mymail' ), '<strong>' . number_format_i18n( $mails_left ) . '</strong>', '<strong title="' . date_i18n( $timeformat, $next_reset + $timeoffset, true ) . '">' . human_time_diff( $next_reset ) . '</strong>' ); ?> &ndash; <a href="<?php echo add_query_arg( 'reset-limits', 1 ) ?>"><?php esc_html_e( 'reset these limits', 'mymail' );?></a></p>

	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Split campaigns', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[split_campaigns]" value="1" <?php checked( mymail_option( 'split_campaigns' ) )?>> <?php esc_html_e( 'send campaigns simultaneously instead of one after the other', 'mymail' )?></label> </td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Pause campaigns', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[pause_campaigns]" value="1" <?php checked( mymail_option( 'pause_campaigns' ) );?>> <?php esc_html_e( 'pause campaigns if an error occurs', 'mymail' )?></label><p class="description"><?php esc_html_e( 'MyMail will change the status to "pause" if an error occur otherwise it tries to finish the campaign', 'mymail' );?></p></td>
	</tr>
	<tr valign="top">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Time between mails', 'mymail' )?></th>
			<td><p><input type="text" name="mymail_options[send_delay]" value="<?php echo mymail_option( 'send_delay' ); ?>" class="small-text"> <?php esc_html_e( 'milliseconds', 'mymail' );?></p><p class="description"><?php esc_html_e( 'define a delay between mails in milliseconds if you have problems with sending two many mails at once', 'mymail' );?></p>
		</td>
	</tr>
	<tr valign="top">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Max. Execution Time', 'mymail' )?></th>
			<td><p><input type="text" name="mymail_options[max_execution_time]" value="<?php echo mymail_option( 'max_execution_time', 0 ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'mymail' );?></p><p class="description"><?php esc_html_e( 'define a maximum execution time to prevent server timeouts. If set to zero, no time limit is imposed.', 'mymail' );?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Send Test', 'mymail' )?></th>
		<td>
		<input type="text" value="<?php echo $current_user->user_email ?>" autocomplete="off" class="form-input-tip" id="mymail_testmail">
		<input type="button" value="<?php esc_html_e( 'Send Test', 'mymail' )?>" class="button mymail_sendtest" data-role="basic">
		<div class="loading test-ajax-loading"></div>
		</td>
	</tr>
</table>

<?php

$deliverymethods = apply_filters( 'mymail_delivery_methods', array(
		'simple' => __( 'Simple', 'mymail' ),
		'smtp' => 'SMTP',
		'gmail' => 'Gmail',
	) );

$method = mymail_option( 'deliverymethod', 'simple' );

?>

<h3><?php esc_html_e( 'Delivery Method', 'mymail' );?></h3>
<div class="updated inline"><p><?php printf( __( 'You are currently sending with the %s delivery method', 'mymail' ), '<strong>' . $deliverymethods[$method] . '</strong>' ) ?></p></div>
<div id="deliverynav" class="nav-tab-wrapper hide-if-no-js">
	<?php foreach ( $deliverymethods as $id => $name ) {
?>
	<a class="nav-tab <?php if ( $method == $id ) {
		echo 'nav-tab-active';
	}
	?>" href="#<?php echo $id ?>"><?php echo $name ?></a>
	<?php }?>
	<a href="plugin-install.php?tab=search&s=mymail+revaxarts&plugin-search-input=Search+Plugins" class="alignright"><?php esc_html_e( 'search for more delivery methods', 'mymail' );?></a>
</div>

<input type="hidden" name="mymail_options[deliverymethod]" id="deliverymethod" value="<?php echo esc_attr( $method ); ?>" class="regular-text">

<div class="subtab" id="subtab-simple" <?php if ( $method == 'simple' ) {
	echo 'style="display:block"';
}
?>>
	<p class="description">
	<?php esc_html_e( 'use this option if you don\'t have access to a SMTP server or any other provided options', 'mymail' );?>
	</p>
	<?php $basicmethod = mymail_option( 'simplemethod', 'sendmail' );?>
	<table class="form-table">
		<tr valign="top">
			<td><label><input type="radio" name="mymail_options[simplemethod]" value="sendmail" <?php checked( $basicmethod, 'sendmail' )?> id="sendmail"> Sendmail</label>
			<div class="sendmailpath">
				<label>Sendmail Path: <input type="text" value="<?php echo mymail_option( 'sendmail_path' ); ?>" class="form-input-tip" name="mymail_options[sendmail_path]"></label>
			</div>
			</td>
		</tr>
		<tr valign="top">
			<td><label><input type="radio" name="mymail_options[simplemethod]" value="mail" <?php checked( $basicmethod, 'mail' )?>> PHPs mail() function</label></td>
		</tr>
		<tr valign="top">
			<td><label><input type="radio" name="mymail_options[simplemethod]" value="qmail" <?php checked( $basicmethod, 'qmail' )?>> QMail</label></td>
		</tr>
	</table>
</div>
<div class="subtab" id="subtab-smtp" <?php if ( $method == 'smtp' ) {
	echo 'style="display:block"';
}
?>>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">SMTP Host : Port</th>
			<td><input type="text" name="mymail_options[smtp_host]" value="<?php echo esc_attr( mymail_option( 'smtp_host' ) ); ?>" class="regular-text ">:<input type="text" name="mymail_options[smtp_port]" id="mymail_smtp_port" value="<?php echo intval( mymail_option( 'smtp_port' ) ); ?>" class="small-text smtp"></td>
		</tr>
		<tr valign="top">
			<th scope="row">Timeout</th>
			<td><span><input type="text" name="mymail_options[smtp_timeout]" value="<?php echo mymail_option( 'smtp_timeout' ); ?>" class="small-text"> <?php esc_html_e( 'seconds', 'mymail' );?></span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Secure connection', 'mymail' )?></th>
			<?php $secure = mymail_option( 'smtp_secure' ); ?>
			<td>
			<label><input type="radio" name="mymail_options[smtp_secure]" value="" <?php checked( !$secure )?> class="smtp secure" data-port="25"> <?php esc_html_e( 'none', 'mymail' )?></label>
			<label><input type="radio" name="mymail_options[smtp_secure]" value="ssl" <?php checked( $secure == 'ssl' )?> class="smtp secure" data-port="465"> SSL</label>
			<label><input type="radio" name="mymail_options[smtp_secure]" value="tls" <?php checked( $secure == 'tls' )?> class="smtp secure" data-port="465"> TLS</label>
			 </td>
		</tr>
		<tr valign="top">
			<th scope="row">SMTPAuth</th>
			<td>
			<?php $smtpauth = mymail_option( 'smtp_auth' );?>
			<label>
			<select name="mymail_options[smtp_auth]">
				<option value="0" <?php selected( !$smtpauth );?>><?php esc_html_e( 'none', 'mymail' ) ?></option>
				<option value="PLAIN" <?php selected( 'PLAIN', $smtpauth );?>>Plain</option>
				<option value="LOGIN" <?php selected( 'LOGIN', $smtpauth );?>>Login</option>
				<option value="CRAM-MD5" <?php selected( 'CRAM-MD5', $smtpauth );?>>CRAM-MD5</option>
			</select></label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Username', 'mymail' )?></th>
			<td><input type="text" name="mymail_options[smtp_user]" value="<?php echo esc_attr( mymail_option( 'smtp_user' ) ); ?>" class="regular-text"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Password', 'mymail' )?></th>
			<td><input type="password" name="mymail_options[smtp_pwd]" value="<?php echo esc_attr( mymail_option( 'smtp_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Self Signed Certificates', 'mymail' )?></th>
			<td><label title="<?php esc_html_e( 'Enabling this option may solve connection problems to SMTP servers', 'mymail' ); ?>"><input type="checkbox" name="mymail_options[allow_self_signed]" value="1" <?php checked( mymail_option( 'allow_self_signed' ) );?>> <?php esc_html_e( 'allow self signed certificates', 'mymail' )?></label>
			</td>
		</tr>
	</table>
</div>
<div class="subtab" id="subtab-gmail" <?php if ( $method == 'gmail' ) {
	echo 'style="display:block"';
}
?>>
	<p class="description">
	<?php esc_html_e( 'Gmail has a limit of 500 mails within 24 hours! Also sending a mail can take up to one second which is quite long. This options is only recommended for few subscribers. DKIM works only if set the from address to your Gmail address.', 'mymail' );?>
	</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Username', 'mymail' )?></th>
			<td><input type="text" name="mymail_options[gmail_user]" value="<?php echo esc_attr( mymail_option( 'gmail_user' ) ); ?>" class="regular-text" placeholder="@gmail.com"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Password', 'mymail' )?></th>
			<td><input type="password" name="mymail_options[gmail_pwd]" value="<?php echo esc_attr( mymail_option( 'gmail_pwd' ) ); ?>" class="regular-text" autocomplete="new-password"></td>
		</tr>
	</table>
</div>
<?php foreach ( $deliverymethods as $id => $name ) {
	if ( in_array( $id, array( 'simple', 'smtp', 'gmail' ) ) ) {
		continue;
	}

?>
<div class="subtab" id="subtab-<?php echo $id ?>" <?php if ( $method == $id ) {
		echo 'style="display:block"';
	}
	?>>
	<?php do_action( 'mymail_deliverymethod_tab' )?>
	<?php do_action( 'mymail_deliverymethod_tab_' . $id )?>
</div>
<?php }?>
