<?php get_header(); ?>
<div class="page-header-wrapper container wrapper">
	<?php 
		// Check and get Sidebar Class
		$sidebar = get_post_meta($post->ID,'page-option-sidebar-template',true);
		$sidebar_array = gdl_get_sidebar_size( $sidebar );
		
		// print title
		$gdl_show_title = get_post_meta($post->ID, 'page-option-show-title', true);
		if( $gdl_show_title != 'No' ){
			$caption = get_post_meta($post->ID, 'page-option-caption', true);
			$header_bg = get_post_meta($post->ID, 'page-option-title-background', true);
			
			if( !empty($header_bg) ){
				$header_bg = wp_get_attachment_image_src( $header_bg , 'full' );
				echo '<div class="page-header-container container" style="background-image: url(\'' . $header_bg[0] . '\')" >';
			}else{
				echo '<div class="page-header-container container">';
			}
			echo '</div>';
		}
	?>		
</div>
<div class="content-outer-wrapper container wrapper">
<div class="content-wrapper container main">
	<div class="page-wrapper single-page <?php echo $sidebar_array['sidebar_class']; ?>">
		<?php
			$left_sidebar = get_post_meta( $post->ID , 'page-option-choose-left-sidebar', true);
			$right_sidebar = get_post_meta( $post->ID , 'page-option-choose-right-sidebar', true);
			
			echo '<div class="row">';
			echo '<div class="gdl-page-left mb0 ' . $sidebar_array['page_left_class'] . '">';
			
			echo '<div class="row">';
			echo '<div class="gdl-page-item mb20 ' . $sidebar_array['page_item_class'] . '">';

			// page content
			global $gdl_item_row_size;
			while (have_posts()){ 
				the_post(); 

				echo "<h1 class='post_title'>".$post->post_title . '</h1>';
				// print content
				$gdl_show_content = get_post_meta($post->ID, 'page-option-show-content', true);
				if( $gdl_show_content !== 'No' ){
					$content = get_the_content();
					$content = apply_filters('the_content', $content);
					if(empty($content)){
						$gdl_item_row_size = print_item_size( '1/1', $gdl_item_row_size ,'mb0');
					}else{
						$gdl_item_row_size = print_item_size( '1/1', $gdl_item_row_size);
					}				
					
					echo '<div class="gdl-page-content">';
					echo $content;
					wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'gdl_front_end' ) . '</span>', 'after' => '</div>' ) );
					echo '</div>';
				}
			}
			
			// Page Item Part
			if(!empty($gdl_page_xml) && !post_password_required() ){
				$page_xml_val = new DOMDocument();
				$page_xml_val->loadXML($gdl_page_xml);
				foreach( $page_xml_val->documentElement->childNodes as $item_xml){
					if( $item_xml->nodeName == 'Title' || 
						$item_xml->nodeName == 'Portfolio'|| $item_xml->nodeName == 'Blog'  ){
						$additional_class = 'mb0';
					}else if( $item_xml->nodeName == 'Column-Service' ){
						$additional_class = 'column-service-row';
					}else{
						$additional_class = '';
					}
					$gdl_item_row_size = print_item_size(find_xml_value($item_xml, 'size'), $gdl_item_row_size, $additional_class);
					switch($item_xml->nodeName){
						case 'Accordion' : print_accordion_item($item_xml); break;
						case 'Blog' : print_blog_item($item_xml); break;
						case 'Blog-Port-Description' : print_blog_port_description($item_xml); break;
						case 'Contact-Form' : print_contact_form($item_xml); break;
						case 'Column': print_column_item($item_xml); break;
						case 'Column-Service' : print_column_service($item_xml); break;
						case 'Content' : print_content_item($item_xml); break;
						case 'Divider' : print_divider($item_xml); break;
						case 'Gallery' : print_gallery_item($item_xml); break;								
						case 'Message-Box' : print_message_box($item_xml); break;
						case 'Page': print_page_item($item_xml); break;
						case 'Personnal': print_personnal_item($item_xml); break;							
						case 'Portfolio' : print_portfolio($item_xml); break;
						case 'Post-Slider' : print_post_slider_item($item_xml); break;
						case 'Price-Item': print_price_item($item_xml); break;						
						case 'Slider' : print_slider_item($item_xml); break;
						case 'Stunning-Text' : print_stunning_text($item_xml); break;
						case 'Tab' : print_tab_item($item_xml); break;
						case 'Testimonial' : print_testimonial($item_xml); break;
						case 'Toggle-Box' : print_toggle_box_item($item_xml); break;
						default: break;
					}
					echo '</div>'; // close column from print_item_size()
				}
			}
			echo '<div class="clear"></div>';
			echo '</div>'; // close row from print_item_size()
			echo '</div>'; // end of gdl-page-item
			
			get_sidebar('left');	
			echo '<div class="clear"></div>';			
			echo '</div>'; // row
			echo '</div>'; // gdl-page-left

			get_sidebar('right');
			echo '<div class="clear"></div>';
			echo '</div>'; // row
			echo '</div>'; // row
		?>
		<div class="clear"></div>
	</div> <!-- page wrapper -->
</div> <!-- content wrapper -->
<?php get_footer(); ?>