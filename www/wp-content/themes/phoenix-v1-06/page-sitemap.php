<?php 
/**
 * Template Name: Site Map
 */

get_header(); ?>
<div class="page-header-container container">
	<?php 
		// Check and get Sidebar Class
		$sidebar_array = gdl_get_sidebar_size( 'no-sidebar' );
		
		// print title
		print_page_header(get_the_title());		
	?>		
</div>
<div class="content-outer-wrapper container wrapper">
<div class="top-slider-bottom-bar container wrapper"></div>
<div class="content-wrapper container main">			
	<div class="page-wrapper sitemap-page <?php echo $sidebar_array['sidebar_class']; ?>">
		<?php
			
			echo '<div class="row">';
			echo '<div class="gdl-page-left mb0 ' . $sidebar_array['page_left_class'] . '">';
			
			echo '<div class="row">';
			echo '<div class="gdl-page-item mb20 ' . $sidebar_array['page_item_class'] . '">';
			?>
			
			<div class="row">
				<div class="four columns">
					<?php dynamic_sidebar( 'Site Map 1' ); ?>
				</div>
				<div class="four columns">
					<?php dynamic_sidebar( 'Site Map 2' ); ?>
				</div>
				<div class="four columns">
					<?php dynamic_sidebar( 'Site Map 3' ); ?>
				</div>
			</div>
			
			<?php 
			echo '<div class="clear"></div>';
			echo "</div>"; // end of gdl-page-item
			
			echo '<div class="clear"></div>';			
			echo "</div>"; // row
			echo "</div>"; // gdl-page-left

			echo '<div class="clear"></div>';
			echo "</div>"; // row
		?>
		<div class="clear"></div>
	</div> <!-- page wrapper -->
</div> <!-- container wrapper -->
<?php get_footer(); ?>