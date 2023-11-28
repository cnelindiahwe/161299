<?php

class Ajax_clientdate extends MY_Controller {
	
	function index()
	{
		
		// Get server time
		$fields=$_POST;
		
		// Get server time
		$timestamp = time();	
			
		// create the DateTimeZone object for later
		$dtzone = new DateTimeZone($fields['TimeZoneIn']);
		 
		// first convert the timestamp into a string representing the local time
		$time = date('r', $timestamp);
		 
		// now create the DateTime object for this time
		$dtime = new DateTime($time);
		 
		// convert this to the user's timezone using the DateTimeZone object
		$dtime->setTimeZone($dtzone);
		 
		// print the time using your preferred format
		$usertimedata = $dtime->format('d-M-Y');
		echo $usertimedata;			
		
		
	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>