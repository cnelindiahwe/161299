<?php

//libraries/MY_Controller.php
class MY_Controller extends CI_Controller
{
    
    public function MY_Controller()
    {
        //If the user is not logged in then proceed no further!
		$is_logged_in = $this->session->userdata('logged_in');
        if(!isset($is_logged_in) || $is_logged_in != true)
		{
				redirect('main');
			}
       }
}

/* End of file MY_Controller.php */
/* Location: ./system/application/libraries/MY_Controller.php */