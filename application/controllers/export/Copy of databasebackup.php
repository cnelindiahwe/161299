<?php

class Databasebackup extends MY_Controller {


	function index()
	{

			$DBUSER=$this->db->username;
			$DBPASSWD=$this->db->password;
			$DATABASE=$this->db->database;
			
			//$filename = $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql.gz";
			$filename = $DATABASE . "-" . date("Y-m-d") . ".sql.gz";
			$save_path = $_SERVER['NFSN_SITE_ROOT']  . 'logs/' . $filename;
			$cmd = "mysqldump -u $DBUSER --password=$DBPASSWD  --host=zowtest.db $DATABASE | gzip --best > " . $save_path;
			echo $cmd;
			exec( $cmd );
			

			/*
			 * $this->load->dbutil();
			$this->load->model('trakclients', '', TRUE);
			$getclients = $this->db->query("SELECT * FROM zowtrakclients");
			if($getclients )
			{
			unset($getclients);
				$prefs = array(
		                //'tables'      => array('table1', 'table2'),  // Array of tables to backup.
		                //'ignore'      => array(),           // List of tables to omit from the backup
		                'format'      => 'txt',             // gzip, zip, txt
		                //'filename'    => 'mybackup.sql',    // File name - NEEDED ONLY WITH ZIP FILES
		                'add_drop'    => FALSE,              // Whether to add DROP TABLE statements to backup file
		                'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
		                'newline'     => "\n"               // Newline character used in backup file
		              );
				
				$backup =$this->dbutil->backup($prefs);
				
				$name = date("Y.m.d")."ZOWTrak_db_BU.sql";
				$this->load->helper('download');
				force_download($name, $backup);
			}
			else {
				echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			}
			 */
			 
			 

	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>