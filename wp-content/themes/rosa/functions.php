<?php

automatic_feed_links();

if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '',
		'after_title' => '',
	));

function display_recent_categories(){
	global $wpdb, $wp_rewrite;
	
	$query = "select t.term_id as term_ids, t.name, t.slug, max(p.ID) as id, tx.count from $wpdb->terms t, $wpdb->term_taxonomy tx, $wpdb->term_relationships tr, $wpdb->posts p where t.term_id = tx.term_id and tx.parent != 0 and tx.taxonomy = 'category' and tr.term_taxonomy_id = t.term_id and tr.object_id = id and p.post_status = 'publish' group by term_ids order by id desc limit 5";
	
	$results = $wpdb->get_results($query);
	
	foreach ($results as $result) {
		$catlink = str_replace( '%category%', $result->slug, $wp_rewrite->get_category_permastruct());
		$catlink = get_option( 'home' ) . user_trailingslashit( $catlink, 'category' );
		echo "<li><a href='".$catlink."'>".$result->name."</a></li>";
	}
}

function display_selected_categories(){
	//display_categories_from_posts('category_name=albums&orderby=date&order=DESC&tag=selected');
	
	global $wpdb;
	
	$query = "select t.term_id as term_ids, t.name, t.slug, max(p.ID) as id, tx.count from $wpdb->terms t, $wpdb->term_taxonomy tx, $wpdb->term_relationships tr, $wpdb->posts p where t.name = 'selected' and t.term_id = tx.term_id and tx.parent != 0 and tx.taxonomy = 'category' and tr.term_taxonomy_id = t.term_id and tr.object_id = id and p.post_status = 'publish' group by term_ids order by id desc limit 5";
	
	echo $query;
	
	$results = $wpdb->get_results($query);
	
	foreach ($results as $result) {
		echo "<li><a href='".get_category_link($result->id)."'>".$result->name."</a></li>";
	}
}

?>
