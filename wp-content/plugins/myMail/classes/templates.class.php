<?php
/**
 *
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


class MyMailTemplates {

	public $path;
	public $url;

	private $download_url = 'https://data.newsletter-plugin.com/mymailtemplate/mymail.zip';
	private $headers = array(
		'name' => 'Template Name',
		'label' => 'Name',
		'uri' => 'Template URI',
		'description' => 'Description',
		'author' => 'Author',
		'author_uri' => 'Author URI',
		'version' => 'Version',
	);

	/**
	 *
	 */
	public function __construct() {

		$this->path = MYMAIL_UPLOAD_DIR . '/templates';
		$this->url = MYMAIL_UPLOAD_URI . '/templates';

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'mymail_get_screenshots', array( &$this, 'get_screenshots' ), 10, 4 );

	}


	/**
	 *
	 */
	public function init() {

		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 50 );
		add_action( 'wp_update_plugins', array( &$this, 'get_mymail_templates' ), 99 );
		add_action( 'mymail_copy_template', array( &$this, 'copy_template' ) );
	}


	/**
	 *
	 */
	public function admin_menu() {

		if ( $updates = $this->get_updates() ) {
			$updates = ' <span class="update-plugins count-' . $updates . '" title="' . sprintf( _n( '%d Update available', '%d Updates available', $updates, 'mymail' ), $updates ) . '"><span class="update-count">' . $updates . '</span></span>';
		} else {
			$updates = '';
		}

		$page = add_submenu_page( 'edit.php?post_type=newsletter', __( 'Templates', 'mymail' ), __( 'Templates', 'mymail' ) . $updates, 'mymail_manage_templates', 'mymail_templates', array( &$this, 'templates' ) );
		add_action( 'load-' . $page, array( &$this, 'scripts_styles' ) );
		add_action( 'load-' . $page, array( &$this, 'edit_entry' ), 99 );
		add_action( 'load-' . $page, array( &$this, 'download_envato_template' ), 99 );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_path() {
		return $this->path;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_url() {
		return $this->url;
	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function remove_template( $slug = '' ) {

		$this->templatepath = $this->path . '/' . $slug;

		if ( !file_exists( $this->templatepath . '/index.html' ) ) {
			return false;
		}

		mymail_require_filesystem();

		global $wp_filesystem;
		if ( $wp_filesystem->delete( $this->templatepath, true ) ) {

			$screenshots = MYMAIL_UPLOAD_DIR . '/screenshots/' . $slug;
			if ( is_dir( $screenshots ) ) {
				$wp_filesystem->delete( $screenshots, true );
			}

			return true;

		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $templatefile
	 * @param unknown $renamefolder (optional)
	 * @param unknown $overwrite    (optional)
	 * @param unknown $backup_old   (optional)
	 * @return unknown
	 */
	public function unzip_template( $templatefile, $renamefolder = null, $overwrite = false, $backup_old = false ) {

		global $wp_filesystem;

		mymail_require_filesystem();

		$uploadfolder = MYMAIL_UPLOAD_DIR . '/uploads/' . uniqid();

		if ( !is_dir( $uploadfolder ) ) {
			wp_mkdir_p( $uploadfolder );
		}

		if ( is_wp_error( unzip_file( $templatefile, $uploadfolder ) ) ) {
			$wp_filesystem->delete( $uploadfolder, true );
			return new WP_Error( 'unzip', __( 'Unable to unzip template', 'mymail' ) );
		}

		$templates = $this->get_templates( true );

		if ( $folders = scandir( $uploadfolder ) ) {

			foreach ( $folders as $folder ) {

				if ( in_array( $folder, array( '.', '..' ) ) || !is_dir( $uploadfolder . '/' . $folder ) ) {
					continue;
				}

				if ( !is_null( $renamefolder ) ) {

					$renamefolder = sanitize_file_name( $renamefolder );

					if ( $renamefolder == $folder ) {
						$moved = true;
					} else {
						if ( !( $moved = $wp_filesystem->move( $uploadfolder . '/' . $folder, $uploadfolder . '/' . $renamefolder, true ) ) ) {
							$moved = @rename( $uploadfolder . '/' . $folder, $uploadfolder . '/' . $renamefolder );
						}
					}

					if ( $moved ) {
						$folder = $renamefolder;
					} else {
						$wp_filesystem->delete( $uploadfolder, true );
						return new WP_Error( 'not_writeable', __( 'Unable to save template', 'mymail' ) );
					}
				}

				$templateslug = $folder;

				if ( !$overwrite && in_array( $templateslug, $templates ) ) {

					$data = $this->get_template_data( $uploadfolder . '/' . $folder . '/index.html' );

					$wp_filesystem->delete( $uploadfolder, true );

					return new WP_Error( 'template_exists', sprintf( __( 'Template %s already exists!', 'mymail' ), '"' . $data['name'] . '"' ) );

				}

				//need index.html file
				if ( file_exists( $uploadfolder . '/' . $folder . '/index.html' ) ) {
					$data = $this->get_template_data( $uploadfolder . '/' . $folder . '/index.html' );

					$files = list_files( $uploadfolder . '/' . $folder );

					foreach ( $files as $file ) {
						//remove unallowed files
						if ( is_file( $file ) && !preg_match( '#\.(html|gif|png|jpg|jpeg|tiff|json)$#', $file ) ) {
							$wp_filesystem->delete( $file, true );
						}

					}

					//with name value
					if ( !empty( $data['name'] ) ) {
						wp_mkdir_p( $this->path . '/' . $folder );

						if ( $backup_old ) {
							$old_data = $this->get_template_data( $this->path . '/' . $folder . '/index.html' );
							$old_files = list_files( $this->path . '/' . $folder, 1 );
							$new_files = list_files( $uploadfolder . '/' . $folder, 1 );
							foreach ( $new_files as $file ) {
								if ( is_file( $file ) && preg_match( '#\.html$#', $file ) ) {
									$old_file = str_replace( $uploadfolder, $this->path, $file );
									if ( file_exists( $old_file ) ) {
										if ( md5_file( $file ) == md5_file( $old_file ) ) {
											continue;
										}

										if ( !$wp_filesystem->copy( $old_file, preg_replace( '#\.html$#', '-' . $old_data['version'] . '.html', $old_file ) ) ) {
											copy( $old_file, preg_replace( '#\.html$#', '-' . $old_data['version'] . '.html', $old_file ) );

										}
									}
								}
							}
						}

						copy_dir( $uploadfolder . '/' . $folder, $this->path . '/' . $folder );
					} else {
						$wp_filesystem->delete( $uploadfolder, true );
						return new WP_Error( 'wrong_header', __( 'The header of this template files is missing or corrupt', 'mymail' ) );
					}

				} else {

					$all_files = list_files( $uploadfolder );
					$zips = preg_grep( '#\/([^\/]+)?mymail([^\/]+)?\.zip$#i', $all_files );
					foreach ( $zips as $zip ) {

						$result = $this->unzip_template( $zip, $renamefolder, $overwrite, $backup_old );
						if ( !is_wp_error( $result ) ) {
							$wp_filesystem->delete( $uploadfolder, true );
							return $result;
						}

					}

					$wp_filesystem->delete( $uploadfolder, true );
					return new WP_Error( 'wrong_file', __( 'This is not a valid MyMail template ZIP', 'mymail' ) );

				}

				if ( file_exists( $uploadfolder . '/' . $folder . '/colors.json' ) ) {

					$colors = $wp_filesystem->get_contents( $uploadfolder . '/' . $folder . '/colors.json' );

					if ( $colors ) {
						$colorschemas = json_decode( $colors );

						$customcolors = get_option( 'mymail_colors', array() );

						if ( !isset( $customcolors[$folder] ) ) {

							$customcolors[$folder] = array();
							foreach ( $colorschemas as $colorschema ) {
								$hash = md5( implode( '', $colorschema ) );
								$customcolors[$folder][$hash] = $colorschema;
							}

							update_option( 'mymail_colors', $customcolors );

						}

					}
				}
			}

			$wp_filesystem->delete( $uploadfolder, true );

			if ( $templateslug ) {

				//force a reload
				$this->get_mymail_templates( $templateslug, true );

				return $data;
			}

		}

		return new WP_Error( 'file_error', __( 'There was a problem progressing the file', 'mymail' ) );

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function renew_default_template( $slug = 'mymail' ) {

		if ( !function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$zip = download_url( $this->download_url, 60 );

		if ( is_wp_error( $zip ) ) {
			return $zip;
		}

		return $this->unzip_template( $zip, $slug );

	}


	/**
	 *
	 */
	public function templates() {

		if ( current_user_can( 'mymail_upload_templates' ) ) {
			remove_action( 'post-plupload-upload-ui', 'media_upload_flash_bypass' );
			wp_enqueue_script( 'plupload-all' );
		}

		include MYMAIL_DIR . 'views/templates.php';

	}


	/**
	 *
	 *
	 * @param unknown $return (optional)
	 * @param unknown $nonce  (optional)
	 */
	private function ajax_nonce( $return = null, $nonce = 'mymail_nonce' ) {
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce ) ) {
			die( $return );
		}

	}


	/**
	 *
	 */
	private function ajax_filesystem() {
		if ( 'ftpext' == get_filesystem_method() && ( !defined( 'FTP_HOST' ) || !defined( 'FTP_USER' ) || !defined( 'FTP_PASS' ) ) ) {
			$return['msg'] = __( 'WordPress is not able to access to your filesystem!', 'mymail' );
			$return['msg'] .= "\n" . sprintf( __( 'Please add following lines to the wp-config.php %s', 'mymail' ), "\n\ndefine('FTP_HOST', 'your-ftp-host');\ndefine('FTP_USER', 'your-ftp-user');\ndefine('FTP_PASS', 'your-ftp-password');\n" );
			$return['success'] = false;
			echo json_encode( $return );
			exit;
		}

	}


	/**
	 *
	 *
	 * @param unknown $slugsonly (optional)
	 * @return unknown
	 */
	public function get_templates( $slugsonly = false ) {

		$templates = array();

		if ( !function_exists( 'list_files' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$files = list_files( $this->path, 2 );
		sort( $files );

		foreach ( $files as $file ) {
			if ( basename( $file ) == 'index.html' ) {

				$filename = str_replace( $this->path . '/', '', $file );
				$slug = dirname( $filename );
				if ( !$slugsonly ) {
					$templates[$slug] = $this->get_template_data( $file );
				} else {
					$templates[] = $slug;
				}
			}
		}
		ksort( $templates );
		return $templates;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_all_files() {

		$templates = $this->get_templates();

		$files = array();

		foreach ( $templates as $slug => $data ) {
			$files[$slug] = $this->get_files( $slug );
		}

		return $files;

	}


	/**
	 *
	 *
	 * @param unknown $slug           (optional)
	 * @param unknown $group_versions (optional)
	 * @return unknown
	 */
	public function get_files( $slug = '', $group_versions = false ) {

		if ( empty( $slug ) ) {
			return array();
		}

		$templates = array();
		$files = list_files( $this->path . '/' . $slug, 1 );

		sort( $files );

		$list = array(
			'index.html' => $this->get_template_data( $this->path . '/' . $slug . '/index.html' ),
		);

		if ( file_exists( $this->path . '/' . $slug . '/notification.html' ) ) {
			$list['notification.html'] = $this->get_template_data( $this->path . '/' . $slug . '/notification.html' );
		}

		foreach ( $files as $file ) {

			if ( strpos( $file, '.html' ) && is_file( $file ) ) {
				$list[basename( $file )] = $this->get_template_data( $file );
			}

		}

		if ( !$group_versions ) {
			return $list;
		}

		$group_list = array();
		foreach ( $list as $file => $data ) {
			$v = 'edge';
			if ( preg_match( '#-(([0-9.]+)\.([0-9]+))\.html$#', $file, $hits ) ) {
				$v = $hits[1];
			}
			if ( !isset( $group_list[$v] ) ) {
				$group_list[$v] = array();
			}

			$group_list[$v][$file] = $data;
		}

		return $group_list;

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function get_versions( $slug = null ) {

		$templates = $this->get_templates();
		$versions = array();
		foreach ( $templates as $s => $data ) {

			$versions[$s] = $data['version'];
		}

		return !is_null( $slug ) ? ( isset( $versions[$slug] ) ? $versions[$slug] : null ) : $versions;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_updates() {

		if ( !current_user_can( 'mymail_update_templates' ) ) {
			return 0;
		}

		if ( !$templates = get_option( 'mymail_templates' ) ) {
			return 0;
		}

		if ( empty( $templates['templates'] ) ) {
			return 0;
		}

		return array_sum( wp_list_pluck( $templates['templates'], 'update' ) );

	}


	/**
	 *
	 *
	 * @param unknown $file (optional)
	 * @return unknown
	 */
	public function get_raw_template( $file = 'index.html' ) {
		if ( !file_exists( $this->path . '/' . $this->slug . '/' . $file ) ) {
			return false;
		}

		return file_get_contents( $this->path . '/' . $this->slug . '/' . $file );
	}


	/**
	 *
	 */
	public function scripts_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mymail-templates', MYMAIL_URI . 'assets/css/templates-style' . $suffix . '.css', array(), MYMAIL_VERSION );
		wp_enqueue_style( 'mymail-templates' );
		wp_enqueue_style( 'mymail-codemirror', MYMAIL_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MYMAIL_VERSION );
		wp_enqueue_script( 'mymail-codemirror', MYMAIL_URI . 'assets/js/libs/codemirror' . $suffix . '.js', array(), MYMAIL_VERSION );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
		wp_register_script( 'mymail-templates', MYMAIL_URI . 'assets/js/templates-script' . $suffix . '.js', array( 'jquery' ), MYMAIL_VERSION );
		wp_enqueue_script( 'mymail-templates' );
		wp_localize_script( 'mymail-templates', 'mymailL10n', array(
				'delete_template_file' => __( 'Do you really like to remove file %s from template %s?', 'mymail' ),
				'enter_template_name' => __( 'Please enter the name of the new template', 'mymail' ),
				'uploading' => __( 'uploading zip file %s', 'mymail' ),
				'enter_license' => __( 'Please enter a valid Licensecode!', 'mymail' ),
				'confirm_delete' => __( 'You are about to delete this template "%s"', 'mymail' ),
				'update_note' => __( 'You are about to OVERWRITE your exiting template files with a new version!', 'mymail' ) . "\n\n" . __( 'Please make sure you have a backup of your files.', 'mymail' ),
			) );

	}


	/**
	 *
	 */
	public function download_envato_template() {

		if ( !isset( $_GET['mymail_nonce'] ) ) {
			return;
		}

		if ( wp_verify_nonce( $_GET['mymail_nonce'], 'envato-activate' ) ) {

			$redirect_to = 'edit.php?post_type=newsletter&page=mymail_templates&more';

			if ( isset( $_GET['mymail_error'] ) ) {

				$error = urldecode( $_GET['mymail_error'] );

				//thanks to the crappy Envato API we have to check for the text of the error.
				switch ( $error ) {
				case 'Could not find item with provided item id or purchase code':
					$template = $this->get_mymail_templates( $_GET['mymail_slug'] );
					$error = '<strong>' . __( 'You haven\'t purchased this template with this account!', 'mymail' ) . '</strong>';
					$error .= ' <a class="external" href="' . esc_attr( add_query_arg( array(
								'utm_source' => 'MyMail+Templates+Page',
							), $template['uri'] ) ) . '">' . __( 'get this template', 'mymail' ) . '</a>';
					break;
				default:
					$error = sprintf( 'There was an error loading the template: %s', '<strong>' . $error . '</strong>' );
					break;
				}

				mymail_notice( $error, 'error', true );
			}

			if ( isset( $_GET['mymail_download_url'] ) ) {
				$download_url = urldecode( $_GET['mymail_download_url'] );
				$slug = isset( $_GET['mymail_slug'] ) ? urldecode( $_GET['mymail_slug'] ) : null;

				if ( !function_exists( 'download_url' ) ) {
					include ABSPATH . 'wp-admin/includes/file.php';
				}

				$tempfile = download_url( $download_url );

				$result = $this->unzip_template( $tempfile, $slug, true, true );
				if ( is_wp_error( $result ) ) {
					mymail_notice( sprintf( 'There was an error loading the template: %s', '<strong>' . $result->get_error_message() . '</strong>' ), 'error', true );
				} else {
					mymail_notice( __( 'Template successful loaded!', 'mymail' ), 'updated', true );
					$redirect_to = 'edit.php?post_type=newsletter&page=mymail_templates';
					//force a reload
					update_option( 'mymail_templates', false );
				}

			}

		}

		wp_redirect( $redirect_to );
		exit;

	}


	/**
	 *
	 */
	public function edit_entry() {

		if ( isset( $_GET['action'] ) ) {

			$templates = $this->get_templates();

			switch ( $_GET['action'] ) {

			case 'activate':

				$slug = esc_attr( $_GET['template'] );
				if ( isset( $templates[$slug] ) && wp_verify_nonce( $_GET['_wpnonce'], 'activate-' . $slug ) && current_user_can( 'mymail_manage_templates' ) ) {

					if ( mymail_update_option( 'default_template', esc_attr( $_GET['template'] ) ) ) {
						mymail_notice( sprintf( __( 'Template %s is now your default template', 'mymail' ), '"' . $templates[$slug]['name'] . '"' ), 'updated', true );
						$this->get_screenshots( $slug, 'index.html', true );
						wp_redirect( 'edit.php?post_type=newsletter&page=mymail_templates' );
						exit;
					}
				}
				break;

			case 'delete':

				$slug = esc_attr( $_GET['template'] );
				if ( isset( $templates[$slug] ) && wp_verify_nonce( $_GET['_wpnonce'], 'delete-' . $slug ) && current_user_can( 'mymail_delete_templates' ) ) {

					if ( $slug == mymail_option( 'default_template' ) ) {
						mymail_notice( sprintf( __( 'Cannot delete the default template %s', 'mymail' ), '"' . $templates[$slug]['name'] . '"' ), 'error', true );
					} else if ( $this->remove_template( $slug ) ) {
							mymail_notice( sprintf( __( 'Template %s has been deleted', 'mymail' ), '"' . $templates[$slug]['name'] . '"' ), 'updated', true );
							//force a reload
							update_option( 'mymail_templates', false );
						} else {
						mymail_notice( sprintf( __( 'Template %s has not been deleted', 'mymail' ), '"' . $templates[$slug]['name'] . '"' ), 'error', true );
					}
					wp_redirect( 'edit.php?post_type=newsletter&page=mymail_templates' );
					exit;

				}
				break;

			case 'download':
			case 'update':

				$slug = esc_attr( $_GET['template'] );

				if ( wp_verify_nonce( $_GET['_wpnonce'], 'download-' . $slug ) && current_user_can( 'mymail_manage_templates' ) ) {

					if ( $template = $this->get_mymail_templates( $slug ) ) {

						$this->download_slug = $slug;

						add_filter( 'http_request_args', array( &$this, 'download_http_request_args' ), 100, 2 );
						$tempfile = download_url( $template['download_url'], 3000 );
						remove_filter( 'http_request_args', array( &$this, 'download_http_request_args' ), 100, 2 );

						if ( is_wp_error( $tempfile ) ) {

							( $tempfile->get_error_code() == 'http_404' && !$tempfile->get_error_message() )
								? mymail_notice( '[ 404 ] ' . sprintf( __( 'File does not exist. Please contact %s for help!', 'mymail' ), '<a href="' . $template['author_uri'] . '">' . $template['author'] . '</a>' ), 'error', true )
								: mymail_notice( sprintf( __( 'There was an error: %s', 'mymail' ), '"<strong>' . $tempfile->get_error_message() . '</strong>"' ), 'error', true );

							$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';

						} else {

							$result = $this->unzip_template( $tempfile, null, true, true );

							if ( is_wp_error( $result ) ) {
								mymail_notice( sprintf( __( 'There was an error: %s', 'mymail' ), '"<strong>' . $result->get_error_message() . '</strong>"' ), 'error', true );

								$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';

							} else if ( $result ) {

									( $_GET['action'] == 'update' )
										? mymail_notice( __( 'Template successful updated!', 'mymail' ), 'updated', true )
										: mymail_notice( __( 'Template successful loaded!', 'mymail' ) . ' ' . ( $slug != mymail_option( 'default_template' ) ? '<a href="edit.php?post_type=newsletter&page=mymail_templates&action=activate&template=' . $slug . '&_wpnonce=' . wp_create_nonce( 'activate-' . $slug ) . '" class="button button-primary button-small">' . __( 'Use as default', 'mymail' ) . '</a>' : '' ), 'updated', true );

									//force a reload
									update_option( 'mymail_templates', false );
									$this->get_screenshots( $slug, 'index.html', true );

								}

							$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? remove_query_arg( 'more', $_SERVER['HTTP_REFERER'] ) : 'edit.php?post_type=newsletter&page=mymail_templates';
							$redirect = add_query_arg( array( 'new' => $slug ), $redirect );

							@unlink( $tempfile );
						}

					}

					wp_redirect( $redirect );
					exit;

				}
				break;

			case 'license':

				$slug = esc_attr( $_GET['template'] );

				if ( wp_verify_nonce( $_GET['_wpnonce'], 'license-' . $slug ) && current_user_can( 'mymail_manage_templates' ) ) {

					if ( $template = $this->get_mymail_templates( $slug ) ) {

						$this->download_slug = $slug;

						if ( $this->update_license( $slug, $_GET['license'] ) ) {

							mymail_notice( __( 'Licensecode has been updated!', 'mymail' ), 'updated', true );

						}

					}

					$redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : 'edit.php?post_type=newsletter&page=mymail_templates&more';
					wp_redirect( $redirect );
					exit;

				}
				break;

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $r
	 * @param unknown $url
	 * @return unknown
	 */
	public function download_http_request_args( $r, $url ) {

		include ABSPATH . WPINC . '/version.php';

		if ( !$wp_version ) {
			global $wp_version;
		}

		$template = $this->get_mymail_templates( $this->download_slug );

		$version = $this->get_versions( $this->download_slug );

		$body = array(
			'licensecode' => $this->get_license( $this->download_slug ),
			'version' => $version,
			'wp-version' => $wp_version,
			'referer' => home_url(),
			'multisite' => is_multisite(),
		);

		$r['headers'] = array(
			'x-updatecenter' => serialize( $body ),
		);

		return $r;

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	private function get_license( $slug = null ) {

		$licenses = get_option( 'mymail_template_licenses', array() );

		//sanitize
		$licenses = preg_grep( '/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $licenses );

		return is_null( $slug ) ? $licenses : ( isset( $licenses[$slug] ) ? $licenses[$slug] : null );

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $license
	 * @return unknown
	 */
	private function update_license( $slug, $license ) {

		$licenses = get_option( 'mymail_template_licenses', array() );

		$licenses[$slug] = $license;

		return update_option( 'mymail_template_licenses', $licenses );

	}


	/**
	 *
	 *
	 * @param unknown $slug (optional)
	 * @return unknown
	 */
	public function remove_screenshot( $slug = null ) {

		global $wp_filesystem;

		$folder = MYMAIL_UPLOAD_DIR . '/screenshots';

		if ( !is_null( $slug ) ) {
			$folder .= '/' . $slug;
		}

		if ( !is_dir( $folder ) ) {
			return;
		}

		mymail_require_filesystem();

		return $wp_filesystem->delete( $folder, true );

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file (optional)
	 * @param unknown $size (optional)
	 * @return unknown
	 */
	public function get_screenshot( $slug, $file = 'index.html', $size = 600 ) {

		global $wp_filesystem;

		$fileuri = $this->url . '/' . $slug . '/' . $file;
		$filedir = $this->path . '/' . $slug . '/' . $file;

		if ( !file_exists( $filedir ) ) {
			return;
		}

		$hash = md5_file( $filedir );

		$screenshotfile = MYMAIL_UPLOAD_DIR . '/screenshots/' . $slug . '/' . $hash . '.jpg';
		$screenshoturi = MYMAIL_UPLOAD_URI . '/screenshots/' . $slug . '/' . $hash . '.jpg';

		//serve saved
		if ( file_exists( $screenshotfile ) && file_exists( $filedir ) ) {
			$url = str_replace( ' ', '%20', $screenshoturi );
		} else if ( !file_exists( $filedir ) ) {
				$url = 'https://mymailapp.github.io/preview/not_available.gif';
			} else if ( mymail_is_local() ) {
				$url = 'https://mymailapp.github.io/preview/not_available.gif';
			} else {

			static $mymail_get_screenshot_delay;

			//get count based on the numbers in the "queue" (cron)
			if ( !$mymail_get_screenshot_delay ) {
				$mymail_get_screenshot_delay = substr_count( serialize( get_option( 'cron' ) ), 'mymail_get_screenshots' );
			}

			$process_module = $is_default = $slug == mymail_option( 'default_template' );
			//$process_module = true;

			$delay = $is_default ? 0 : 60 * ( $mymail_get_screenshot_delay++ );

			$this->schedule_screenshot( $slug, $file, $process_module, $delay );

			$url = 'https://mymailapp.github.io/preview/create.gif';

		}

		return $url;
	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file    (optional)
	 * @param unknown $modules (optional)
	 * @param unknown $async   (optional)
	 */
	public function get_screenshots( $slug, $file = 'index.html', $modules = false, $async = true ) {

		global $wp_filesystem;

		$slug = ( $slug );
		$file = ( $file );

		$filedir = MYMAIL_UPLOAD_DIR . '/templates/' . $slug . '/' . $file;
		$fileuri = MYMAIL_UPLOAD_URI . '/templates/' . $slug . '/' . $file;

		if ( !file_exists( $filedir ) ) {
			return;
		}

		$hash = md5_file( $filedir );

		$screenshot_folder = MYMAIL_UPLOAD_DIR . '/screenshots/' . $slug . '/';
		$screenshot_modules_folder = MYMAIL_UPLOAD_DIR . '/screenshots/' . $slug . '/modules/' . $hash . '/';
		$screenshotfile = MYMAIL_UPLOAD_DIR . '/screenshots/' . $slug . '/' . $hash . '.jpg';
		$screenshoturi = MYMAIL_UPLOAD_URI . '/screenshots/' . $slug . '/' . $hash . '.jpg';

		mymail_require_filesystem();

		if ( !is_dir( $screenshot_folder ) ) {
			wp_mkdir_p( $screenshot_folder );
		}

		//not on localhost
		if ( !mymail_is_local() ) {

			$url = 'https://s.wordpress.com/mshots/v1/' . ( rawurlencode( $fileuri . '?c=' . $hash ) ) . '?w=600&h=800';

			$response = wp_remote_get( $url, array( 'redirection' => 0, 'method' => 'HEAD' ) );
			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 == $code ) {

				if ( !function_exists( 'download_url' ) ) {
					include ABSPATH . 'wp-admin/includes/file.php';
				}

				$tmp_file = download_url( $url );

				if ( !is_wp_error( $tmp_file ) ) {
					if ( !is_dir( dirname( $screenshotfile ) ) ) {
						wp_mkdir_p( dirname( $screenshotfile ) );
					}

					if ( !$wp_filesystem->copy( $tmp_file, $screenshotfile ) ) {
						@copy( $tmp_file, $screenshotfile );
					}
				}

			} else if ( 307 == $code ) {

					$this->schedule_screenshot( $slug, $file, false, 60 );

				} else {

			}
		}

		if ( !$modules ) {
			return;
		}

		$raw = file_get_contents( $filedir );
		preg_match( '#<modules([^>]*)>(.*)<\/modules>#is', $raw, $matches );

		if ( empty( $matches ) ) {
			return;
		}

		$modules_html = $matches[0];

		$request_url = 'http://screenshot.newsletter-plugin.com/v2/';

		$file_size = strlen( $raw );
		$hash = md5( $modules_html );

		$headers = array(
			'accept' => 'application/json',
			'x-mymail-length' => $file_size,
			'x-mymail-hash' => $hash,
			'x-mymail-version' => MYMAIL_VERSION,
			'x-mymail-site' => get_bloginfo( 'url' ),
			'x-mymail-url' => $fileuri,
		);

		$response = wp_remote_get( $request_url, array(
				'headers' => $headers,
				'timeout' => 2,
			) );

		$response_headers = wp_remote_retrieve_headers( $response );
		$response_code = wp_remote_retrieve_response_code( $response );

		//file hasn't been generated yet
		if ( 404 == $response_code ) {

			$headers['content-type'] = 'application/binary';
			$headers['content-length'] = $file_size;

			$response = wp_remote_post( $request_url, array(
					'headers' => $headers,
					'body' => $raw,
					'timeout' => $async ? 1 : 20,
					'blocking' => $async ? false : true,
				) );

			unset( $raw );

			if ( $async ) {
				$this->schedule_screenshot( $slug, $file, true, 20, $async );
				return;

			};

			$response_headers = wp_remote_retrieve_headers( $response );
			$response_code = wp_remote_retrieve_response_code( $response );

		}

		if ( 200 != $response_code ) {

			switch ( $response_code ) {
			case 500:
			case 503:
				$this->schedule_screenshot( $slug, $file, true, 1800, $async );
				break;
			}

			return;

		}

		$body = wp_remote_retrieve_body( $response );
		$result = json_decode( $body );

		if ( !function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$processed = 0;

		if ( isset( $result->modules ) && is_array( $result->modules ) ) {
			foreach ( $result->modules as $i => $fileurl ) {
				if ( file_exists( $screenshot_modules_folder . $i . '.jpg' ) ) {
					continue;
				}

				$tempfile = download_url( $fileurl );

				if ( !is_wp_error( $tempfile ) ) {

					if ( function_exists( 'exif_imagetype' ) && 2 != exif_imagetype( $tempfile ) ) {
						continue;
					}

					if ( !is_dir( $screenshot_modules_folder ) ) {
						wp_mkdir_p( $screenshot_modules_folder );
					}

					if ( !$wp_filesystem->copy( $tempfile, $screenshot_modules_folder . $i . '.jpg' ) ) {
						copy( $tempfile, $screenshot_modules_folder . $i . '.jpg' );
					}

					$processed++;

					if ( $processed >= 30 ) {
						$this->schedule_screenshot( $slug, $file, true, 10 );
						break;
					}
				}

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file
	 * @param unknown $modules (optional)
	 * @param unknown $delay   (optional)
	 * @param unknown $async   (optional)
	 */
	public function schedule_screenshot( $slug, $file, $modules = false, $delay = 0, $async = true ) {

		if ( !mymail_option( 'module_thumbnails' ) ) {
			$modules = false;
		}

		if ( !wp_next_scheduled( 'mymail_get_screenshots', array( $slug, $file, $modules, $async ) ) && !wp_next_scheduled( 'mymail_get_screenshots', array( $slug, $file, true, $async ) ) ) {
			wp_schedule_single_event( time() + $delay, 'mymail_get_screenshots', array( $slug, $file, $modules, $async ) );
		}

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		try {
			$this->copy_template();
		} catch ( Exception $e ) {
			if ( !wp_next_scheduled( 'mymail_copy_template' ) ) {
				wp_schedule_single_event( time(), 'mymail_copy_template' );
			}

		}


	}


	/**
	 *
	 */
	public function copy_template() {

		$uploadfolder = wp_upload_dir();

		if ( !is_dir( $uploadfolder['basedir'] . '/myMail/templates' ) ) {

			mymail_require_filesystem();

			wp_mkdir_p( $uploadfolder['basedir'] . '/myMail/templates' );
			copy_dir( MYMAIL_DIR . 'templates', $uploadfolder['basedir'] . '/myMail/templates' );

		}

	}


	/**
	 *
	 *
	 * @param unknown $nodes
	 * @param unknown $separator (optional)
	 * @return unknown
	 */
	private function get_html_from_nodes( $nodes, $separator = '' ) {

		$parts = array();

		if ( !$nodes ) {
			return '';
		}

		foreach ( $nodes as $node ) {
			$parts[] = $this->get_html_from_node( $node );
		}

		return implode( $separator, $parts );
	}


	/**
	 *
	 *
	 * @param unknown $node
	 * @return unknown
	 */
	private function get_html_from_node( $node ) {

		$html = $node->ownerDocument->saveXML( $node );
		return $html;

	}


	/**
	 *
	 *
	 * @param object  $node
	 * @param unknown $name
	 * @param unknown $attributes (optional)
	 * @return unknown
	 */
	private function dom_rename_element( DOMElement $node, $name, $attributes = true ) {
		$renamed = $node->ownerDocument->createElement( $name );

		if ( $attributes ) {
			foreach ( $node->attributes as $attribute ) {
				$renamed->setAttribute( $attribute->nodeName, $attribute->nodeValue );
			}
		}
		while ( $node->firstChild ) {
			$renamed->appendChild( $node->firstChild );
		}

		return $node->parentNode->replaceChild( $renamed, $node );
	}


	/**
	 *
	 *
	 * @param unknown $file
	 * @return unknown
	 */
	public function get_template_data( $file ) {

		$cache_key = 'get_template_data_' . md5( $file );
		$cached = mymail_cache_get( $cache_key );
		if ( $cached ) {
			return $cached;
		}

		$basename = false;
		if ( !file_exists( $file ) && is_string( $file ) ) {
			$file_data = $file;
		} else {
			$basename = basename( $file );
			$fp = fopen( $file, 'r' );
			$file_data = fread( $fp, 2048 );
			fclose( $fp );
		}

		foreach ( $this->headers as $field => $regex ) {
			preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field} );
			if ( !empty( ${$field} ) ) {
				${$field} = _cleanup_header_comment( ${$field}[1] );
			} else {
				${$field} = '';
			}

		}

		$file_data = compact( array_keys( $this->headers ) );

		if ( empty( $file_data['name'] ) ) {
			$file_data['name'] = ucwords( basename( dirname( $file ) ) );
		}

		if ( empty( $file_data['author'] ) ) {
			$file_data['author'] = __( 'unknown', 'mymail' );
		}

		if ( preg_match( '#index(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$file_data['label'] = __( 'Base', 'mymail' ) . ( !empty( $hits[2] )
				? ' ' . $hits[2] : '' );
		}

		if ( preg_match( '#notification(-([0-9.]+))?\.html?#', $basename, $hits ) ) {
			$file_data['label'] = __( 'Notification', 'mymail' ) . ( !empty( $hits[2] )
				? ' ' . $hits[2] : '' );
		}

		if ( empty( $file_data['label'] ) ) {
			$file_data['label'] = substr( $basename, 0, strrpos( $basename, '.' ) );
		}

		$file_data['label'] = str_replace( ' rtl', ' (RTL)', $file_data['label'] );

		mymail_cache_set( $cache_key, $file_data );
		return $file_data;

	}


	/**
	 *
	 *
	 * @param unknown $slug  (optional)
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function get_mymail_templates( $slug = null, $force = false ) {

		$timeout = defined( 'DOING_CRON' ) && DOING_CRON ? 20 : 5;
		$mymail_templates = get_option( 'mymail_templates', false );
		if ( !$mymail_templates ) {
			$mymail_templates = array( 'timestamp' => 0, 'templates' => array() );
			$timeout = 10;
		}

		//time before next check
		$pause = 86400;
		$url = 'http://mymailapp.github.io/v2/templates.json';

		if ( time() - $mymail_templates['timestamp'] <= $pause && !$force ) {
			$templates = $mymail_templates['templates'];
			return !is_null( $slug ) && isset( $templates[$slug] ) ? $templates[$slug] : $templates;
		}

		$response = wp_remote_get( $url, array( 'timeout' => 3 ) );

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code != 200 || is_wp_error( $response ) ) {
			$templates = $mymail_templates['templates'];
		} else {
			$templates = json_decode( $response_body, true );
			$templates = $this->get_mymail_templates_info( $templates, $timeout );
		}

		update_option( 'mymail_templates', array(
				'timestamp' => time(),
				'templates' => $templates,
			) );

		return !is_null( $slug ) && isset( $templates[$slug] ) ? $templates[$slug] : $templates;

	}


	/**
	 *
	 *
	 * @param unknown $mymail_templates
	 * @param unknown $timeout          (optional)
	 * @return unknown
	 */
	private function get_mymail_templates_info( $mymail_templates, $timeout = 5 ) {

		$endpoints = wp_list_pluck( $mymail_templates, 'endpoint' );
		$templates = $this->get_templates();
		include ABSPATH . WPINC . '/version.php';

		if ( !$wp_version ) {
			global $wp_version;
		}

		$referer = home_url();
		$multisite = is_multisite();
		$versions = $this->get_versions();
		$collection = array();
		foreach ( $endpoints as $slug => $endpoint ) {
			if ( !isset( $collection[$endpoint] ) ) {
				$collection[$endpoint] = array();
			}

			$collection[$endpoint][$slug] = isset( $templates[$slug] ) ? array(
				'licensecode' => $this->get_license( $slug ),
				'version' => $templates[$slug]['version'],
				'wp-version' => $wp_version,
				'referer' => $referer,
				'multisite' => $multisite,
			) : array();

			$collection[$endpoint][$slug]['envato_item_id'] = isset( $mymail_templates[$slug]['envato_item_id'] ) ? $mymail_templates[$slug]['envato_item_id'] : null;
		}

		$default = array(
			'name' => __( 'unknown', 'mymail' ),
			'image' => null,
			'description' => null,
			'uri' => null,
			'endpoint' => null,
			'new_version' => false,
			'update' => false,
			'author' => __( 'unknown', 'mymail' ),
			'author_profile' => '',
			'homepage' => null,
			'download_url' => null,
		);

		foreach ( $collection as $endpoint => $items ) {

			$post = array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
					'x-ip' => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : null,
				),
				'timeout' => $timeout,
			);

			$envato_items = wp_list_pluck( $items, 'envato_item_id' );

			if ( array_filter( $envato_items ) ) {

				$remote_url = $endpoint;

				$response = wp_remote_get( add_query_arg( array(
							'items' => $envato_items,
						), $remote_url ), $post );

			} else {

				$remote_url = trailingslashit( $endpoint );

				$response = wp_remote_get( add_query_arg( array(
							'updatecenter_action' => 'versions',
							'updatecenter_slug' => array_keys( $items ),
							'updatecenter_data' => array_values( $items ),
						), $remote_url ), $post );

			}

			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = trim( wp_remote_retrieve_body( $response ) );

			if ( $response_code != 200 || is_wp_error( $response ) ) {
				foreach ( $items as $slug => $data ) {
					if ( isset( $mymail_templates[$slug] ) ) {
						unset( $mymail_templates[$slug] );
					}

				}
				continue;

			} else {

				$response = array_values( json_decode( $response_body, true ) );
				$i = -1;
				foreach ( $items as $slug => $data ) {
					$i++;
					$mymail_templates[$slug] = wp_parse_args( $mymail_templates[$slug], $default );

					$mymail_templates[$slug]['version'] = isset( $versions[$slug] ) ? $versions[$slug] : null;
					if ( gettype( $response ) != 'array' || empty( $response[$i] ) ) {
						unset( $mymail_templates[$slug] );
						continue;
					}
					if ( isset( $response[$i]['name'] ) ) {
						$mymail_templates[$slug]['name'] = esc_attr( strip_tags( $response[$i]['name'] ) );
					}

					if ( isset( $response[$i]['version'] ) ) {
						$mymail_templates[$slug]['new_version'] = esc_attr( strip_tags( $response[$i]['version'] ) );
					}

					$mymail_templates[$slug]['update'] = isset( $data['version'] ) && version_compare( $response[$i]['version'], $data['version'], '>' );
					if ( isset( $response[$i]['author'] ) ) {
						$mymail_templates[$slug]['author'] = esc_attr( strip_tags( $response[$i]['author'] ) );
					}

					if ( isset( $response[$i]['author_profile'] ) ) {
						$mymail_templates[$slug]['author_profile'] = esc_url( strip_tags( $response[$i]['author_profile'] ) );
					}

					if ( isset( $response[$i]['homepage'] ) ) {
						$mymail_templates[$slug]['homepage'] = esc_url( strip_tags( $response[$i]['homepage'] ) );
					}

					if ( isset( $response[$i]['description'] ) ) {
						$mymail_templates[$slug]['description'] = strip_tags( $response[$i]['description'], '<a><strong>' );
					}

					if ( isset( $response[$i]['download_link'] ) ) {
						$mymail_templates[$slug]['download_url'] = esc_url( strip_tags( $response[$i]['download_link'] ) );
					}

				}

			}

		}
		return $mymail_templates;

	}


	/**
	 *
	 *
	 * @param unknown $errors (optional)
	 */
	public function media_upload_form( $errors = null ) {

		global $type, $tab, $pagenow, $is_IE, $is_opera;

		if ( function_exists( '_device_can_upload' ) && !_device_can_upload() ) {
			echo '<p>' . __( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="http://wordpress.org/extend/mobile/">native app for your device</a> instead.', 'mymail' ) . '</p>';
			return;
		}

		$upload_size_unit = $max_upload_size = wp_max_upload_size();
		$sizes = array( 'KB', 'MB', 'GB' );

		for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
			$upload_size_unit /= 1024;
		}

		if ( $u < 0 ) {
			$upload_size_unit = 0;
			$u = 0;
		} else {
			$upload_size_unit = (int) $upload_size_unit;
		}
?>

    <div id="media-upload-notice"><?php

		if ( isset( $errors['upload_notice'] ) ) {
			echo $errors['upload_notice'];
		}

		?></div>
    <div id="media-upload-error"><?php

		if ( isset( $errors['upload_error'] ) && is_wp_error( $errors['upload_error'] ) ) {
			echo $errors['upload_error']->get_error_message();
		}

		?></div>
    <?php
		if ( is_multisite() && !is_upload_space_available() ) {
			return;
		}

		$post_params = array(
			"action" => "mymail_template_upload_handler",
			"_wpnonce" => wp_create_nonce( 'mymail_nonce' ),
		);
		$upload_action_url = admin_url( 'admin-ajax.php' );

		$plupload_init = array(
			'runtimes' => 'html5,silverlight,flash,html4',
			'browse_button' => 'plupload-browse-button',
			'container' => 'plupload-upload-ui',
			'drop_element' => 'drag-drop-area',
			'file_data_name' => 'async-upload',
			'multiple_queues' => true,
			'max_file_size' => $max_upload_size . 'b',
			'url' => $upload_action_url,
			'flash_swf_url' => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters' => array( array( 'title' => __( 'MyMail Template ZIP file', 'mymail' ), 'extensions' => 'zip' ) ),
			'multipart' => true,
			'urlstream_upload' => true,
			'multipart_params' => $post_params,
			'multi_selection' => false,
		);

?>

    <script type="text/javascript">
    var wpUploaderInit = <?php echo json_encode( $plupload_init ); ?>;
    </script>

    <div id="plupload-upload-ui" class="hide-if-no-js">
    <div id="drag-drop-area">
        <div class="drag-drop-inside">
        <p class="drag-drop-info"><?php esc_html_e( 'Drop your ZIP file here to upload new template', 'mymail' );?></p>
        <p><?php _ex( 'or', 'Uploader: Drop files here - or - Select Files', 'mymail' );?></p>
        <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select File', 'mymail' );?>" class="button" /></p>
        <p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', 'mymail' ), esc_html( $upload_size_unit . $sizes[$u] ) );?></p>
        <p class="uploadinfo"></p>
        </div>
    </div>
    </div>

    <div id="html-upload-ui" class="hide-if-js">
        <p id="async-upload-wrap">
            <label class="screen-reader-text" for="async-upload"><?php esc_html_e( 'Upload', 'mymail' );?></label>
            <input type="file" name="async-upload" id="async-upload" />
            <?php submit_button( __( 'Upload', 'mymail' ), 'button', 'html-upload', false );?>
            <a href="#" onclick="try{top.tb_remove();}catch(e){}; return false;"><?php esc_html_e( 'Cancel', 'mymail' );?></a>
        </p>
        <div class="clear"></div>
    </div>

    <?php
		if ( ( $is_IE || $is_opera ) && $max_upload_size > 100 * 1024 * 1024 ) {?>
        <span class="big-file-warning"><?php esc_html_e( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'mymail' );?></span>
    <?php }

	}


}
