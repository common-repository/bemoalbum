<?php


class BEMOOptions
{
	protected $license_key_field_name = '';
	protected $url = '';
	
	protected function __construct()
	{

	}

	public function options_check()
	{
		$license_key_field_name = $this->license_key_field_name.'_license_key';

		// Read in existing option value from database
		$license_key_val = get_option( $license_key_field_name );

		$stringtocompare = $this->license_key_field_name. $_SERVER['SERVER_NAME'] .$this->license_key_field_name;
		$stringtocomparedev = $this->license_key_field_name."developer".$this->license_key_field_name;
		
		if(md5($stringtocompare) != $license_key_val && md5($stringtocomparedev) != $license_key_val)
			return false;
			
		return true;	
	}
	
	
}

?>
