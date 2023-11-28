<?php

class Ajax_trashentrynew extends MY_Controller {

	
	function index()
	{
		
		$this->load->model('trakentries', '', TRUE);
		$fields=$_POST;
		$fields['Trash'] = 1;

		$uentry = $this->trakentries->UpdateEntry($fields);
		
	}
	

}

/* End of file deleteentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>