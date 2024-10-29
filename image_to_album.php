<?php
add_action( 'admin_menu', 'register_bemoalbum_images_to_albums' );

function register_bemoalbum_images_to_albums(){
	//add_menu_page( 'custom menu title', 'custom menu', 'manage_options', 'custompage', 'bemoalbum_images_to_albums', plugins_url( 'myplugin/images/icon.png' ), 6 ); 
	add_submenu_page( 'upload.php', 'Images To Albums', 'Images To Albums', 'manage_options', 'images-to-bemoalbums', 'bemoalbum_images_to_albums' );	
}

function bemoalbum_images_to_albums()
{
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Images To Albums</h2>';
	echo '</div>';	
	
	require_once(plugin_dir_path( __FILE__ ). "classes/class.Image_To_Album_Table.php");	

	tt_render_list_page();
}
?>
