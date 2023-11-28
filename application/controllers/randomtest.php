<?php

class Randomtest extends Controller {

	function Randomtest()
	{
		parent::Controller();	
	}
	

		
		function index()
		{
		// check if logged in
			if($this->session->userdata('logged_in')) {
				$Owner_em= $this->session->userdata('user_email');
				$Owner_na= explode("@", $Owner_em);
				$Owner_fi= explode(".", $Owner_na[0]);
				if ($Owner_fi[0]) {
					$Owner= $Owner_fi[0];
					echo $Owner;
				}
				else {
					$Owner= $Owner_na[0];
					echo $Owner;
				}
			} 
			else {
				$this->load->view('login');	
			}
		}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>