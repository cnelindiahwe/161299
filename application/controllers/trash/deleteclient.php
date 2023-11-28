<?php

class Deleteclient extends MY_Controller {

	function Deleteclient()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{

		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->DeleteEntry($options = array('ID' => $this->uri->segment(3)));
		
		$this->load->helper('url');
		redirect('trash', 'trakclients');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>