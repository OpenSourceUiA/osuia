<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>

<div class="submitbox" id="submitpost">

<?php do_action( 'post_submitbox_start' );?>
<div id="preview-action">
<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>
<div class="clear"></div>

<?php

$now = time();

$sent = $this->get_sent( $post->ID );

?>

<div>

	<div id="misc-publishing-actions">

	<div id="delete-action">
	<?php
if ( current_user_can( "delete_post", $post->ID ) ):
	if ( !EMPTY_TRASH_DAYS ) {
		$delete_text = __( 'Delete Permanently', 'mymail' );
	} else {
	$delete_text = __( 'Move to Trash', 'mymail' );
}

?>
		<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a>
		<?php endif;?>
	</div>

	<span class="spinner ajax-loading" id="ajax-loading"></span>
	<?php if ( !in_array( $post->post_status, array( 'active', 'finished' ) ) && !isset( $_GET['showstats'] ) ): ?>
	<p class="clear" id="password-field">
		<label for="post_password"><input type="checkbox" name="use_pwd" id="use_pwd" value="1" <?php checked( !!$post->post_password );?>> <?php esc_html_e( 'Password', 'mymail' )?></label>
		<span id="password-wrap" <?php if ( !$post->post_password ) {
	echo 'style="display:none;"';
}
?>>
			<input type="hidden" name="post_password" value="">
			<input type="text" class="widefat" name="post_password" id="post_password" value="<?php echo $post->post_password ?>" maxlength="20"><br>
			<span class="description"><?php esc_html_e( 'protect the webversion with a password', 'mymail' )?></span>
		</span>
	</p>
	<?php elseif ( !!$post->post_password ): ?>
	<p class="description alignright"><?php esc_html_e( 'password protected', 'mymail' );?></p>
	<?php endif;?>
	</div>
	<div id="major-publishing-actions">
	<div id="publishing-action">
	<?php
if ( $post->post_status == 'finished' ) {
?>
	<?php if ( current_user_can( 'duplicate_newsletters' ) && current_user_can( 'duplicate_others_newsletters', $post->ID ) ) {?><a class="button duplicate" href="edit.php?post_type=newsletter&duplicate=<?php echo $post->ID ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mymail_nonce' ) ?>"><?php esc_html_e( 'Duplicate', 'mymail' )?></a> <?php }?>
		<?php
} else if ( !in_array( $post->post_status, array( 'publish', 'future', 'private', 'paused' ) ) || 0 == $post->ID ) {

		if ( isset( $_GET['showstats'] ) ): ?>

	<?php if ( $can_publish && in_array( $post->post_status, array( 'paused', 'autoresponder' ) ) ): ?>
		<a class="button" href="post.php?post=<?php echo $post->ID ?>&action=edit"><?php esc_html_e( 'Edit', 'mymail' )?></a>
	<?php else: ?>
		<a class="button pause" href="edit.php?post_type=newsletter&pause=<?php echo $post->ID ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mymail_nonce' ) ?>"><?php esc_html_e( 'Pause', 'mymail' )?></a>
	<?php endif;?>
	<?php
				elseif ( $can_publish ):
					if ( $post->post_status == 'active' ): ?>
				<a class="button pause" href="edit.php?post_type=newsletter&pause=<?php echo $post->ID ?>&edit=1&_wpnonce=<?php echo wp_create_nonce( 'mymail_nonce' ) ?>"><?php esc_html_e( 'Pause', 'mymail' )?></a>
		<?php elseif ( $post->post_status == 'queued' ):
							if ( $post->post_status != 'active' ): ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mymail' )?>" />
						<?php submit_button( __( 'Save', 'mymail' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) );?>
		<?php endif;
				if ( $this->post_data['timestamp'] < $now && in_array( $post->post_status, array( 'paused' ) ) && $sent ): ?>
				<input name="resume" type="submit" value="<?php esc_attr_e( 'Resume', 'mymail' ) ?>" class="button resume-button" title="<?php esc_attr_e( 'Save and resume campaign', 'mymail' ) ?>" />
				<?php else: ?>
				<input name="sendnow" type="submit" value="<?php esc_attr_e( 'Send now', 'mymail' ) ?>" class="button sendnow-button" title=" <?php esc_attr_e( 'Save and send campaign', 'mymail' ) ?>" />
				<?php endif;?>
	<?php elseif ( $post->post_status == 'autoresponder' ): ?>
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mymail' )?>" />
			<a href="<?php echo add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'showstats' => 1 ), '' ); ?>" class="button statistics"><?php esc_html_e( 'Statistic', 'mymail' );?></a>
	<?php elseif ( in_array( $post->post_status, array( 'draft', 'auto-draft' ) ) ): ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mymail' )?>" />
				<?php submit_button( __( 'Save as draft', 'mymail' ), '', 'draft', false, array( 'accesskey' => 'd' ) );?>
				<?php submit_button( __( 'Save', 'mymail' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) );?>
	<?php elseif ( in_array( $post->post_status, array( 'pending' ) ) ): ?>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'mymail' )?>" />
				<?php submit_button( __( 'Save as draft', 'mymail' ), '', 'draft', false, array( 'accesskey' => 'd' ) );?>
				<?php submit_button( __( 'Confirm', 'mymail' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) );?>
	<?php endif;
			else: ?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review', 'mymail' )?>" />
			<?php submit_button( __( 'Submit for Review', 'mymail' ), 'primary', 'publish', false, array( 'accesskey' => 'p' ) );?>
	<?php
			endif;
	} else {
?>
			<?php if ( !isset( $_GET['showstats'] ) ): ?>
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="15" accesskey="p" value="<?php esc_attr_e( 'Update', 'mymail' )?>" />
			<?php
		if ( $can_publish && in_array( $post->post_status, array( 'paused', 'queued' ) ) ): ?>

					<?php if ( in_array( $post->post_status, array( 'paused' ) ) && $sent ): ?>
						<input name="resume" type="submit" value="<?php esc_attr_e( 'Resume', 'mymail' ) ?>" class="button resume-button" title="<?php esc_attr_e( 'Save and resume campaign', 'mymail' ) ?>" />
						<?php else: ?>
						<input name="sendnow" type="submit" value="<?php esc_attr_e( 'Send now', 'mymail' ) ?>" class="button sendnow-button" title=" <?php esc_attr_e( 'Save and send campaign', 'mymail' ) ?>" />
					<?php endif;?>

					<a href="<?php echo add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'showstats' => 1 ), '' ); ?>" class="button statistics"><?php esc_html_e( 'Statistic', 'mymail' );?></a>
				<?php endif;?>
			<?php else: ?>
			<p class="clear">
			<a href="<?php echo add_query_arg( array( 'post' => $post->ID, 'action' => 'edit' ), '' ); ?>" class="button statistics edit"><?php esc_html_e( 'Edit', 'mymail' );?></a>
			<?php if($sent) : ?>
			<input name="resume" type="submit" value="<?php esc_attr_e( 'Resume', 'mymail' ) ?>" class="button resume-button" title="<?php esc_attr_e( 'Save and resume campaign', 'mymail' ) ?>" />
			<?php else : ?>
			<input name="sendnow" type="submit" value="<?php esc_attr_e( 'Send now', 'mymail' ) ?>" class="button sendnow-button" title=" <?php esc_attr_e( 'Save and send campaign', 'mymail' ) ?>" />
			<?php endif; ?>
			</p>

			<?php endif;?>
			<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'mymail' )?>" />
	<?php
}?>
	</div>
	<div class="clear"></div>
	</div>
<div class="clear"></div>
</div>

</div>
