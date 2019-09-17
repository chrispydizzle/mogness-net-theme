<?php get_header(); 
	$current_page_style = get_option(THEME_SHORT_NAME.'_use_portfolio_as', 'portfolio style');
	if( $current_page_style == 'portfolio style' ){

		// Check and get Sidebar Class
		$sidebar = get_post_meta($post->ID,'post-option-sidebar-template',true);
		$sidebar_array = gdl_get_sidebar_size( $sidebar );

		// Translator words
		global $gdl_admin_translator;	
		if( $gdl_admin_translator == 'enable' ){
			$translator_tag = get_option(THEME_SHORT_NAME.'_translator_tags', 'Tags: ');
			$translator_client = get_option(THEME_SHORT_NAME.'_translator_client', 'Client: ');
			$translator_skill = get_option(THEME_SHORT_NAME.'_translator_skill', 'Skill: ');
			$translator_visit_website = get_option(THEME_SHORT_NAME.'_translator_visit_website', 'Visit Website: ');
			$translator_related_portfolio = get_option(THEME_SHORT_NAME.'_translator_related_portfolio', 'Related Portfolio');
			
			$translator_about_author = get_option(THEME_SHORT_NAME.'_translator_about_author', 'About the Author');
			$translator_social_share = get_option(THEME_SHORT_NAME.'_translator_social_shares', 'Social Share');
		}else{
			$translator_tag = __('Tags: ','gdl_front_end');
			$translator_client =  __('Client: ','gdl_front_end');
			$translator_skill =  __('Skill: ','gdl_front_end');
			$translator_visit_website = __('Visit Website: ','gdl_front_end');	
			$translator_related_portfolio = get_option(THEME_SHORT_NAME.'_translator_related_portfolio', 'Related Portfolio');	
			
			$translator_about_author = __('About the Author','gdl_front_end');
			$translator_social_share = __('Social Share','gdl_front_end');
		}		
		
		// related portfolio check
		$related_portfolio = get_option( THEME_SHORT_NAME.'_gdl_related_portfolio' ,'Yes');
		$related_portfolio_class = ($related_portfolio == 'Yes')? 'mb0' : '';
		
		// page header
		echo '<div class="page-header-container container">';

		$caption = get_post_meta($post->ID, 'post-option-caption', true);
		print_page_header(get_the_title(), $caption);
		
		echo '</div>';
	?>
<div class="content-outer-wrapper container wrapper">
<div class="top-slider-bottom-bar container wrapper"></div>
<div class="content-wrapper container main">	
	<div class="page-wrapper single-portfolio <?php echo $sidebar_array['sidebar_class']; ?> ">
		<?php
			$left_sidebar = get_post_meta( $post->ID , "post-option-choose-left-sidebar", true);
			$right_sidebar = get_post_meta( $post->ID , "post-option-choose-right-sidebar", true);		

			global $port_single_size, $sidebar_type;
			$item_size = $port_single_size[$sidebar_type];
			
			// starting the content
			echo '<div class="row">';
			echo '<div class="gdl-page-left  mb0 ' . $sidebar_array['page_left_class'] . '">';
			
			echo '<div class="row">';
			echo '<div class="gdl-page-item  mb20 gdl-blog-full ' . $sidebar_array['page_item_class'] . '">';
			echo '<div class="gdl-single-portfolio ' . $related_portfolio_class . '">';
			if (have_posts()){
				while (have_posts()){the_post();
				
					print_single_port_thumbnail( get_the_ID(), $item_size );
					
					echo '<div class="port-content-wrapper">';
					echo '<div class="port-info-wrapper">';
					
					// navigation
					echo '<div class="port-nav">';
					next_post_link('<div class="port-prev-nav">%link</div>', '');
					previous_post_link('<div class="port-next-nav">%link</div>', '');
					echo '<div class="clear"></div>';
					echo '</div>'; 
					
					// client
					$client_name = get_post_meta($post->ID, 'post-option-clients-name', true);
					if(!empty($client_name)){
						echo '<div class="port-client port-info"><span class="head">' . $translator_client . '</span>';
						echo $client_name;
						echo '</div>';
					}	

					// skill
					$skill_val = get_post_meta($post->ID, 'post-option-skill-value', true);
					if(!empty($skill_val)){
						echo '<div class="port-skill port-info"><span class="head">' . $translator_skill . '</span>';
						echo $skill_val;
						echo '</div>';
					}						
					
					// tags
					$portfolio_tag = get_the_term_list( $post->ID, 'portfolio-tag', '', ', ' , '' );
					if(!empty($portfolio_tag)){
					echo '<div class="port-tags port-info"><span class="head">' . $translator_tag . '</span>';
					echo $portfolio_tag;
					echo '</div>';
					}
					
					// visit website
					$website_link = get_post_meta( $post->ID, 'post-option-website-url', true); 
					if(!empty($website_link)){
						echo '<div class="port-visit-website port-info"><span class="head">' . $translator_visit_website . '</span>';
						echo '<a href="' . $website_link . '" target="_blank" >' . $website_link . '</a>';
						echo '</div>';
					}						
					
					echo '</div>'; // port info
					
					echo '<div class="port-content">';
					the_content();
					wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'gdl_front_end' ) . '</span>', 'after' => '</div>' ) );
					echo '</div>';
					echo '</div>'; // port content wrapper
					echo '<div class="clear"></div>';
				}
			}
			
			// print related portfolio
			if( $related_portfolio == 'Yes' ){
				echo '<div class="gdl-related-portfolio">';
				$port_size = get_option( THEME_SHORT_NAME.'_gdl_related_portfolio_size' , '1/4');
				$num_fetch = get_option( THEME_SHORT_NAME.'_gdl_related_portfolio_num_fetch' , 4);
				$show_title = (get_option( THEME_SHORT_NAME.'_gdl_related_portfolio_title' , 'Yes') == 'Yes') ? true: false;
				$show_tag = (get_option( THEME_SHORT_NAME.'_gdl_related_portfolio_tag' , 'Yes') == 'Yes') ? true: false;
				
				print_item_header( $translator_related_portfolio );
				
				$portfolio_tags = get_the_terms(get_the_ID(), 'portfolio-tag');
				$portfolio_terms = array();
				if( !empty($portfolio_tags) ){
					foreach( $portfolio_tags as $portfolio_tag ){
						$portfolio_terms[] = $portfolio_tag->slug;
					}
					
					$tax_query = array( array('taxonomy'=>'portfolio-tag', 'field'=>'slug', 'terms'=>$portfolio_terms) );
					query_posts(array('post_type'=>'portfolio', 'tax_query'=>$tax_query, 'posts_per_page'=>$num_fetch));
					
					print_normal_portfolio($port_size, $show_title, $show_tag);
				}
				echo '<div class="clear"></div>';
				echo '</div>'; // gdl-related-portfolio
			}
			echo '</div>'; // gdl-single-portfolio
			
			wp_reset_query();
			
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
</div> <!-- content wrapper -->
	<?php 
	
	}else{
		// blog style

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
					
					$portfolio_tag = get_the_term_list( $post->ID, 'portfolio-tag', '', ', ' , '' );
					if(!empty($portfolio_tag)){
						echo '<div class="blog-tag"><span class="blog-info-head">' . __('TAG : ','gdl_front_end') . '</span>';
						echo $portfolio_tag;
						echo '</div>';
					}			
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
	<?php
	}
	
get_footer(); 
?>