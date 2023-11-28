<?php

class MY_Controller extends CI_Controller
{
   public function __construct()
    {
        parent::__construct();
				$this->load->library('session');
				$this->load->helper(array('url'));	
			 //If the user is not logged in then proceed no further!
				$is_logged_in = $this->session->userdata('logged_in');
				if(!isset($is_logged_in) || $is_logged_in != true)
				{
					
					// if($this->uri->segment('1') == 'download-file'){
					// 	redirect('download-file');

					// }else{
						redirect('main');

					// }
				}
		}
}

/* End of file MY_Controller.php */
/* Location: ./system/application/libraries/MY_Controller.php */