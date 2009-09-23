<?php 
	get_header();
	
	$current_cat = get_query_var('cat');
?>

	<div id="content" class="autosize">
		<div id="menu-left" class="menu">
			<ul>
				<?php
				$categories = get_categories();
				
				$category_output = '';
				$i = 1;
				foreach($categories as $cat):
					$category_output .= '<li><a title="' . sprintf(__( 'View all posts filed under %s' ), attribute_escape($cat->name)) . '"';
					if ($cat->cat_ID == $current_cat) {
						$category_output .= ' href="' . get_category_link( $cat->term_id ) . '"><strong>' . attribute_escape($cat->name) . '</strong></a>';
					} else {
						$category_output .= ' href="' . get_category_link( $cat->term_id ) . '">' . attribute_escape($cat->name) . '</a>';
					}
					if($cat->slug = "videos") {
						$category_output .= ' (' . intval($cat->count) . ')</li>';
					}
				endforeach;

				$category_output .= '<li>contact: <a href="mailto:globaleffect@gmail.com">globaleffect@gmail.com</a></li><ul>';
				echo $category_output;
				?>
			</ul>
		</div>
		
		<div id="photobook">

		<?php
		$vimeo_user_name = 'douceoffensive';
		
		// endpoints
		$api_endpoint = 'http://www.vimeo.com/api/v2/'.$vimeo_user_name;
		$oembed_endpoint = 'http://vimeo.com/api/oembed.xml?url=http%3A//vimeo.com/';

		// Curl helper function
		function curl_get($url) {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			$return = curl_exec($curl);
			curl_close($curl);
			return $return;
		}
		
		// Load the user clips
		$videos = simplexml_load_string(curl_get($api_endpoint.'/videos.xml'));
		
		foreach ($videos->video as $video):
			$embed = simplexml_load_string(curl_get($api_endpoint.$video->id));?>
			<div class="video"><?=$embed->html?></div>
		<?php endforeach;?>

		</div>

		<div id="menu-right" class="menu">
			<ul>
				<?php echo $category_output; ?>
			</ul>
		</div>
	</div> <!-- #content -->

<?php get_footer() ?>