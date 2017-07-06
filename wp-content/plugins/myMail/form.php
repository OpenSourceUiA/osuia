<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


$time_start = microtime( true );
if ( !defined( 'ABSPATH' ) ) {

	require_once '../../../wp-load.php';

}

do_action( 'mymail_form_header' );

?><!DOCTYPE html>
<!--[if IE 8]><html class="lt-ie10 ie8" <?php language_attributes();?>><![endif]-->
<!--[if IE 9]><html class="lt-ie10 ie9" <?php language_attributes();?>><![endif]-->
<!--[if gt IE 9]><!--><html <?php language_attributes();?>><!--<![endif]-->
<html <?php language_attributes();?> class="mymail-emebed-form">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' );?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
	<meta name='robots' content='noindex,nofollow'>
	<?php do_action( 'mymail_form_head' );?>

</head>
<body>
	<div class="mymail-form-body">
		<div class="mymail-form-wrap">
			<div class="mymail-form-inner">
			<?php do_action( 'mymail_form_body' );?>
			</div>
		</div>
	</div>
<?php do_action( 'mymail_form_footer' );?>
</body>
</html>
