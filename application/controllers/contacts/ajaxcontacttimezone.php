<?php

class Ajaxcontacttimezone extends MY_Controller {

	
	function index()
	{
		
		$this->load->helper(array('tracking'));
		
		//get entry #
		$fields=$_POST;
		$Originator=explode(' ',$fields['Originator']);
		$last=count($Originator)-1;
		
		$this->load->helper('url');	
		/* load model and connect to db */
		$this->load->model('trakcontacts', '', TRUE);

		$Contact = $this->trakcontacts->GetEntryLike($options = array('LastName'=>$Originator[$last],'FirstName'=>$Originator[0],'CompanyName'=>$fields['CompanyName'], 'Trash'=>'0'));
    	//Call model routine
		if ($Contact){
			echo $Contact[0]->TimeZone;
		
		}
	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>