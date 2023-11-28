<?php

class Databasebackup extends MY_Controller {


	function index()
	{

			$DBUSER=$this->db->username;
			$DBPASSWD=$this->db->password;
			$DATABASE=$this->db->database;
			
			//$filename = $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql.gz";
			$filename = $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql.gz";
			//$filename = $DATABASE . "-" . date("Y-m-d_H-i-s") . ".sql";
			$save_path = $_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/database_bu' ;
			
			
			$files = scandir($save_path );
			// Count number of files and store them to variable.. (-2 for . and ..)
			$num_files = count($files)-2;
			
			// If more than 3, delete oldest file
			if ($num_files >14) {

				$files = glob( $save_path.'/*.*' );
				$exclude_files = array('.', '..');
				if (!in_array($files, $exclude_files)) {
				// Sort files by modified time, latest to earliest
				// Use SORT_ASC in place of SORT_DESC for earliest to latest
				array_multisort(
				array_map( 'filemtime', $files ),
				SORT_NUMERIC,
				SORT_ASC,
				$files
				);
				}
				// delete oldest files, leaving 2
				for ($x = 0; $x <= abs(15-$num_files); $x++) {
					unlink ($files[$x]);
					//echo "deleted ".$files[$x]."<br/>";
				}
			}
		
		
				

			
			//$cmd0="mysqldump -u ".$DBUSER." -p'".$DBPASSWD."' ".$DATABASE." > /home/zowtempa/etc/database_bu/".$filename;
			//exec($cmd0);
			
			$cmd1="mysqldump -u ".$DBUSER." -p'".$DBPASSWD."' ".$DATABASE." | gzip --best";
		 	
			//https://stackoverflow.com/questions/11679275/mysqldump-via-php
			//echo $cmd;
			//echo "<br />";
			//exec("(mysqldump -u ".$DBUSER." -p".$DBPASSWD." ".$DATABASE." | gzip --best > /home/zowtempa/etc/database_bu/zowtrak.sql) 2>&1", $output, $exit_status);
		    //var_dump($exit_status); // (int) The exit status of the command (0 for success, > 0 for errors)
			//echo "<br />";
			//var_dump($output); // (array) If exit status != 0 this will handle the error message. 
		
			//die();
		
			$mime = "application/x-gzip";
			header( "Content-Type: " . $mime );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			passthru($cmd1);

	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>