<?php
/**
 * The file contains a short help info.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2014, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * Common Settings
 */
class OnpImgEvr_HowToUsePage extends FactoryPages321_AdminPage  {

    public $id = "how-to-use";
    
    public function __construct(Factory325_Plugin $plugin) {   
        parent::__construct($plugin);

        $this->menuTitle = __('Image Elevator', 'sociallocker');
        $this->menuIcon = IMGEVR_PLUGIN_URL . '/assets/admin/img/menu-icon.png';
    }
  
    public function assets($scripts, $styles) {
        $this->scripts->request('jquery');
        $this->styles->add(IMGEVR_PLUGIN_URL . '/assets/admin/css/howtouse.020001.css');  
        $this->styles->request('bootstrap.core', 'bootstrap');
    }
    
    protected $_pages = false;
    
    protected function getPages() {
        if ( $this->_pages !== false ) return $this->_pages;
            
            $items = array(
                array(
                    'name' => 'getting-started',
                    'function' => array( $this, 'gettingStarted'),
                    'title' => __('Gettings started', 'sociallocker')
                ),
                array(
                    'name' => 'reviews',
                    'function' => array( $this, 'browsers'),
                    'title' => __('Browser Compatibility', 'sociallocker')
                ),
                array(
                    'name' => 'troubleshooting',
                    'function' => array( $this, 'troubleshooting'),
                    'title' => __('Troubleshooting', 'sociallocker')
                ),
                array(
                    'name' => 'premium',
                    'function' => array( $this, 'premium'),
                    'title' => '<i class="fa fa-star-o"></i> ' . __('Premium Version', 'sociallocker') . ' <i class="fa fa-star-o"></i>'
                )
            );
            
        

        
        
        
        $this->_pages = apply_filters( 'onp_sl_help_pages', $items );
        return $this->_pages;
    }
    
    protected function showNav() {
        $pages = $this->getPages();
        
        ?>
        <div class="onp-help-nav">
            <ul>
            <?php foreach( $pages as $page ) { ?>
                <li><a href='<?php echo admin_url( 'admin.php' ) . '?page=how-to-use-clipboard-images&onp_sl_page=' . $page['name'] ?>'><?php echo $page['title'] ?></a></li>
            <?php } ?>
            </ul>
        </div>
        <?php
    }
    
    /**
     * Shows one of the help pages.
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function indexAction() {
        $currentPage = isset( $_GET['onp_sl_page'] ) ? $_GET['onp_sl_page'] : 'index';
        $pages = $this->getPages();
        
        $foundItem = false;
        foreach( $pages as $item ) {
            if ( $item['name'] == $currentPage ) {
                $foundItem = $item;
                break;
            }
        }
        
        ?>
        
        <div class="wrap factory-bootstrap-329 factory-fontawesome-320">
            <?php $this->showNav('getting-started') ?>
            <div class="onp-help-content">
                
            <?php
            if ( empty( $foundItem ) ) {
                $this->gettingStarted();
                return;
            }
            call_user_func( $foundItem['function'] );
            ?>
                
            </div>    
        </div> 
        <?php  
        return;
    }
    
    /**
     * Page 'Gettings Started'
     * 
     * @since 3.4.6
     */
    public function gettingStarted() {
        

        ?>

            <div class="onp-help-section">
                <h1><?php _e('Getting Started', 'sociallocker'); ?></h1>

                <p>
                    The Image Elevator plugin is ready to use out-of-the-box. 
                </p>    
                <p>
                    The plugin helps you to copy and paste images from different sources via clipboard. Copy wanted images to clipboard and paste them directly into your post editor.
                </p>

            </div>


            <div class="onp-help-section">
                <h2><?php _e('Visual and Text modes', 'imageelevetor'); ?></h2>
                <p>You can use Image Elevator to paste images in the Text Mode as well as the Visual Mode.</p>
                <p><strong>Pasting in the Visual Mode</strong></p>
                <p>Switch the Wordpress editor to the Visual Mode and press [ctrl] + [v] for Windows or [cmd] + [v] for Mac. You will see the graphic loader. 
                   After a while the image will appear in the editor.
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/visual-mode.gif' ?>' />
                </p>
                <p><strong>Pasting in the Text Mode</strong></p>
                <p>Switch the Wordpress editor to the Text Mode and press [ctrl] + [v] for Windows or [cmd] + [v] for Mac. You will see the text "[{ loading... }]. 
                   After a while the image code will appear in the editor.
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/text-mode.gif' ?>' />
                </p>
            </div>

            <div class="onp-help-section">
                <h2><?php _e('From where sources I can copy images to paste?', 'imageelevetor'); ?></h2>
                <p>You can paste images from any program which is able to save images into clipboard. In Firefox, you can also copy images from your desktop or the explorer. Check out the most popular sources below.</p>
                
                <p><strong>1. Graphical Editors</strong></p>
                <p>If you often process images with a graphical editor before publishing them on your blog, Image Elevator will save you a lot of time.</p>
                <p>Remember how much steps you need to do in order to add images to your posts after editing in a graphical editor: selecting a folder to save (1), typing a title (2), saving (3), opening the WP Media Library (4), selecting the folder where you have just saved image again (5), uploading (6).</p>
                <p>With Image Elevator, you only need to select a region (1), copy & paste (2).</p>
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/grapical-editors.png' ?>' />
                </p>
                <p class='onp-remark'>
                    <span class="onp-inner-wrap">
                        <strong>How</strong>: select a region of an image in your favorite graphic editor you would like to copy, press [ctrl] + [c] (Mac: [cmd] + [c]). Then put your mouse pointer in the Wordpress post editor in the place where you want to paste the selected region, press [ctrl] + [v] (Mac: [cmd] + [v]).</p>
                    </span>
                </p>
                
                <p><strong>2. Screenshots & Screen Clippings</strong></p>
                <p>With Image Elevator, you can make screenshots and paste them directly into the post editor.</p>
                <p>There are plenty of great programs which allows to make screenshots of the entire screen or a portion of the screen, and copy them in clipboard. For example: <A href="http://www.techsmith.com/download/jing/">Jing</a>, <a href="http://tinytake.com/">TyniTake<a/>, <A href="http://www.pixclip.net/download">pixclip</a>, <a href="http://evernote.com/skitch/">Skitch</a>, <a href="http://www.onenote.com/">OneNote</a>. Also by default operating systems provide users with some abilities to capture the screen.</p>
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/screen-capture.png' ?>' />
                </p>
                <p class='onp-remark'>
                    <span class="onp-inner-wrap">
                        <strong>How</strong>: if you work on Windows, press [print screen] to make a snaphot of your screen or [alt] + [print screen] for a snaphot of the active window.
                        If you work under Mac, press: [cmd] + [shift] + [3] to capture the entire screen, [cmd] + [shift] + [4] to capture a portion of the screen.
                    </span>
                </p>
                
                <p><strong>3. Internet & Websites</strong></p>
                <p>All the most popular web browsers allow to copy selected images from a webpage into clipboard. Then you can paste them into the Wordpress post editor.  Please keep in mind the images in internet may be subject to copyright.</p>
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/websites.png' ?>' />
                </p>
                <p class='onp-remark'>
                    <span class="onp-inner-wrap">
                        <strong>How</strong>: visit the website where from you want to copy an image, call the context manu (by the right button of your mouse) for the wanted image, select the item "Copy Image" and paste the image into the post editor.
                    </span>
                </p>

                <p><strong>4. Local Images stored on your PC (in Firefox only)</strong></p>
                <p>Firefox provides access to files, copied from the explorer, for web applications like Image Elevator. It's the quickest way to add images from your PC into your posts.</p>
                <p class='onp-img'>
                    <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/local-files.png' ?>' />
                </p>
                <p class='onp-remark'>
                    <span class="onp-inner-wrap">
                        <strong>How</strong>: call the context menu for the image you want to copy and select the item "Copy". Then insert the image in the post editor.
                    </span>
                </p>
                
                <p><strong>5. Other Programs Working With Clipboard</strong></p>
                <p>In fact any program which has abilities to work with images allows also to copy them into clipboard. And then you can paste images in your posts with Image Elevator.</p>
            
            </div>
        <?php
    }
    
    /**
     * Shows 'Have a plugin review?'
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function browsers() {
        ?>
        <div class="onp-help-section">
            <h1><?php _e('Browser Compatibility', 'sociallocker'); ?></h1>

            <p><?php _e('Image Elevator supports the most major browsers. Below are list of the supported browsers and features. If your browser is not in this list, it means that it was not tested or not supported.', 'imageelevator'); ?></p> 
            
            <table class="table table-bordered" id="browser-compatibility-table">
                <thead>
                    <th class="onp-imgevr-title"></th>
                    <th>Chrome</th>
                    <th>Firefox</th>
                    <th>IE11</th>         
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Visual Mode</td>
                    </tr>        
                    <tr>
                        <td class="onp-imgevr-title">Pasting regions from graphic editors</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-yes">yes</td>
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Pasting screenshots & screen clippings</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-yes">yes</td>
                    </tr>   
                    <tr>
                        <td class="onp-imgevr-title">Copying images from websites</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-yes">yes</td>
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Pasting local images storing on PC</td>
                        <td class="onp-imgevr-no">no</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-no">no</td>
                    </tr> 
                    <tr>
                        <td colspan="4">Text Mode</td>
                    </tr>    
                    <tr>
                        <td class="onp-imgevr-title">Pasting regions from graphic editors</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-no">no</td>
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Pasting screenshots and screen clippings</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-no">no</td>
                    </tr>   
                    <tr>
                        <td class="onp-imgevr-title">Copying images from websites</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-no">no</td>
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Pasting local images storing on PC</td>
                        <td class="onp-imgevr-no">no</td>
                        <td class="onp-imgevr-yes">yes</td> 
                        <td class="onp-imgevr-no">no</td>
                    </tr> 
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Shows 'Troubleshooting'
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function troubleshooting() {
        ?>
        <div class="onp-help-section">
            <h1><?php _e('Troubleshooting', 'sociallocker'); ?></h1>

            <p><?php _e('If you have any questions or faced with any troubles while using our plugin, please check our <a href="http://support.onepress-media.com/" target="_blank">knowledge base</a>. It is possible that instructions for resolving your issue have already been posted.', 'sociallocker'); ?></p>  
            <p>
                <?php _e('If the answer to your question isn’t listed, please submit a ticket <a href="http://support.onepress-media.com/create-ticket/" target="_blank">here</a>.<br />You can also email us directly <strong>support@byonepress.com</strong>', 'sociallocker'); ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Shows 'Get more features!'
     * 
     * @sinve 1.0.0
     * @return void
     * 
     */
    public function premium() {
        ?>
        <div class="onp-help-section">
            <h1><?php _e('Upgrade to Premium!', 'sociallocker'); ?></h1>

            <p>
                The plugin you're using is a free edition of the premium plugin <a href="<?php echo onp_licensing_325_get_purchase_url( $this->plugin ) ?>">Image Elevator</a> sold on CodeCanyon. 
            </p>
            <p class='onp-remark'>
                <span class="onp-inner-wrap">
                The premium version provides more features improving your productivity when you're working with images, allows you to rename images after pasting, resize and compress them on the fly.
                </span>
            </p>
        </div>

        <div class="onp-help-section">
            <h2><i class="fa fa-star-o"></i> Comparation of versions</h2>
            <table class="table table-bordered" id="onp-imgevr-version-comparation">
                <thead>
                    <tr>
                        <th></th>
                        <th>Free</th>
                        <th class="onp-imgevr-premium">Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="onp-imgevr-title">Pasting images from clipboard</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium">yes</td>   
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Adding pasted images to the Media Library</td>
                        <td class="onp-imgevr-yes">yes</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium">yes</td>   
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Renaming any images</td>
                        <td class="onp-imgevr-no">no</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium"><strong>yes</strong></td>   
                    </tr>
                    <tr>
                        <td class="onp-imgevr-title">Resizing images on the fly (<strong>new!</strong>)</td>
                        <td class="onp-imgevr-no">no</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium"><strong>yes</strong></td>   
                    </tr>              
                    <tr>
                        <td class="onp-imgevr-title">Compressing images on the fly</td>
                        <td class="onp-imgevr-no">no</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium"><strong>yes</strong></td>   
                    </tr> 
                    <tr>
                        <td class="onp-imgevr-title">Updates</td>
                        <td class="onp-imgevr-no">not guaranteed</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium">primary updates</td>   
                    </tr>         
                    <tr>
                        <td class="onp-imgevr-title">Support</td>
                        <td class="onp-imgevr-no">not guaranteed</td>
                        <td class="onp-imgevr-yes onp-imgevr-premium">guaranteed</td>   
                    </tr> 
                    <tr class="onp-imgevr-actions">
                        <td></td>
                        <td></td>
                        <td class="onp-imgevr-premium">
                            <a class="button button-primary" href="<?php echo onp_licensing_325_get_purchase_url( $this->plugin ) ?>">Upgrade for $13 only!</a>
                        </td>   
                    </tr>     
                </tbody>
            </table>
        </div>

        <div class="onp-help-section">
            <h2><i class="fa fa-star-o"></i> Rename images and improve SEO</h2>
            <p>By default Wordpress doesn't allow to change file names of the images in your posts. But the relevant filename is good for SEO as well as the relevant headline.</p>
            <p>The premium version of the Image Elevator allows you to rename images easily.</p>
            <p class='onp-img'>
                <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/renaming-feature.gif' ?>' />
            </p>
        </div>

        <div class="onp-help-section">
            <h2><i class="fa fa-star-o"></i> Resize images on the fly</h2>
            <p>Do you want all pasted images to match a format of your blog? Or want to quickly generate thumbnails for pasted images keeping the original images available at a click?</p>
            <p>Turn of the resizing feature in the premium version in order to resize all pasted images automatically and effortlessly.</p>
            <p class='onp-img'>
                <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/resizing.png' ?>' />
            </p>
        </div>

        <div class="onp-help-section">
            <h2><i class="fa fa-star-o"></i> Compress images on the fly</h2>
            <p>When you paste images, especially photos, they can be quite large and add extra loads on your website.</p>
            <p>Turn on the compression feature in the premium version, set the max allowed size for pasted images and this problem is over. Your images will be automatically converted to jpeg with specified the quality value.</p>
            <p class='onp-img'>
                <img src='<?php echo IMGEVR_PLUGIN_URL . '/assets/admin/img/how-to-use/compression.png' ?>' />
            </p>
        </div>

        <div class="onp-help-section">
            <h2><i class="fa fa-star-o"></i> Need other features?</h2>
            <p>Just let us know. Were closely working with our customers, getting suggestions by which we fill our ToDo list. <a href="http://support.onepress-media.com/create-ticket/">Click here</a> to tell us about your needs.</p>
        <?php
    }    
}

FactoryPages321::register($clipImages, 'OnpImgEvr_HowToUsePage');