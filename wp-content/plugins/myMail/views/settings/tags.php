<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>
	<p class="description"><?php printf( __( 'Tags are placeholder for your newsletter. You can set them anywhere in your newsletter template with the format %s. Custom field tags are individual for each subscriber.', 'mymail' ), '<code>{tagname}</code>' ); ?></p>
	<p class="description"><?php printf( __( 'You can set alternative content with %1$s which will be uses if %2$s is not defined. All unused tags will get removed in the final message', 'mymail' ), '<code>{tagname|alternative content}</code>', '[tagname]' ); ?></p>
		<?php $reserved = array( 'unsub', 'unsublink', 'webversion', 'webversionlink', 'forward', 'forwardlink', 'subject', 'preheader', 'profile', 'profilelink', 'headline', 'content', 'link', 'email', 'emailaddress', 'firstname', 'lastname', 'fullname', 'year', 'month', 'day', 'share', 'tweet' )?>
	<p id="reserved-tags" data-tags='["<?php echo implode( '","', $reserved ) ?>"]'><?php esc_html_e( 'reserved tags', 'mymail' );?>: <code>{<?php echo implode( '}</code>, <code>{', $reserved ) ?>}</code></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Permanent Tags', 'mymail' )?>:</th>
		<td class="tags">
		<p class="description"><?php esc_html_e( 'These are permanent tags which cannot get deleted. The CAN-SPAM tag is required in many countries.', 'mymail' );?> <a href="http://en.wikipedia.org/wiki/CAN-SPAM_Act_of_2003" class="external"><?php esc_html_e( 'Read more', 'mymail' );?></a></p>
<?php if ( $tags = mymail_option( 'tags' ) ) : ?>
<?php foreach ( $tags as $tag => $content ) { ?>
		<div class="tag"><span><code>{<?php echo $tag ?>}</code></span> &#10152; <input type="text" name="mymail_options[tags][<?php echo $tag ?>]" value="<?php echo esc_attr( $content ) ?>" class="regular-text tag-value"></div>
<?php } ?>
<?php endif; ?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Custom Tags', 'mymail' )?>:</th>
		<td class="tags">
		<p class="description"><?php esc_html_e( 'Add your custom tags here. They work like permanent tags', 'mymail' );?></p>
<?php if ( $tags = mymail_option( 'custom_tags' ) ) : ?>
<?php foreach ( $tags as $tag => $content ) { ?>
		<div class="tag"><span><code>{<?php echo $tag ?>}</code></span> &#10152; <input type="text" name="mymail_options[custom_tags][<?php echo $tag ?>]" value="<?php echo esc_attr( $content ) ?>" class="regular-text tag-value"> <a class="tag-remove">&#10005;</a></div>
<?php } ?>
<?php endif; ?>

	<input type="button" value="<?php esc_html_e( 'add', 'mymail' )?>" class="button" id="mymail_add_tag">
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Special Tags', 'mymail' )?>:</th>
		<td class="customfields">
		<p class="description"><?php esc_html_e( 'Special tags display dynamic content and are equally for all subscribers', 'mymail' );?></p>
		<div class="customfield"><span><code>{tweet:username}</code></span> &#10152; <?php printf( __( 'displays the last tweet from Twitter user [username] (cache it for %s minutes)', 'mymail' ), '<input type="text" name="mymail_options[tweet_cache_time]" value="' . esc_attr( mymail_option( 'tweet_cache_time' ) ) . '" class="small-text">' ); ?></div>
		<p class="description">
			<?php printf( __( 'To enable the tweet feature you have to create a new %s and insert your credentials', 'mymail' ), '<a href="https://dev.twitter.com/apps/new" class="external">Twitter App</a>' ); ?>
		</p>
		<p>
		<div class="mymail_text">&nbsp;<label><?php esc_html_e( 'Access token', 'mymail' );?>:</label> <input type="text" name="mymail_options[twitter_token]" value="<?php echo esc_attr( mymail_option( 'twitter_token' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mymail_text">&nbsp;<label><?php esc_html_e( 'Access token Secret', 'mymail' );?>:</label> <input type="password" name="mymail_options[twitter_token_secret]" value="<?php echo esc_attr( mymail_option( 'twitter_token_secret' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mymail_text">&nbsp;<label><?php esc_html_e( 'Consumer key', 'mymail' );?>:</label> <input type="text" name="mymail_options[twitter_consumer_key]" value="<?php echo esc_attr( mymail_option( 'twitter_consumer_key' ) ); ?>" class="regular-text" autocomplete="off"></div>
		<div class="mymail_text">&nbsp;<label><?php esc_html_e( 'Consumer secret', 'mymail' );?>:</label> <input type="password" name="mymail_options[twitter_consumer_secret]" value="<?php echo esc_attr( mymail_option( 'twitter_consumer_secret' ) ); ?>" class="regular-text" autocomplete="off"></div>
		</p>
		<br>
		<div class="customfield"><span><code>{share:twitter}</code></span> &#10152; <?php printf( __( 'displays %1$s to share the newsletter via %2$s', 'mymail' ), '<img src="' . MYMAIL_URI . '/assets/img/share/share_twitter.png">', 'Twitter' ); ?></div>
		<div class="customfield"><span><code>{share:facebook}</code></span> &#10152; <?php printf( __( 'displays %1$s to share the newsletter via %2$s', 'mymail' ), '<img src="' . MYMAIL_URI . '/assets/img/share/share_facebook.png">', 'Facebook' ); ?></div>
		<div class="customfield"><span><code>{share:google}</code></span> &#10152; <?php printf( __( 'displays %1$s to share the newsletter via %2$s', 'mymail' ), '<img src="' . MYMAIL_URI . '/assets/img/share/share_google.png">', 'Google+' ); ?></div>
		<div class="customfield"><span><code>{share:linkedin}</code></span> &#10152; <?php printf( __( 'displays %1$s to share the newsletter via %2$s', 'mymail' ), '<img src="' . MYMAIL_URI . '/assets/img/share/share_linkedin.png">', 'LinkedIn' ); ?></div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Dynamic Tags', 'mymail' )?></th>
		<td><p class="description"><?php esc_html_e( 'Dynamic tags let you display your posts or pages in a reverse chronicle order. Some examples:', 'mymail' );?></p>
		<div class="customfield"><span><code>{post_title:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest post title', 'mymail' );?></div>
		<div class="customfield"><span><code>{page_title:-4}</code></span> &#10152; <?php esc_html_e( 'displays the fourth latest page title', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_image:-1}</code></span> &#10152; <?php esc_html_e( 'displays the feature image of the latest posts', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_image:-4|23}</code></span> &#10152; <?php esc_html_e( 'displays the feature image of the fourth latest posts. Uses the image with ID 23 if the post doesn\'t have a feature image', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_content:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts content', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_excerpt:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts excerpt or content if no excerpt is defined', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_date:-1}</code></span> &#10152; <?php esc_html_e( 'displays the latest posts date', 'mymail' );?></div>
		<p class="description"><?php esc_html_e( 'you can also use absolute values', 'mymail' );?></p>
		<div class="customfield"><span><code>{post_title:23}</code></span> &#10152; <?php esc_html_e( 'displays the post title of post ID 23', 'mymail' );?></div>
		<div class="customfield"><span><code>{post_link:15}</code></span> &#10152; <?php esc_html_e( 'displays the permalink of post ID 15', 'mymail' );?></div>
		<p class="description"><?php esc_html_e( 'Instead of "post_" and "page_" you can use custom post types too', 'mymail' );?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Image Fallback', 'mymail' )?></th>
		<td><label><?php esc_html_e( 'image ID', 'mymail' );?>:
		<input type="text" name="mymail_options[fallback_image]" value="<?php echo intval( mymail_option( 'fallback_image', 0 ) ) ?>" class="small-text"></label>
		<p class="description"><?php esc_html_e( 'Fallback image for dynamic image tags', 'mymail' );?></p>
		</td>
	</tr>
</table>
