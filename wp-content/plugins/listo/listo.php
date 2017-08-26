<?php
/*
Plugin Name: Listo
Plugin URI: http://contactform7.com/listo
Description: Listo is a simple plugin that supplies commonly used lists to other plugins and themes.
Author: Takayuki Miyoshi
Author URI: http://ideasilo.wordpress.com/
Text Domain: listo
Domain Path: /languages/
Version: 1.2
*/

define( 'LISTO_VERSION', '1.2' );
define( 'LISTO_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'LISTO_MODULES_DIR', path_join( LISTO_PLUGIN_DIR, 'modules' ) );
define( 'LISTO_LANGUAGES_DIR', path_join( LISTO_PLUGIN_DIR, 'languages' ) );

require_once LISTO_PLUGIN_DIR . '/rest-api.php';

interface Listo {
	public static function items();
	public static function groups();
}

class Listo_Manager {

	private static $lists = array();

	private static function get_list_types() {
		$list_types = array(
			'countries' => 'Listo_Countries',
			'in_subdivisions' => 'Listo_IN_Subdivisions',
			'us_subdivisions' => 'Listo_US_Subdivisions',
			'currencies' => 'Listo_Currencies',
			'time_zones' => 'Listo_Time_Zones' );

		return apply_filters( 'listo_list_types', $list_types );
	}

	public static function get_list_items( $type, $args = '' ) {
		$args = wp_parse_args( $args, array(
			'group' => '',
			'locale' => 'en_US' ) );

		$list_types = self::get_list_types();

		if ( ! isset( $list_types[$type] ) ) {
			return false;
		}

		$class = $list_types[$type];

		if ( ! class_exists( $class ) ) {
			$mod = sanitize_file_name( str_replace( '_', '-', $type ) . '.php' );
			$mod = path_join( LISTO_MODULES_DIR, $mod );

			if ( file_exists( $mod ) ) {
				require_once $mod;
			}
		}

		if ( ! is_callable( array( $class, 'items' ) ) ) {
			return false;
		}

		$group = trim( $args['group'] );
		$locale = trim( $args['locale'] );
		$cloak_ticket = md5( $type . $group . $locale );

		if ( isset( self::$lists[$cloak_ticket] ) ) {
			return self::$lists[$cloak_ticket];
		}

		self::load_textdomain( $locale );

		$items = call_user_func( array( $class, 'items' ) );

		if ( $group && is_callable( array( $class, 'groups' ) ) ) {
			$groups = call_user_func( array( $class, 'groups' ) );

			if ( isset( $groups[$group] ) ) {
				$items = array_intersect_key(
					$items,
					array_fill_keys( $groups[$group], '' ) );
			}
		}

		self::$lists[$cloak_ticket] = $items;
		return $items;
	}

	private static function load_textdomain( $locale = '' ) {
		if ( ! did_action( 'init' ) ) {
			return false;
		}

		static $last_locale = '';
		$domain = 'listo';

		if ( '' == $locale || 'en_US' == $locale ) {
			unload_textdomain( $domain );
			return false;
		}

		if ( $last_locale && $locale == $last_locale ) {
			return false;
		}

		unload_textdomain( $domain );

		$mofile = sprintf( '%s-%s.mo', $domain, $locale );

		if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile )
		|| load_textdomain( $domain, LISTO_LANGUAGES_DIR . '/' . $mofile ) ) {
			$last_locale = $locale;
			return true;
		}

		return false;
	}
}

function listo( $type, $args = '' ) {
	return Listo_Manager::get_list_items( $type, $args );
}
