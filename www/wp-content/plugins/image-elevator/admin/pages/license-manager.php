<?php

/**
 * License page is a place where a user can check updated and manage the license.
 */
class OnpImgEvr_LicesenceManager extends OnpLicensing325_LicenseManagerPage  {
 
    public $purchaseUrl = 'http://codecanyon.net/item/image-elevator-for-wordpress/4311188/?ref=OnePress';
    public $purchasePrice = '$13';
    public $customTarget = true;
    
    public function configure() {
        global $clipImages;
        
        $this->trial = false;
            $this->menuTarget = factory_pages_321_get_page_id($clipImages, 'how-to-use');
        

    }
}

FactoryPages321::register($clipImages, 'OnpImgEvr_LicesenceManager');