<?php get_header(); ?>
	<?php
		// Check and get Sidebar Class
		$sidebar = get_post_meta($post->ID,'post-option-sidebar-template',true);
		if( empty($sidebar) ){
			global $default_post_sidebar;
			$sidebar = $default_post_sidebar; 
		}		
		$sidebar_array = gdl_get_sidebar_size( $sidebar );

		// Translator words
		if( $gdl_admin_translator == 'enable' ){
			$translator_about_author = get_option(THEME_SHORT_NAME.'_translator_about_author', 'About the Author');
			$translator_social_share = get_option(THEME_SHORT_NAME.'_translator_social_shares', 'Social Share');
		}else{
			$translator_about_author = __('About the Author','gdl_front_end');
			$translator_social_share = __('Social Share','gdl_front_end');
		}
		
		// page header
		echo '<div class="page-header-container container">';
		$title = get_post_meta( $post->ID, "post-option-blog-header-title", true);
		if(empty( $title )){
			$title = get_option(THEME_SHORT_NAME . '_default_post_header','Blog Post');
		}
		$caption = get_post_meta($post->ID, 'post-option-caption', true);
		print_page_header($title, $caption);
		echo '</div>';		

	?>
<div class="content-outer-wrapper container wrapper">	
<div class="top-slider-bottom-bar container wrapper"></div>
<div class="content-wrapper container main">		
	<div class="page-wrapper single-blog <?php echo $sidebar_array['sidebar_class']; ?>">
		<?php
			global $left_sidebar, $right_sidebar, $default_post_left_sidebar, $default_post_right_sidebar;
			$left_sidebar = get_post_meta( $post->ID , "post-option-choose-left-sidebar", true);
			$right_sidebar = get_post_meta( $post->ID , "post-option-choose-right-sidebar", true);
			if( empty( $left_sidebar )){ $left_sidebar = $default_post_left_sidebar; } 
			if( empty( $right_sidebar )){ $right_sidebar = $default_post_right_sidebar; } 
			
			global $blog_single_size, $sidebar_type;
			$item_size = $blog_single_size[$sidebar_type];
			
			// starting the content
			echo '<div class="row">';
			echo '<div class="gdl-page-left ' . $sidebar_array['page_left_class'] . '">';
			
			echo '<div class="row">';
			echo '<div class="gdl-page-item  mb20 gdl-blog-full ' . $sidebar_array['page_item_class'] . '">';
			if ( have_posts() ){
				while (have_posts()){
					the_post();
					
					// blog date	
					echo '<div class="blog-date-wrapper">';
					echo '<div class="blog-month-value">' . strtoupper(get_the_time('M')) . '</div>';		
					echo '<div class="blog-date-value">' . get_the_time('d') . '</div>';
					echo '<div class="blog-year-value">' . get_the_time('Y') . '</div>';
					echo '</div>';					
					
					echo '<div class="blog-content-wrapper">';
					
					// blog thumbnail
					print_single_blog_thumbnail( get_the_ID(), $item_size );
					
					// blog title
					echo '<h1 class="blog-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h1>';
					
					// blog content
					echo '<div class="blog-content">';
					the_content();
					wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'gdl_front_end' ) . '</span>', 'after' => '</div>' ) );
					echo '<div class="clear"></div>';
					echo '</div>';
		
					// blog information
					echo '<div class="blog-info-wrapper">';					
					echo '<div class="blog-author">';
					echo '<span class="blog-info-head">' . __('BY : ','gdl_front_end') . '</span>';
					echo the_author_posts_link();
					echo '</div>';	
					
					echo '<div class="blog-comment">';
					echo '<span class="blog-info-head">' . __('COMMENT : ','gdl_front_end') . '</span>';
					comments_popup_link( __('0','gdl_front_end'),
						__('1','gdl_front_end'),
						__('%','gdl_front_end'), '',
						__('Off','gdl_front_end') );
					echo '</div>';		
					
					$tags_opening = '<div class="blog-tag">';
					$tags_opening = $tags_opening . '<span class="blog-info-head">' . __('TAG : ','gdl_front_end') . '</span>';
					$tags_ending = '</div>';
					the_tags( $tags_opening, ', ', $tags_ending );					
					echo '<div class="clear"></div>';
					echo '</div>'; // blog information

					// About Author
					if(get_post_meta($post->ID, 'post-option-author-info-enabled', true) != "No"){
						echo "<div class='about-author-wrapper'>";
						echo "<div class='about-author-avartar'>" . get_avatar( get_the_author_meta('ID'), 90 ) . "</div>";
						echo "<div class='about-author-info'>";
						echo "<h5 class='about-author-title'>" . $translator_about_author . "</h5>";
						echo get_the_author_meta('description');
						echo "</div>";
						echo "<div class='clear'></div>";
						echo "</div>";
					}
					
					// Include Social Shares
					if(get_post_meta($post->ID, 'post-option-social-enabled', true) != "No"){
						echo "<h3 class='social-share-title'>" . $translator_social_share . '</h3>';
						include_social_shares();
						echo "<div class='clear'></div>";
					}
				
					echo '<div class="comment-wrapper">';
					comments_template(); 
					echo '</div>';
					
					echo '</div>'; // blog content wrapper
				}
			}
			echo "</div>"; // end of gdl-page-item
			
			get_sidebar('left');	
			echo '<div class="clear"></div>';			
			echo "</div>"; // row
			echo "</div>"; // gdl-page-left

			get_sidebar('right');
			echo '<div class="clear"></div>';
			echo "</div>"; // row
		?>
		<div class="clear"></div>
	</div> <!-- page wrapper -->
</div> <!-- container wrapper -->

<?php get_footer(); ?>