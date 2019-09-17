<?php

include(IMGEVR_PLUGIN_ROOT . '/admin/activation.php');
    include_once(IMGEVR_PLUGIN_ROOT . '/admin/pages/how-to-use.php');


    include(IMGEVR_PLUGIN_ROOT . '/admin/pages/license-manager.php');



/**
 * Adds scripts and styles in the admin area.
 */
function imgevr_admin_assets() {
    ?>
    <style>
        .notice-clipboard-images.factory-hero .factory-inner-wrap {
            padding-left: 60px !important;
            background: url("<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/notice-background.png' ?>") 2px 0px no-repeat;
        }
    </style>
    <?php
}

add_action( 'admin_print_styles', 'imgevr_admin_assets' );

include(IMGEVR_PLUGIN_ROOT . '/admin/notices.php');
include(IMGEVR_PLUGIN_ROOT . '/admin/ajax/image-uploading.php');
include_once(IMGEVR_PLUGIN_ROOT . '/admin/pages/wp-editor.php');

function imgevr_add_plugin($plugin_array) {  
   $plugin_array['imgelevator'] = IMGEVR_PLUGIN_URL . '/assets/admin/js/image-elevator.tinymce.js';
   return $plugin_array;  
}  

function imgevr_mce_options( $options ) {
    $options['paste_data_images'] = false;
    $options['paste_preprocess'] = 'function(plugin, args) { args.content = window.imgevr.context.processPastedContent( args.content ); }';    
    return $options;
}

function imgevr_mce_css( $mce_css ) {
    if ( ! empty( $mce_css ) ) $mce_css .= ',';
    $mce_css .= IMGEVR_PLUGIN_URL . "/assets/admin/css/editor.css";
    return $mce_css;
}

add_filter('mce_css', 'imgevr_mce_css');
add_filter('mce_external_plugins', 'imgevr_add_plugin'); 
add_filter( 'tiny_mce_before_init', 'imgevr_mce_options', 1, 50 );

/**
 * Returns an URL where we should redirect a user after success activation of the plugin.
 * 
 * @since 3.1.0
 * @return string
 */
function onp_imgevr_license_manager_success_button() {
    return 'Learn how to use the plugin <i class="fa fa-lightbulb-o"></i>';
}
add_action('onp_license_manager_success_button_clipboard-images', 'onp_imgevr_license_manager_success_button');

/**
 * Returns an URL where we should redirect a user after success activation of the plugin.
 * 
 * @since 3.1.0
 * @return string
 */
function onp_imgevr_license_manager_success_redirect() {
    global $sociallocker;
    
    $args = array(
        'fy_plugin' => 'clipboard-images',
        'fy_page' => 'how-to-use'
    );

    return admin_url( 'admin.php?' . http_build_query( $args ) );
}
add_action('onp_license_manager_success_redirect_clipboard-images',  'onp_imgevr_license_manager_success_redirect');