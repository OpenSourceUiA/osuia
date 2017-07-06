<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


class Ip2Country {

	//maxmind doesn't provide a zip version so I've uploaded it to bitbucket (updated weekly)
	public $zip = 'http://mymailapp.github.io/GeoIPv6.zip';
	private $dbfile;
	private $gi;
	private $renew = false;

	/**
	 *
	 */
	public function __construct() {

		require_once MYMAIL_DIR . 'classes/libs/geoip.inc.php';

		$this->dbfile = MYMAIL_UPLOAD_DIR . '/GeoIPv6.dat';

		if ( mymail_option( 'countries_db' ) && mymail_option( 'countries_db' ) != $this->dbfile ) {
			$this->dbfile = mymail_option( 'countries_db' );
		} else if ( !file_exists( $this->dbfile ) ) {
				add_action( 'shutdown', array( &$this, 'renew' ) );
			}

		if ( file_exists( $this->dbfile ) ) {
			$this->gi = new mymail_GeoIP( $this->dbfile );
		}
	}


	/**
	 *
	 *
	 * @param unknown $code
	 * @return unknown
	 */
	public function country( $code ) {
		return ( isset( $this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[strtoupper( $code )] ) ) ? $this->gi->GEOIP_COUNTRY_NAMES[$this->gi->GEOIP_COUNTRY_CODE_TO_NUMBER[strtoupper( $code )]] : $code;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_countries() {

		$rawcountries = $this->gi->GEOIP_COUNTRY_NAMES;
		$countries = array();
		foreach ( $rawcountries as $key => $country ) {
			if ( !$key ) {
				continue;
			}

			$countries[$this->gi->GEOIP_COUNTRY_CODES[$key]] = $country;
		}

		return $countries;
	}


	/**
	 *
	 *
	 * @param unknown $ip
	 * @param unknown $part (optional)
	 * @return unknown
	 */
	public function get( $ip, $part = null ) {

		//append two semicollons for ipv4 addresses
		if ( strlen( $ip ) <= 15 ) {
			$ip = '::' . $ip;
		}

		//prevent some errors
		$error = ini_get( "error_reporting" );
		error_reporting( E_ERROR );

		if ( !is_null( $part ) ) {
			if ( method_exists( $this->gi, 'geoip_country_' . $part . '_by_addr_v6' ) ) {
				return call_user_func( array( $this->gi, 'geoip_country_' . $part . '_by_addr_v6' ), $ip );
			} else {
				return false;
			}
		}
		$return = (object) array(
			'id' => call_user_func( array( $this->gi, 'geoip_country_ip_by_addr_v6' ), $ip ),
			'code' => call_user_func( array( $this->gi, 'geoip_country_code_by_addr_v6' ), $ip ),
			'country' => call_user_func( array( $this->gi, 'geoip_country_name_by_addr_v6' ), $ip ),
		);

		error_reporting( $error );

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function renew( $force = false ) {

		global $wp_filesystem;

		$filemtime = file_exists( $this->dbfile ) ? filemtime( $this->dbfile ) : 0;

		if ( !$filemtime || $force || $this->renew ) {
			$do_renew = true;
		} else {
			$r = wp_remote_get( $this->zip, array( 'method' => 'HEAD' ) );
			$headers = wp_remote_retrieve_headers( $r );
			//check header
			if ( $headers['content-type'] != 'application/zip' ) {
				return new WP_Error( 'wrong_filetype', 'wrong file type' );
			}

			$lastmodified = strtotime( $headers['last-modified'] );
			$do_renew = $lastmodified - $filemtime > 0;
		}

		if ( $do_renew ) {

			mymail_require_filesystem();
			@set_time_limit( 120 );

			if ( !function_exists( 'download_url' ) ) {
				include ABSPATH . 'wp-admin/includes/file.php';
			}

			//download
			$tempfile = download_url( $this->zip );

			//create directory
			if ( !is_dir( dirname( $this->dbfile ) ) ) {
				if ( !wp_mkdir_p( dirname( $this->dbfile ) ) ) {
					return new WP_Error( 'create_directory', sprintf( 'not able to create directory %s', dirname( $this->dbfile ) ) );
				}
			}

			//unzip
			if ( !unzip_file( $tempfile, dirname( $this->dbfile ) ) ) {
				return new WP_Error( 'unzip_file', 'error unzipping file' );
			}

			if ( !file_exists( $this->dbfile ) ) {
				mymail_update_option( 'trackcountries', false );
				return new WP_Error( 'file_missing', 'file is missing' );
			}

			$this->gi = new mymail_GeoIP( $this->dbfile );

		}

		return file_exists( $this->dbfile ) ? $this->dbfile : false;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function remove() {

		global $wp_filesystem;
		mymail_require_filesystem();

		return $wp_filesystem->delete( $this->dbfile );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_real_ip() {
		return mymail_get_ip();
	}


}
