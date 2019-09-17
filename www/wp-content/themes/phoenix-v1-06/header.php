<!DOCTYPE html>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <title><?php bloginfo( 'name' ); ?><?php wp_title(); ?></title>

    <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css"/>

	<?php global $gdl_is_responsive ?>
	<?php if ( $gdl_is_responsive ) { ?>
        <meta name="viewport" content="width=device-width, user-scalable=no">
        <link rel="stylesheet" href="<?php echo GOODLAYERS_PATH; ?>/stylesheet/foundation-responsive.css">
	<?php } else { ?>
        <link rel="stylesheet" href="<?php echo GOODLAYERS_PATH; ?>/stylesheet/foundation.css">
	<?php } ?>

	<?php

	// start calling header script
	wp_head();

	// include favicon in the header
	if ( get_option( THEME_SHORT_NAME . '_enable_favicon', 'disable' ) === 'enable' ) {
		$gdl_favicon = get_option( THEME_SHORT_NAME . '_favicon_image' );
		if ( $gdl_favicon ) {
			$gdl_favicon = wp_get_attachment_image_src( $gdl_favicon, 'full' );
			echo '<link rel="shortcut icon" href="' . $gdl_favicon[0] . '" type="image/x-icon" />';
		}
	}

	// add facebook thumbnail to this page
	$thumbnail_id = get_post_thumbnail_id();
	if ( ! empty( $thumbnail_id ) ) {
		$thumbnail = wp_get_attachment_image_src( $thumbnail_id, '150x150' );
		echo '<link rel="image_src" href="' . $thumbnail[0] . '" />';
	}

	?>
</head>
<body <?php echo body_class(); ?>>

<?php
// print custom background
$background_style = get_option( THEME_SHORT_NAME . '_background_style', 'Pattern' );
if ( $background_style == 'Custom Image' ) {
	$background_id = get_option( THEME_SHORT_NAME . '_background_custom' );
	$alt_text      = get_post_meta( $background_id, '_wp_attachment_image_alt', true );

	if ( ! empty( $background_id ) ) {
		$background_image = wp_get_attachment_image_src( $background_id, 'full' );
		echo '<div class="gdl-custom-full-background">';
		echo '<img src="' . $background_image[0] . '" alt="' . $alt_text . '" />';
		echo '</div>';
	}
}
?>
<div class="body-wrapper">

	<?php // feedback button 
	$gdl_feedback_link = get_option( THEME_SHORT_NAME . '_feedback_button_link' );
	if ( $gdl_feedback_link ) {
		echo '<div class="feedback-wrapper">';
		echo '<a href="' . do_shortcode( __( $gdl_feedback_link, 'gdl_front_end' ) ) . '" target="_blank">';
		echo get_option( THEME_SHORT_NAME . '_feedback_button_text' );
		echo '</a>';
		echo '</div>';
	}
	?>
    <div class="header-outer-wrapper container wrapper">
        <div class="header-wrapper container">

            <!-- Get Logo -->
            <div class="logo-wrapper">
                <h1><a href="<?php bloginfo( 'url' ) ?>"><?php bloginfo( 'name' ) ?></a></h1>
                <h2><?php bloginfo( 'description' ) ?></h2>
            </div>
			<?php
			// Logo right text
			if ( get_option( THEME_SHORT_NAME . '_logo_position' ) != 'Center' ) {
				echo '<div class="logo-right-text">';
				echo do_shortcode( __( get_option( THEME_SHORT_NAME . '_logo_right_text' ), 'gdl_front_end' ) );
				echo '</div>';
			}
			?>

            <!-- Navigation -->
            <div class="clear"></div>
            <div class="gdl-navigation-wrapper">
				<?php
				// responsive menu
				if ( $gdl_is_responsive ) {
					dropdown_menu( array( 'dropdown_title'  => '-- Main Menu --',
					                      'indent_string'   => '- ',
					                      'indent_after'    => '',
					                      'container'       => 'div',
					                      'container_class' => 'responsive-menu-wrapper',
					                      'theme_location'  => 'main_menu'
					) );
				}

				// main menu
				echo '<div class="navigation-wrapper ">';
				echo '<div class="navigation-sliding-bar" id="navigation-sliding-bar"></div>';
				wp_nav_menu( array( 'container'       => 'div',
				                    'container_class' => 'menu-wrapper',
				                    'container_id'    => 'main-superfish-wrapper',
				                    'menu_class'      => 'sf-menu',
				                    'theme_location'  => 'main_menu'
				) );
				echo '</div>';
				?>
                <div class="clear"></div>
            </div>
        </div> <!-- header wrapper container -->

        <div class="navigation-bottom-bar container wrapper"></div>
    </div> <!-- header wrapper container wrapper -->
	