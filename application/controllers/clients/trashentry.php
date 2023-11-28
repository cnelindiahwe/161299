<?php

class Trashentry extends MY_Controller {


	
	function index()
	{
		
		$this->load->model('trakclients', '', TRUE);

		$fields["ID"] = $this->uri->segment(3);
		$fields['Trash'] = 1;

		$uentry = $this->trakclients->UpdateEntry($fields);


	
		
		$this->load->helper('url');
		redirect('clients', 'refresh');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>