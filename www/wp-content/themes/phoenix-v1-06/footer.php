	<div class="footer-wrapper container wrapper">
	<div class="footer-top-bar"></div>
	
	<!-- Get Footer Widget -->
	<?php $gdl_show_footer = get_option(THEME_SHORT_NAME.'_show_footer','enable'); ?>
	<?php if( $gdl_show_footer == 'enable' ){ ?>
		<div class="footer-container container">
			<div class="footer-widget-wrapper">
				<div class="row">
					<?php
						$gdl_footer_class = array(
							'footer-style1'=>array('1'=>'three columns', '2'=>'three columns', '3'=>'three columns', '4'=>'three columns'),
							'footer-style2'=>array('1'=>'six columns', '2'=>'three columns', '3'=>'three columns', '4'=>''),
							'footer-style3'=>array('1'=>'three columns', '2'=>'three columns', '3'=>'six columns', '4'=>''),
							'footer-style4'=>array('1'=>'four columns', '2'=>'four columns', '3'=>'four columns', '4'=>''),
							'footer-style5'=>array('1'=>'eight columns', '2'=>'four columns', '3'=>'', '4'=>''),
							'footer-style6'=>array('1'=>'four columns', '2'=>'eight columns', '3'=>'', '4'=>''),
							);
						$gdl_footer_style = get_option(THEME_SHORT_NAME.'_footer_style', 'footer-style1');
					 
						for( $i=1 ; $i<=4; $i++ ){
							$footer_class = $gdl_footer_class[$gdl_footer_style][$i];
								if( !empty($footer_class) ){
								echo '<div class="' . $footer_class . ' gdl-footer-' . $i . ' mb0">';
								dynamic_sidebar('Footer ' . $i);
								echo '</div>';
							}
						}
					?>
					<div class="clear"></div>
				</div> <!-- close row -->
			</div>
		</div> 
	<?php } ?>

	<!-- Get Copyright Text -->
	<?php $gdl_show_copyright = get_option(THEME_SHORT_NAME.'_show_copyright','enable'); ?>
	<?php if( $gdl_show_copyright == 'enable' ){ ?>
		<div class="container copyright-container">
			<div class="copyright-wrapper">
				<div class="copyright-left">
					<!-- Get Social Icons -->
					<div id="gdl-social-icon" class="social-wrapper">
						<div class="social-icon-wrapper">
							<?php
								global $gdl_icon_type;
								$gdl_social_icon = array(
									'delicious'=> array('name'=>THEME_SHORT_NAME.'_delicious', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/delicious.png'),
									'deviantart'=> array('name'=>THEME_SHORT_NAME.'_deviantart', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/deviantart.png'),
									'digg'=> array('name'=>THEME_SHORT_NAME.'_digg', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/digg.png'),
									'facebook' => array('name'=>THEME_SHORT_NAME.'_facebook', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/facebook.png'),
									'flickr' => array('name'=>THEME_SHORT_NAME.'_flickr', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/flickr.png'),
									'lastfm'=> array('name'=>THEME_SHORT_NAME.'_lastfm', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/lastfm.png'),
									'linkedin' => array('name'=>THEME_SHORT_NAME.'_linkedin', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/linkedin.png'),
									'picasa'=> array('name'=>THEME_SHORT_NAME.'_picasa', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/picasa.png'),
									'rss'=> array('name'=>THEME_SHORT_NAME.'_rss', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/rss.png'),
									'stumble-upon'=> array('name'=>THEME_SHORT_NAME.'_stumble_upon', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/stumble-upon.png'),
									'tumblr'=> array('name'=>THEME_SHORT_NAME.'_tumblr', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/tumblr.png'),
									'twitter' => array('name'=>THEME_SHORT_NAME.'_twitter', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/twitter.png'),
									'vimeo' => array('name'=>THEME_SHORT_NAME.'_vimeo', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/vimeo.png'),
									'youtube' => array('name'=>THEME_SHORT_NAME.'_youtube', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/youtube.png'),
									'google_plus' => array('name'=>THEME_SHORT_NAME.'_google_plus', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/google-plus.png'),
									'email' => array('name'=>THEME_SHORT_NAME.'_email', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/email.png'),
									'pinterest' => array('name'=>THEME_SHORT_NAME.'_pinterest', 'url'=> GOODLAYERS_PATH.'/images/icon/social-icon/pinterest.png')
								);
								
								foreach( $gdl_social_icon as $social_name => $social_icon ){
									$social_link = get_option($social_icon['name']);
									
									if( !empty($social_link) ){
										echo '<div class="social-icon"><a target="_blank" href="' . $social_link . '">' ;
										echo '<img src="' . $social_icon['url'] . '" alt="' . $social_name . '"/>';
										echo '</a></div>';
									}
								}
							?>
						</div> <!-- social icon wrapper -->
					</div> <!-- social wrapper -->	
				</div> 
				<div class="copyright-right">
					<?php echo do_shortcode( __(get_option(THEME_SHORT_NAME.'_copyright_right_area'), 'gdl_front_end') ); ?>
				</div> 
				<div class="clear"></div>
			</div>
		</div>
	<?php } ?>
	
	</div><!-- footer wrapper -->
	</div><!-- content outer wrapper -->
</div> <!-- body wrapper -->
	
<?php wp_footer(); ?>

</body>
</html>