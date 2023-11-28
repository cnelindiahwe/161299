<?php

class Zt2016_limbodir extends MY_Controller {
	
	function index()
	{
		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('zowtrakui','url','zt2016_limbo','userpermissions','form'));

		//check that user is not going back to base dir	
		if (uri_string()=="limbo/zt2016_limbo/" ){
			redirect(base_url()."limbo/zt2016_limbo/");
		} 	
		// ###################### retrieve directory
		$templateData['limbo_dir'] = str_replace("limbo/zt2016_limbodir/", "", uri_string());
		$templateData['limbo_dir'] = str_replace("%20", " ", $templateData['limbo_dir']);
		if ($templateData['limbo_dir']=="") {
			redirect(base_url()."limbo/zt2016_limbo/");
		}	

		// ###################### build page
		$templateData['title'] = 'Limbo';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';

		
		$templateData['main_content'] =$this-> _display_limbo_page($templateData); 		
			
		$this->load->view('admin_temp/main_temp',$templateData);	
		
	}
	
		// ################## display page ##################	
		function  _display_limbo_page($templateData)
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
			$pageOutput.='<div class="panel panel-default"><div class="panel-heading">'."\n"; 


			$pageOutput.='<h4 class="col-lg-12">Limbo</h4>'."\n";

			$pageOutput.=limboToolset($templateData['limbo_dir']);


			$pageOutput.="</div><!--panel-heading-->\n"."\n\n";

			############## panel body	
			$pageOutput.='<div class="panel-body">'."\n";

			$pageOutput.=$this-> _getdircontent($templateData['limbo_dir']);

			#### end panel		
			$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
			$pageOutdput ='
			<div class="content container-fluid">
			<div class="row">
			<div class="col-sm-12">
			';
							   $pageOutdput .=  $this-> _getdircontent($templateData['limbo_dir']);
							   $pageOutdput .=  '</div></div></div></div>
				  
				   ';
			return $pageOutdput;/**/		

		}
	
	function _getdircontent($dir)
	{

		$filelist=array();
		$dirlist=array();
		$finallist=array();
		$finalshortdir=$dir;
		$dir = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$dir;
		// Open a known directory, and proceed to read its contents

		if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if ($file!='.' && $file!='..') {
							if(is_dir($dir.'/'.$file)){
								$dirlist[]= $file;
								} else {								
								$filelist[]= $file;
							}
						}
					}
					closedir($dh);

					# sort and add dir and file names arrays
					natcasesort ($dirlist);
					natcasesort ($filelist);

					foreach ($dirlist as $xfile){
						$finallist[]=$xfile;
					}
					foreach ($filelist as $xfile){
						$finallist[]=$xfile;
					}						


				}
				else {echo "No files found".$clientcode." ".$dir;}
			}

		return get_list_of_folder($finallist,$finalshortdir);
	
	}
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>