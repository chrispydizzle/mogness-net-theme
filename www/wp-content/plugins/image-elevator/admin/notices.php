<?php

add_filter('factory_notices_clipboard-images', 'imgevr_admin_notices', 10, 2);

function imgevr_admin_notices( $notices ) {
    global $clipImages;
    $forceToShowNotices = defined('ONP_DEBUG_IMGEVR_OFFER_PREMIUM') && ONP_DEBUG_IMGEVR_OFFER_PREMIUM;

    if ( ( !$clipImages->license || $clipImages->build !== "free" ) && !$forceToShowNotices ) return $notices;
    
    $closed = get_option('factory_notices_closed', array());
    
    $lastCloase  = isset( $closed['imgevr-offer-to-purchase'] ) 
        ? $closed['imgevr-offer-to-purchase']['time'] 
        : 0;
    
    // shows every 7 days
    if ( ( time() - $lastCloase > 60*60*7 ) || $forceToShowNotices ) {
        
        $notices[] = array(
            'id'        => 'imgevr-offer-to-purchase',
            
            'class'     => 'call-to-action ',
            'icon'      => 'fa fa-arrow-circle-o-up',
            'header'    => '<span class="onp-hightlight">' . __('Explode your productivity with Image Elevator!', 'sociallocker') . '</span>',
            'message'   => __('Rename images in the editor by a single click and compress large images on the fly! Upgrade your copy of the Image Elevator to get these features.', 'sociallocker'),   
            'plugin'    => $clipImages->pluginName,
            'where'     => array('plugins','dashboard', 'edit'),

            // buttons and links
            'buttons'   => array(
                array(
                    'title'     => '<i class="fa fa-arrow-circle-o-up"></i> Learn More & Upgrade',
                    'class'     => 'button button-primary',
                    'action'    => admin_url('admin.php') . '?page=how-to-use-clipboard-images&onp_sl_page=premium'
                ),
                array(
                    'title'     => __('No, thanks', 'onepress-ru'),
                    'class'     => 'button',
                    'action'    => 'x'
                )
            )
        ); 
    }
    
    return $notices;
}