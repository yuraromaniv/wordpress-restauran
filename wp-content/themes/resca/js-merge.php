<?php

/**
 * ThimCssMerge class
 * @version 1.0
 * @author Phong Lo -  foobla.com
 * @link http://phonglo.com
 * @copyright (C) 2016 foobla.com. All Rights Reserved.
 * Install: just add this file in to your theme and include it at the end of function.php file
 * */
class ThimJsMerge {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array($this, 'run'), 100000 );
	}

	public static function getHandleDeps( $handle ) {
		$wp_scripts = wp_scripts();
		$handles = array();
		$item = $wp_scripts->registered[$handle];

		if ( is_array( $item->deps ) && !empty( $item->deps ) ) {
			foreach ( $item->deps as $chandle ) {
				$tmp_handles = self::getHandleDeps( $chandle );
				if ( $tmp_handles && !empty( $tmp_handles ) ) {
					$handles = array_merge( $handles, $tmp_handles );
				}
			}
		}
		if ( !in_array( $handle, $handles ) ) {
			$handles[] = $handle;
		}
		return $handles;
	}

	public static function isHandleMergeAble( $handle ) {
		$wp_scripts = wp_scripts();
		$details = isset( $wp_scripts->registered[$handle] ) ? $wp_scripts->registered[$handle] : false;
		if ( !$details ) {
			return false;
		}
//		if ( !$details->src ) {
//			return false;
//		}
//		if ( $handle == 'jquery' ) {
//			echo '<pre>' . print_r( $wp_scripts->registered[$handle], true ) . '</pre>';
//			exit();
//		}
		$src = $details->src;
		$t = parse_url( $wp_scripts->base_url );
		$base_url = str_replace( $t['scheme'] . ':', '', $wp_scripts->base_url );
		if ( filter_var( $src, FILTER_VALIDATE_URL ) ) {
			if ( strlen( $src ) >= strlen( $wp_scripts->base_url ) && substr( $src, 0, strlen( $wp_scripts->base_url ) ) === $wp_scripts->base_url ) {
				$src = str_replace( $wp_scripts->base_url, '', $src );
			} else {
				return false;
			}
		}

		if ( strpos( $src, $base_url ) !== FALSE ) {
			$src = str_replace( $base_url, '', $src );
		}

		$t = parse_url( $src );
		if ( isset( $t['host'] ) ) {
			return false;
		}

		$src = trim( $src, '/' );

		$src = str_replace( '/', DIRECTORY_SEPARATOR, $src );
		$path = rtrim( ABSPATH, '/\\' ) . DIRECTORY_SEPARATOR . $src;
		return $path;
	}

	public static function find_relative_path( $frompath, $topath ) {
		$from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
		$to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
		$relpath = '';

		$i = 0;
		// Find how far the path is the same
		while ( isset( $from[$i] ) && isset( $to[$i] ) ) {
			if ( $from[$i] != $to[$i] )
				break;
			$i++;
		}
		$j = count( $from ) - 1;
		// Add '..' until the path is the same
		while ( $i <= $j ) {
			if ( !empty( $from[$j] ) )
				$relpath .= '..' . DIRECTORY_SEPARATOR;
			$j--;
		}
		// Go to folder from where it starts differing
		while ( isset( $to[$i] ) ) {
			if ( !empty( $to[$i] ) )
				$relpath .= $to[$i] . DIRECTORY_SEPARATOR;
			$i++;
		}

		// Strip last separator
		return substr( $relpath, 0, -1 );
	}

	public static function repairStyleContent( $path ) {
		$tp_theme_dir = str_replace( '/', DIRECTORY_SEPARATOR, TP_THEME_DIR );
		$relative_path = self::find_relative_path( $tp_theme_dir, dirname( $path ) );
		$relative_url = str_replace( DIRECTORY_SEPARATOR, '/', $relative_path );
		$content = file_get_contents( $path );

		preg_match_all( "/url\s*\(\s*'([^']+)'\)/i", $content, $result, PREG_OFFSET_CAPTURE );
		if ( isset( $result[1] ) && !empty( $result[1] ) ) {
			for ( $i = count( $result[1] ) - 1; $i >= 0; $i-- ) {
				if ( !filter_var( $result[1][$i][0], FILTER_VALIDATE_URL ) ) {
					$new_url = $relative_url . '/' . $result[1][$i][0];
					$content = substr_replace( $content, $new_url, $result[1][$i][1], strlen( $result[1][$i][0] ) );
				}
			}
		}

		preg_match_all( '/url\s*\(\s*"([^"\']+)"\)/i', $content, $result, PREG_OFFSET_CAPTURE );
		if ( isset( $result[1] ) && !empty( $result[1] ) ) {
			for ( $i = count( $result[1] ) - 1; $i >= 0; $i-- ) {
				if ( !filter_var( $result[1][$i][0], FILTER_VALIDATE_URL ) ) {
					$new_url = $relative_url . '/' . $result[1][$i][0];
					$content = substr_replace( $content, $new_url, $result[1][$i][1], strlen( $result[1][$i][0] ) );
				}
			}
		}

		preg_match_all( "/url\s*\(\s*([^\)'\"]+)\)/i", $content, $result, PREG_OFFSET_CAPTURE );
		if ( isset( $result[1] ) && !empty( $result[1] ) ) {
			for ( $i = count( $result[1] ) - 1; $i >= 0; $i-- ) {
				if ( !filter_var( $result[1][$i][0], FILTER_VALIDATE_URL ) ) {
					$new_url = $relative_url . '/' . $result[1][$i][0];
					$content = substr_replace( $content, $new_url, $result[1][$i][1], strlen( $result[1][$i][0] ) );
				}
			}
		}

		return $content;
	}

	public function processPage( $option_key, $option_data = false ) {
		//~ 1. lay danh sach enqueu
		$wp_scripts = wp_scripts();
		
		$queues = $wp_scripts->queue;
		if ( !$option_data || !self::isInCache( $option_key ) ) {
			//~ 2. lay danh sach enqueu thuc su
			$handles = array();
			foreach ( $queues as $handle ) {
				$tmp_handles = self::getHandleDeps( $handle );
				if ( is_array( $tmp_handles ) && !empty( $tmp_handles ) ) {
					$tmp_handles = array_diff( $tmp_handles, $handles );
					$handles = array_merge( $handles, $tmp_handles );
				}
			}
			$handles = array_unique( $handles );

			//~ 3. lay danh sach cac $handle co the merge
			$merge_able = array();
			foreach ( $handles as $handle ) {
				$path = self::isHandleMergeAble( $handle );
				if ( $path ) {
					$merge_able[$handle] = $path;
				}
			}
//
//			echo '<pre>' . print_r( $merge_able, true ) . '</pre>';
//			echo '<pre>' . print_r( $wp_scripts, true ) . '</pre>';
//			exit();
			//~ 4. sua cac cac file css
			//~ 5. meger noi dung cac file css 
			//~	6. dequeue style merged $handle 
			$new_content = '';
			$option_data_arr = array();
			foreach ( $merge_able as $handle => $path ) {
				$extra_data = isset( $wp_scripts->registered[$handle]->extra['data'] ) && $wp_scripts->registered[$handle]->extra['data'] ? $wp_scripts->registered[$handle]->extra['data'] : '';
				if ( !is_file( $path ) && in_array( $handle, $queues ) ) {
					$option_data_arr[] = $handle;
					wp_dequeue_script( $handle );
//					echo '<pre>' . print_r( $wp_scripts->registered[$handle]->src, true ) . '</pre>';
//					echo '<pre>' . print_r( $handle, true ) . '</pre>';
//					exit();
					continue;
				}
				$content = file_get_contents( $path );
				$new_content .= "\n\n/***--------------------------------\n";
				$new_content .= $handle . "\n";
				$new_content .= str_replace( ABSPATH, '', $path );
				$new_content .= "\n--------------------------------***/\n\n";
				$new_content .= "\n//extra_data\n" . $extra_data;
				$new_content .= "\n//file: " . str_replace( ABSPATH, '', $path ) . "\n" . $content;
				if ( in_array( $handle, $queues ) ) {
					$option_data_arr[] = $handle;
					wp_dequeue_script( $handle );
				}
			}
			file_put_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $option_key . '.js', $new_content );
			$option_data = json_encode( $option_data_arr );
			update_option( $option_key, $option_data );
		} elseif ( is_file( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $option_key . '.js' ) ) {
			$option_data_arr = json_decode( $option_data, true );
			foreach ( $option_data_arr as $handle ) {
				wp_dequeue_script( $handle );
			}
		}
		//~ 7. enqueue new file style
		if ( is_file( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $option_key . '.js' ) ) {
			wp_register_script( 'thim-' . $option_key, TP_THEME_URI . $option_key . '.js' );
			wp_enqueue_script( 'thim-' . $option_key );
		}
	}

	public static function isInCache( $option_key ) {
//		return false;
		$file_path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $option_key . '.js';
		if ( !is_file( $file_path ) ) {
			return false;
		}
		$created_time = filemtime( $file_path );
		$now = time();
		if ( $now > $created_time + (7 * 24 * 60 * 60) ) {
			return false;
		}
		return true;
	}

	function run() {
//		return;
		if ( is_admin() ) {
			return;
		}
		// 1 get url
		$current_url = $_SERVER['PHP_SELF'];

		$option_key = 'js-merge-' . (is_user_logged_in() ? '1' : '0') . '-' . md5( $current_url );
		$option_data = get_option( $option_key, false );
		self::processPage( $option_key, $option_data );
	}

}

new ThimJsMerge();
