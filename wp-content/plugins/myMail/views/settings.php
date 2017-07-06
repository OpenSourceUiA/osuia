<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


global $wpdb, $current_user, $wp_post_statuses, $wp_roles, $locale;

$customfields = mymail()->get_custom_fields();
$roles = $wp_roles->get_names();
$translations = get_transient( '_mymail_translation' );

?>
<form id="mymail-settings-form" method="post" action="options.php" autocomplete="off" enctype="multipart/form-data">
<input style="display:none"><input type="password" style="display:none">
<div class="wrap">
	<p class="alignright">
		<input type="submit" class="submit-form button-primary" value="<?php esc_html_e( 'Save Changes', 'mymail' )?>" disabled />
	</p>
<div class="icon32" id="icon-options-general"><br></div>
<h2><?php esc_html_e( 'Newsletter Settings', 'mymail' )?></h2>
<?php

$active = count( mymail_get_active_campaigns() );

$templatefiles = mymail( 'templates' )->get_files( mymail_option( 'default_template' ) );
$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
$timeoffset = mymail( 'helper' )->gmt_offset( true );

if ( $active ) {
	echo '<div class="error inline"><p>' . sprintf( _n( '%d campaign is active. You should pause it before you change the settings!', '%d campaigns are active. You should pause them before you change the settings!', $active, 'mymail' ), $active ) . '</p></div>';
}

?>
<?php wp_nonce_field( 'mymail_nonce', 'mymail_nonce', false );?>
<?php settings_fields( 'newsletter_settings' );?>
<?php do_settings_sections( 'newsletter_settings' );?>

<?php
$sections = apply_filters( 'mymail_setting_sections', array(
		'general' => __( 'General', 'mymail' ),
		'frontend' => __( 'Frontend', 'mymail' ),
		'subscribers' => __( 'Subscribers', 'mymail' ),
		'wordpress-users' => __( 'WordPress Users', 'mymail' ),
		'texts' => __( 'Texts', 'mymail' ) . ( $translations ? ' <span class="update-translation-available wp-ui-highlight" title="' . __( 'update available', 'mymail' ) . '"><span>!</span></span>' : '' ),
		'tags' => __( 'Tags', 'mymail' ),
		'delivery' => __( 'Delivery', 'mymail' ),
		'cron' => __( 'Cron', 'mymail' ),
		'capabilities' => __( 'Capabilities', 'mymail' ),
		'bounce' => __( 'Bouncing', 'mymail' ),
		'authentication' => __( 'Authentication', 'mymail' ),
		'purchasecode' => __( 'Purchase Code', 'mymail' ),
		'advanced' => __( 'Advanced', 'mymail' ),
		'system_info' => __( 'System Info', 'mymail' ),
	) );

if ( !current_user_can( 'mymail_manage_capabilities' ) && !current_user_can( 'manage_options' ) ) {
	unset( $sections['capabilities'] );
}

if ( get_option( 'mymail_purchasecode_disabled' ) && mymail_option( 'purchasecode' ) ) {
	unset( $sections['purchasecode'] );
}

?>
<input type="hidden" name="mymail_options[purchasecode]" value="<?php echo esc_attr( mymail_option( 'purchasecode' ) ); ?>">

	<div class="settings-wrap">
	<div class="settings-nav">
		<div class="mainnav contextual-help-tabs hide-if-no-js">
		<ul>
		<?php foreach ( $sections as $id => $name ) {?>
			<li><a href="#<?php echo $id; ?>" class="nav-<?php echo $id; ?>"><?php echo $name; ?></a></li>
		<?php }?>
		<?php do_action( 'mymail_settings_tabs' )?>
		</ul>
		</div>
	</div>

	<div class="settings-tabs"> <div class="tab"><h3>&nbsp;</h3></div>

	<?php foreach ( $sections as $id => $name ) {
?>
	<div id="tab-<?php echo esc_attr( $id ) ?>" class="tab">
		<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
		<?php do_action( 'mymail_section_tab' )?>
		<?php do_action( 'mymail_section_tab_' . $id )?>

		<?php if ( file_exists( MYMAIL_DIR . 'views/settings/' . $id . '.php' ) ) {
		include MYMAIL_DIR . 'views/settings/' . $id . '.php';
	}
?>

	</div>
	<?php }?>

<?php
$extra_sections = apply_filters( 'mymail_extra_setting_sections', array() );

foreach ( $extra_sections as $id => $name ) {?>
	<div id="tab-<?php echo esc_attr( $id ) ?>" class="tab">
		<h3><?php echo esc_html( strip_tags( $name ) ); ?></h3>
		<?php do_action( 'mymail_section_tab' )?>
		<?php do_action( 'mymail_section_tab_' . $id )?>
	</div>
	<?php }?>
	<?php if ( is_super_admin() ): ?>
		<p class="resetbutton">
			<a onclick='return confirm("<?php esc_html_e( 'Do you really like to reset the options? This cannot be undone!', 'mymail' );?>");' href="options-general.php?page=newsletter-settings&reset-settings=1&_wpnonce=<?php echo wp_create_nonce( 'mymail-reset-settings' ) ?>"><?php esc_html_e( 'Reset all settings', 'mymail' );?></a>
		</p>
	<?php endif;?>
		<p class="submitbutton">
			<input type="submit" class="submit-form button-primary" value="<?php esc_html_e( 'Save Changes', 'mymail' )?>" disabled />
		</p>
	</div>

	</div>

	<?php do_action( 'mymail_settings' )?>

	<input type="password" class="hidden" name="mymail_foo" value="bar"><input class="hidden" type="password" name="mymail_bar" value="foo">

	<input type="text" class="hidden" name="mymail_options[got_url_rewrite]" value="<?php echo esc_attr( mymail_option( 'got_url_rewrite' ) ); ?>">
	<input type="text" class="hidden" name="mymail_options[profile_form]" value="<?php echo esc_attr( mymail_option( 'profile_form', 0 ) ); ?>">
	<input type="text" class="hidden" name="mymail_options[ID]" value="<?php echo esc_attr( mymail_option( 'ID' ) ); ?>">

	<br class="clearfix">
<span id="settingsloaded"></span>
</div>
</form>
