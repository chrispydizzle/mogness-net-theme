<?php

class ImageElevatorActivate extends Factory325_Activator {
    
    public function activate() {

            $this->plugin->license->setDefaultLicense( array(
                'Category'      => 'free',
                'Build'         => 'free',
                'Title'         => __('OnePress Public License', 'sociallocker'),
                'Description'   => __('Public License is a GPLv2 compatible license. 
                                    It allows you to change this version of the plugin and to
                                    use the plugin free. Please remember this license 
                                    covers only free edition of the plugin. Premium versions are 
                                    distributed with other type of a license.', 'sociallocker')
            ));
        

        
        add_option('imgelv_clipboard_enable', true);
        add_option('imgelv_dragdrop_enable', true);
        
        add_option('imgelv_compression_max_size', 400);  
        add_option('imgelv_compression_quality', 80);   
    } 
}

$clipImages->registerActivation('ImageElevatorActivate');