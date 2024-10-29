<?php

function get_bemoalbum_selector()
{
	$args = array(
		'orderby' => 'count',
		'hide_empty' => 0
		);
	
	$albums = get_terms( 'album', $args );
	
	$retval = '<select name="album_id" >';
	
	for($i=0;$i<count($albums);$i++)
		$retval .= '<option value="'.$albums[$i]->name.'">'.$albums[$i]->name.'</option>';
	
	$retval .= '</select>';
	
	return $retval;
}

add_action('admin_footer', 'bemoalbum_admin_footer_function');
function bemoalbum_admin_footer_function() 
{
?>
<div id="bemoalbum-dialog-form" title="Add Album">
  <form id="bemoalbum_enter">
    <fieldset>
	<div>
 	</div>
 	<div  class="filter_specific" style="display: none">
      <label for="albumcolumns">Album</label>
	  <?php echo get_bemoalbum_selector(); ?> 
      <label for="albumcolumns">Album Columns</label>
      <input type="text" name="albumcolumns" id="albumcolumns" value="2" class="text ui-widget-content ui-corner-all">
      <label for="picturecolumns">Picture Columns</label>
      <input type="text" name="picturecolumns" id="picturecolumns" value="4" class="text ui-widget-content ui-corner-all">
      <label for="backlink_text">Backlink Text</label>
      <input type="text" name="backlink_text" id="backlink_text" value="<< Back" class="text ui-widget-content ui-corner-all">
      <label for="albumcaptions">Include Album Captions</label>
      <input type="checkbox" name="albumcaptions" id="albumcaptions" value="1" checked class="text ui-widget-content ui-corner-all">
      <label for="picturecaptions">Include Picture Captions</label>
      <input type="checkbox" name="picturecaptions" id="picturecaptions" value="1" checked class="text ui-widget-content ui-corner-all">
      <label for="albumsclass">Albums CSS Class(es)</label>
      <input type="text" name="albumsclass" id="albumsclass" value="albums row" class="text ui-widget-content ui-corner-all">
      <label for="albumclass">Album CSS Class(es)</label>
      <input type="text" name="albumclass" id="albumclass" value="album" class="text ui-widget-content ui-corner-all">
      <label for="picturesclass">Pictures CSS Class(es)</label>
      <input type="text" name="picturesclass" id="picturesclass" value="pictures row" class="text ui-widget-content ui-corner-all">
      <label for="pictureclass">Picture CSS Class(es)</label>
      <input type="text" name="pictureclass" id="pictureclass" value="picture" class="text ui-widget-content ui-corner-all">
	</div>	
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
<?php
}

/* Register buttons */
add_action('init', 'bemoalbum_add_button');

function bemoalbum_add_button() {
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
   {
     add_filter('mce_external_plugins', 'bemoalbum_add_plugin');
     add_filter('mce_buttons', 'bemoalbum_register_button');
   }
}

function bemoalbum_register_button($buttons) {
   array_push($buttons, "bemoalbum");
   return $buttons;
}

function bemoalbum_add_plugin($plugin_array) {
   $plugin_array['bemoalbum'] = plugins_url('js/bemoalbum.js', __FILE__ );
   return $plugin_array;
}

function bemoalbum_admin_scripts_and_styles()
{
	/* Jquery UI Dialog*/
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_style("wp-jquery-ui-dialog");

   wp_register_style( 'bemoalbum_admin_stylesheet', plugins_url('css/bemoalbum-admin.css', __FILE__) );
   wp_enqueue_style('bemoalbum_admin_stylesheet');
   
}

add_action( 'admin_init', 'bemoalbum_admin_scripts_and_styles' );
?>
