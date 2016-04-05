<?php
/*
Plugin Name: Video Game Collector Post Type
Description: Keep track of the games in your collection
Author: Patrick Jones
Author URI: http://www.patrickmjones.com
Version: 0.0.1
*/

add_action('init', 'pj_game_opt');

function pj_game_opt() {
	register_post_type( 'videogame', array(
		'labels' => array(
			'name' => 'Videogames',
			'singular_name' => 'Videogame'
		),
		'description' => 'Videogames in your collection.',
		'public' => true,
		'menu_position' => 20,
		'supports' => array('title', 'editor', 'custom-fields'),
		'menu_icon' => 'dashicons-album'
	));

	register_taxonomy(
		'videogame_developer',
		'videogame',
		array(
			'labels' => array(
				'name' => __( 'Developers'),
				'singular_name' => __('Developer'),
				'add_new_item' => __('Add New Developer'),
				'edit_item' => __('Edit Developer'),
				'new_item' => __('New Developer'),
				'search_items' => __('Search Developers')
			),
			'rewrite' => array('slug' => 'videogame_developer'),
			'hierarchical' => false
		)
	);
	register_taxonomy(
		'videogame_publisher',
		'videogame',
		array(
			'labels' => array(
				'name' => __( 'Publishers'),
				'singular_name' => __('Publisher'),
				'add_new_item' => __('Add New Publisher'),
				'edit_item' => __('Edit Publisher'),
				'new_item' => __('New Publisher'),
				'search_items' => __('Search Publishers')
			),
			'hierarchical' => false
		)
	);
	register_taxonomy(
		'videogame_platform',
		'videogame',
		array(
			'labels' => array(
				'name' => __( 'Platforms'),
				'singular_name' => __('Platform'),
				'add_new_item' => __('Add New Platform'),
				'edit_item' => __('Edit Platform'),
				'new_item' => __('New Platform'),
				'search_items' => __('Search Platforms')
			),
			'hierarchical' => false
		)
	);

	add_filter('manage_edit-videogame_columns', 'pj_game_add_new_videogame_columns');
	add_filter("template_include", 'pj_videogame_template_include' );
	add_action('manage_videogame_posts_custom_column', 'pj_game_manage_videogame_columns', 10, 2);
} 

function pj_game_add_new_videogame_columns($videogame_columns) {
	$new_videogame_columns['cb'] = '<input type="checkbox" />';
	$new_videogame_columns['title'] = _x('Game Name', 'column name');
	$new_videogame_columns['platform'] = __('Platform');
	$new_videogame_columns['publisher'] = __('Publisher');
	$new_videogame_columns['developer'] = __('Developer');
	$new_videogame_columns['manual'] = __('Manual');
	$new_videogame_columns['complete'] = __('Complete');

	return $new_videogame_columns;
}
 
function pj_game_manage_videogame_columns($column_name, $id) {
	global $wpdb;
	switch ($column_name) {
		case 'platform':
			$platform_id = get_field('platform', $id);
			$term = get_term( $platform_id, 'videogame_platform');
			echo $term->name;
			break;
		case 'publisher':
			$publisher_id = get_field('publisher', $id);
			$term = get_term( $publisher_id, 'videogame_publisher');
			echo $term->name;
			break;
		case 'developer':
			$developer_id = get_field('developer', $id);
			$term = get_term( $developer_id, 'videogame_developer');
			echo $term->name;
			break;
		case 'manual':
			$has_manual = get_field('has_manual', $id);
			if($has_manual) {
				echo '<input type="checkbox" checked="checked" disabled />';
			} else {
				echo '<input type="checkbox" disabled />';
			}
			break;
		case 'complete':
			$is_complete = get_field('is_complete', $id);
			if($is_complete) {
				echo '<input type="checkbox" checked="checked" disabled />';
			} else {
				echo '<input type="checkbox" disabled />';
			}
			break;
		default:
			break;
	} // end switch
}   

function pj_videogame_template_include( $template_path ) {
	if ( get_post_type() == 'videogame' ) {
		if ( is_single() ) {
			// checks if the file exists in the theme first,
			// otherwise serve the file from the plugin
			if ( $theme_file = locate_template( array ( 'single-videogame.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/single-videogame.php';
			}
		}else if ( is_archive() ) {
			if ( $theme_file = locate_template( array ( 'archive-videogame.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/archive-videogame.php';
			}
		}
	}
	return $template_path;
}

?>
