<?php

class Ajax_clienttimezone extends MY_Controller {
	
	function index()
	{
		
		$this->load->helper(array('tracking'));
		
		//get entry #
		$fields=$_POST;
		$CompanyName=str_replace('~','&',$fields['client']);
		
		$this->load->helper('url');	
		/* load model and connect to db */
		$this->load->model('trakclients', '', TRUE);

		$Client = $this->trakclients->GetEntry($options = array('CompanyName'=>$CompanyName, 'Trash'=>'0'));
    	//Call model routine
		if ($Client){
			//echo $this->db->last_query();// $Client->TimeZone;
			echo $Client->TimeZone;
		
		}
	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>