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
		</div>
		
		<script type="text/javascript">

			// Change this to your username to load in your clips
			var vimeoUserName = 'douceoffensive';

			// Tell Vimeo what function to call
			var callback = 'showThumbs';

			// Set up the URLs
			var url = 'http://www.vimeo.com/api/v2/' + vimeoUserName + '/videos.json?callback=' + callback;

			// This function loads the data from Vimeo
			function init() {
				var js = document.createElement('script');
				js.setAttribute('type', 'text/javascript');
				js.setAttribute('src', url);
				document.getElementsByTagName('head').item(0).appendChild(js);
			}

			// This function goes through the clips and puts them on the page
			function showThumbs(videos) {
				var thumbs = document.getElementById('photobook');
				thumbs.innerHTML = '';

				for (var i = 0; i < videos.length; i++) {
					var thumb = document.createElement('img');
					thumb.setAttribute('src', videos[i].thumbnail_medium);
					thumb.setAttribute('alt', videos[i].title);
					thumb.setAttribute('title', videos[i].title);

					var a = document.createElement('a');
					a.setAttribute('href', videos[i].url);
					a.appendChild(document.createTextNode(videos[i].title));

					var p = document.createElement('p');
					p.appendChild(thumb);
					p.appendChild(document.createElement('br'));
					p.appendChild(a);
					thumbs.appendChild(p);
				}
			}

			// Call our init function when the page loads
			window.onload = init;

		</script>

		<div id="menu-right" class="menu">
			<ul>
				<?php echo $category_output; ?>
			</ul>
		</div>
	</div> <!-- #content -->

<?php get_footer() ?>