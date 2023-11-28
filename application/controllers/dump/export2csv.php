<?php

class Export2csv extends MY_Controller {

	function Export2csv()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		if ($this->uri->segment(3)=="") {
			$Msg="System error";
			$this->session->set_flashdata('system_message', $Msg);
			redirect('staff/portal', 'refresh');
		}
		else {
			$this->load->dbutil();
			$this->load->model('gdy_properties', '', TRUE);
			$PropertyType=$this->uri->segment(3);
			$PropInfo = $this->db->query("SELECT * FROM ".$PropertyType);
			$delimiter = ",";
			$newline = "\n";
			if ($PropInfo) {
				$data = $this->dbutil->csv_from_result($PropInfo, $delimiter, $newline);
				$name = "GDY_".$PropertyType.".csv";
				$this->load->helper('download');
				force_download($name, $data);
			}
			else {
				echo $this->db->last_query();
			}
					
		}

	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>