<?php
/**
 * Plugin Name: Goodlayers Twitter Widget
 * Plugin URI: http://goodlayers.com/
 * Description: A widget that show feeds from twitter.
 * Version: 1.0
 * Author: Goodlayers
 * Author URI: http://www.goodlayers.com
 *
 */

add_action( 'widgets_init', 'twitter_widget' );
function twitter_widget() {
	register_widget( 'Twitter' );
}

class Twitter extends WP_Widget {

	// Initialize the widget
	function Twitter() {
		parent::WP_Widget('twitter-widget', __('Twitter (Goodlayers)','gdl_back_office'), 
			array('description' => __('A widget that show Twitter feeds.', 'gdl_back_office')));  
	}

	// Output of the widget
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );
		$twitter_username = $instance['twitter_username'];
		$show_num = $instance['show_num'];

		// Opening of widget
		echo $before_widget;
		
		// Open of title tag
		if ( $title ){ 
			echo $before_title . $title . $after_title; 
		}
			
		echo '<div class="twitter-whole">';
		echo '<ul id="twitter_update_list"><li>' . __('Twitter feed loading', 'gdl_front_end') . '</li></ul>';
		echo '</div>';
		
		?>
			<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
			<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/<?php echo $twitter_username;?>.json?callback=twitterCallback2&amp;count=<?php echo $show_num;?>"></script>
		<?php

		// Closing of widget
		echo $after_widget;
	}

	// Widget Form
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$twitter_username = esc_attr( $instance[ 'twitter_username' ] );
			$show_num = esc_attr( $instance[ 'show_num' ] );
		} else {
			$title = '';
			$twitter_username = '';
			$show_num = '5';
		}
		?>

		<!-- Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title :', 'gdl_back_office' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<!-- Twitter Username -->
		<p>
			<label for="<?php echo $this->get_field_id('twitter_username'); ?>"><?php _e( 'Twitter username :', 'gdl_back_office' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('twitter_username'); ?>" name="<?php echo $this->get_field_name('twitter_username'); ?>" type="text" value="<?php echo $twitter_username; ?>" />
		</p>		
		
		<!-- Show Num --> 
		<p>
			<label for="<?php echo $this->get_field_id( 'show_num' ); ?>"><?php _e('Show Count :', 'gdl_back_office'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'show_num' ); ?>" name="<?php echo $this->get_field_name( 'show_num' ); ?>" type="text" value="<?php echo $show_num; ?>" />
		</p>

	<?php
	}
	
	// Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username'] );
		$instance['show_num'] = strip_tags( $new_instance['show_num'] );

		return $instance;
	}	
}

?>