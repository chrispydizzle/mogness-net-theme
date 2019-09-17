<?php get_header(); ?>
<div class="page-header-container container">
	<?php
		// Check and get Sidebar Class
		$sidebar = get_option(THEME_SHORT_NAME.'_search_archive_sidebar','no-sidebar');
		$sidebar_array = gdl_get_sidebar_size( $sidebar );

		// print title
		if( is_category() || is_tax('portfolio-category') ){
			$title = __('Category','gdl_front_end');
		}else if( is_tag() || is_tax('portfolio-tag') ){
			$title = __('Tag','gdl_front_end');
		}else if( is_day() ){
			$title = __('Day','gdl_front_end');
		}else if( is_month() ){
			$title = __('Month','gdl_front_end');
		}else if( is_year() ){
			$title = __('Year','gdl_front_end');
		}		
		
		if(is_category() || is_tag() || is_tax('portfolio-category') || is_tax('portfolio-tag') ){
			$caption = single_cat_title('', false);
		}else if( is_day() ){
			$caption = get_the_date('F j, Y');
		}else if( is_month() ){
			$caption = get_the_date('F Y');
		}else if( is_year() ){
			$caption = get_the_date('Y');
		}	
		print_page_header($title, '// ' . $caption);
		
	
	?>
</div>
<div class="content-outer-wrapper container wrapper">
<div class="top-slider-bottom-bar container wrapper"></div>
<div class="content-wrapper container main">	
	<div class="page-wrapper archive-page <?php echo $sidebar_array['sidebar_class']; ?>">
		<?php
			$left_sidebar = get_option(THEME_SHORT_NAME.'_search_archive_left_sidebar');
			$right_sidebar = get_option(THEME_SHORT_NAME.'_search_archive_right_sidebar');					
				
			echo '<div class="row">';
			echo '<div class="gdl-page-left mb0 ' . $sidebar_array['page_left_class'] . '">';
			
			echo '<div class="row">';
			echo '<div class="gdl-page-item mb20 ' . $sidebar_array['page_item_class'] . '">';
			
			if( !is_tax('portfolio-category') && !is_tax('portfolio-tag') ){
			
				// blog archive
				$item_type = '1/1 Full Thumbnail';
				$num_excerpt = get_option(THEME_SHORT_NAME.'_search_archive_num_excerpt', 285);
				$full_content = get_option(THEME_SHORT_NAME.'_search_archive_full_blog_content', 'No');

				global $blog_div_size_num_class;
				$item_class = $blog_div_size_num_class[$item_type]['class'];
				$item_size = $blog_div_size_num_class[$item_type][$sidebar_type];		

					
				echo '<div id="blog-item-holder" class="blog-item-holder">';
				print_blog_full($item_class, $item_size, $num_excerpt, $full_content);
				echo '</div>'; // blog-item-holder
			}else{
				
				// portfolio archive
				$port_size = get_option(THEME_SHORT_NAME.'_portfolio_archive_size' ,'1/4');
				$show_title = get_option(THEME_SHORT_NAME.'_portfolio_archive_show_title' ,'Yes');
				$show_tag = get_option(THEME_SHORT_NAME.'_portfolio_archive_show_tags' ,'Yes');
				print_normal_portfolio($port_size, $show_title, $show_tag);
			}

			echo '<div class="clear"></div>';
			pagination();
			
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
<?php get_footer(); ?>
