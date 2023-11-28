<?php

class Zt2016_export extends MY_Controller {


	function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 


		$this->load->helper(array('userpermissions','form','url'));

		$zowuser=_superuseronly(); 
		$templateData['title'] = 'Export';
		$templateData['ZOWuser']=_getCurrentUser();		
		$templateData['sidebar_content']='sidebar';
		
		$templateData['main_content'] =$this-> _displaypage(); 		
		
		$this->load->view('admin_temp/main_temp',$templateData);	


	}
	
	// ################## top ##################	

	function  _displaypage()

	{

		$pageOutput='';
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$pageOutput.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('SuccessMessage');
			$pageOutput.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$pageOutput.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$pageOutput.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('ErrorMessage');
			$pageOutput.='</div>'."\n";
		}		
		
		############## panel header	
		$pageOutput.='<div class="panel panel-primary"><div class="panel-heading">'."\n"; 
		
		
		$pageOutput.='<h4>Export</h4>'."\n";
		
		
		$pageOutput.="</div><!--panel-heading-->\n"."\n\n";

		############## panel body	
		$pageOutput.='<div class="panel-body">'."\n";
			
			$pageOutput .="<div class='row'>";
			$pageOutput .="	<div class='col col-md' style=\"margin:0 1em;\">";
			
			$pageOutput .= "		<p>CSV Excel-compatible tables:</p>"."\n";
			$pageOutput .= "		<p>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/entries\" class=\"btn btn-info\">Entries</a>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/clients\" class=\"btn btn-info\">Clients</a>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/contacts\" class=\"btn btn-info\">Contacts</a>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/invoices\" class=\"btn btn-info\">Invoices</a>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/annualclientfigures\" class=\"btn btn-info\">Annual Client Figures</a>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/data2csv/annualoriginatorfigures\" class=\"btn btn-info\">Annual Originator Figures</a>"."\n";
			$pageOutput .= "		</p>"."\n";

			$pageOutput .= "<hr />"."\n";
			
			$pageOutput .= "		<p style=\"margin-top:1.5em;\">SQL:</p>"."\n";
			$pageOutput .= "		<p id=\"backupbuttons\">"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/databasebackup\" class=\"btn btn-danger\">DB BackUp</a>"."\n";
			$pageOutput .= "		</p>"."\n";
			$pageOutput .= $this->_displayautodbbackup();
			

			$pageOutput .= "<hr />"."\n";

		    $pageOutput .= "		<p style=\"margin-top:1.5em;\">Other:</p>";
			$pageOutput .= "		<p>"."\n";
			$pageOutput .= "		<a href=\"".site_url()."export/zt2016_exportdata/phpinfo\" class=\"btn btn-success\" target=\"_blank\">PHP Info <span class=\"glyphicon glyphicon-new-window\" aria-hidden=\"true\"></span></a>"."\n";		
			$pageOutput .= "		</p>"."\n";

			$pageOutput .="	</div> <!-- col -->"."\n";
			$pageOutput .="</div> <!-- row -->"."\n";
		
		
		#### end panel		
		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		return $pageOutput;/**/		

	}

	function _displayautodbbackup()
	######## list existing automatic db backup files	
	{
		$dir=$_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/database_bu' ;
		$pageOutput = "";
		
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file!='.' && $file!='..') {
						$filelist[]= $file;
				}
			}
			closedir($dh);
			
			# sort files
			rsort ($filelist);
			$pageOutput .= "<div id=\"backupfileslist\">"."\n";
			$pageOutput .= "<p>Existing automatic back-up files:</p>"."\n";
			$pageOutput .= "<ul class=\"list-group\">"."\n";
			for ($x = 0; $x < count($filelist); $x++ ){
				$pageOutput .= "<li class=\"list-group-item\">"."\n";
				$pageOutput .= "<a href=\"".base_url()."export/zt2016_downloaddbbackup/".$filelist[$x]."\">"."\n";
				$pageOutput .= $filelist[$x];
				$pageOutput .= "</a>"."\n";
				$pageOutput .= "</li>"."\n";
			}
			$pageOutput .= "</ul>"."\n";
			$pageOutput .= "</div>"."\n";
		}
		else {$pageOutput.="<p>No automatic db backup files found</p>"."\n";}
		
		return $pageOutput;
		
	}

	// ################## Trashed entry list ##################	
	/* function  _getEntriesDump()
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array( 'sortBy'=> 'id','sortDirection'=> 'desc'));
		
		if($getentries)
		{

			$pageOutput="";
			//$pageOutput.= $this->db->last_query();
			
			//Get header names
			$headers =$getentries[0] ;
			$dbfields= get_object_vars($headers);
			$pageOutput .= "<table id=\"entriesdump\">\n";
			$pageOutput .= "<thead>\n";
			$pageOutput .= "<tr>\n";
			foreach ($dbfields as $key=>$value)
				{
				   $pageOutput .= "<th class=\"".$key."\">".$key."</th>";
				} 
			$pageOutput .= "</tr>\n";
			$pageOutput .= "</thead>\n";


			
			$pageOutput .= "<tbody>\n";
			foreach($getentries as $row)
			{
				$pageOutput .= "<tr>";
				$dbfields= get_object_vars($row);
				//$pageOutput .= "<td class=\"".$key."\">".$key."</td>";
				foreach ($dbfields as $key=>$value)
					{
					   if  ($key=="DateIn" OR $key=="DateOut" ){
					   		$mysqldate = date( 'd/M/Y',strtotime($value));
							$pageOutput .= "<td class=\"".$key."\">".$mysqldate."</td>";
					   }
					   else {
					   
					   $pageOutput .= "<td class=\"".$key."\">".$value."</td>";
					  }
					} 
				$pageOutput .= "</tr>\n";
			}
			$pageOutput .= "</tbody>\n";
			$pageOutput .= "</table>\n";

		}
		else
		{
			$pageOutput = "No trashed jobs";
		}
		return $pageOutput;
	}
	// ################## Trashed Client list ##################	
	function  _getClientsDump()
	{		$this->load->model('trakclients', '', TRUE);
		
		$getentries = $this->trakclients->GetEntry($options = array( 'sortBy'=> 'ID','sortDirection'=> 'desc'));
		
		if($getentries)
		{

			
			$headers =$getentries[0] ;
			$dbfields= get_object_vars($headers);
			$pageOutput= "";
			$pageOutput.= "<table id=\"clientsdump\">\n";
			$pageOutput .= "<thead>\n";
			$pageOutput .= "<tr>\n";
			foreach ($dbfields as $key=>$value)
				{
				   $pageOutput .= "<th class=\"".$key."\">".$key."</th>";
				} 
			$pageOutput .= "</tr>\n";
			$pageOutput .= "</thead>\n";
			
			$pageOutput .= "<tbody>\n";
			foreach($getentries as $row)
			{
				$pageOutput .= "<tr>";
				$dbfields= get_object_vars($row);
				//$pageOutput .= "<td class=\"".$key."\">".$key."</td>";
				foreach ($dbfields as $key=>$value)
					{
					   $pageOutput .= "<td class=\"".$key."\">".$value."</td>";
					} 
				$pageOutput .= "</tr>\n";
			}
			$pageOutput .= "</tbody>\n";
			$pageOutput .= "</table>\n";

		}
		else
		{
			$pageOutput = "No trashed clients";
		}
		return $pageOutput;
	}
	*/




}

/* End of file zt2016_export.php */
/* Location: ./system/application/controllers/export/zt2016_export */
?>