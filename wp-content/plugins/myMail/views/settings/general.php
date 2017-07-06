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
		<th scope="row"><?php esc_html_e( 'From Name', 'mymail' )?> *</th>
		<td><input type="text" name="mymail_options[from_name]" value="<?php echo esc_attr( mymail_option( 'from_name' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'mymail' )?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'From Email', 'mymail' )?> *</th>
		<td><input type="text" name="mymail_options[from]" value="<?php echo esc_attr( mymail_option( 'from' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'mymail' )?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Reply-to Email', 'mymail' )?> *</th>
		<td><input type="text" name="mymail_options[reply_to]" value="<?php echo esc_attr( mymail_option( 'reply_to' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The address users can reply to', 'mymail' )?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Default Template', 'mymail' )?> *</th>
		<td><p><select name="mymail_options[default_template]" class="postform">
		<?php
$templates = mymail( 'templates' )->get_templates();
$selected = mymail_option( 'default_template' );
foreach ( $templates as $slug => $data ) {
?>
			<option value="<?php echo $slug ?>"<?php if ( $slug == $selected ) {
		echo " selected";
	}
	?>><?php echo esc_attr( $data['name'] ) ?></option>
		<?php
}
?>
		</select> <a href="edit.php?post_type=newsletter&page=mymail_templates"><?php esc_html_e( 'show Templates', 'mymail' );?></a> | <a href="edit.php?post_type=newsletter&page=mymail_templates&more"><?php esc_html_e( 'get more', 'mymail' )?></a>
		</p></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Send delay', 'mymail' )?> *</th>
		<td><input type="text" name="mymail_options[send_offset]" value="<?php echo esc_attr( mymail_option( 'send_offset' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'The default delay in minutes for sending campaigns.', 'mymail' )?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Delivery by Time Zone', 'mymail' )?> *</th>
		<td><input type="checkbox" name="mymail_options[timezone]" value="1" <?php checked( mymail_option( 'timezone' ) );?>> <?php esc_html_e( 'Send Campaigns based on the subscribers timezone if known', 'mymail' )?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Embed Images', 'mymail' )?> *</th>
		<td><label><input type="checkbox" name="mymail_options[embed_images]" value="1" <?php checked( mymail_option( 'embed_images' ) );?>> <?php esc_html_e( 'Embed images in the mail', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[module_thumbnails]" value="1" <?php checked( mymail_option( 'module_thumbnails' ) );?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Post List Count', 'mymail' )?></th>
		<td><input type="text" name="mymail_options[post_count]" value="<?php echo esc_attr( mymail_option( 'post_count' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Number of posts or images displayed at once in the editbar.', 'mymail' )?></span></td>
	</tr>
	<?php if ( function_exists( 'find_core_auto_update' ) ): //only 3.7+ ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Auto Update', 'mymail' )?></th>
					<td>
					<?php
	$is = mymail_option( 'autoupdate', 'minor' );
$types = array(
	'1' => __( 'enabled', 'mymail' ),
	'0' => __( 'disabled', 'mymail' ),
	'minor' => __( 'only minor updates', 'mymail' ),
);
?>
					<select name="mymail_options[autoupdate]">
						<?php foreach ( $types as $value => $name ) {?>
						<option value="<?php echo $value; ?>" <?php selected( $is == $value )?>><?php echo $name; ?></option>
						<?php }?>
					</select>
					<p class="description"><?php esc_html_e( 'auto updates are recommended for important fixes.', 'mymail' );?></p>
					</td>
				</tr>
				<?php endif;?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'System Mails', 'mymail' )?>
		<p class="description"><?php esc_html_e( 'decide how MyMail uses the wp_mail function', 'mymail' );?></p></th>
		<td>
		<p><label><input type="radio" name="mymail_options[system_mail]" class="system_mail" value="0" <?php checked( !mymail_option( 'system_mail' ) )?>> <?php esc_html_e( 'Do not use MyMail for outgoing WordPress mails', 'mymail' )?></label></p>
		<p><label><input type="radio" name="mymail_options[system_mail]" class="system_mail" value="1" <?php checked( mymail_option( 'system_mail' ) == 1 )?>> <?php esc_html_e( 'Use MyMail for all outgoing WordPress mails', 'mymail' )?></label><br>
			<label><input type="radio" name="mymail_options[system_mail]" class="system_mail" value="template" <?php checked( mymail_option( 'system_mail' ) == 'template' )?>> <?php esc_html_e( 'Use only the template for all outgoing WordPress mails', 'mymail' )?></label></p>
		<p>&nbsp;&nbsp;<?php esc_html_e( 'use', 'mymail' );?><select name="mymail_options[system_mail_template]" class="system_mail_template" <?php echo !mymail_option( 'system_mail' ) ? 'disabled' : '' ?>>
		<?php
$selected = mymail_option( 'system_mail_template', 'notification.html' );
foreach ( $templatefiles as $slug => $filedata ) {
	if ( $slug == 'index.html' ) {
		continue;
	}

?>
			<option value="<?php echo $slug ?>"<?php selected( $slug == $selected )?>><?php echo esc_attr( $filedata['label'] ) ?> (<?php echo $slug ?>)</option>
		<?php
}
?>
		</select></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'CharSet', 'mymail' )?> / <?php esc_html_e( 'Encoding', 'mymail' )?></th>
		<td>
		<?php
$is = mymail_option( 'charset', 'UTF-8' );
$charsets = array(
	'UTF-8' => 'Unicode 8',
	'ISO-8859-1' => 'Western European',
	'ISO-8859-2' => 'Central European',
	'ISO-8859-3' => 'South European',
	'ISO-8859-4' => 'North European',
	'ISO-8859-5' => 'Latin/Cyrillic',
	'ISO-8859-6' => 'Latin/Arabic',
	'ISO-8859-7' => 'Latin/Greek',
	'ISO-8859-8' => 'Latin/Hebrew',
	'ISO-8859-9' => 'Turkish',
	'ISO-8859-10' => 'Nordic',
	'ISO-8859-11' => 'Latin/Thai',
	'ISO-8859-13' => 'Baltic Rim',
	'ISO-8859-14' => 'Celtic',
	'ISO-8859-15' => 'Western European revision',
	'ISO-8859-16' => 'South-Eastern European',
)?>
		<select name="mymail_options[charset]">
			<?php foreach ( $charsets as $code => $region ) {?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code )?>><?php echo $code; ?> - <?php echo $region; ?></option>
			<?php }?>
		</select>
		<?php
$is = mymail_option( 'encoding', '8bit' );
$encoding = array(
	'8bit' => '8bit',
	'7bit' => '7bit',
	'binary' => 'binary',
	'base64' => 'base64',
	'quoted-printable' => 'quoted-printable',
)?> /
		<select name="mymail_options[encoding]">
			<?php foreach ( $encoding as $code ) {?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code )?>><?php echo $code; ?></option>
			<?php }?>
		</select>
		<p class="description"><?php esc_html_e( 'change Charset and encoding of your mails if you have problems with some characters', 'mymail' );?></p>
		</td>
	</tr>
	<?php
$geoip = mymail_option( 'trackcountries' );
$geoipcity = mymail_option( 'trackcities' );
if ( isset( $_GET['nogeo'] ) ) {
	$geoip = $geoipcity = false;
}

?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Track Geolocation', 'mymail' )?>
		<div class="loading geo-ajax-loading"></div></th>
		<td>
		<p><label><input type="checkbox" id="mymail_geoip" name="mymail_options[trackcountries]" value="1" <?php checked( $geoip );?>> <?php esc_html_e( 'Track Countries in Campaigns', 'mymail' )?></label></p>
		<p><button id="load_country_db" class="button-primary" data-type="country" <?php disabled( !$geoip );?>><?php ( is_file( mymail_option( 'countries_db' ) ) ) ? esc_html_e( 'Update Country Database', 'mymail' ) : esc_html_e( 'Load Country Database', 'mymail' );?></button> <?php esc_html_e( 'or', 'mymail' );?> <a id="upload_country_db_btn" href="#"><?php esc_html_e( 'upload file', 'mymail' );?></a>
		</p>
		<p id="upload_country_db" class="hidden">
			<input type="file" name="country_db_file"> <input type="submit" class="button" value="<?php esc_html_e( 'Upload', 'mymail' )?>" />
			<br><span class="description"><?php esc_html_e( 'upload the GeoIPv6.dat you can find in the package here:', 'mymail' );?> <a href="http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz">http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz</a></span>
		</p>

		<input id="country_db_path" type="text" name="mymail_options[countries_db]" class="widefat" value="<?php echo mymail_option( 'countries_db' ) ?>" placeholder="<?php echo MYMAIL_UPLOAD_DIR . '/GeoIPv6.dat' ?>">
		<p><label><input type="checkbox" id="mymail_geoipcity" name="mymail_options[trackcities]" value="1" <?php checked( $geoipcity );?><?php disabled( !$geoip );?>> <?php esc_html_e( 'Track Cities in Campaigns', 'mymail' )?></label></p>
		<p><button id="load_city_db" class="button-primary" data-type="city" <?php disabled( !$geoipcity );?>><?php ( is_file( mymail_option( 'cities_db' ) ) ) ? esc_html_e( 'Update City Database', 'mymail' ) : esc_html_e( 'Load City Database', 'mymail' );?></button> <?php esc_html_e( 'or', 'mymail' );?> <a id="upload_city_db_btn" href="#"><?php esc_html_e( 'upload file', 'mymail' );?></a>
		</p>
		<p id="upload_city_db" class="hidden">
			<input type="file" name="city_db_file"> <input type="submit" class="button" value="<?php esc_html_e( 'Upload', 'mymail' )?>" />
			<br><span class="description"><?php esc_html_e( 'upload the GeoLiteCity.dat you can find in the package here:', 'mymail' );?> <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz</a></span>
		</p>
		<p class="description"><?php esc_html_e( 'The city DB is about 12 MB. It can take a while to load it', 'mymail' );?></p>
		<input id="city_db_path" type="text" name="mymail_options[cities_db]" class="widefat" value="<?php echo mymail_option( 'cities_db' ) ?>" placeholder="<?php echo MYMAIL_UPLOAD_DIR . '/GeoIPCity.dat' ?>">

		</td>
	</tr>
	<?php if ( $geoip && is_file( mymail_option( 'countries_db' ) ) ): ?>
	<tr valign="top">
		<th scope="row"></th>
		<td>
	<?php if ( mymail_is_local() ): ?>
	<div class="error inline"><p><strong><?php esc_html_e( 'Geolocation is not available on localhost!', 'mymail' )?></strong></p></div>
	<?php endif;?>
		<p class="description"><?php esc_html_e( 'If you don\'t find your country down below the geo database is missing or corrupt', 'mymail' )?></p>
		<p>
		<strong><?php esc_html_e( 'Your IP', 'mymail' )?>:</strong> <?php echo mymail_get_ip() ?><br>
		<strong><?php esc_html_e( 'Your country', 'mymail' )?>:</strong> <?php echo mymail_ip2Country( '', 'name' ) ?><br>&nbsp;&nbsp;<strong><?php esc_html_e( 'Last update', 'mymail' )?>: <?php echo date( $timeformat, filemtime( mymail_option( 'countries_db' ) ) + $timeoffset ) ?> </strong><br>
	<?php if ( $geoipcity && is_file( mymail_option( 'cities_db' ) ) ): ?>
		<strong><?php esc_html_e( 'Your city', 'mymail' )?>:</strong> <?php echo mymail_ip2City( '', 'city' ) ?><br>&nbsp;&nbsp;<strong><?php esc_html_e( 'Last update', 'mymail' )?>: <?php echo date( $timeformat, filemtime( mymail_option( 'cities_db' ) ) + $timeoffset ) ?></strong>
	<?php endif;?>
		</p>
		<p class="description">This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com" class="external">http://www.maxmind.com</a></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Google API Key', 'mymail' )?>
		</th>
		<td><input type="password" name="mymail_options[google_api_key]" value="<?php echo esc_attr( mymail_option( 'google_api_key' ) ); ?>" class="regular-text" autocomplete="new-password">
		<p class="description">
		<?php esc_html_e( 'The Google API key is used to display Maps for MyMail on the back end.', 'mymail' ); ?><br>
		<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" class="external"><?php esc_html_e( 'Get your Google API Key.', 'mymail' ); ?></a></p>
		</td>
	</tr>
	<?php
	endif;
	?>
</table>
<p class="description">* <?php esc_html_e( 'can be changed in each campaign', 'mymail' )?></p>
	<?php if ( get_bloginfo( 'language' ) != 'en-US' && 'translator string' !== strip_tags( _x( 'translator string', 'Translators: put your personal info here to display it on the settings page. Leave blank for no info', 'mymail' ), '<a>' ) ): ?>
<p class="alignright tiny"><?php printf( __( 'This plugin has been translated by %s', 'mymail' ), _x( 'translator string', 'Translators: put your personal info here to display it on the settings page. Leave blank for no info', 'mymail' ) ); ?><div class="clearfix"></div></p>
	<?php endif;?>
