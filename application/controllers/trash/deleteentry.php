<?php

class Deleteentry extends MY_Controller {

	
	function index()
	{
		
		$this->load->model('trakentries', '', TRUE);
		$getentries = $this->trakentries->DeleteEntry($options = array('id' => $this->uri->segment(2)));
		
		$this->load->helper('url');
		redirect('trash', 'refresh');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>