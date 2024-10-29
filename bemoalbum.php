<?php
/**
 * Plugin Name: BEMO Album Plugin
 * Plugin URI: http://www.bemoore.com/
 * Description: Shows a collection of albums that you can click into to reveal the images.
 * Version: 1.0.0
 * Author: Bob Moore, BeMoore Software
 * Author URI: http://www.bemoore.com/
 * License: GPL2
 */
include("bemoalbum_form.php");
include("bemoalbum_options.php");

require_once(plugin_dir_path( __FILE__ ). "categories-images.php");
require_once(plugin_dir_path( __FILE__ ). "image_to_album.php");

add_action( 'wp_ajax_bemoalbum_category_update', 'bemoalbum_category_update' );

add_action( 'admin_menu','bemoalbum_add_options_menu' );

function bemoalbum_add_options_menu()
{
	$options = new BEMOAlbumOptions();	
	$options->add_options_menu();
}

function bemoalbum_category_update() {

    $name = $_POST['name'];
	$values = explode(',',$_POST['values']);

    //echo "Name : $name, Values: $values";
	
	$start = strpos($name,'[');
	$end = strpos($name,']');
	
	$name = substr($name,$start+1, $end - $start - 1);
	
	//print_r($values);
	
	wp_delete_object_term_relationships( $name, "album" );	
	wp_set_post_terms( $name, $values, "album"  );
	
    die();
}
 

//Create the album taxonomy
function bemoalbum_album_taxonomy() {
  $labels = array(
    'name'              => _x( 'Albums', 'taxonomy general name' ),
    'singular_name'     => _x( 'Album', 'taxonomy singular name' ),
    'search_items'      => __( 'Search Albums' ),
    'all_items'         => __( 'All Albums' ),
    'parent_item'       => __( 'Parent Albums' ),
    'parent_item_colon' => __( 'Parent Album:' ),
    'edit_item'         => __( 'Edit Album' ), 
    'update_item'       => __( 'Update Album' ),
    'add_new_item'      => __( 'Add New Album' ),
    'new_item_name'     => __( 'New Album' ),
    'menu_name'         => __( 'Albums' ),
	'not_found'          => __( 'No albums found' ),
    'not_found_in_trash' => __( 'No albums found in the Trash' ), 	
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
	'show_admin_column' => true,
  );
  register_taxonomy( 'album', 'attachment', $args );
}
add_action( 'init', 'bemoalbum_album_taxonomy', 0 );

//Add albums to media library
function bemoalbum_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'album', 'attachment' );
}
add_action( 'init' , 'bemoalbum_add_categories_to_attachments' );

function show_album($album_id,$current_name)
{
	if(get_album_name($album_id) == $current_name)
		return true;
	
	return false;
}

function get_album_name($album_id)
{
	$args = array(
		'orderby' => 'count',
		'hide_empty' => 0,
		'include' => array($album_id)
		);
	
	$terms = get_terms( 'album', $args );

	return $terms[0]->name; 
}

//Create the album shortcode
// Parameters:
//name: The name of the album (required)
//albumcaptions: Whether or not to show album captions (true|false). Default true
//picturecaptions: Whether or not to show pixture captions (true|false). Default true
//albumsclass: css class for albums. Default 'albums'
//albumclass: css class for albums. Default 'album'
//picturesclass: css class for albums. Default 'pictures'
//pictureclass: css class for albums. Default 'picture'

function bemoalbum_shortcode($atts)
{
//Default settings ...	
	if(!isset($atts['name']))
	{
		echo 'ERROR: you must set the name in the shortcode, e.g. [album name="My Album"]';
		return;
	}
		
	$atts['albumcolumns'] = isset($atts['albumcolumns'])?$atts['albumcolumns']:2;
	$atts['picturecolumns'] = isset($atts['picturecolumns'])?$atts['picturecolumns']:4;
	$atts['albumcaptions'] = isset($atts['albumcaptions'])?$atts['albumcaptions']:"true";
	$atts['backlink_text'] = isset($atts['backlink_text'])?$atts['backlink_text']:"<< Back";
	$atts['picturecaptions'] = isset($atts['picturecaptions'])?$atts['picturecaptions']:"true";
	$atts['albumsclass'] = isset($atts['albumsclass'])?$atts['albumsclass']:'albums row';
	$atts['albumclass'] = isset($atts['albumclass'])?$atts['albumclass']:'album';
	$atts['picturesclass'] = isset($atts['picturesclass'])?$atts['picturesclass']:'pictures row';
	$atts['pictureclass'] = isset($atts['pictureclass'])?$atts['pictureclass']:'picture';

	$albumcols_bootstrap_class = 'col-md-' . floor(12 / (int)$atts['albumcolumns']);
	$picturecols_bootstrap_class = 'col-md-' . floor(12 / (int)$atts['picturecolumns']);
	
	$atts['albumclass'] .= ' '.$albumcols_bootstrap_class;
	$atts['pictureclass'] .= ' '.$picturecols_bootstrap_class;
	
	$album_id = get_query_var( 'album_id' );

	$args = array(
		'orderby'           => 'name', 
		'order'             => 'ASC',
		'hide_empty'        => false, 
		'posts_per_page'	=> -1
	); 
	
	if(isset($album_id) && $album_id != "")
		$args['parent'] = $album_id;
	else if(isset($atts['name']))
		//Here we get the terms
		$args['name__like'] = $atts['name'];

		
	$show_albums = get_terms(array( 'album' ), $args);		
	
	//Get the back link
	$child_term = get_term( $album_id, 'album' );
	
	$retval = '';
	
	if(isset($child_term->parent))
	{
		$link = add_query_arg( 'album_id',$child_term->parent );
		
		if($child_term->parent == 0)
			$link = remove_query_arg( 'album_id' );
		
		$retval .= '<div class="row album-back-link">';
			$retval .= '<div class="col-md-12">';
				$retval .= '<a href="'.$link.'">'.$atts['backlink_text'].'</a>';
			$retval .= '</div>';
		$retval .= '</div>';
	}
	
	//Now get the albums  ...
	$retval .= '<div class="'.$atts['albumsclass'].'" >';
	
	$optionsObj = new BEMOAlbumOptions();
	
	for($i=0;$i<count($show_albums);$i++)
	{
		$retval .= '<div class="'.$atts['albumclass'].'" >';
		
		$show_albums[$i]->image = get_option('z_taxonomy_image'.$show_albums[$i]->term_id);
		
		if($show_albums[$i]->image == '')
			$show_albums[$i]->image = get_placeholder_image();
		
		//Build the link
		$link = add_query_arg( 'album_id', $show_albums[$i]->term_id );
		
		$retval .= '<a href="'.$link.'">';
			$retval .= '<img src="'.$show_albums[$i]->image.'" ></img>';
		$retval .= '</a>';
		
		if(!$optionsObj->options_check())
			$retval .= '<div class="additional_info"></div>';		
			
		if(trim(strtolower($atts['albumcaptions'])) == "true" && $show_albums[$i]->name != '')
		{
			$retval .= '<p class="caption">'.$show_albums[$i]->name;
			$retval .= '</p>';
		}
		
		$retval .= '</div>';
	}

	$retval .= '</div>';	//Close the albums class
	
	if((int)$album_id > 0 && $atts['albumcaptions'])
	{
		$album_name = get_album_name($album_id);
		$retval .= '<h2 class="albumcaption">'.$album_name.'</h2>';		
	}
	
	//if(show_album($album_id,$atts['name']))
	$retval .= get_pictures($album_id,$atts);
	
	if(current_user_can( 'manage_options' ) && ! $optionsObj->options_check())
		$retval .= $optionsObj->get_options_check_message();

	return $retval;
}

add_shortcode('album','bemoalbum_shortcode');

function get_placeholder_image()
{
	return untrailingslashit(plugins_url('', __FILE__)).'/images/placeholder.png';
}

function get_pictures($term_id,$atts)
{
	$args = array(
	'post_type' => 'attachment',
	'post_status' => 'inherit',
	'tax_query' => 
		array(
			array(
				'taxonomy' => 'album',
				'include_children' => false,
				'field'    => 'term_id',
				'terms' => array($term_id)
			),
		),
		'posts_per_page' => -1
	);

	$the_query = new WP_Query( $args );

	$retval .= '<div class="'.$atts['picturesclass'].'">';

	global $post;
	
	$default_attr = array(
		'class'	=> "attachment-thumbnail bemoalbum-picture"
	);	
	
	$optionsObj = new BEMOAlbumOptions();
	
	while ( $the_query->have_posts() ) {
		$the_query->the_post();

		$retval .= '<div class="'.$atts['pictureclass'].'" >';
		
		$src = wp_get_attachment_image_src( $post->ID, 'large' );
		
		$retval .= '<a class="colorbox_popup" href="'.$src[0].'" title="'.$post->post_title.'">';
		
			$retval .= wp_get_attachment_image( $post->ID, 'thumbnail' , 0, $default_attr  );
		$retval .= '</a>';	
		
		if(!$optionsObj->options_check())
			$retval .= '<div class="additional_info"></div>';			

		if(trim(strtolower($atts['picturecaptions'])) == "true" && $post->post_title != '')
			$retval .= '<p class="caption">'.$post->post_title.'</p>';
			
		$retval .= '</div>';
		
	}
	
	$retval .= '</div>';
	wp_reset_postdata();	
	return $retval;
}

/* Add query var */
function add_bemoalbum_query_var( $vars ){
  $vars[] = "album_id";
  return $vars;
}
add_filter( 'query_vars', 'add_bemoalbum_query_var' );

//Colorbox
add_action( 'wp_enqueue_scripts', 'bemoalbum_enqueue_script' );
function bemoalbum_enqueue_script() {
	$css_path = untrailingslashit(plugins_url( '', __FILE__ )) . "/css/bemoalbum.css";
	$colorbox_path = untrailingslashit(plugins_url( '', __FILE__ )) . "/colorbox/colorbox.css";
	$bootstrap_path = untrailingslashit(plugins_url( '', __FILE__ )) . "/css/bootstrap.min.css";
	$bootstrap_theme_path = untrailingslashit(plugins_url( '', __FILE__ )) . "/css/bootstrap-theme.min.css";
	
	wp_register_style( 'bemoalbum-style', $css_path );
	wp_register_style( 'colorbox', $colorbox_path );
	wp_register_style( 'bootstrap3', $bootstrap_path );
	wp_register_style( 'bootstrap3_theme', $bootstrap_theme_path );

	wp_enqueue_style( 'bemoalbum-style' );		
	wp_enqueue_style( 'colorbox' );					
	wp_enqueue_style( 'bootstrap3' );					
	wp_enqueue_style( 'bootstrap3_theme' );					
	
	wp_enqueue_script( 'colorbox', plugins_url( '/colorbox/jquery.colorbox-min.js', __FILE__ ), array('jquery') );
	wp_enqueue_script( 'slideshow', plugins_url( '/js/slideshow.js', __FILE__ ), array('jquery') );	
}

?>
