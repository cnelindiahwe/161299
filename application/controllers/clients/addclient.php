<?php

class Addclient extends MY_Controller {


	function index()
	{
		
		$this->load->helper('url');	
		/* load model and connect to db */
		$this->load->model('trakclients', '', TRUE);

    	//Call model routine
		
		foreach ($_POST as $key=>$value) {
			$required = array ("CompanyName","ClientCode","ZOWContact","BasePrice","Currency");
			if (in_array($key,$required) && $value==""){
				echo "Required field ".$key." is missing";
				redirect('clients', 'refresh');
			}
			else{
				$FormValues[$key]=trim($value);
			}
		} 

		$newentry = $this->trakclients->AddEntry($FormValues);
		
		if($newentry)
			{

			redirect('clients', 'refresh');
			//echo "Entry Added";
			}
		else
			echo "There was an error adding your entry.";
			//echo $this->db->last_query();
	}


}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>