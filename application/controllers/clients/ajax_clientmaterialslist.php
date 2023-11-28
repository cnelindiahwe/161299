<?php

class Ajax_clientmaterialslist extends MY_Controller {

	function index()
	{
			$this->load->helper(array('clients'));	
			$clientcode = $this->uri->segment(3);
			$SuperUser = $this->uri->segment(4);
			//get unit materials
			echo getTravellerClientMaterials($clientcode,$SuperUser);

			//get group materials
			echo getTravellerClientGroupMaterials($clientcode,$SuperUser);
			
		}
	
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>