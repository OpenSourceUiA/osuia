<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


if ( $translations ) {
	$url = add_query_arg( array(
			'action' => 'do-translation-upgrade',
			'_wpnonce' => wp_create_nonce( 'upgrade-translations' ),
		), network_admin_url( 'update-core.php' ) );
?>
	<div class="error inline"><p><strong><?php esc_html_e( 'an update to your language is available!', 'mymail' )?></strong> <a class="button button-primary button-small" href="<?php echo esc_url( $url ) ?>"><?php esc_html_e( 'update now', 'mymail' );?></a></p></div>
	<?php
}
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Subscription Form', 'mymail' )?><p class="description"><?php esc_html_e( 'Define messages for the subscription form', 'mymail' );?>.<br><?php if ( mymail_option( 'homepage' ) ) {
	printf( __( 'Some text can get defined on the %s as well', 'mymail' ), '<a href="post.php?post=' . mymail_option( 'homepage' ) . '&action=edit">Newsletter Homepage</a>' );
}
?></p></th>
		<td>
		<div class="mymail_text"><label><?php esc_html_e( 'Confirmation', 'mymail' );?>:</label> <input type="text" name="mymail_texts[confirmation]" value="<?php echo esc_attr( mymail_text( 'confirmation' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Successful', 'mymail' );?>:</label> <input type="text" name="mymail_texts[success]" value="<?php echo esc_attr( mymail_text( 'success' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Error Message', 'mymail' );?>:</label> <input type="text" name="mymail_texts[error]" value="<?php echo esc_attr( mymail_text( 'error' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Unsubscribe', 'mymail' );?>:</label> <input type="text" name="mymail_texts[unsubscribe]" value="<?php echo esc_attr( mymail_text( 'unsubscribe' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Unsubscribe Error', 'mymail' );?>:</label> <input type="text" name="mymail_texts[unsubscribeerror]" value="<?php echo esc_attr( mymail_text( 'unsubscribeerror' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Profile Update', 'mymail' );?>:</label> <input type="text" name="mymail_texts[profile_update]" value="<?php echo esc_attr( mymail_text( 'profile_update' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Newsletter Sign up', 'mymail' );?>:</label> <input type="text" name="mymail_texts[newsletter_signup]" value="<?php echo esc_attr( mymail_text( 'newsletter_signup' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Field Labels', 'mymail' )?><p class="description"><?php esc_html_e( 'Define texts for the labels of forms. Custom field labels can be defined on the Subscribers tab', 'mymail' );?></p></th>
		<td>
		<div class="mymail_text"><label><?php esc_html_e( 'Email', 'mymail' );?>:</label> <input type="text" name="mymail_texts[email]" value="<?php echo esc_attr( mymail_text( 'email' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'First Name', 'mymail' );?>:</label> <input type="text" name="mymail_texts[firstname]" value="<?php echo esc_attr( mymail_text( 'firstname' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Last Name', 'mymail' );?>:</label> <input type="text" name="mymail_texts[lastname]" value="<?php echo esc_attr( mymail_text( 'lastname' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Lists', 'mymail' );?>:</label> <input type="text" name="mymail_texts[lists]" value="<?php echo esc_attr( mymail_text( 'lists' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Submit Button', 'mymail' );?>:</label> <input type="text" name="mymail_texts[submitbutton]" value="<?php echo esc_attr( mymail_text( 'submitbutton' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Profile Button', 'mymail' );?>:</label> <input type="text" name="mymail_texts[profilebutton]" value="<?php echo esc_attr( mymail_text( 'profilebutton' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Unsubscribe Button', 'mymail' );?>:</label> <input type="text" name="mymail_texts[unsubscribebutton]" value="<?php echo esc_attr( mymail_text( 'unsubscribebutton' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Mail', 'mymail' )?><p class="description"><?php esc_html_e( 'Define texts for the mails', 'mymail' );?></p></th>
		<td>
		<div class="mymail_text"><label><?php esc_html_e( 'Unsubscribe Link', 'mymail' );?>:</label> <input type="text" name="mymail_texts[unsubscribelink]" value="<?php echo esc_attr( mymail_text( 'unsubscribelink' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Webversion Link', 'mymail' );?>:</label> <input type="text" name="mymail_texts[webversion]" value="<?php echo esc_attr( mymail_text( 'webversion' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Forward Link', 'mymail' );?>:</label> <input type="text" name="mymail_texts[forward]" value="<?php echo esc_attr( mymail_text( 'forward' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'Profile Link', 'mymail' );?>:</label> <input type="text" name="mymail_texts[profile]" value="<?php echo esc_attr( mymail_text( 'profile' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Other', 'mymail' )?></th>
		<td>
		<div class="mymail_text"><label><?php esc_html_e( 'already registered', 'mymail' );?>:</label> <input type="text" name="mymail_texts[already_registered]" value="<?php echo esc_attr( mymail_text( 'already_registered' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'new confirmation message', 'mymail' );?>:</label> <input type="text" name="mymail_texts[new_confirmation_sent]" value="<?php echo esc_attr( mymail_text( 'new_confirmation_sent' ) ); ?>" class="regular-text"></div>
		<div class="mymail_text"><label><?php esc_html_e( 'enter your email', 'mymail' );?>:</label> <input type="text" name="mymail_texts[enter_email]" value="<?php echo esc_attr( mymail_text( 'enter_email' ) ); ?>" class="regular-text"></div>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Change Language', 'mymail' )?></th>
		<td>
			<p class="description">
			<?php esc_html_e( 'change language of texts if available to', 'mymail' );?>
<?php

$dir = defined( 'WP_LANG_DIR' ) ? WP_LANG_DIR : MYMAIL_DIR . '/languages/';
$files = array();

if ( is_dir( $dir ) ) {
	$files = list_files( $dir );
	$files = preg_grep( '/mymail-(.*)\.po$/', $files );
}

?>
					<select name="language-file">
						<option<?php selected( preg_match( '#^en_#', $locale ) );?> value="en_US"><?php esc_html_e( 'English', 'mymail' );?> (en_US)</option>
<?php
foreach ( $files as $file ) {
	$lang = str_replace( array( '.po', 'mymail-' ), '', basename( $file ) );
?>
						<option<?php selected( $lang == $locale );?> value="<?php echo $lang; ?>"><?php echo $lang; ?></option>
						<?php
}
?>
					</select>
			<button name="change-language" class="button"><?php esc_html_e( 'change language', 'mymail' );?></button>
			<br class="clearfix">
			</p>
		</td>
	</tr>
</table>
