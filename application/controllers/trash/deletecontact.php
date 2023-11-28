<?php

class Deletecontact extends MY_Controller {

	function Deletecontact()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{

		$this->load->model('trakcontacts', '', TRUE);
		$getentries = $this->trakcontacts->DeleteEntry($options = array('ID' => $this->uri->segment(3)));
		
		$this->load->helper('url');
		redirect('trash', 'refresh');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>