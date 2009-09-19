<?php get_header(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<div id="post_<?php the_id(); ?>" class="post">
			<div class="container">

				<div id="showroom_203124" class="showroom">
  					<div class="full_image_wrapper"><?php display_photo(); ?></div>
  					<div style="display: none;" class="rtw_button" id="look_label_203124">
						<div class="rtw_button_inner">
    						<div class="content">achetez cette sélection 

							</div>
    						<div class="end"/>
						</div>
  					</div>
				</div><!-- div.showroom -->
				
			</div> <!-- div.container -->
			<div class="shadow-bottom"><div class="start"/></div>
			<div class="shadow-right"/>

			<div class="clear"/>

		</div>

	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>

<?php get_footer(); ?>


<!-- <div id="rtw_" class="rtw">
	<div class="container">

		<div id="showroom_203124" class="showroom">
			<div class="full_image_wrapper"><img style="" 	src="http://images.gucci.com/images/categories/fw09rtw/full/fw09_wrtw_17m_001_full.jpg" class="full" id="showroom_image_203124"/></div>
			<div style="display: none;" class="rtw_button" id="look_label_203124">
				<div class="rtw_button_inner">
					<div class="content">achetez cette sélection 

					</div>
					<div class="end"/>
				</div>
			</div>
		</div><
		
	</div>
	<div class="shadow-bottom"><div class="start"/></div>
	<div class="shadow-right"/>

	<div class="clear"/>

</div> -->