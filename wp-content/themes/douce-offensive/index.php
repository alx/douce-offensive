<?php 

get_header();

?>



<body>  



<div class="main">  
  
  
  
<div class="left">  

	<h1 id="header"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>

	<font class="subname"><?php bloginfo('description'); ?>
	
	<br /> <br />
	
	<?php include (TEMPLATEPATH . '/sidebar2.php'); ?>

	<div id="menu">
	<div id="nav">
		<ul id="sidebar">

			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) : else : ?> <?php endif; ?>

		</ul>
	</div><!-- end NAV -->
	</div><!-- end MENU -->
	
</div>  
	
	
	
	
	
    <div class="right">  
      <div id="nav" class="nav">  

	  
	  <?php get_sidebar(); ?>
	  
	  
      </div>  
    </div>  


<div class="rap">  




<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



<div class="post">


<table border=0 class="null">

	<tr>
	<td>
		<center>
		<div class="date_month"><?php the_time('M') ?></div>
		<div class="date_day"><?php the_time('d') ?></div>
		<div class="date_year"><?php the_time('Y') ?></div>
	</td>


	<td width=10px>
	<br />
	</td>


	<td align="left">
		
		<h3 class="storytitle" id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
		<div class="meta">	
			<?php the_category(',') ?><?php the_tags('. ', ', ', ''); ?> <?php edit_post_link(__('Edit This')); ?>
		</div><!-- end META -->

	</td>
	</tr>
</table>	

	

	<div class="storycontent">

		<?php the_content(__('(more...)')); ?>

	</div><!-- end STORYCONTENT -->

	

	<div class="feedback">

            <?php comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)')); ?>

	</div><!-- end FEEDBACK -->

	

	<!--

	<?php trackback_rdf(); ?>

	-->
	
	<div class="break">
	</div>
	
	
	
	

</div><!-- end POST -->



<div id="comments-post">

<?php comments_template(); // Get wp-comments.php template ?>

</div><!-- end COMMENTS-POST -->



<?php endwhile; else: ?>

<p><center><br /><br /><br /><br /><br /><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif; ?>


<center>
<?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;'));  ?>




</div> <!-- rap -->

</div> <!-- MAIN -->



<?php get_footer(); ?>

