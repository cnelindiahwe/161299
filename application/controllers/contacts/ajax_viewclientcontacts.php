<?php

class Ajax_viewclientcontacts extends MY_Controller {


	
	function index()
	{

		$fields=$_POST;
		$CompanyName=str_replace('~','&',$fields['client']);
		$this->load->model('trakcontacts', '', TRUE);
		
		if (!isset ($fields['inactive'])) {
			$ContactList  = $this->trakcontacts->GetEntry($options = array('Trash'=>'0','CompanyName'=>$CompanyName,'Active'=>'1','sortBy'=>'FirstName','sortDirection'=>'ASC	'));
		}
		else {
			if ($fields['inactive']=="yes") {
				$ContactList  = $this->trakcontacts->GetEntry($options = array('Trash'=>'0','CompanyName'=>$CompanyName,'sortBy'=>'FirstName','sortDirection'=>'ASC	'));
			} else {
				$ContactList  = $this->trakcontacts->GetEntry($options = array('Trash'=>'0','CompanyName'=>$CompanyName,'Active'=>'1','sortBy'=>'FirstName','sortDirection'=>'ASC	'));
			}
		}
		$newdropdown = "<select id=\"Originator\" name=\"Originator\" class=\"Origin Originator\">";
		if (count((array)$ContactList)!=1)	$newdropdown .="<option></option>";
		if ($ContactList) {	
			foreach($ContactList as $client)
			{
				$newdropdown .="<option value=\"".$client->FirstName." ".$client->LastName."\">".$client->FirstName." ".$client->LastName."</option>";
			}	
		}
		//$newdropdown .="<option value=\"newcontact\">Create new contact</option>";
		$newdropdown .= "</select>";		

		echo $newdropdown;

	}

	
}

/* End of file addcontact.php */
/* Location: ./system/application/controllers/contacts/addcontact.php */
?>