<?php

class Zt2016_manageclientmaterials extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients'));
		
		$this->load->model('trakcontacts', '', TRUE);
		

		//$templateVars['ZOWuser']=_superuseronly(); 
	
		$templateVars['ZOWuser']= _getCurrentUser();
		
		$templateVars['current'] =$this->input->post('ClientCode');
		
		//echo $templateVars['current'];
		
		if($templateVars['current']) {
			redirect('clients/zt2016_manageclientmaterials/'.$templateVars['current'], 'refresh');
		}
		elseif(!$templateVars['current'])
		{
			if 	($this->uri->segment(3)!=""){
				$templateVars['current'] =$this->uri->segment(3);
			}				
			else {
				redirect(Base_Url().'clients');
			} 
				
		}
		$this->load->model('trakclients', '', TRUE);
		$ClientTable  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach($ClientTable as $client)
		{
			if ($client->ClientCode==$templateVars['current'] ){
				$CurrentClient=$client;
			}
		}	
		

		$templateData['title'] = 'Manage Client Materials';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_listclientmaterials($ClientTable ,$CurrentClient,$templateVars['ZOWuser']);; 
		$templateData['ZOWuser']=_getCurrentUser();

	
		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## list clients materials ##################	
	function   _listclientmaterials ($ClientTable ,$CurrentClient,$ZOWuser)
	{
		
		$clientmaterials="";
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$clientmaterials.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$clientmaterials.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$clientmaterials.=$this->session->flashdata('SuccessMessage');
			$clientmaterials.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$clientmaterials.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$clientmaterials.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$clientmaterials.='  <span class="sr-only">Error:</span>'."\n";
			$clientmaterials.=$this->session->flashdata('ErrorMessage');
			$clientmaterials.='</div>'."\n";
		}

		
		$clientmaterials.=$this->_clientscontrol($ClientTable ,$CurrentClient,$ZOWuser);
		
		$clientmaterials.='<div id="clientmaterialsmanager" class="panel panel-default"">';
		
		$clientmaterials.='<div class="panel-heading">';	

		$clientmaterials.='Client Materials';
		
    $safeclientName=str_replace(" ", "_", $CurrentClient->CompanyName);
    $safeclientName=str_replace("&", "~", $safeclientName);
		$clientmaterials.='<a href="'.site_url().'clients/zt2016_client_info/'.$safeclientName.'" class="btn btn-warning btn-xs pull-right">Client Info</a>';
	
		$clientmaterials.='</div>';
		
		
		$clientmaterials.='<div class="panel-body">';
		
		
		//############look for client / unit materials
		//superusers
		if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal"|| $ZOWuser=="jirka.blom") {
				
			if ($CurrentClient->ClientDir!=""){
					$matsdir = dirname(dirname(dirname(__FILE__)))."/zowtempa/etc/clientmaterials/".$CurrentClient->ClientDir."/";	
					$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/".$CurrentClient->ClientDir,1);
			} else {
					$matsdir = dirname(dirname(dirname(__FILE__)))."/zowtempa/etc/clientmaterials/".$CurrentClient->ClientCode."/";	
					$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/".$CurrentClient->ClientCode,1);
			}

			//check that directory exists			
			if (is_dir($matsdir)) {

				$clientmaterials .= "<p><small>Looking at ".$matsdir."</small></p>";		
				$clientmaterials.=$this->_clientmaterialsuploadform ($CurrentClient->ClientDir,"ClientUpload",$CurrentClient->ClientCode);
			} else {
				
				$createdirname = dirname(dirname(dirname(__FILE__))).'/zowtempa/etc/clientmaterials/'.$CurrentClient->ClientCode."/".$CurrentClient->ClientDir;
				if ($CurrentClient->ClientDir!=""){
					$clientmaterials.="<a href=\"".Base_Url()."clients/zt2016_createclientmaterialsfolder/".$CurrentClient->ClientCode."/".$CurrentClient->ClientDir."\" class=\"btn btn-info btn-xs\">Create Client Materials Directory (".$CurrentClient->ClientDir.")</a>";
				}else {
					$clientmaterials.="<a href=\"".Base_Url()."clients/zt2016_createclientmaterialsfolder/".$CurrentClient->ClientCode."/".$CurrentClient->ClientCode."\" class=\"btn btn-info btn-xs\">Create Client Materials Directory (".$CurrentClient->ClientCode.")</a>";
					$this->load->model('trakclients', '', TRUE);
					$this->trakclients->UpdateEntry(array("ID"=>$CurrentClient->ID,"ClientDir"=>$CurrentClient->ClientCode));
				}
			}
		}
		//regular users
		else {
			if ($CurrentClient->ClientDir!=""){
					$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/".$CurrentClient->ClientDir,0);
			} else {
					$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/".$CurrentClient->ClientCode,0);
			}
			
		}
		$clientmaterials.='</div>';
		$clientmaterials.='</div>';	
		
		//############look for  group materials
		if($CurrentClient->GroupDir!=""){
			$clientmaterials.='<div id="groupmaterialsmanager" class="panel panel-default"">';
			
			$clientmaterials.='<div class="panel-heading">Group Materials';
			
		  	$clientmaterials.='</div>';
			
			
			$clientmaterials.='<div class="panel-body">';
		//superusers
			
			if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {

				$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/group/".$CurrentClient->GroupDir,1);
				
				//check that directory exists	
				$matsdir = dirname(dirname(dirname(__FILE__)))."/zowtempa/etc/clientmaterials/group/".$CurrentClient->GroupDir."/";	

				if (is_dir($matsdir)) {
					$clientmaterials .= "<p><small>Looking at ".$matsdir."</small></p>";		
					$clientmaterials.=$this->_clientmaterialsuploadform ("group/".$CurrentClient->GroupDir,"GroupUpload",$CurrentClient->ClientCode);
				} else {
					 $createdirname = dirname(dirname(dirname(__FILE__))).'/zowtempa/etc/clientmaterials/'.$CurrentClient->ClientCode."/".$CurrentClient->ClientDir;
					 if ($CurrentClient->ClientDir!="") {
						$clientmaterials.="<a href=\"".Base_Url()."clients/zt2016_createclientmaterialsfolder/".$CurrentClient->ClientCode."/group/".$CurrentClient->GroupDir."\" class=\"btn btn-info btn-xs\">Create Client Materials Directory (".$CurrentClient->GroupDir.")</a>";
					 }
				}
			} 
			//regular users			
			else {
				$clientmaterials.= zt2016_getClientMaterials($CurrentClient->ClientCode."/group/".$CurrentClient->GroupDir,0);
			}
		};	
		
		$clientmaterials.='</div>';
		$clientmaterials.='</div>';
		return $clientmaterials;

	
	}	


	// ################## clients control ##################	
	function   _clientscontrol($ClientTable ,$CurrentClient,$ZOWuser)
	{
		$attributes['id'] = 'clientcontrol';
		$attributes['class'] = 'form-inline bottom-buffer-20';
		$clientscontrol= form_open(site_url()."clients/zt2016_manageclientmaterials\n",$attributes);

			
			//Clients

				$options=array();
				foreach($ClientTable  as $client)
				{
				$options[$client->ClientCode]=$client->CompanyName;
				}
				asort($options);
				$options=array('all'=>"All")+$options;		
				$more = 'id="clientselector" class="selector form-control input"';			
				$selected=$CurrentClient->ClientCode;
				//$clientscontrol .=form_label('Manage client materials:','client');
				$clientscontrol .=form_dropdown('ClientCode', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit form-control"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Go',$more);
				$clientscontrol .= form_close()."\n";


		return $clientscontrol;
	
	}

	// ################## upload form ##################	
	function   _clientmaterialsuploadform ($DirName,$target,$ClientCode="")
	{
			
		//$myuploadform='<div class="container">';
		$myuploadform='<button type="button" class="btn btn-info btn-xs" data-toggle="collapse" data-target="#'.$target.'">Upload More Materials</button>';
		
		$myuploadform.='<div id="'.$target.'" class="collapse" style="margin-top:1em;">';
		
		$formattributes = array('id' => $target.'form','class' => 'form-inline' );
		$myuploadform.= form_open_multipart('clients/zt2016_uploadclientmaterials/',$formattributes);
		
		$data = array(
              'name'        => 'fileuploadname',
              'id'          => $target.'fileupload',
              'class'          => 'form-control',
              'maxlength'   => '100',
              'size'        => '25',
              'type' 		=> 'file'
            );		
		$myuploadform.=form_input($data);
		
		$myuploadform.=form_hidden('clientdir', $DirName);
		$myuploadform.=form_hidden('clientcode',$ClientCode);
	
		$more = 'id="'.$target.'submit" class="form-control" type ="submit"';			
		$myuploadform.=form_submit($target.'submit', 'Upload',$more);
		$myuploadform.= form_close()."\n";
		$myuploadform.="</div>\n";
		//$myuploadform.="</div>\n";

		//$myuploadform.="<input type=\"submit\" value=\"upload\" />\n";
		return $myuploadform;
	
	}	

		
		// ################## clients form ##################	
	function   _getclientformdetails($CurrentClient)
	{
		 $clienformdetails="<div class=\"clearfix\"></div>";
		 $clienformdetails .=_getClientForm($CurrentClient);	

		return $clienformdetails;
	
	}
	
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>