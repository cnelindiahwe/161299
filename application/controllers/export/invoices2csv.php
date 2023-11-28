<?php

class Invoices2csv extends MY_Controller {


	function index()
	{

			$this->load->dbutil();
			$this->load->model('trakinvoices', '', TRUE);
			$getclients = $this->db->query("SELECT * FROM zowtrakinvoices");
			if($getclients )
			{
				$delimiter = ",";
				$newline = "\n";
				$data = $this->dbutil->csv_from_result($getclients , $delimiter, $newline);
				$name = "ZOWTrak_Invoices.csv";
				$this->load->helper('download');
				force_download($name, $data);
			}
			else {
				echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			}

	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */ ?>