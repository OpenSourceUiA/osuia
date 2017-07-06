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
		<th scope="row"><?php esc_html_e( 'Purchase Code', 'mymail' );?></th>
		<td><input type="text" name="mymail_options[purchasecode]" value="<?php echo esc_attr( mymail_option( 'purchasecode' ) ); ?>" class="regular-text" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" pattern="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}" maxlength="36">
		<p class="description"><?php esc_html_e( 'Enter your Purchase Code to enable automatic updates', 'mymail' );?></p>
		<p><a class="button button-primary external" href="https://mymail.newsletter-plugin.com/buy/?utm_campaign=plugin&utm_medium=settings+page+button"><?php _e('Buy new License', 'mymail') ?></a></p>
		</td>
	</tr>
	<?php do_action( 'mymail_purchasecode_tab' );?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Disable this tab', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_purchasecode_disabled" value="1"> <?php esc_html_e( 'Hide this tab if the Purchase Code has been entered', 'mymail' )?> <p class="description"><?php printf( __( 'If you disable this tab you cannot access it anymore from the settings page. If you would like to bring it back delete the %1$s option from the %2$s table', 'mymail' ), '"mymail_purchasecode_disabled"', '"' . $wpdb->options . '"' ); ?></p></label>
		</td>
	</tr>
</table>
