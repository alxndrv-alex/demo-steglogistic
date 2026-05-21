<?php 
	class FD_Zlog {
		static function log($args = array()) {
			if( defined('CUSTOM_LOG_DIR') && CUSTOM_LOG_DIR ) {
				$log_dir = CUSTOM_LOG_DIR;
			} else {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				$log_dir = get_home_path() . 'log/';
			}

			if( substr($log_dir, -1) != "/" ) {
				$log_dir .= "/";
			}

			$current_date = new DateTime();

			$defaults = [
				'add_date' => true,
				'filename' => 'log-' . $current_date->format('Y-m-d') . '.log',
			];

			$args = wp_parse_args($args, $defaults);

			$text = $args['text'];
			if( !is_string($text) ) {
				$text = print_r($text, true);
			}

			$date_text = $args['add_date'] ? $current_date->format('Y-m-d H:i:s') . "\n" : '';

			try {
				if (!is_dir($log_dir)) {
					mkdir($log_dir);
				};
				self::set_log_file_params($log_dir);

				$filename = $log_dir . $args['filename'];
				if( ! file_exists($filename) ) {
					touch($filename);
				}
				self::set_log_file_params($filename);

				if( is_writable($filename) ) {
					file_put_contents(
						$log_dir . $args['filename'],
						$date_text . $text . "\n\n",
						FILE_APPEND | LOCK_EX
					);
				}
			} catch (Exception $e) {
				return $e;
			}
			return true;
		}

		static function set_log_file_params($filename) {
			if( file_exists($filename) ) {
				try {
					if( function_exists('posix_getpwuid') && function_exists('posix_getgrgid') ) {
						$fileowner_id = fileowner($filename);
						$fileowner_arr = posix_getpwuid($fileowner_id);
						if( $fileowner_arr ) {
							$fileowner = $fileowner_arr['name'];
						}

						$filegroup_id = filegroup($filename);
						$filegroup_arr = posix_getgrgid($filegroup_id);
						if( $filegroup_arr ) {
							$filegroup = $filegroup_arr['name'];
						}

						$perms = fileperms($filename);
						$new_perms = $perms | 0x0010; //add g+w

						if( defined('LOG_FILES_USER') && LOG_FILES_USER && (LOG_FILES_USER != $fileowner) ) {
							@chown($filename, LOG_FILES_USER);
						}
						if( defined('LOG_FILES_GROUP') && LOG_FILES_GROUP && (LOG_FILES_GROUP != $filegroup) ) {
							@chgrp($filename, LOG_FILES_GROUP);
						}
						if( defined('LOG_FILES_GROUP_WRITABLE') && LOG_FILES_GROUP_WRITABLE && ($perms != $new_perms) ) {
							@chmod($filename, $new_perms);
						}
					}
				} catch (Exception $e) {
					return $e;
				}
			}
		}
	}
