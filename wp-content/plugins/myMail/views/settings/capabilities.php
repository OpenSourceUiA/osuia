<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>
<p class="description"><?php esc_html_e( 'Define capabilities for each user role. To add new roles you can use a third party plugin. Administrator has always all privileges', 'mymail' );?></p>
<div id="current-cap"></div>

<table class="form-table"><?php

unset( $roles['administrator'] );

?>
<tr valign="top">
	<td>
		<table id="capabilities-table">
			<thead>
				<tr>
				<th>&nbsp;</th><?php
foreach ( $roles as $role => $name ) {
	echo '<th><input type="hidden" name="mymail_options[roles][' . $role . '][]" value="">' . $name . ' <input type="checkbox" class="selectall" value="' . $role . '" title="' . __( 'toggle all', 'mymail' ) . '"></th>';

}?>			</tr>
			</thead>
			<tbody>
<?php include MYMAIL_DIR . 'includes/capability.php'; ?>

<?php foreach ( $mymail_capabilities as $capability => $data ) { ?>
			<tr><th><?php echo $data['title'] ?></th>
		<?php foreach ( $roles as $role => $name ) {
		$r = get_role( $role );
		echo '<td><label title="' . sprintf( __( '%1$s can %2$s', 'mymail' ), $name, $data['title'] ) . '"><input name="mymail_options[roles][' . $role . '][]" type="checkbox" class="cap-check-' . $role . '" value="' . $capability . '" ' . checked( !empty( $r->capabilities[$capability] ), 1, false ) . ' ' . ( $role == 'administrator' ? 'readonly' : '' ) . '></label></td>';
	}?>
			</tr>
<?php }?>
			</tbody>
		</table>
	</td>
</tr>
</table>
<p>
<a onclick='return confirm("<?php esc_html_e( 'Do you really like to reset all capabilities? This cannot be undone!', 'mymail' );?>");' href="options-general.php?page=newsletter-settings&reset-capabilities=1&_wpnonce=<?php echo wp_create_nonce( 'mymail-reset-capabilities' ) ?>"><?php esc_html_e( 'Reset all capabilities', 'mymail' );?></a>
</p>
