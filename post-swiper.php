<?php
/*
Plugin Name:  Swiper (LITE)
Plugin URI:   https://southdevondigital.com/plugins/
Description:  Swiper gives you the ability to embed Tinder style swipable cards with content from your posts or pages. Users can swipe through these cards, left to 'dislike', or right to 'like'. Liked items will then be saved in a list below.
Version:      1.2.2
Author:       South Devon Digital
Author URI:   https://southdevondigital.com
Text Domain:  sdd-swiper
*/

// postswiper shortcode
function swiper_shortcode( $attributes ) {
	global $post;
	global $woocommerce;

	extract( shortcode_atts( array(
		'post_type' => '',
		'category' => '',
		'style' => '',
		'limit' => '',
		'orderby' => '',
		'order' => ''
	), $attributes ) );

	if ($post_type == 'post' || $post_type == 'posts'){
			$query_args = array(
				'post_type' => 'post',
				'posts_per_page' => $limit,
				'category_name' => $category,
				'orderby' => $orderby,
				'order' => $order,
			);
	} elseif ($post_type == 'page' || $post_type == 'pages'){
		$query_args = array(
			'post_type' => 'page',
			'posts_per_page' => $limit,
			'orderby' => $orderby,
			'order' => $order,
		);
	}

	$the_query = new WP_Query($query_args);

	if ( $the_query->have_posts() ) {
		wp_enqueue_script('jquery-mobile', plugins_url('/incs/jquery-mobile/jquery.mobile.custom.min.js', __FILE__), array('jquery'));
		wp_enqueue_style('jquery-mobile-css', plugins_url('/incs/jquery-mobile/jquery.mobile.structure-1.4.5.min.css', __FILE__) );
		wp_enqueue_script('js-cookie', plugins_url('/incs/js.cookie.min.js', __FILE__));
		wp_enqueue_script('postswiper-js', plugins_url('/js/post-swiper.js', __FILE__), array('jquery'));
		wp_enqueue_style('postswiper-css', plugins_url('/css/style.css', __FILE__));
		wp_enqueue_style('dashicons');
		ob_start(); ?>
		<div class="postswiper-wrapper<?php if ($style != ''){ echo ' '.$style; } ?>"><div class="postswiper-overlay"></div><p>You\'ve swiped through everything!<br />Check out your \'liked\' list below, or <a href="#" id="swiper-start-again">start again</a>.<br /><br /><span class="dashicons dashicons-arrow-down-alt"></span></p>
		<?php
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			if ( $post_type == "posts" || $post_type == "post") {
				$html = '<div class="postswiper-post">' .
							'<div class="postswiper-postheader">' .
								'<h3 class="postswiper-posttitle">' .
									'<a href="' . get_the_permalink() . '">' .
										get_the_title() .
									'</a>' .
								'</h3>' .
								'<span class="postswiper-postdate">' .
									'<span class="dashicons dashicons-calendar-alt"></span>' .
									get_the_date() .
								'</span>' .
							'</div>' .
							'<div class="postswiper-postinfo">';
							if (get_the_post_thumbnail_url() != '') {
								$html .= '<div class="postswiper-featuredimg" style="background-image: url('. get_the_post_thumbnail_url() .')"></div>';
							}
					$html .= get_the_excerpt() .
							'</div>' .
							'<div class="postswiper-postcats">';
					$postid = get_the_id();
					$postcats = get_the_category();
					foreach ($postcats as $cat) {
						$html .= '<a href="' . $cat -> slug . '" class="postswiper-cat-link">';
						$html .= '<span class="dashicons dashicons-category"></span>';
						$html .= $cat -> name . '</a>';
					}
					$html .= '</div>' .
						'</div>';
			} elseif ( $post_type == "pages" || $post_type == "page") {
				$html = '<div class="postswiper-post">' .
							'<div class="postswiper-postheader">' .
								'<h3 class="postswiper-posttitle">' .
									'<a href="' . get_the_permalink() . '">' .
										get_the_title() .
									'</a>' .
								'</h3>' .
							'</div>' .
							'<div class="postswiper-postinfo" style="border-bottom: none; padding-bottom: 0; margin-bottom: 0;">';
							if (get_the_post_thumbnail_url() != '') {
								$html .= '<div class="postswiper-featuredimg" style="background-image: url('. get_the_post_thumbnail_url() .')"></div>';
							}
					$html .= get_the_excerpt() .
							'</div>' .
							'<div class="postswiper-postcats">';
					$postid = get_the_id();
					$postcats = get_the_category();
					$html .= '</div>' .
						'</div>';
			} else {
				$html = '<p>Unknown post type</p>';
			}
			echo wp_unslash($html);
			wp_reset_query();
		}
		?>
		</div>
		<div class="postswiper-likedlist-wrapper<?php if ($style != ''){ echo ' '.$style; } ?>">
			<a class="disabled postswiper-likedlist-opener" href="/" onclick="return false;">
				<span class="dashicons dashicons-heart"></span><span class="postswiper-likedlist-count">View my liked items (0)</span>
			</a>
			<div class="postswiper-likedlist-list"></div>
		</div>
		<?php return wp_unslash(ob_get_clean());
	} else {
		return 'No posts found, try generating a new shortcode.';
	}

	wp_reset_postdata();

}
add_shortcode( 'swiper', 'swiper_shortcode' );

/* Admin menu page */
function register_admin_page(){
    add_menu_page(
        __( 'Swiper (LITE)', 'sdd-swiper' ),
        'Swiper (LITE)',
        'manage_options',
        'swiper',
        'write_admin_page',
        'dashicons-image-flip-horizontal',
        22
    );
}
add_action( 'admin_menu', 'register_admin_page' );

/* Function to render the admin menu page */
function write_admin_page(){
    ?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Swiper (LITE)</h1>
		<div class="admin-section" id="shortcode-generator">
			<div class="admin-section-title">
				<h3>Shortcode Generator</h3>
			</div>
			<div class="admin-section-content">
				<label for="post-type-select">Show:</label>
				<br />
				<select id="post-type-select">
					<option value="posts">Posts</option>
					<option value="pages">Pages</option>
					<option value="" disabled>Woocommerce Products (PRO)</option>
					<option value="" disabled>BuddyPress Profiles (PRO)</option>
				</select>
				<div id="post-categories">
					<label for="post-category-name">From the category:</label>
					<br />
					<select id="post-category-name">
						<option value="">Any</option>
						<?php
							$categories = get_categories( array(
								'orderby' => 'name',
								'order'   => 'ASC'
							) );

							foreach( $categories as $category ) {
								$category_option = sprintf(
									'<option>%1$s</option>',
									esc_html( $category->name )
								);

								echo $category_option;
							}
						?>
					</select>
				</div>
				<div id="style-select-wrapper">
					<label for="style-select">Using the style:</label>
					<br />
					<select id="style-select">
						<option value="">Default</option>
						<option value="dark">Dark</option>
						<option value="steel">Steel</option>
						<option value="carbon" disabled>Carbon (PRO)</option>
						<option value="wavy" disabled>Wavy (PRO)</option>
						<option value="argyle" disabled>Argyle (PRO)</option>
						<option value="metro" disabled>Metro (PRO)</option>
						<option value="honeycomb" disabled>Honeycomb (PRO)</option>
						<option value="armchair" disabled>Armchair (PRO)</option>
						<option value="hearts" disabled>Hearts (PRO)</option>
						<option value="paper" disabled>Paper (PRO)</option>
					</select>
					<div class="input-note">
						<strong>Note:</strong> Link colours will be inherited from your site's theme. To change them in css, use <em>.postswiper-post a</em>
					</div>
				</div>
				<div id="limit-num-wrapper">
					<label for="limit-num">Limit cards to:</label>
					<br />
					<input type="number" id="limit-num" value="0" min="0" />
					<div class="input-note">
						<strong>Note:</strong> Set to 0 for unlimited
					</div>
				</div>
				<div id="post-sort-select-wrapper">
					<label for="post-sort-select">Order by:</label>
					<br />
					<select id="post-sort-select">
						<option value="">Publish Date</option>
						<option value="rand">Random</option>
						<option value="modified">Last Modified Date</option>
						<option value="ID">ID</option>
						<option value="author">Author</option>
						<option value="title">Title</option>
						<option value="name">Name</option>
						<option value="comment_count">Number of Comments</option>
						<option value="menu_order">Menu Order</option>
					</select>
				</div>
				<div id="order-select-wrapper">
					<label for="order-select">Order:</label>
					<br />
					<select id="order-select">
						<option value="">Descending</option>
						<option value="ASC">Ascending</option>
					</select>
				</div>
				<div id="shortcode-output-wrapper">
					<label for="shortcode-output">Shortcode:</label>
					<br />
					<input type="text" id="shortcode-output" readonly /><button id="copy-shortcode"><span class="dashicons dashicons-admin-page"></span></button>
					<div id="shortcode-copied">Copied to clipboard</div>
				</div>
			</div>
		</div>
		<div class="admin-section">
			<div class="admin-section-title">
				<h3>Style Preview</h3>
			</div>
			<div class="admin-section-content">
				<div class="postswiper-wrapper">
					<div class="postswiper-post" style="">
						<div class="postswiper-postheader">
							<h3 class="postswiper-posttitle">
								<a href="/" onclick="return false;">Lorem ipsum dolor sit amet</a>
							</h3>
							<span class="postswiper-postdate"><span class="dashicons dashicons-calendar-alt"></span>August 31, 2018</span>
						</div>
						<div class="postswiper-postinfo">
							<div class="postswiper-featuredimg" style="background-image: url(<?php echo plugins_url('images/preview.jpg',__FILE__ ) ?>)"></div>
							Donec orci lectus, aliquam ut nulla. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Sed fringilla mauris sit amet nibh. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean ut eros et nisl sagittis vestibulum. Ut varius tincidunt […]
						</div>
						<div class="postswiper-postcats">
							<a href="/" onclick="return false;"><span class="dashicons dashicons-category"></span>Music</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="admin-section">
			<div class="admin-section-title">
				<h3>Upgrade to the Pro Version</h3>
			</div>
			<div class="admin-section-content">
				<p><a class="button button-primary button-large alignright" style="margin-left: 5px;" href="https://www.southdevondigital.com/shop/swiper-pro/" target="_blank" rel="noopener noreferrer">Upgrade</a>Upgrading to Swiper PRO enables support for WooCommerce products, BuddyPress profiles, lots of extra styles, and gives you one year of free updates & priority support. It also helps support the development of this plugin (feature requests are welcome)!</p>
				<p>If you would like to show your support but aren't ready to upgrade, please consider <a href="https://wordpress.org/plugins/swiper-lite/#reviews" target="_blank">leaving a review on the WordPress plugin repository</a>. If you have come across an issue, please <a href="https://wordpress.org/support/plugin/swiper-lite/" target="_blank">open a support ticket</a> before leaving a review.</p>
				<h4 style="margin-bottom: 11px;">Follow South Devon Digital on Facebook for updates, news & offers</h4>
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2&appId=759596830864125&autoLogAppEvents=1';
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
				<div class="fb-like" data-href="https://www.facebook.com/SouthDevonDigital/" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
			</div>
		</div>
	</div>
	<?php
}

/* load admin css on the post swiper admin page */
add_action('admin_enqueue_scripts', 'admin_enqueues');

function admin_enqueues($hook){

	$current_screen = get_current_screen();

	if ( strpos($current_screen->base, 'swiper') === false) {
		return;
	} else {
		wp_enqueue_style('admin_css', plugins_url('css/admin.css',__FILE__ ));
		wp_enqueue_script('admin_js', plugins_url('js/admin.js',__FILE__ ));
		wp_enqueue_style('style_css', plugins_url('css/style.css',__FILE__ ));
	}
}
