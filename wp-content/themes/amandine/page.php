<?php get_header(); ?>

	<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="entry">
				<?php the_content('<p class="serif">Lire le reste de cette page &raquo;</p>'); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div>
		<?php edit_post_link('Modifier cette page.', '<p>', '</p>'); ?>
	 <?php endwhile; endif; ?>
	</div>

<?php get_footer(); ?>