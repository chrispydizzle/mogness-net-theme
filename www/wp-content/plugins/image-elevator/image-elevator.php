<?php
/**
Plugin Name: OnePress Image Elevator
Plugin URI: http://onepress-media.com/portfolio
Description: Save tons of time, when adding images into your posts! Paste images from clipboard directly into the post editor! Write articles, tutorials, reviews, news with pleasure by using Image Elevator!
Author: OnePress
Version: 2.5.8
Author URI: http://onepress-media.com/portfolio
*/



define('IMGEVR_PLUGIN_ROOT', dirname(__FILE__));
define('IMGEVR_PLUGIN_URL', plugins_url( null, __FILE__ ));



require('libs/factory/core/boot.php');
global $clipImages;
$clipImages = new Factory325_Plugin(__FILE__, array(
    'name'      => 'clipboard-images',
    'title'     => 'Image Elevator',
    'version'   => '2.5.8',
    'assembly'  => 'free',
    'api'       => 'http://api.byonepress.com/1.1/',
    'premium'   => 'http://api.byonepress.com/public/1.0/get/?product=clipboard-images',
    'account'   => 'http://accounts.byonepress.com/',
    'updates'   => IMGEVR_PLUGIN_ROOT . '/includes/updates/',
    'tracker'   => /*@var:tracker*/'0ec2f14c9e007ba464c230b3ddd98384'/*@*/,
));

// requires factory modules
$clipImages->load(array(
    array( 'libs/factory/bootstrap', 'factory_bootstrap_329', 'admin' ),
    array( 'libs/factory/font-awesome', 'factory_fontawesome_320', 'admin' ),
    array( 'libs/factory/forms', 'factory_forms_328', 'admin' ),
    array( 'libs/factory/notices', 'factory_notices_323', 'admin' ),
    array( 'libs/factory/pages', 'factory_pages_321', 'admin' ),
    array( 'libs/onepress/api', 'onp_api_320' ),
    array( 'libs/onepress/licensing', 'onp_licensing_325' ),
    array( 'libs/onepress/updates', 'onp_updates_324' )
));

// Loads rest of code that is created manually via the standard wordpress plugin api.
if ( is_admin() ) include( IMGEVR_PLUGIN_ROOT . '/admin/init.php' );

        