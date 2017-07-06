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
		<th scope="row"><?php esc_html_e( 'Sync WordPress Users', 'mymail' )?></th>
		<td>
		<label><input type="checkbox" name="mymail_options[sync]" value="1" <?php checked( mymail_option( 'sync' ) )?> id="sync_list_check"> <?php esc_html_e( 'Sync WordPress Users with Subscribers', 'mymail' )?></label>
		<p class="description"><?php esc_html_e( 'keep WordPress User data and Subscriber data synchronized. Only affects existing Subscribers', 'mymail' );?></p>
		</td>
	</tr>
</table>
<div id="sync_list" <?php if ( !mymail_option( 'sync' ) ) {
	echo 'style="display:none"';
}
?>>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Meta Data List', 'mymail' )?><p class="description"><?php esc_html_e( 'select the custom field which should sync with a certain meta field', 'mymail' )?></p></th>
		<td>

		<?php
$synclist = mymail_option( 'synclist', array() );
$synclist = array( '_' => '_' ) + $synclist;
$meta_values = wp_parse_args( mymail( 'helper' )->get_wpuser_meta_fields(), array( 'user_login', 'user_nicename', 'user_email', 'user_url', 'display_name', 'first_name', 'last_name', 'nickname' ) );
$i = 0;
foreach ( $synclist as $field => $metavalue ) {
	$customfield_dropdown = '<option value="-1">--</option><optgroup label="' . __( 'Custom Fields', 'mymail' ) . '">';
	foreach ( array( 'email' => __( 'Email', 'mymail' ), 'firstname' => __( 'Firstname', 'mymail' ), 'lastname' => __( 'Lastname', 'mymail' ) ) as $key => $name ) {
		$customfield_dropdown .= '<option value="' . $key . '" ' . selected( $field, $key, false ) . '>' . $name . '</option>';
	}
	foreach ( $customfields as $key => $data ) {
		$customfield_dropdown .= '<option value="' . $key . '" ' . selected( $field, $key, false ) . '>' . $data['name'] . '</option>';
	}
	$customfield_dropdown .= '</optgroup>';
	$meta_value_dropdown = '<option value="-1">--</option><optgroup label="' . __( 'Meta Fields', 'mymail' ) . '">';
	foreach ( $meta_values as $key ) {
		$meta_value_dropdown .= '<option value="' . $key . '" ' . selected( $metavalue, $key, false ) . '>' . $key . '</option>';
	}
	$meta_value_dropdown .= '</optgroup>';
?>
			<div class="mymail_syncitem" title="<?php printf( __( '%s syncs with %s', 'mymail' ), $field, $metavalue ) ?>">
				<select name="mymail_options[synclist][<?php echo $i ?>][meta]"><?php echo $meta_value_dropdown ?>:</select> &#10234;
				<select name="mymail_options[synclist][<?php echo $i ?>][field]"><?php echo $customfield_dropdown ?>:</select>
				<a class="remove-sync-item">&#10005;</a>
			</div>
			<?php $i++;}?>
			<a class="button" id="add_sync_item"><?php esc_html_e( 'add', 'mymail' )?></a>
		</td>
	</tr>
</table>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><p class="description"><?php esc_html_e( 'manually sync all existing users based on the above settings. (save required)', 'mymail' );?></p></th>
		<td>

		<p>
		<button class="button sync-button" id="sync_subscribers_wp"><?php echo __( 'Subscribers', 'mymail' ) . ' &#x21D2; ' . __( 'WordPress Users', 'mymail' ) ?></button>
		<button class="button sync-button" id="sync_wp_subscribers"><?php echo __( 'WordPress Users', 'mymail' ) . ' &#x21D2; ' . __( 'Subscribers', 'mymail' ) ?></button>
		<span class="loading sync-ajax-loading"></span>
		</p>
		</td>
	</tr>
</table>
</div>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Delete Subscriber', 'mymail' )?></th>
		<td>
		<label><input type="checkbox" name="mymail_options[delete_wp_subscriber]" value="1" <?php checked( mymail_option( 'delete_wp_subscriber' ) )?>> <?php esc_html_e( 'Delete Subscriber if the WordPress User gets deleted', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Delete WordPress User', 'mymail' )?></th>
		<td>
		<label>
		<?php if ( !current_user_can( 'delete_users' ) ): ?>
		<input type="hidden" name="mymail_options[delete_wp_user]" value="<?php echo !!mymail_option( 'delete_wp_user' ) ?>">
		<input type="checkbox" name="mymail_options[delete_wp_user]" value="1" <?php checked( mymail_option( 'delete_wp_user' ) )?> disabled readonly>
		<?php else: ?>
		<input type="checkbox" name="mymail_options[delete_wp_user]" value="1" <?php checked( mymail_option( 'delete_wp_user' ) )?>>
		<?php endif;?>

		<?php esc_html_e( 'Delete WordPress User if the Subscriber gets deleted', 'mymail' )?></label>
			<p class="description"><?php esc_html_e( 'Attention! This option will remove assigned WordPress Users without further notice. You must have the capability to delete WordPress Users. Administrators and the current user can not get deleted with this option', 'mymail' )?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Registered Users', 'mymail' )?></th>
		<td>
		<?php if ( get_option( 'users_can_register' ) ): ?>
		<label><input type="checkbox" name="mymail_options[register_signup]" value="1" <?php checked( mymail_option( 'register_signup' ) )?> class="users-register" data-section="users-register_signup"> <?php esc_html_e( 'new WordPress users can choose to sign up on the register page', 'mymail' )?></label>
		<?php else: ?>
		<p class="description"><?php printf( __( 'allow %s to your blog to enable this option', 'mymail' ), '<a href="options-general.php">' . __( 'users to subscribe', 'mymail' ) . '</a>' ); ?></p>
		<?php endif;?>
		</td>
	</tr>
</table>
<div id="users-register_signup" <?php if ( !get_option( 'users_can_register' ) || !mymail_option( 'register_signup' ) ) {
		echo ' style="display:none"';
	}
?>>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"></th>
			<td>
			<label><input type="checkbox" name="mymail_options[register_signup_checked]" value="1" <?php checked( mymail_option( 'register_signup_checked' ) )?>> <?php esc_html_e( 'checked by default', 'mymail' )?></label>
			<br><label><input type="checkbox" name="mymail_options[register_signup_confirmation]" value="1" <?php checked( mymail_option( 'register_signup_confirmation' ) )?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'mymail' );?></label>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'mymail' );?></p>
			<?php

mymail( 'lists' )->print_it( null, null, 'mymail_options[register_signup_lists]', false, mymail_option( 'register_signup_lists' ) );

?>
			</td>
		</tr>
	</table>
</div>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'New Comments', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[register_comment_form]" value="1" <?php checked( mymail_option( 'register_comment_form' ) )?> class="users-register" data-section="users-register_comment_form"> <?php esc_html_e( 'allow users to signup on the comment form if they are currently not subscribed to any list', 'mymail' );?></label>
		</td>
	</tr>
</table>
<div id="users-register_comment_form" <?php if ( !mymail_option( 'register_comment_form' ) ) {
	echo ' style="display:none"';
}
?>>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"></th>
			<td>
			<p><label><input type="checkbox" name="mymail_options[register_comment_form_checked]" value="1" <?php checked( mymail_option( 'register_comment_form_checked' ) )?>> <?php esc_html_e( 'checked by default', 'mymail' )?></label></p>
			<p><?php esc_html_e( 'sign up only if comment is', 'mymail' );?><br>&nbsp;&nbsp;

			<label><input type="checkbox" name="mymail_options[register_comment_form_status][]" value="1" <?php checked( in_array( '1', mymail_option( 'register_comment_form_status', array() ) ), true )?>> <?php esc_html_e( 'approved', 'mymail' );?></label>
			<label><input type="checkbox" name="mymail_options[register_comment_form_status][]" value="0" <?php checked( in_array( '0', mymail_option( 'register_comment_form_status', array() ) ), true )?>> <?php esc_html_e( 'not approved', 'mymail' );?></label>
			<label><input type="checkbox" name="mymail_options[register_comment_form_status][]" value="spam" <?php checked( in_array( 'spam', mymail_option( 'register_comment_form_status', array() ) ), true )?>> <?php esc_html_e( 'spam', 'mymail' );?></label>
			</p>
			<br><label><input type="checkbox" name="mymail_options[register_comment_form_confirmation]" value="1" <?php checked( mymail_option( 'register_comment_form_confirmation' ) )?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'mymail' );?></label>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'mymail' );?></p>
			<?php

mymail( 'lists' )->print_it( null, null, 'mymail_options[register_comment_form_lists]', false, mymail_option( 'register_comment_form_lists' ) );

?></td>
		</tr>
	</table>
</div>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Others', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[register_other]" value="1" <?php checked( mymail_option( 'register_other' ) )?> class="users-register" data-section="users-register_other"> <?php esc_html_e( 'add people who are added via the backend or any third party plugin', 'mymail' );?></label>
		</td>
	</tr>
</table>
<div id="users-register_other" <?php if ( !mymail_option( 'register_other' ) ) {
	echo ' style="display:none"';
}
?>>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"></th>
			<td>
			<p><label><input type="checkbox" name="mymail_options[register_other_confirmation]" value="1" <?php checked( mymail_option( 'register_other_confirmation' ) )?>> <?php esc_html_e( 'send confirmation (double-opt-in)', 'mymail' );?></label></p>
			<p class="description"><?php esc_html_e( 'Subscribe them to these lists:', 'mymail' );?></p>
			<?php

mymail( 'lists' )->print_it( null, null, 'mymail_options[register_other_lists]', false, mymail_option( 'register_other_lists' ) );

?>
			<p class="description"><?php esc_html_e( 'only with these user roles:', 'mymail' );?></p>
			<ul><?php

$set = mymail_option( 'register_other_roles', array() );

foreach ( $roles as $role => $name ) {
	echo '<li><input type="checkbox" name="mymail_options[register_other_roles][]" value="' . $role . '" ' . checked( in_array( $role, $set ), true, false ) . '> ' . $name . '</li>';

}?></ul></td>
		</tr>
	</table>
</div>
