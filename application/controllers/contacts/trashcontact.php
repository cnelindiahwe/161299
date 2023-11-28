<?php

class Trashcontact extends MY_Controller {


	
	function index()
	{
		
		$this->load->helper('url');
		$this->load->model('trakcontacts', '', TRUE);

		$fields["ID"] = $this->uri->segment(3);
		$fields['Trash'] = 1;

		$uentry = $this->trakcontacts->UpdateEntry($fields);
		$gentry = $this->trakcontacts->GetEntry($options = array('ID'=>$fields["ID"]));
		if($gentry)
			{
			//echo $this->db->last_query();
			//$result=$query->row(0);
			$CompanyName=$gentry->CompanyName;
			$this->load->model('trakclients', '', TRUE);
			$currentclient = $this->trakclients->GetEntry($options = array('CompanyName' => $CompanyName));
				if($currentclient){
					redirect('contacts/viewclientcontacts/'.$currentclient->ID, 'refresh');
				}
			
			}

			echo "There was an error deleting the contact.";
			echo $this->db->last_query();

		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>