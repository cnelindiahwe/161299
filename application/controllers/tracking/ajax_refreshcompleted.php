<?php

class Ajax_refreshcompleted extends MY_Controller {

	
	function index()
	{

		$this->load->helper(array('tracking','general'));	
		echo $this-> _listPastJobs();

	}
		// ################## _list Past Jobs ##################	
	function  _listPastJobs()
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array('Status' => 'COMPLETED','Trash' => '0', 'sortBy'=> 'DateOut','sortDirection'=> 'desc', 'limit'=> '20'));
		
		if($getentries)
		{
			$entries= _entrydatatable($getentries);
		}
		else
		{
			$entries="PHP system error";
		}
		return $entries;
	}

}


/* End of file newentry.php */
/* Location: ./system/application/controllers/updateentry.php */
?>