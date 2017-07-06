<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


/**
 *
 *
 * @param unknown $subclass (optional)
 * @return unknown
 */
function mymail( $subclass = NULL ) {
	global $mymail;

	$args = func_get_args();
	$subclass = array_shift( $args );

	if ( is_null( $subclass ) ) return $mymail;

	return call_user_func_array( array( $mymail, $subclass ), $args );

}


/**
 *
 *
 * @param unknown $option
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mymail_option( $option, $fallback = NULL ) {

	global $mymail_options;
	return isset( $mymail_options[$option] ) ? $mymail_options[$option] : $fallback;

}


/**
 *
 *
 * @return unknown
 */
function mymail_options() {
	global $mymail_options;
	return $mymail_options;
}


if ( function_exists( 'wp_cache_add_non_persistent_groups' ) )
	wp_cache_add_non_persistent_groups( array( 'mymail' ) );


/**
 *
 *
 * @param unknown $key
 * @param unknown $data
 * @param unknown $expire (optional)
 * @return unknown
 */
function mymail_cache_add( $key, $data, $expire = 0 ) {
	if ( mymail_option( 'disable_cache' ) ) return true;
	return wp_cache_add( $key, $data, 'mymail', $expire );
}


/**
 *
 *
 * @param unknown $key
 * @param unknown $data
 * @param unknown $expire (optional)
 * @return unknown
 */
function mymail_cache_set( $key, $data, $expire = 0 ) {
	if ( mymail_option( 'disable_cache' ) ) return true;
	return wp_cache_set( $key, $data, 'mymail', $expire );
}


/**
 *
 *
 * @param unknown $key
 * @param unknown $force (optional)
 * @param unknown $found (optional, reference)
 * @return unknown
 */
function mymail_cache_get( $key, $force = false, &$found = null ) {
	if ( mymail_option( 'disable_cache' ) ) return false;
	return wp_cache_get( $key, 'mymail', $force, $found );
}


/**
 *
 *
 * @param unknown $key
 * @return unknown
 */
function mymail_cache_delete( $key ) {
	return wp_cache_delete( $key, 'mymail' );
}


/**
 *
 *
 * @param unknown $option
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mymail_text( $option, $fallback = '' ) {
	global $mymail_texts;
	if ( empty( $mymail_texts ) ) {
		$mymail_texts = get_option( 'mymail_texts', array() );
	}
	return apply_filters( 'mymail_text', ( isset( $mymail_texts[$option] ) ? $mymail_texts[$option] : $fallback ), $option, $fallback );
}


/**
 *
 *
 * @param unknown $option
 * @param unknown $value
 * @param unknown $temp   (optional)
 * @return unknown
 */
function mymail_update_option( $option, $value, $temp = false ) {
	global $mymail_options;

	if ( is_array( $option ) ) {
		$temp = (bool) $value;
		$mymail_options = wp_parse_args( $option, $mymail_options );
	}else {
		$mymail_options[$option] = $value;
	}

	if ( $temp ) {
		$mymail_options = mymail( 'settings' )->verify( $mymail_options );
		return true;
	}

	return update_option( 'mymail_options', $mymail_options );
}


/**
 *
 *
 * @param unknown $headline
 * @param unknown $content
 * @param unknown $to          (optional)
 * @param unknown $replace     (optional)
 * @param unknown $attachments (optional)
 * @param unknown $template    (optional)
 * @param unknown $headers     (optional)
 * @return unknown
 */
function mymail_send( $headline, $content, $to = '', $replace = array(), $attachments = array(), $template = 'notification.html', $headers = NULL ) {

	_deprecated_function( __FUNCTION__, '2.0', 'mymail(\'notification\')->send($args)' );

	if ( empty( $to ) ) {
		$current_user = wp_get_current_user();
		$to = $current_user->user_email;
	}


	$defaults = array( 'notification' => '' );

	$replace = apply_filters( 'mymail_send_replace', wp_parse_args( $replace, $defaults ) );

	$mail = mymail( 'mail' );

	//extract the header if it's already Mime encoded
	if ( !empty( $headers ) ) {
		if ( is_string( $headers ) ) {
			$headerlines = explode( "\n", trim( $headers ) );
			foreach ( $headerlines as $header ) {
				$parts = explode( ':', $header, 2 );
				$key = trim( $parts[0] );
				$value = trim( $parts[1] );

				//if fom is set, use it!
				if ( 'from' == strtolower( $key ) ) {
					if ( preg_match( '#(.*)?<([^>]+)>#', $value, $matches ) ) {
						$mail->from = trim( $matches[2] );
						$mail->from_name = trim( $matches[1] );
					}else {
						$mail->from = $value;
						$mail->from_name = '';
					}
				}else if ( !in_array( strtolower( $key ), array( 'content-type' ) ) ) {
						$mail->headers[$key] = trim( $value );
					}
			}
		}else if ( is_array( $headers ) ) {
				foreach ( $headers as $key => $value ) {
					$mail->mailer->addCustomHeader( $key, $value );
				}
			}
	}

	$mail->to = $to;
	$mail->subject = $headline;
	$mail->attachments = $attachments;

	return $mail->send_notification( $content, $headline, $replace, false, $template );
}


/**
 *
 *
 * @param unknown $to
 * @param unknown $subject
 * @param unknown $message
 * @param unknown $headers     (optional)
 * @param unknown $attachments (optional)
 * @param unknown $template    (optional)
 * @return unknown
 */
function mymail_wp_mail( $to, $subject, $message, $headers = '', $attachments = array(), $template = 'notification.html' ) {

	$mail = mymail( 'mail' );
	$mail->from = apply_filters( 'wp_mail_from', mymail_option( 'from' ) );
	$mail->from_name = apply_filters( 'wp_mail_from_name', mymail_option( 'from_name' ) );

	$mail->apply_raw_headers( $headers );

	$mail->to = $to;
	$mail->message = $message;
	$mail->subject = $subject;

	if ( !is_array( $attachments ) )
		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
	$mail->attachments = $attachments;

	$replace = apply_filters( 'mymail_send_replace', array( 'notification' => '' ) );
	$message = apply_filters( 'mymail_send_message', $message );
	$headline = apply_filters( 'mymail_send_headline', $subject );

	return $mail->send_notification( $message, $headline, $replace, false, $template );

}


/**
 * depreciated
 *
 * @param unknown $campaign
 * @param unknown $subscriber
 * @param unknown $track      (optional)
 * @param unknown $forcesend  (optional)
 * @param unknown $force      (optional)
 * @return unknown
 */
function mymail_send_campaign_to_subscriber( $campaign, $subscriber, $track = false, $forcesend = false, $force = false ) {

	$campaign_id = is_int( $campaign ) ? $campaign : $campaign->ID;
	$subscriber_id = is_int( $subscriber ) ? $subscriber : $subscriber->ID;

	mymail( 'campaigns' )->send_to_subscriber( $campaign_id, $subscriber_id, $track, $forcesend || $force, false );

	if ( is_wp_error( $result ) ) {
		return false;
	}

	return $result;

}


/**
 *
 *
 * @param unknown $id          (optional)
 * @param unknown $echo        (optional)
 * @param unknown $classes     (optional)
 * @param unknown $depreciated (optional)
 * @return unknown
 */
function mymail_form( $id = 0, $echo = true, $classes = '', $depreciated = '' ) {

	//tabindex is depreciated but for backward compatibility
	if ( is_int( $echo ) ) {
		$classes = $depreciated;
		$echo = $classes;
	}

	$form = mymail( 'form' )->id( $id );
	$form->add_class( $classes );
	$form = $form->render( false );

	if ( $echo ) {
		echo $form;
	} else {
		return $form;
	}
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_active_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_active( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_paused_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_paused( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_queued_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_queued( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_draft_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_drafted( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_finished_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_finished( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_pending_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_pending( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_autoresponder_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_autoresponder( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_get_campaigns( $args = '' ) {

	return mymail( 'campaigns' )->get_campaigns( $args );
}


/**
 *
 *
 * @param unknown $args (optional)
 * @return unknown
 */
function mymail_list_newsletter( $args = '' ) {
	$defaults = array(
		'title_li' => __( 'Newsletters', 'mymail' ),
		'post_type' => 'newsletter',
		'post_status' => array( 'finished', 'active' ),
		'echo' => 1,
	);
	$r = wp_parse_args( $args, $defaults );

	extract( $r, EXTR_SKIP );

	$output = '';

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();
	$r['exclude'] = implode( ',', apply_filters( 'mymail_list_newsletter_excludes', $exclude_array ) );

	$newsletters = get_posts( $r );

	if ( !empty( $newsletters ) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		foreach ( $newsletters as $newsletter ) {
			$output .= '<li class="newsletter_item newsletter-item-'.$newsletter->ID.'"><a href="'.get_permalink( $newsletter->ID ).'">'.$newsletter->post_title.'</a></li>';
		}

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters( 'mymail_list_newsletter', $output, $r );

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}


/**
 *
 *
 * @param unknown $ip  (optional)
 * @param unknown $get (optional)
 * @return unknown
 */
function mymail_ip2Country( $ip = '', $get = 'code' ) {

	if ( !mymail_option( 'trackcountries' ) ) return 'unknown';

	try{


		if ( empty( $ip ) )
			$ip = mymail_get_ip( );

		require_once  MYMAIL_DIR . 'classes/libs/Ip2Country.php';
		$i = new Ip2Country();
		$code = $i->get( $ip, $get );

		if ( !$code ) {
			$code = mymail_ip2City( $ip, $get ? 'country_'.$get : NULL );
		}
		return ( $code ) ? $code : 'unknown';

	} catch ( Exception $e ) {
		return 'error';
	}
}


/**
 *
 *
 * @param unknown $ip  (optional)
 * @param unknown $get (optional)
 * @return unknown
 */
function mymail_ip2City( $ip = '', $get = NULL ) {

	if ( !mymail_option( 'trackcities' ) ) return 'unknown';

	$geo = mymail( 'geo' );

	$code = $geo->get_city_by_ip( $ip, $get );

	return ( $code ) ? $code : 'unknown';

}


/**
 *
 *
 * @return unknown
 */
function mymail_get_ip( ) {

	$ip = apply_filters( 'mymail_get_ip', NULL );

	if ( !is_null( $ip ) ) return $ip;

	$ip = '';
	foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
		if ( array_key_exists( $key, $_SERVER ) === true ) {
			foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
				$ip = trim( $ip ); // just to be safe

				if ( mymail_validate_ip( $ip ) ) {
					return $ip;
				}
			}
		}
	}
	return $ip;
}


/**
 *
 *
 * @return unknown
 */
function mymail_is_local( ) {

	$whitelist = array(
		'127.0.0.1',
		'::1',
	);

	return in_array( $_SERVER['REMOTE_ADDR'], $whitelist );
}


/**
 *
 *
 * @param unknown $ip
 * @return unknown
 */
function mymail_validate_ip( $ip ) {
	if ( strtolower( $ip ) === 'unknown' )
		return false;

	// generate ipv4 network address
	$ip = ip2long( $ip );

	// if the ip is set and not equivalent to 255.255.255.255
	if ( $ip !== false && $ip !== -1 ) {
		// make sure to get unsigned long representation of ip
		// due to discrepancies between 32 and 64 bit OSes and
		// signed numbers (ints default to signed in PHP)
		$ip = sprintf( '%u', $ip );
		// do private network range checking
		if ( $ip >= 0 && $ip <= 50331647 ) return false;
		if ( $ip >= 167772160 && $ip <= 184549375 ) return false;
		if ( $ip >= 2130706432 && $ip <= 2147483647 ) return false;
		if ( $ip >= 2851995648 && $ip <= 2852061183 ) return false;
		if ( $ip >= 2886729728 && $ip <= 2887778303 ) return false;
		if ( $ip >= 3221225984 && $ip <= 3221226239 ) return false;
		if ( $ip >= 3232235520 && $ip <= 3232301055 ) return false;
		if ( $ip >= 4294967040 ) return false;
	}
	return true;
}


/**
 *
 *
 * @param unknown $fallback (optional)
 * @return unknown
 */
function mymail_get_lang( $fallback = false ) {

	return isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? strtolower( substr( trim( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ), 0, 2 ) ) : $fallback;

}


/**
 *
 *
 * @param unknown $string (optional)
 * @return unknown
 */
function mymail_get_user_client( $string = NULL ) {

	$client = apply_filters( 'mymail_get_user_client', NULL );

	if ( !is_null( $client ) ) return $client;

	require_once MYMAIL_DIR . 'classes/libs/MyMailUserAgent.php';
	$agent = new MyMailUserAgent( $string );
	$client = $agent->get();
	return $client;

}


/**
 *
 *
 * @param unknown $email
 * @param unknown $userdata      (optional)
 * @param unknown $lists         (optional)
 * @param unknown $double_opt_in (optional)
 * @param unknown $overwrite     (optional)
 * @param unknown $mergelists    (optional)
 * @param unknown $template      (optional)
 * @return unknown
 */
function mymail_subscribe( $email, $userdata = array(), $lists = array(), $double_opt_in = NULL, $overwrite = true, $mergelists = NULL, $template = 'notification.html' ) {

	$entry = wp_parse_args( array(
			'email' => $email
		), $userdata );

	if ( !is_null( $double_opt_in ) )
		$entry['status'] = $double_opt_in ? 0 : 1;

	$subscriber_id = mymail( 'subscribers' )->add( $entry, $overwrite );

	if ( is_wp_error( $subscriber_id ) ) {
		return false;
	}

	if ( !is_array( $lists ) ) $lists = array( $lists );

	$new_lists = array();

	foreach ( $lists as $list ) {
		if ( is_numeric( $list ) ) {
			$new_lists[] = intval( $list );
		}else {
			if ( $list_id = mymail( 'lists' )->get_by_name( $list, 'ID' ) ) {
				$new_lists[] = $list_id;
			}
		}
	}


	mymail( 'subscribers' )->assign_lists( $subscriber_id, $new_lists, $mergelists );

	return true;

}


/**
 * depreciated
 *
 * @param unknown $email_hash_id
 * @param unknown $campaign_id   (optional)
 * @param unknown $logit         (optional)
 * @return unknown
 */
function mymail_unsubscribe( $email_hash_id, $campaign_id = NULL, $logit = true ) {

	if ( is_int( $email_hash_id ) ) {

		return mymail( 'subscribers' )->unsubscribe( $email_hash_id, $campaign_id );

	}else if ( preg_match( '#^[0-9a-f]{32}$#', $email_hash_id ) ) {

			return mymail( 'subscribers' )->unsubscribe_by_hash( $email_hash_id, $campaign_id );

		}else {

		return mymail( 'subscribers' )->unsubscribe_by_mail( $email_hash_id, $campaign_id );
	}

}



/**
 *
 *
 * @return unknown
 */
function mymail_get_subscribed_subscribers( ) {
	return mymail_get_subscribers();
}


/**
 *
 *
 * @return unknown
 */
function mymail_get_unsubscribed_subscribers( ) {
	return mymail_get_subscribers( 2 );
}


/**
 *
 *
 * @return unknown
 */
function mymail_get_hardbounced_subscribers( ) {
	return mymail_get_subscribers( 5 );
}


/**
 *
 *
 * @param unknown $status (optional)
 * @return unknown
 */
function mymail_get_subscribers( $status = NULL ) {
	return mymail( 'subscribers' )->get_totals( $status );
}


/**
 *
 *
 * @param unknown $part       (optional)
 * @param unknown $deprecated (optional)
 * @return unknown
 */
function mymail_clear_cache( $part = '' , $deprecated = false ) {

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_mymail_".esc_sql( $part )."%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_mymail_".esc_sql( $part )."%'" );

	return true;

}


/**
 *
 *
 * @param unknown $args
 * @param unknown $type (optional)
 * @param unknown $once (optional)
 * @param unknown $key  (optional)
 * @return unknown
 */
function mymail_notice( $args, $type = '', $once = false, $key = NULL ) {


	global $mymail_notices;

	if ( !is_array( $args ) ) {
		$args = array(
			'text' => $args,
			'type' => ( empty( $type ) ) ? 'updated' : ( $type == 'error' ? $type : 'updated' ),
			'once' => $once,
			'key' => $key ? $key : uniqid(),
		);
	}
	$args = wp_parse_args( $args, array(
			'text' => '',
			'type' => 'updated',
			'once' => false,
			'key' => uniqid(),
			'cb' => NULL,
		) );

	$mymail_notices = get_option( 'mymail_notices' , array() );

	$mymail_notices[$args['key']] = array(
		'text' => $args['text'],
		'type' => $args['type'],
		'once' => $args['once'],
		'cb' => $args['cb'],
	);

	do_action( 'mymail_notice', $args['text'], $args['type'], $args['key'] );

	update_option( 'mymail_notices', $mymail_notices );

	return $args['key'];

}


/**
 *
 *
 * @param unknown $key
 * @return unknown
 */
function mymail_remove_notice( $key ) {

	global $mymail_notices;

	$mymail_notices = get_option( 'mymail_notices' , array() );

	if ( isset( $mymail_notices[$key] ) ) {

		unset( $mymail_notices[$key] );

		do_action( 'mymail_remove_notice', $key );

		return update_option( 'mymail_notices', $mymail_notices );
	}

	return false;

}


/**
 *
 *
 * @param unknown $email
 * @return unknown
 */
function mymail_is_email( $email ) {

	// First, we check that there's one @ symbol, and that the lengths are right
	if ( !preg_match( "/^[^@]{1,64}@[^@]{1,255}$/", $email ) ) {
		// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
		return false;
	}
	// Split it into sections to make life easier
	$email_array = explode( "@", $email );
	$local_array = explode( ".", $email_array[0] );
	for ( $i = 0; $i < sizeof( $local_array ); $i++ ) {
		if ( !preg_match( "/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i] ) ) {
			return false;
		}
	}
	if ( !preg_match( "/^\[?[0-9\.]+\]?$/", $email_array[1] ) ) { // Check if domain is IP. If not, it should be valid domain name
		$domain_array = explode( ".", $email_array[1] );
		if ( sizeof( $domain_array ) < 2 ) {
			return false; // Not enough parts to domain
		}
		for ( $i = 0; $i < sizeof( $domain_array ); $i++ ) {
			if ( !preg_match( "/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i] ) ) {
				return false;
			}
		}
	}

	return true;

}


/**
 *
 *
 * @param unknown $id_email_or_hash
 * @param unknown $type             (optional)
 * @return unknown
 */
function mymail_get_subscriber( $id_email_or_hash, $type = null ) {

	$id_email_or_hash = trim( $id_email_or_hash );

	if ( !is_null( $type ) ) {
		if ( $type == 'id' ) {
			return mymail( 'subscribers' )->get( $id_email_or_hash );
		}else if ( $type == 'email' ) {
				return mymail( 'subscribers' )->get_by_mail( $id_email_or_hash );
			}else if ( $type == 'hash' ) {
				return mymail( 'subscribers' )->get_by_hash( $id_email_or_hash );
			}
	}

	if ( is_numeric( $id_email_or_hash ) ) {
		return mymail( 'subscribers' )->get( $id_email_or_hash );
	}else if ( preg_match( '#[0-9a-f]{32}#', $id_email_or_hash ) ) {
			return mymail( 'subscribers' )->get_by_hash( $id_email_or_hash );
		}else if ( mymail_is_email( $id_email_or_hash ) ) {
			return mymail( 'subscribers' )->get_by_mail( $id_email_or_hash );
		}
	return false;
}


/**
 *
 *
 * @param unknown $tag
 * @param unknown $callbackfunction
 * @return unknown
 */
function mymail_add_tag( $tag, $callbackfunction ) {

	if ( is_array( $callbackfunction ) ) {

		if ( !method_exists( $callbackfunction[0], $callbackfunction[1] ) ) return false;

	}else {

		if ( !function_exists( $callbackfunction ) ) return false;

	}

	global $mymail_tags;

	if ( !isset( $mymail_tags ) ) $mymail_tags = array();

	$mymail_tags[$tag] = $callbackfunction;

	return true;

}


/**
 *
 *
 * @param unknown $tag
 * @return unknown
 */
function mymail_remove_tag( $tag ) {

	global $mymail_tags;

	if ( isset( $mymail_tags[$tag] ) ) unset( $mymail_tags[$tag] );

	return true;

}


/**
 *
 *
 * @param unknown $callbackfunction
 * @return unknown
 */
function mymail_add_style( $callbackfunction ) {

	global $mymail_mystyles;

	if ( is_array( $callbackfunction ) ) {
		if ( !method_exists( $callbackfunction[0], $callbackfunction[1] ) ) return false;
	}else {
		if ( !function_exists( $callbackfunction ) ) return false;
	}

	if ( !isset( $mymail_mystyles ) ) $mymail_mystyles = array();

	$args = func_get_args();
	$args = array_slice( $args, 1 );
	$mymail_mystyles[] = call_user_func_array( $callbackfunction, $args );

	return true;

}


/**
 *
 *
 * @param unknown $text
 * @return unknown
 */
function mymail_update_notice( $text ) {

	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'thickbox' );

	return sprintf( __( 'MyMail has been updated to %s.', 'mymail' ), '<strong>'.MYMAIL_VERSION.'</strong>' ).' <a class="thickbox" href="'.network_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=mymail&amp;section=changelog&amp;TB_iframe=true&amp;width=772&amp;height=745' ).'">'.__( 'Changelog', 'mymail' ).'</a>';

}


/**
 *
 *
 * @return unknown
 */
function is_mymail_newsletter_homepage() {

	global $post;

	return isset( $post ) && $post->ID == mymail_option( 'homepage' );

}


/**
 *
 *
 * @param unknown $redirect (optional)
 * @param unknown $method   (optional)
 * @param unknown $showform (optional)
 * @return unknown
 */
function mymail_require_filesystem( $redirect = '', $method = '', $showform = true ) {

	global $wp_filesystem;

	//force direct method
	add_filter( 'filesystem_method', create_function( '$a', 'return "direct";' ) );

	if ( !function_exists( 'request_filesystem_credentials' ) ) {

		require_once ABSPATH . 'wp-admin/includes/file.php';

	}

	ob_start();

	if ( false === ( $credentials = request_filesystem_credentials( $redirect, $method ) ) ) {
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $data ) ) {
			include_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			include ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return false;
	}

	if ( !$showform ) {
		return false;
	}


	if ( ! WP_Filesystem( $credentials ) ) {
		request_filesystem_credentials( $redirect, $method, true ); // Failed to connect, Error and request again
		$data = ob_get_contents();
		ob_end_clean();
		if ( ! empty( $data ) ) {
			include_once ABSPATH . 'wp-admin/admin-header.php';
			echo $data;
			include ABSPATH . 'wp-admin/admin-footer.php';
			exit;
		}
		return false;
	}

	return $wp_filesystem;

}


if ( !function_exists( 'http_negotiate_language' ) ) :


	/**
	 *
	 *
	 * @param unknown $supported
	 * @param unknown $http_accept_language (optional)
	 * @return unknown
	 */
	function http_negotiate_language( $supported, $http_accept_language = 'auto' ) {

		if ( $http_accept_language == "auto" ) $http_accept_language = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';

		preg_match_all( "/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
			"(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
			$http_accept_language, $hits, PREG_SET_ORDER );

		// default language (in case of no hits) is the first in the array
		$bestlang = $supported[0];
		$bestqval = 0;

		foreach ( $hits as $arr ) {
			// read data from the array of this hit
			$langprefix = strtolower( $arr[1] );
			if ( !empty( $arr[3] ) ) {
				$langrange = strtolower( $arr[3] );
				$language = $langprefix . "-" . $langrange;
			}
			else $language = $langprefix;
			$qvalue = 1.0;
			if ( !empty( $arr[5] ) ) $qvalue = floatval( $arr[5] );

			// find q-maximal language
			if ( in_array( $language, $supported ) && ( $qvalue > $bestqval ) ) {
				$bestlang = $language;
				$bestqval = $qvalue;
			}
			// if no direct hit, try the prefix only but decrease q-value by 10% (as http_negotiate_language does)
			else if ( in_array( $langprefix, $supported ) && ( ( $qvalue*0.9 ) > $bestqval ) ) {
					$bestlang = $langprefix;
					$bestqval = $qvalue*0.9;
				}
		}

		return $bestlang;

	}


endif;

if ( !function_exists( 'inet_pton' ) ) :


	/**
	 *
	 *
	 * @param unknown $ip
	 * @return unknown
	 */
	function inet_pton( $ip ) {
		// ipv4
		if ( strpos( $ip, '.' ) !== FALSE ) {
			if ( strpos( $ip, ':' ) === FALSE ) $ip = pack( 'N', ip2long( $ip ) );
			else {
				$ip = explode( ':', $ip );
				$ip = pack( 'N', ip2long( $ip[count( $ip )-1] ) );
			}
		}
		// ipv6
		elseif ( strpos( $ip, ':' ) !== FALSE ) {
			$ip = explode( ':', $ip );
			$parts=8-count( $ip );
			$res='';$replaced=0;
			foreach ( $ip as $seg ) {
				if ( $seg!='' ) $res .= str_pad( $seg, 4, '0', STR_PAD_LEFT );
				elseif ( $replaced==0 ) {
					for ( $i=0;$i<=$parts;$i++ ) $res.='0000';
					$replaced=1;
				} elseif ( $replaced==1 ) $res.='0000';
			}
			$ip = pack( 'H'.strlen( $res ), $res );
		}
		return $ip;
	}


endif;
