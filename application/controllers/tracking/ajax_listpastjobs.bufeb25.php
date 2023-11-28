<?php

class Ajax_listpastjobs extends MY_Controller {

	function index()
	{
		$this->load->helper(array('trackingnew','general'));	
		$this->load->model('trakentries', '', TRUE);

	if ($_POST['originator'])
		{
		if ($_POST['originator']!="All")
			{
			$contactentries = $this->trakentries->GetEntry($options = array('Originator' => $_POST['originator'],'sortBy'=> 'DateOut','sortDirection'=> 'desc', 'limit'=> '20'));
			}
		else
			{
			$contactentries = $this->trakentries->GetEntry($options = array('Status' => 'COMPLETED','Trash' => '0', 'sortBy'=> 'DateOut','sortDirection'=> 'desc', 'limit'=> '20'));
			}
	
		if($contactentries)
			{
			$entries= _entrydatatable($contactentries);
			echo $entries;
			}
		else
			echo $_POST['originator'];
		}

	}


}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>