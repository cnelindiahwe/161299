<?php

class Updateclient extends My_Controller {


	
	function index()
	{


		/* load model and connect to db */
		$this->load->model('trakclients', '', TRUE);
		$this->load->helper('url');

   		//Call model routine
		
		
			$fields=$_POST;
			$fields["ID"] = $this->uri->segment(3);
			foreach ($fields as $key=>$value) {
				$required = array ("CompanyName","ZOWContact","BasePrice","Currency");
				if (in_array($key,$required) && $value==""){
					echo "Required field ".$key." is missing";
					redirect('clients', 'refresh');
				}
				else{
					$FormValues[$key]=trim($value);
					//echo $key.":".$FormValues[$key]."<br>";
				}
			} 


		$gentry = $this->trakclients->GetEntry(array ("ID"=>$fields["ID"]));
		
		if 	($gentry)
			{
				$uentry = $this->trakclients->UpdateEntry($FormValues);
				if($uentry)
					{
					//Update databases if company name changes
					if 	($gentry->CompanyName!=$fields["CompanyName"]){
						$this->load->model('trakcontacts', '', TRUE);
						$companycontacts = $this->trakcontacts->GetEntry(array ("CompanyName"=>$gentry->CompanyName));
						if ($companycontacts ){
							foreach ($companycontacts as $row){
								$ucontact = $this->trakcontacts->UpdateEntry(array("ID"=>$row->ID,"CompanyName"=>$fields["CompanyName"]));
							}
						}
						$this->load->model('trakentries', '', TRUE);
						$companyentries = $this->trakentries->GetEntry(array ("Client"=>$gentry->CompanyName));
						if ($companyentries ){
							foreach ($companyentries as $row){
								$uentry = $this->trakentries->UpdateEntry(array("id"=>$row->id,"Client"=>$fields["CompanyName"]));
							}
						}
					}
					redirect('clients', 'refresh');
					}
			}

		echo "There was an error adding your entry. Query was as follows:<br>";
		echo $this->db->last_query();

		
	}
}

/* End of file updateclient.php */
/* Location: ./system/application/controllers/clients/updateclient.php */
?>