<?php

/* 
 * Plugin Name:   idavi
 * Version:       1.0.2
 * Plugin URI:    http://idavi.com
 * Description:   Automatically post products from idavi RSS feeds. 
 * Author:        Mike Myers
 * Author URI:    http://idavi.com
 */


@error_reporting(0);
require_once(ABSPATH . WPINC . "/rss.php");

//include_once("versioning.inc.php");
include_once("idavi_mailads.php");
include_once("functions.inc.php");
include_once("idavi_hopad.php");
include_once("idavi_categories.php");
include_once("idavi_ads.php");



if (!function_exists("is_vector")) {
   function is_vector( &$array ) {
      if ( !is_array($array) || empty($array) ) {
         return -1;
      }
      $next = 0;
      foreach ( $array as $k => $v ) {
         if ( $k !== $next ) return false;
         $next++;
      }
      return true;
   }
}

//determine version of WP we are using
$mode = 21;
if(floatval($wp_version) >= 2.7) {
	$mode = 27;
}

//idavi version

define("MFOS_APP_NAME", "Idavi");
define("MFOS_APP_VERSION", "1.0.2");

//set path info
$path = trailingslashit(dirname(__FILE__));

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );

if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );


//Load Our Options
$idavi_opts = "";
idavi_loadOptions();
register_activation_hook( __FILE__, 'idaviads_init' );
	
// Setup the "hourly" action so we can do our stuff.
add_action('idavi_hourly', 'idavi_run');




if (function_exists('add_action')) {
   	add_action('admin_menu', 'idavi_menu_setup');
	add_action('admin_menu', 'idaviads_add_post_boxes');
	add_action('admin_notices', 'idaviads_admin_warning');
	add_action('edit_post', 'add_idaviads_custom_field');
	add_action('publish_post', 'add_idaviads_custom_field');
	add_action('save_post', 'add_idaviads_custom_field');
	
	add_filter('pre_get_posts','idavi_cat_exclude_categories');
	add_filter('the_content', 'idaviads_insert', 9);
	
   register_activation_hook(__FILE__, 'idavi_activate');
   register_deactivation_hook(__FILE__, 'idavi_deactivate');
}



?>