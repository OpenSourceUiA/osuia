<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


$mymail_social_services = apply_filters( 'mymail_social_services', array(
		'twitter' => array(
			'name' => 'Twitter',
			'url' => "https://twitter.com/intent/tweet?source=myMail&text=%title&url=%url",
			'width' => 845,
			'height' => 600,
		),
		'facebook' => array(
			'name' => 'Facebook',
			'url' => "https://www.facebook.com/sharer.php?u=%url&t=%title",
			'height' => 600,
		),
		'google' => array(
			'name' => 'Google+',
			'url' => "https://plus.google.com/share?url=%url&title=%title",
			'width' => 495,
		),
		'pinterest' => array(
			'name' => 'Pinterest',
			'url' => "http://pinterest.com/pin/create/button/?url=%url&description=%title",
			'width' => 750,
			'height' => 600,
		),
		'buffer' => array(
			'name' => 'Buffer',
			'url' => "https://buffer.com/add?url=%url&text=%title",
			'width' => 720,
			'height' => 600,
		),
		'delicious' => array(
			'name' => 'Delicious',
			'url' => "https://delicious.com/save?v=5&provider=MyMail&noui&jump=close&url=%url&title=%title",
			'height' => 600,
		),
		'blogger' => array(
			'name' => 'Blogger',
			'url' => "http://www.blogger.com/blog_this.pyra?t&u=%url&n=%title",
		),
		'sharethis' => array(
			'name' => 'ShareThis',
			'url' => "https://www.sharethis.com/share?url=%url&title=%title",
			'height' => 720,
		),
		'reddit' => array(
			'name' => 'Reddit',
			'url' => "http://en.reddit.com/submit?url=%url&title=%title",
		),
		'digg' => array(
			'name' => 'Digg',
			'url' => "http://digg.com/submit?url=%url&title=%title",
		),
		'evernote' => array(
			'name' => 'Evernote',
			'url' => "http://s.evernote.com/grclip?url=%url&title=%title",
			'width' => 960,
			'height' => 550,
		),
		'stumbleupon' => array(
			'name' => 'StumbleUpon',
			'url' => "http://www.stumbleupon.com/submit?url=%url",
			'width' => 780,
			'height' => 580,
		),
		'linkedin' => array(
			'name' => 'LinkedIn',
			'url' => 'http://www.linkedin.com/shareArticle?mini=true&url=%url&title=%title',
		),
		'xing' => array(
			'name' => 'Xing',
			'url' => "http://www.xing.com/app/user?op=share;url=%url;title=%title",
			'width' => 570,
			'height' => 580,
		),
		'yahoo' => array(
			'name' => 'Yahoo!',
			'url' => "http://bookmarks.yahoo.com/toolbar/savebm?u=%url&t=%title",
		),
	) );
