<?php

class Untrashclient extends MY_Controller {

	function Untrashclient()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		
		$this->load->model('trakclients', '', TRUE);

		$fields["ID"] = $this->uri->segment(3);
		$fields['Trash'] = 0;

		$uentry = $this->trakclients->UpdateEntry($fields);
		
		$this->load->helper('url');		
		redirect('trash', 'refresh');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>