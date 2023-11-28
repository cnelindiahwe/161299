<?php

die('Coming soon!');

class Main extends CI_Controller {

	public function Main()
	{
	
		// load parent controller	
		parent::__construct();

		// load libraries here
		$this->load->database();
		$this->load->library('session');
		$this->load->library('SimpleLoginSecure');
		
		//Load the URL helper
		$this->load->helper('url');	
	}
	
		public function index()
		{



		// check if logged in
			if($this->session->userdata('logged_in')) {
				redirect('tracking');
			} else {
				$this->load->view('login');	
			}




		}


		public function login()
		{
				
			//Load
			$this->load->library('form_validation');
			
			//validate incoming variables
			$this->form_validation->set_rules('login_username', 'Username', 'required|min_length[4]|max_length[255]|valid_email');
			$this->form_validation->set_rules('login_password', 'Password', 'required|min_length[4]|max_length[255]|alpha_dash');

				
			if ($this->form_validation->run() == false) {
				echo 'validation error';	
			} else {
				//Log user
				if($this->simpleloginsecure->login($this->input->post('login_username'), $this->input->post('login_password'))) {
				$newdata = array(
					   'timezone'  => $this->input->post('login_timezone')
				   );
					
					$this->session->set_userdata($newdata);
				
					redirect('tracking');	
				} else {
				//Error loging user
					echo 'User not recognized';				
				}			
			}
		}

	function logout()
			//Logout
	{
		// check if logged in
		if($this->session->userdata('logged_in')) { 
			$this->simpleloginsecure->logout();
		}
		redirect('main');
	}
	

	

}

/* End of file main.php */
/* Location: ./system/application/controllers/main.php */