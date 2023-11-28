<?php

class Tzcalc extends Controller {

	function Tzcalc()
	{
		parent::Controller();	
	}
	
	function index()
	{
	 if ($this->input->post('tz')!=""){
	 	$utz=$this->_getusertimedata($this->input->post('tz'));
	 }

	if (isset($utz)){$templateVars['usertime'] =$utz;}
 	$this->load->vars($templateVars);		


	$this->load->view('tzview');

	}
	
	function  _getusertimedata($utz)
	{

		// Get server time
		$timestamp = time();
		 
	 
		// create the DateTimeZone object for later
		$dtzone = new DateTimeZone($utz);
		 
		// first convert the timestamp into a string representing the local time
		$time = date('r', $timestamp);
		 
		// now create the DateTime object for this time
		$dtime = new DateTime($time);
		 
		// convert this to the user's timezone using the DateTimeZone object
		$dtime->setTimeZone($dtzone);
		 
		// print the time using your preferred format
		$usertimedata = $dtime->format('g:i A m/d/y');
		$usertimedata .= " ".$utz;
		return $usertimedata;

		}


}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>