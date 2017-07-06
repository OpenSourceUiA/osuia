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
		<th scope="row"><?php esc_html_e( 'Newsletter Homepage', 'mymail' )?></th>
		<td><select name="mymail_options[homepage]" class="postform">
			<option value="0"><?php esc_html_e( 'Choose', 'mymail' )?></option>
		<?php
$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish,private,draft', 'posts_per_page' => -1 ) );
$newsletter_homepage = mymail_option( 'homepage' );
foreach ( $pages as $page ) {
?>
			<option value="<?php echo $page->ID ?>"<?php if ( $page->ID == $newsletter_homepage ) {
		echo " selected";
	}
	?>><?php echo esc_attr( $page->post_title );if ( $page->post_status != 'publish' ) {
		echo ' (' . $wp_post_statuses[$page->post_status]->label . ')';
	}
	?></option>
		<?php
}
?>
		</select>
		<?php if ( $newsletter_homepage ): ?>
		<span class="description">
			<a href="post.php?post=<?php echo intval( $newsletter_homepage ); ?>&action=edit"><?php esc_html_e( 'edit', 'mymail' );?></a>
			<?php esc_html_e( 'or', 'mymail' )?>
			<a href="<?php echo get_permalink( $newsletter_homepage ); ?>" class="external"><?php esc_html_e( 'visit', 'mymail' );?></a>

			</span>
		<?php else: ?>
		<span class="description"><a href="?mymail_create_homepage=1"><?php esc_html_e( 'create it right now', 'mymail' );?></a></span>
		<?php endif;?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Search Engine Visibility', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[frontpage_public]" value="1" <?php checked( mymail_option( 'frontpage_public' ) );?>> <?php esc_html_e( 'Discourage search engines from indexing your campaigns', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Pagination', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[frontpage_pagination]" value="1" <?php checked( mymail_option( 'frontpage_pagination' ) );?>> <?php esc_html_e( 'Allow users to view the next/last newsletters', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Share Button', 'mymail' )?></th>
		<td><label><input type="checkbox" name="mymail_options[share_button]" value="1" <?php checked( mymail_option( 'share_button' ) )?>> <?php esc_html_e( 'Offer share option for your customers', 'mymail' )?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Services', 'mymail' )?></th>
		<td><ul class="social_services"><?php

$social_services = mymail( 'helper' )->social_services();

$services = mymail_option( 'share_services', array() );

foreach ( $social_services as $service => $data ) {
?>
			<li class="<?php echo $service ?>"><label><input type="checkbox" name="mymail_options[share_services][]" value="<?php echo $service ?>" <?php checked( in_array( $service, $services ) );?>> <?php echo $data['name']; ?></label></li>
		<?php
}
?>
		</ul></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Campaign slug', 'mymail' )?></th>
		<td><p>
		<?php if ( mymail( 'helper' )->using_permalinks() ): ?>
		<span class="description"><?php echo get_bloginfo( 'url' ) ?>/</span><input type="text" name="mymail_options[slug]" value="<?php echo esc_attr( mymail_option( 'slug', 'newsletter' ) ); ?>" class="small-text" style="width:80px"><span class="description">/my-campaign</span><br><span class="description"><?php esc_html_e( 'changing the slug may cause broken links in previous sent campaigns!', 'mymail' )?></span>
		<?php else: ?>
		<span class="description"><?php printf( _x( 'Define a %s to enable custom slugs', 'Campaign slug', 'mymail' ), '<a href="options-permalink.php">' . __( 'Permalink Structure', 'mymail' ) . '</a>' ) ?></span>
		<input type="hidden" name="mymail_options[slug]" value="<?php echo esc_attr( mymail_option( 'slug', 'newsletter' ) ); ?>">
		<?php endif;?>
		</p>
		</td>
	</tr>
	<?php
$slugs = mymail_option( 'slugs', array(
		'confirm' => 'confirm',
		'subscribe' => 'subscribe',
		'unsubscribe' => 'unsubscribe',
		'profile' => 'profile',
	) );

if ( mymail( 'helper' )->using_permalinks() && mymail_option( 'homepage' ) ):
	$homepage = trailingslashit( get_permalink( mymail_option( 'homepage' ) ) );
?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Homepage slugs', 'mymail' )?></th>
					<td class="homepage-slugs">
					<p>
					<label><?php esc_html_e( 'Confirm Slug', 'mymail' )?>:</label><br>
						<span>
							<?php echo $homepage ?><strong><?php echo $slugs['confirm'] ?></strong>/
							<a class="button button-small hide-if-no-js edit-slug"><?php echo __( 'Edit', 'mymail' ) ?></a>
						</span>
						<span class="edit-slug-area">
						<?php echo $homepage ?><input type="text" name="mymail_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>" class="small-text">/
						</span>
					</p>
					<p>
					<label><?php esc_html_e( 'Subscribe Slug', 'mymail' )?>:</label><br>
						<span>
							<?php echo $homepage ?><strong><?php echo $slugs['subscribe'] ?></strong>/
							<a class="button button-small hide-if-no-js edit-slug"><?php echo __( 'Edit', 'mymail' ) ?></a>
						</span>
						<span class="edit-slug-area">
						<?php echo $homepage ?><input type="text" name="mymail_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>" class="small-text">/
						</span>
					</p>
					<p>
					<label><?php esc_html_e( 'Unsubscribe Slug', 'mymail' )?>:</label><br>
						<span>
							<a href="<?php echo $homepage . esc_attr( $slugs['unsubscribe'] ) ?>" class="external"><?php echo $homepage ?><strong><?php echo $slugs['unsubscribe'] ?></strong>/</a>
							<a class="button button-small hide-if-no-js edit-slug"><?php echo __( 'Edit', 'mymail' ) ?></a>
						</span>
						<span class="edit-slug-area">
						<?php echo $homepage ?><input type="text" name="mymail_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>" class="small-text">/
						</span>
					</p>
					<p>
					<label><?php esc_html_e( 'Profile Slug', 'mymail' )?>:</label><br>
						<span>
							<a href="<?php echo $homepage . esc_attr( $slugs['profile'] ) ?>" class="external"><?php echo $homepage ?><strong><?php echo $slugs['profile'] ?></strong>/</a>
							<a class="button button-small hide-if-no-js edit-slug"><?php echo __( 'Edit', 'mymail' ) ?></a>
						</span>
						<span class="edit-slug-area">
						<?php echo $homepage ?><input type="text" name="mymail_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>" class="small-text">/
						</span>
					</p>
					</td>
				</tr>
				<?php else: ?>
		<input type="hidden" name="mymail_options[slugs][confirm]" value="<?php echo esc_attr( $slugs['confirm'] ); ?>">
		<input type="hidden" name="mymail_options[slugs][subscribe]" value="<?php echo esc_attr( $slugs['subscribe'] ); ?>">
		<input type="hidden" name="mymail_options[slugs][unsubscribe]" value="<?php echo esc_attr( $slugs['unsubscribe'] ); ?>">
		<input type="hidden" name="mymail_options[slugs][profile]" value="<?php echo esc_attr( $slugs['profile'] ); ?>">

	<?php endif;?>

	<?php if ( mymail( 'helper' )->using_permalinks() ): ?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Archive', 'mymail' )?></th>
		<td class="homepage-slugs"><p><label><input type="hidden" name="mymail_options[hasarchive]" value="0"><input type="checkbox" name="mymail_options[hasarchive]" class="has-archive-check" value="1" <?php checked( mymail_option( 'hasarchive' ) );?>> <?php esc_html_e( 'enable archive function to display your newsletters in a reverse chronological order', 'mymail' )?></label>
			</p>
		<div class="archive-slug" <?php if ( !mymail_option( 'hasarchive' ) ) {
	echo ' style="display:none"';
}
?>>
		<p>
		<label><?php esc_html_e( 'Archive Slug', 'mymail' )?>:</label><br>
			<?php
$homepage = home_url( '/' );
$slug = mymail_option( 'archive_slug', 'newsletter' );
?>
			<span>
				<a href="<?php echo $homepage . esc_attr( $slug ) ?>" class="external"><?php echo $homepage ?><strong><?php echo $slug ?></strong>/</a>
				<a class="button button-small hide-if-no-js edit-slug"><?php echo __( 'Edit', 'mymail' ) ?></a>
			</span>
			<span class="edit-slug-area">
			<?php echo $homepage ?><input type="text" name="mymail_options[archive_slug]" value="<?php echo esc_attr( $slug ); ?>" class="small-text">/
			</span>
		</p>
		<p><label><?php esc_html_e( 'show only', 'mymail' );
$archive_types = mymail_option( 'archive_types', array( 'finished', 'active' ) );?>: </label>
		<label> <input type="checkbox" name="mymail_options[archive_types][]" value="finished" <?php checked( in_array( 'finished', $archive_types ) );?>> <?php esc_html_e( 'finished', 'mymail' );?> </label>
		<label> <input type="checkbox" name="mymail_options[archive_types][]" value="active" <?php checked( in_array( 'active', $archive_types ) );?>> <?php esc_html_e( 'active', 'mymail' );?> </label>
		<label> <input type="checkbox" name="mymail_options[archive_types][]" value="paused" <?php checked( in_array( 'paused', $archive_types ) );?>> <?php esc_html_e( 'paused', 'mymail' );?> </label>
		<label> <input type="checkbox" name="mymail_options[archive_types][]" value="queued" <?php checked( in_array( 'queued', $archive_types ) );?>> <?php esc_html_e( 'queued', 'mymail' );?> </label>
		</p>
	</div>
		</td>
	</tr>
	<?php else: ?>
		<input type="hidden" name="mymail_options[hasarchive]" value="<?php echo esc_attr( mymail_option( 'hasarchive' ) ); ?>">
		<input type="hidden" name="mymail_options[archive_slug]" value="<?php echo esc_attr( mymail_option( 'archive_slug', 'newsletter' ) ); ?>">
	<?php endif;?>

</table>
