<?php

class Zt2016_exportdata extends MY_Controller {


	function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 


		$this->load->helper(array('userpermissions','form','url'));
		
		#### Return if no export option provided via the url
		$numsegments =  $this->uri->total_segments();
		
		if ($numsegments<3) {
			$Message= "No export option provided.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'export/zt2016_export');
		}	
		
		
		### Extract export option from URL
		$exportoption=str_replace("export/zt2016_exportdata/", "", $this->uri->uri_string());
		

		### csv table
		if ($this->uri->segment(3)=='data2csv') $this->_data2csv($this->uri->segment(4));
		
		### dbbackup
		else if ($exportoption=='databasebackup') $this->_databasebackup();
		
		### phpinfo
		else if ($exportoption=='phpinfo') phpinfo();
		
		die ($exportoption);
		### Export option not recognized
		$Message= "Export option not recognized.";
		$this->session->set_flashdata('ErrorMessage',$Message);
		redirect(Base_Url().'export/zt2016_export');

	}
	
	
	function _displayphpinfo(){
		phpinfo();
	}
		
	function _databasebackup(){
	######## Download sql dump
		### Set variables
		$DBUSER=$this->db->username;
		$DBPASSWD=$this->db->password;
		$DATABASE=$this->db->database;
		
		$filename = $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql.gz";		
		 $save_path = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/database_bu" ;
			
		### delete oldest existing automatic backuop file
			$files = scandir($save_path );
			
			# Count number of files in automatic backup dir (-2 for . and ..)
			$num_files = count($files)-2;
			
			# If more than 14 automatic backuop files, delete oldest file
			if ($num_files >14) {

				$files = glob( $save_path.'/*.*' );
				$exclude_files = array('.', '..');
				if (!in_array($files, $exclude_files)) {
				# Sort files by modified time, latest to earliest
				# Use SORT_ASC in place of SORT_DESC for earliest to latest
				array_multisort(
				array_map( 'filemtime', $files ),
				SORT_NUMERIC,
				SORT_ASC,
				$files
				);
				}
				# delete oldest files, leaving 15
				for ($x = 0; $x <= abs(15-$num_files); $x++) {
					unlink ($files[$x]);
				}
			}		

		# https://stackoverflow.com/questions/11679275/mysqldump-via-php
		$cmd1="mysqldump -u ".$DBUSER." -p'".$DBPASSWD."' ".$DATABASE." | gzip --best";
		$mime = "application/x-gzip";
		header( "Content-Type: " . $mime );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		passthru($cmd1);

	}

	function _data2csv($csvoption){
	######## Download csv table
		
		# check if csv table name is recognized
		$availabletables= array('entries','clients','contacts','invoices','annualclientfigures','annualoriginatorfigures');

		if (!in_array($csvoption,$availabletables)) {
			$Message= $csvoption." csv data not available.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect(Base_Url().'export/zt2016_export');
		}
		
		$modelname = 'trak'.$csvoption;
		$dbname = 'zowtrak'.$csvoption;
		$this->load->dbutil();
		//$this->load->model($modelname, TRUE);
		$getdb = $this->db->query("SELECT * FROM ".$dbname);
		if($getdb )
		{
			$delimiter = ",";
			$newline = "\n";
			$data = $this->dbutil->csv_from_result($getdb , $delimiter, $newline);
			$name = "ZOWTrak_".$csvoption.".csv";
			$this->load->helper('download');
			force_download($name, $data);
		}
		else {
			echo "CSV Export problem. Query without results is as follows:<br/>".$this->db->last_query();
		}

	}
	
}


/* End of file zt2016_exportdata.php */
/* Location: ./system/application/controllers/export/zt2016_exportdata.php */
?>