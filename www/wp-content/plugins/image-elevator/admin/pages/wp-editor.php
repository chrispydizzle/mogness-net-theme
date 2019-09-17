<?php

global $imgevr_quick_settings_created;
$imgevr_quick_settings_created = false;

/**
 * Adds the Image Elevator button to the editor.
 */
function imgevr_media_buttons() {
  
    wp_enqueue_script('image-elavator', IMGEVR_PLUGIN_URL . '/assets/admin/js/image-elavator.js', array('jquery'));
    wp_enqueue_style('image-elevator', IMGEVR_PLUGIN_URL . '/assets/admin/css/image-elevator.020503.css' );

    wp_enqueue_style('jquery-qtip-2', IMGEVR_PLUGIN_URL . '/assets/admin/css/jquery.qtip.min.css');
    wp_enqueue_script('jquery-qtip-2', IMGEVR_PLUGIN_URL . '/assets/admin/js/jquery.qtip.min.js', array('jquery'));
    
    ?>
    <?php ?>
    <a class='button imgevr-controller' style="margin-right: 2px;" href='#'><span></span></a>
    <a class='button imgevr-get-premium' href='<?php echo admin_url('admin.php') . '?page=how-to-use-clipboard-images&onp_sl_page=premium' ?>'><span>Get Premium</span></a>
    <?php 
 ?>
    <?php
    
    global $imgevr_quick_settings_created;
    
    if ( $imgevr_quick_settings_created ) return;
    $imgevr_quick_settings_created = true;
    
    add_action('admin_footer', 'imgevr_print_quick_settings');
    add_action('wp_footer', 'imgevr_print_quick_settings');
}
add_action('media_buttons', 'imgevr_media_buttons', 20);

/**
 * Saves the form Quick Settings. 
 */
function imgevr_save_quick_settings() {

    $linksEnabled = empty( $_POST['imgevr_links_enable'] ) ? 0 : 1;
    update_option('imgevr_links_enable', $linksEnabled);
    
    $cssClasses = empty($_POST['imgevr_css_class']) ? '' : trim($_POST['imgevr_css_class']);
    update_option('imgevr_css_class', $cssClasses);
    
    // resizing options
    
    // compression options

        echo json_encode(array(
            'success' => true,
            'imgevr_links_enable' => $linksEnabled,
            'imgevr_css_class' => $cssClasses,
            'imgevr_resizing_enable' => 0,
            'imgevr_resizing_max_width'=>  '',
            'imgevr_resizing_max_height' => '',
            'imgevr_resizing_crop_mode' => 0,
            'imgevr_resizing_save_original' => 0,         
            'imgevr_compression_enable' => 0,
            'imgevr_compression_size' => '',
            'imgevr_compression_jpeg_quality' => ''   
        ));
        
    

    
    exit;
}
add_action('wp_ajax_imgevr_save_quick_settings', 'imgevr_save_quick_settings');

/**
 * Prints the form Quick Settings.
 */
function imgevr_print_quick_settings( ) {
    
    $links = get_option('imgevr_links_enable', false);
    $cssClass = get_option('imgevr_css_class', false);
    
    $resizing = get_option('imgevr_resizing_enable', false);
    $resizingMaxWidth = get_option('imgevr_resizing_max_width', '');
    $resizingMaxHeight = get_option('imgevr_resizing_max_height', '');
    $resizingCropMode = get_option('imgevr_resizing_crop_mode', false);
    $resizingSaveOriginal = get_option('imgevr_resizing_save_original', false);

    $compression = get_option('imgevr_compression_enable', false);
    
    $compressionSize = get_option('imgevr_compression_size', 400);
    $compressionQuality = get_option('imgevr_compression_jpeg_quality', 80); 
    
    if ( empty( $compressionQuality ) ) $compressionQuality = 80;
    ?>
    <div id="imgevr-quick-settings-corner"></div>
    <div id="imgevr-quick-settings" class="imgevr-dialog">
        <div class="imgevr-inner-wrap">
            
            <div class="imgevr-section">
                <div class="imgevr-option imgevr-checkbox-option">
                    <label>
                        <input type="checkbox" id="imgevr-ctrl-links" <?php if ( $links ) echo 'checked="checked"' ?> />
                        <?php _e('Paste images with links.', 'imageelevator') ?>
                    </label>  
                    <div class="imgevr-help">If set, wraps pasted images with the &lt;a&gt; tag.</div>
                </div>
                <div class="imgevr-option">
                    <div class="imgevr-table">
                        <div class="imgevr-row">
                            <div class="imgevr-cell imgevr-collapsed">
                                <label for="imgevr-ctrl-css-class">
                                    <?php _e('CSS classes', 'imageelevator') ?>
                                </label>  
                            </div>    
                            <div class="imgevr-cell">
                                <input type="text" id="imgevr-ctrl-css-class" value="<?php echo $cssClass ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="imgevr-help">Optional. Set extra CSS classes for pasted images.</div>
                </div>           
            </div>

            <div class="imgevr-section">
                
                <?php ?>
                
                <div class="imgevr-option">
                    <label for="imgevr-ctrl-resizing">
                        <input type="checkbox" id="imgevr-ctrl-resizing" disabled="disabled" />
                        <?php _e('Image Resizing', 'imageelevator') ?>
                    </label>
                    <div class="imgevr-help"><?php _e('Resizes pasted images to fit specific dimensions.', 'imageelevator') ?></div>  
                </div>
                
                <?php 
 ?>
                
            </div>

            <div class="imgevr-section">
                
                <?php ?>
                
                <div class="imgevr-option">
                    <label for="imgevr-ctrl-compression">
                        <input type="checkbox" id="imgevr-ctrl-compression" disabled="disabled" />
                        <?php _e('Image Compression', 'imageelevator') ?>  
                    </label>
                    <div class="imgevr-help">Convert pasted images to JPG on the fly.</div>
                </div>
                
                <?php 
 ?>
                
            </div>
            
            <?php ?>
            <div class="imgevr-section">
                <div class="imgevr-alert">Upgrade to the <a href="<?php echo admin_url('admin.php') . '?page=how-to-use-clipboard-images&onp_sl_page=premium' ?>">premium version</a> to unlock the resizing and compression features.</div>
            </div>
            <?php 
 ?>
            
            <div class="imgevr-actions">
                <a href="#" id="imgevr-btn-manage" class="button imgevr-active">
                    <span class="imgevr-active">is active</span>
                    <span class="imgevr-deactive">is inactive</span>
                </a>
                <a href="#" class="button" id="imgevr-btn-cancel"><?php _e('Close', 'imageelevator') ?></a>
                <a href="#" class="button button-primary" id="imgevr-btn-update-rules"><?php _e('Update Rules', 'imageelevator') ?></a>
            </div>
            
        </div>
    </div>
    
    <script>
        if ( !window.imgevr ) window.imgevr = {};
        window.imgevr.assetsUrl = '<?php echo IMGEVR_PLUGIN_URL . '/assets/admin' ?>';
        window.imgevr.ajaxurl = '<?php echo admin_url('admin-ajax.php') ?>';
    </script>
    
    <?php
    ?>
    <script>
        window.imgevr_clipboard_active = true;
    </script>
    <?php
    

}
