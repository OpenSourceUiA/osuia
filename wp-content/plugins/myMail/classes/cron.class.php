<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


class MyMailCron {

	/**
	 *
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	/**
	 *
	 */
	public function init() {

		add_filter( 'cron_schedules', array( &$this, 'filter_cron_schedules' ) );
		add_action( 'mymail_cron', array( &$this, 'hourly_cronjob' ) );
		add_action( 'mymail_cron', array( &$this, 'check' ) );
		add_action( 'mymail_cron_worker', array( &$this, 'handler' ), -1 );
		add_action( 'mymail_cron_worker', array( &$this, 'check' ), 99 );

		add_action( 'mymail_campaign_pause', array( &$this, 'update' ) );
		add_action( 'mymail_campaign_start', array( &$this, 'update' ) );
		add_action( 'mymail_campaign_duplicate', array( &$this, 'update' ) );

		if ( !wp_next_scheduled( 'mymail_cron' ) ) {
			$this->update( true );
		}

	}


	/**
	 * Checks for new newsletter in the queue to start new cronjob
	 */
	public function hourly_cronjob() {

		//check for bounced emails
		do_action( 'mymail_check_bounces' );

		//send confirmations again
		do_action( 'mymail_resend_confirmations' );

		if ( mymail( 'queue' )->size( time() + 3600 ) ) {
			$this->update();
		} else {
			$this->remove_crons();
		}

		if ( version_compare( PHP_VERSION, '5.3' ) < 0 ) {
			mymail_notice( '<strong>' . sprintf( 'MyMail requires PHP version 5.3 and above. Your current version is %s so please update or ask your provider to help you with updating!', '<code>' . PHP_VERSION . '</code>' ) . '</strong>', 'error', false, 'minphpversion' );
		} else {
			mymail_remove_notice( 'minphpversion' );
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function handler() {

		if ( defined( 'MYMAIL_DOING_CRON' ) || defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) || defined( 'WP_INSTALLING' ) || defined( 'MYMAIL_DO_UPDATE' ) ) {
			return false;
		}

		define( 'MYMAIL_DOING_CRON', microtime( true ) );

		register_shutdown_function( array( &$this, 'shutdown_function' ) );

	}


	/**
	 *
	 */
	public function shutdown_function() {

		if ( !defined( 'MYMAIL_DOING_CRON' ) ) {
			return;
		}

		$error = error_get_last();

		if ( !is_null( $error ) && $error["type"] == 1 ) {

			mymail_notice( '<strong>' . sprintf( __( 'It seems your last cronjob hasn\'t been finished! Increase the %1$s, add %2$s to your wp-config.php or reduce the %3$s in the settings', 'mymail' ), "'max_execution_time'", '<code>define("WP_MEMORY_LIMIT", "256M");</code>', '<a href="options-general.php?page=newsletter-settings&settings-updated=true#delivery">' . __( 'Number of mails sent', 'mymail' ) . '</a>' ) . '</strong>', 'error', false, 'cron_unfinished' );

		} else {

			mymail_remove_notice( 'cron_unfinished' );

		}

	}


	/**
	 *
	 *
	 * @param unknown $hourly_only (optional)
	 * @return unknown
	 */
	public function update( $hourly_only = false ) {

		if ( !wp_next_scheduled( 'mymail_cron' ) ) {

			//main schedule always 5 minutes before full hour
			wp_schedule_event( strtotime( 'midnight' ) - 300, 'hourly', 'mymail_cron' );
			//stop here cause mymail_cron triggers the worker if required
			return true;
		} elseif ( $hourly_only ) {
			return false;
		}

		//remove the WordPress cron if "normal" cron is used
		if ( mymail_option( 'cron_service' ) != 'wp_cron' ) {
			wp_clear_scheduled_hook( 'mymail_cron_worker' );
			return false;
		}

		//add worker only once
		if ( !wp_next_scheduled( 'mymail_cron_worker' ) ) {
			wp_schedule_event( floor( time() / 300 ) * 300, 'mymail_cron_interval', 'mymail_cron_worker' );
			return true;
		}

		return false;

	}


	/**
	 * add custom time to cron
	 *
	 * @param unknown $cron_schedules
	 * @return unknown
	 */
	public function filter_cron_schedules( $cron_schedules ) {

		$cron_schedules['mymail_cron_interval'] = array(
			'interval' => mymail_option( 'interval', 5 ) * 60, // seconds
			'display' => 'myMail Cronjob Interval',
		);

		return $cron_schedules;
	}


	/**
	 *
	 *
	 * @param unknown $general (optional)
	 */
	public function remove_crons( $general = false ) {
		wp_clear_scheduled_hook( 'mymail_cron_worker' );
		if ( $general ) {
			wp_clear_scheduled_hook( 'mymail_cron' );
		}

	}


	/**
	 *
	 */
	public function check() {

		global $wpdb;

		$now = time();
		$cron_service = mymail_option( 'cron_service' );

		if ( !mymail( 'queue' )->size() ):

			mymail_remove_notice( 'check_cron' );

		else:

			$interval = mymail_option( 'interval' ) * 60;
		$last_hit = get_option( 'mymail_cron_lasthit', array(
				'ip' => mymail_get_ip(),
				'timestamp' => $now,
				'oldtimestamp' => $now - $interval,
			) );

		if ( !isset( $last_hit['timestamp'] ) ) {
			return;
		}

		//get real delay...
		$real_delay = max( $interval, $last_hit['timestamp'] - $last_hit['oldtimestamp'] );
		$current_delay = $now - $last_hit['timestamp'];

		//..and compare it with the interval (3 times) - also something in the queue
		if ( ( $current_delay > $real_delay * 3 || !$real_delay && !$current_delay ) ):

			if ( $cron_service == 'wp-cron' && defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
				mymail_notice( sprintf( __( 'The WordPress Cron is disabled! Please remove the %s constant from your wp-config.php file or switch to a real cron job!', 'mymail' ), '<code>DISABLE_WP_CRON</code>' ), 'error', false, 'check_cron' );
			} else {
			mymail_notice( sprintf( __( 'Are your campaigns not sending? You may have to check your %1$s', 'mymail' ), '<a href="options-general.php?page=newsletter-settings&mymail_remove_notice=check_cron#cron"><strong>' . __( 'cron settings', 'mymail' ) . '</strong></a>' ), 'error', false, 'check_cron' );
		}

		$this->update();

		else:

			mymail_remove_notice( 'check_cron' );

		endif;

		endif;

	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function lock( $key = 0 ) {

		if ( mymail_option( 'cron_lock' ) == 'db' ) {

			$this->pid = get_option( 'mymail_cron_lock_' . $key, false );

			if ( $this->pid ) {
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}

			}

			$this->pid = getmypid();
			update_option( 'mymail_cron_lock_' . $key, $this->pid, false );
			return true;

		} else {

			$lockfile = MYMAIL_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {
				//return false;

				// Is running?
				$this->pid = file_get_contents( $lockfile );
				if ( $this->is_locked( $key ) ) {
					return $this->pid;
				} else {
				}
			}

			$this->pid = getmypid();
			register_shutdown_function( array( $this, 'unlock' ), $key );
			file_put_contents( $lockfile, $this->pid );
			return true;

		}

	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function unlock( $key = 0 ) {

		if ( mymail_option( 'cron_lock' ) == 'db' ) {

			update_option( 'mymail_cron_lock_' . $key, false, false );

		} else {
			$lockfile = MYMAIL_UPLOAD_DIR . '/CRON_' . $key . '.lockfile';

			if ( file_exists( $lockfile ) ) {

				unlink( $lockfile );
			}

		}

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function is_locked( $key = null ) {

		global $wpdb;

		//$exec = is_callable( 'shell_exec' ) && false === stripos( ini_get( 'disable_functions' ), 'shell_exec' );
		$exec = false;
		if ( is_integer( $key ) && $exec ) {
			$pids = explode( PHP_EOL, `ps -e | awk '{print $1}'` );
			if ( in_array( $this->pid, $pids ) || empty( $pids[0] ) ) {
				return true;
			}

			return false;

		} else {

			if ( !is_integer( $key ) ) {
				$key = '';
			}

		}

		if ( mymail_option( 'cron_lock' ) == 'db' ) {

			$sql = "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value != ''";
			$res = $wpdb->get_var( $wpdb->prepare( $sql, 'mymail_cron_lock_'.$key.'%' ) );

			return !!$res;

		} else {

			$lockfiles = glob( MYMAIL_UPLOAD_DIR . '/CRON_'.$key.'*.lockfile' );

			return !empty( $lockfiles );

		}

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		$this->update();

	}


	/**
	 *
	 */
	public function on_deactivate() {

		$this->remove_crons( true );

	}


}
