<?php

class Ajaxaddcontact extends MY_Controller {


	
	function index()
	{
		$this->load->model('trakclients', '', TRUE);
		$this->load->helper('url');	

		$fields=$_POST;
		$currentclient  = $this->trakclients->GetEntry($options = array('Trash'=>'0','CompanyName'=>$fields['client']));
		echo base_url().'contacts/viewclientcontacts/'.$currentclient->ID;
		

	}

	
}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>