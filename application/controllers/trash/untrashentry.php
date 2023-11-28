<?php

class Untrashentry extends MY_Controller {


	
	function index()
	{
		
		$this->load->model('trakentries', '', TRUE);
	
		$fields["id"] = $this->uri->segment(2);
		$fields['Trash'] = 0;

		$uentry = $this->trakentries->UpdateEntry($fields);

	
		
		$this->load->helper('url');
		redirect('trash', 'refresh');
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>