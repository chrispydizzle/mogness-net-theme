<?php
	/*	
	*	Goodlayers Custom Style File
	*	---------------------------------------------------------------------
	*	This file fetch all style options in admin panel to generate the 
	*	style-custom.css file
	*	---------------------------------------------------------------------
	*/
	
	// This function is called when user save the option ( admin panel )
	function gdl_generate_style_custom( $data = '' ){
		global $gdl_fh, $gdl_custom_stylesheet_name;
		
		$return_data = array('success'=>'-1', 'alert'=>'Cannot write ' . $gdl_custom_stylesheet_name . ' file, you may try setting the style-custom.css file permission to 755 or 777 to solved this. ( If file does not exists, you have to create it yourself )');
		
		// initial the value of the style
		$file_path = SERVER_PATH . "/" . $gdl_custom_stylesheet_name;
		$gdl_fh = @fopen($file_path, 'w');
		
		if( !$gdl_fh ){ die( json_encode($return_data) ); }
		
		gdl_get_style_custom_content();
		
		// close data
		fclose($gdl_fh);	
	}
	
	// This function write the files to the admin panel
	function gdl_write_data( $string ){
		global $gdl_fh;
		fwrite( $gdl_fh, $string . "\r\n" );
	}
	
	// help print the css easier
	function gdl_print_style( $selector, $content ){
		if( empty($content) ) return;
		gdl_write_data( $selector . '{ ' . $content  . '} ');
	}
	
	// help to print the attribute easier
	function gdl_style_att( $attribute, $value, $important = false ){
		if( $value == 'transparent' ) return '';
		if( $important ) return $attribute . ': ' . $value . ' !important; ';
		return $attribute . ': ' . $value . '; ';
	}
	
	function gdl_get_style_custom_content(){
		global $goodlayers_element, $goodlayers_menu;	
		
		$color_menus = $goodlayers_menu[ __('Elements Color', 'gdl_back_office') ];
		foreach( $color_menus as $color_menu_slug ){
			foreach( $goodlayers_element[$color_menu_slug] as $element ){
				$temp_att = '';
				if( !empty( $element['attr'] ) && !empty( $element['selector'] ) ){
					foreach( $element['attr'] as $attr ){
						$temp_att = $temp_att . gdl_style_att( $attr, get_option($element['name'], $element['default']) );
					}	
					gdl_print_style( $element['selector'], $temp_att );
				}
			}
		}
				
		// Background Style
		$background_style = get_option(THEME_SHORT_NAME.'_background_style', 'Pattern');
		if($background_style == 'Pattern'){
			$background_pattern = get_option(THEME_SHORT_NAME.'_background_pattern', '1');
			
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/pattern/pattern-' . $background_pattern . '.png)' );
			gdl_print_style( 'html', $temp_att );
		}
		
		// Logo Margin
		$temp_att = gdl_style_att( 'padding-top', get_option(THEME_SHORT_NAME . "_logo_top_margin", '48') . 'px' );
		$temp_att = $temp_att . gdl_style_att( 'padding-bottom', get_option(THEME_SHORT_NAME . "_logo_bottom_margin", '38') . 'px' );
		gdl_print_style( '.logo-wrapper', $temp_att );
		
		$temp_att = gdl_style_att( 'padding-top', get_option(THEME_SHORT_NAME . "_logo_right_top_margin", '42') . 'px' );
		gdl_print_style( '.logo-right-text', $temp_att );
		
		// Header Font
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_navigation_size", '12') . 'px' );
		gdl_print_style( 'div.navigation-wrapper', $temp_att );			
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_header_title_size", '25') . 'px' );
		gdl_print_style( 'h1.gdl-header-title', $temp_att );			
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_content_size", '13') . 'px' );
		gdl_print_style( 'body, ul.gdl-toggle-box li .toggle-box-title, ul.gdl-accordion li .accordion-title', $temp_att );			
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_widget_title_size", '13') . 'px' );
		gdl_print_style( 'h3.custom-sidebar-title', $temp_att );			
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h1_size", '30') . 'px' );		
		gdl_print_style( 'h1', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h2_size", '25') . 'px' );
		gdl_print_style( 'h2', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h3_size", '20') . 'px' );
		gdl_print_style( 'h3', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h4_size", '18') . 'px' );
		gdl_print_style( 'h4', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h5_size", '16') . 'px' );
		gdl_print_style( 'h5', $temp_att );	
		$temp_att = gdl_style_att( 'font-size', get_option(THEME_SHORT_NAME . "_h6_size", '15') . 'px' );
		gdl_print_style( 'h6', $temp_att );		
		 
		// Font Family
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_content_font"), 2) );
		gdl_print_style( 'body', $temp_att );	
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_header_font"), 2) );
		gdl_print_style( 'h1, h2, h3, h4, h5, h6', $temp_att );			
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_slider_title_font"), 2) );
		gdl_print_style( '.gdl-slider-title', $temp_att );				
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_stunning_text_font"), 2) );
		gdl_print_style( 'h1.stunning-text-title', $temp_att );				
		$temp_att = gdl_style_att( 'font-family', substr(get_option(THEME_SHORT_NAME . "_navigation_font"), 2) );
		gdl_print_style( 'div.navigation-wrapper', $temp_att );	
		
		// Icon Type
		$gdl_icon_type = get_option(THEME_SHORT_NAME.'_icon_type','dark');

			// Accordion - Toggle box (unchecked)
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/accordion-title-active.png)' );
			gdl_print_style( 'ul.gdl-accordion li.active .accordion-title, ul.gdl-toggle-box li.active .toggle-box-title', $temp_att );			
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/accordion-title.png)' );
			gdl_print_style( 'ul.gdl-accordion li .accordion-title, ul.gdl-toggle-box li .toggle-box-title', $temp_att );	
			
			// Testimonial
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/testimonial-quote.png)' );
			gdl_print_style( 'div.gdl-carousel-testimonial .testimonial-content', $temp_att );	
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/testimonial-left.png)' );
			gdl_print_style( 'div.testimonial-prev', $temp_att );	
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/testimonial-right.png)' );
			gdl_print_style( 'div.testimonial-next', $temp_att );	
			
			// Portfolio
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/port-nav.png) no-repeat' );
			gdl_print_style( ".port-nav a", $temp_att );	
			
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/port-nav.png) no-repeat' );
			gdl_print_style( ".port-nav-wrapper .port-nav", $temp_att );			
			
			// Personnal Widget
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-left.png)' );
			gdl_print_style( 'div.personnal-widget-prev', $temp_att );	
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/personnal-widget-right.png)' );
			gdl_print_style( 'div.personnal-widget-next', $temp_att );				
			
			// Search Button (uncheck)
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/search-button.png) no-repeat center' );
			gdl_print_style( "div.gdl-search-button, div.custom-sidebar #searchsubmit", $temp_att );					
			
			// Sidebar bullet
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_icon_type . '/li-arrow.png) no-repeat 0px 14px' );
			gdl_print_style( "div.custom-sidebar ul li", $temp_att );
		
		// Footer Icon Type
		$gdl_footer_icon_type = get_option(THEME_SHORT_NAME.'_footer_icon_type','light');
			
			// Footer Bullet
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/li-arrow.png) no-repeat 0px 14px' );
			gdl_print_style( "div.footer-wrapper div.custom-sidebar ul li", $temp_att );
			
			// Search Icon
			$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/search-button.png) no-repeat center' );
			gdl_print_style( "div.footer-wrapper div.custom-sidebar #searchsubmit", $temp_att );	
			
			// Personnal Widget
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/personnal-widget-left.png)' );
			gdl_print_style( 'div.footer-wrapper div.personnal-widget-prev', $temp_att );	
			$temp_att = gdl_style_att( 'background-image', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_footer_icon_type . '/personnal-widget-right.png)' );
			gdl_print_style( 'div.footer-wrapper div.personnal-widget-next', $temp_att );				

		// Carousel Icon Type
		$gdl_carousel_icon_type = get_option(THEME_SHORT_NAME.'_carousel_icon_type','light');	
		
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_carousel_icon_type . '/carousel-nav-left.png) no-repeat' );
		gdl_print_style( ".flex-carousel .flex-direction-nav li a.flex-prev", $temp_att );
		$temp_att = gdl_style_att( 'background', 'url(' . GOODLAYERS_PATH . '/images/icon/' . $gdl_carousel_icon_type . '/carousel-nav-right.png) no-repeat' );
		gdl_print_style( ".flex-carousel .flex-direction-nav li a.flex-next", $temp_att );		

		/*--- Custom value that can't resides in goodlayers option array ---*/
		
		// Contact Form 
		$temp_val_frame = get_option(THEME_SHORT_NAME.'_contact_form_frame_color', '#f0f0f0');
		$temp_val_shadow = get_option(THEME_SHORT_NAME.'_contact_form_inner_shadow', '#ececec');
		$temp_sel = 'div.contact-form-wrapper input[type="text"], div.contact-form-wrapper input[type="password"], div.contact-form-wrapper textarea, ';
		$temp_sel = $temp_sel . 'div.sidebar-wrapper #search-text input[type="text"], ';
		$temp_sel = $temp_sel . 'div.sidebar-wrapper .contact-widget input, div.custom-sidebar .contact-widget textarea, ';
		$temp_sel = $temp_sel . 'div.comment-wrapper input[type="text"], div.comment-wrapper input[type="password"], div.comment-wrapper textarea';
		//$temp_sel = $temp_sel . 'span.wpcf7-form-control-wrap input[type="text"], span.wpcf7-form-control-wrap input[type="password"], ';
		//$temp_sel = $temp_sel . 'span.wpcf7-form-control-wrap textarea';
		$temp_att = gdl_style_att( 'color', get_option(THEME_SHORT_NAME.'_contact_form_text_color', '#888888') );
		$temp_att = $temp_att . gdl_style_att( 'background-color', get_option(THEME_SHORT_NAME.'_contact_form_background_color', '#fff') );
		$temp_att = $temp_att . gdl_style_att( 'border-color', get_option(THEME_SHORT_NAME.'_contact_form_border_color', '#d9d9d9') );
		$temp_att = $temp_att . gdl_style_att( '-webkit-box-shadow', $temp_val_shadow . ' 0px 1px 4px inset, ' . $temp_val_frame . ' -5px -5px 0px 0px, ' . $temp_val_frame . ' 5px 5px 0px 0px, ' . $temp_val_frame . ' 5px 0px 0px 0px, ' . $temp_val_frame . ' 0px 5px 0px 0px, ' . $temp_val_frame . ' 5px -5px 0px 0px, ' . $temp_val_frame . ' -5px 5px 0px 0px ' );
		$temp_att = $temp_att . gdl_style_att( 'box-shadow', $temp_val_shadow . ' 0px 1px 4px inset, ' . $temp_val_frame . ' -5px -5px 0px 0px, ' . $temp_val_frame . ' 5px 5px 0px 0px, ' . $temp_val_frame . ' 5px 0px 0px 0px, ' . $temp_val_frame . ' 0px 5px 0px 0px, ' . $temp_val_frame . ' 5px -5px 0px 0px, ' . $temp_val_frame . ' -5px 5px 0px 0px ' );
		gdl_print_style( $temp_sel, $temp_att );

		$temp_val = get_option(THEME_SHORT_NAME.'_content_shadow_opacity', '0.4');
		gdl_write_data('div.header-outer-wrapper{ box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');
		gdl_write_data('div.header-outer-wrapper{ -moz-box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');
		gdl_write_data('div.header-outer-wrapper{ -webkit-box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');

		gdl_write_data('div.content-outer-wrapper{ box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');
		gdl_write_data('div.content-outer-wrapper{ -moz-box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');
		gdl_write_data('div.content-outer-wrapper{ -webkit-box-shadow: 0px 0px 6px rgba(0,0,0,' . $temp_val . '); }');
		
		// Additional Style From The admin panel > general > page style
		gdl_write_data(get_option(THEME_SHORT_NAME.'_additional_style', ''));

		// Show/Hide  Post/Portfolio Information
		$temp_val = get_option(THEME_SHORT_NAME.'_show_post_tag','Yes');
		if( $temp_val == 'No' ){ gdl_write_data( 'div.blog-tag{ display: none; }' ); }
		$temp_val = get_option(THEME_SHORT_NAME.'_show_post_comment_info','Yes');
		if( $temp_val == 'No' ){ gdl_write_data( 'div.blog-comment{ display: none; }' ); }
		$temp_val = get_option(THEME_SHORT_NAME.'_show_post_author_info','Yes');
		if( $temp_val == 'No' ){ gdl_write_data( 'div.blog-author{ display: none; }' ); }		
		
		// Logo Position
		$temp_val = get_option(THEME_SHORT_NAME.'_logo_position', 'Center');
		if( $temp_val == 'Left' ){
			gdl_write_data('div.logo-wrapper { float: left; }');
			gdl_write_data('div.logo-right-text { float: right; text-align: right; }');
		}else if( $temp_val == 'Right' ){
			gdl_write_data('div.logo-wrapper { float: right; }');
			gdl_write_data('div.logo-right-text { float: left; }');
		}
		
		// Default header title background
		$temp_val = get_option(THEME_SHORT_NAME.'_page_default_header_title', '');
		if( !empty($temp_val) ){
			$temp_val = wp_get_attachment_image_src( $temp_val , 'full' );
			gdl_write_data('body.page .page-header-container{ background-image: url("' . $temp_val[0] . '"); }');
		}
		
		$temp_val = get_option(THEME_SHORT_NAME.'_post_default_header_title', '');
		if( !empty($temp_val) ){
			$temp_val = wp_get_attachment_image_src( $temp_val , 'full' );
			gdl_write_data('body.single .page-header-container{ background-image: url("' . $temp_val[0] . '"); }');
		}
		
		$temp_val = get_option(THEME_SHORT_NAME.'_search_default_header_title', '');
		if( !empty($temp_val) ){
			$temp_val = wp_get_attachment_image_src( $temp_val , 'full' );
			gdl_write_data('body.search .page-header-container{ background-image: url("' . $temp_val[0] . '"); }');
		}
		
		$temp_val = get_option(THEME_SHORT_NAME.'_404_default_header_title', '');
		if( !empty($temp_val) ){
			$temp_val = wp_get_attachment_image_src( $temp_val , 'full' );
			gdl_write_data('body.error404 .page-header-container{ background-image: url("' . $temp_val[0] . '"); }');
		}

		
	}
	
// -------------------------------------------------------------------------- //
// -------------------------------------------------------------------------- //
	
// function that print additional style for lt IE9
add_action('wp_head', 'gdl_ltie9_style');
function gdl_ltie9_style(){ 

// dropcap support	
?>	
<!--[if lt IE 9]>
<style type="text/css">
	div.shortcode-dropcap.circle{
		z-index: 1000;
		position: relative;
		behavior: url(<?php echo GOODLAYERS_PATH; ?>/stylesheet/ie-fix/PIE.php);
	}
	div.search-wrapper .search-text{ width: 185px; }
	div.feedback-wrapper a{ left: 0px; }
	div.top-navigation-left{ width: 50%; text-align: left; }
	span.hover-link, span.hover-video, span.hover-zoom{ display: none !important; }
	
	.portfolio-media-wrapper:hover span{ display: block !important; }
	.blog-media-wrapper:hover span{ display: block !important; }
</style>
<![endif]-->
<?php 
}
?>
