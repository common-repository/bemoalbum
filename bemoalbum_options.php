<?php
include_once("bemo_options.php");

class BEMOAlbumOptions extends BEMOOptions
{
	function __construct()
	{
		$this->license_key_field_name = 'bemoalbum';
		$this->url = 'http://www.bemoore.com/'.$this->license_key_field_name.'/';	
		
		parent::__construct();
	}
		

	function get_options_check_message()
	{
		return '<p class="unlicensed">BEMOAlbum Unlicensed Version : Please visit <a href="'.$this->url.'">www.bemoore.com</a> to remove this message.</p>';
	}
	
	function add_options_menu()
	{
		add_options_page( 'BEMO Album Options', 'BEMOAlbum', 'manage_options', 'bemoalbum_settings', array($this,'options') );			
	}
	
	function options() {
		if ( !current_user_can( 'manage_options' ) ) 
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			
	  // variables for the field and option names 
		$license_key_field_name = 'bemoalbum_license_key';

		// Read in existing option value from database
		$license_key_val = get_option( $license_key_field_name );

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'

			// Read their posted value
			if(isset($_POST[ $license_key_field_name ]))
				$license_key_val = $_POST[ $license_key_field_name ];

			// Save the posted value in the database
			update_option( $license_key_field_name, $license_key_val );

			// Put a "settings saved" message on the screen


	   // Now display the settings editing screen

		echo '<div class="wrap">';

		// header

		echo "<h2>" . __( 'BEMO Album Settings', 'bemoalbum_options' ) . "</h2>";

		// settings form
		echo '<h2>Purchase a license key <a href="'.$this->url.'" target="_blank">here</a></h2>';
		?>
		<form name="form1" method="post" action="">

		<p><?php _e("License Key:", 'bemoalbum_options' ); ?> 
		<input type="text" name="<?php echo $license_key_field_name; ?>" value="<?php echo $license_key_val; ?>" size="40">
		</p><hr />

		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
		

		</form>
		</div>
	<?php	
	}
}
?>
