<?php

class zt2016_group_edit extends MY_Controller {

	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','zt2016_groups','general','userpermissions','zt2016_timezone'));

		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['GroupName']=strtoupper($this->uri->segment(3));
		
		if (empty($templateData['GroupName'])){
			$templateData['GroupName']=strtoupper($this->input->post('GroupName'));
 		}
		
		if (empty($templateData['GroupName'])){
			$this->session->set_flashdata('ErrorMessage','Failed to show group edit: group name was not provided.');
			redirect('groups/zt2016_groups', 'refresh');
		 } 

		$templateData['GroupClientsCount']=$this->input->post('GroupClientsCount');

		//if (empty($templateData['GroupClientCount'])) {
		//	$this->session->set_flashdata('ErrorMessage','Failed to show group edit: client count was not provided.');
		//	redirect('groups/zt2016_group_info/'.$templateData['GroupName'], 'refresh');				
		//}
	
		
		
		$templateData['title'] =$templateData['GroupName'].' Group Edit';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_group_edit_page($templateData); 

		$this->load->view('admin_temp/main_temp',$templateData); 

	}
	

	// ################## display clients info ##################	
	function  _group_edit_page($templateData)
	{
		$GroupName=$templateData['GroupName'];	
		$ZOWuser=$templateData['ZOWuser'];	

		#load group info	
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupData= $this->zt2016_groups_model->GetGroup($options = array('GroupName'=>$GroupName));		

		
		$FormValues = (array) $GroupData;
		
		$FormValues['Group']=$GroupName;
		$FormValues['ZOWuser']=$templateData['ZOWuser'];
		
		$this->session->set_flashdata('FormValues',$FormValues);

		
		#Create page
		$page_content ="<div class=\"page_content\">\n";

	
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			$page_content.='<div class="alert alert-success" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			$page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}
		
		
		######### panel header
		if ($GroupName=="DEFAULT")
		{$PanelName='DEFAULT / NONE';}
		else {$PanelName=$GroupName;}
		
		$page_content.='<div class="panel panel-success" style="margin-top:2em;"><div class="panel-heading" >'."\n"; 
		$page_content.='<h4>Edit '.$PanelName." group";
		$page_content.=" (".$templateData['GroupClientsCount']." clients)";
		$page_content.="<br/>"."\n"; 
		$page_content.="<small>DEFAULT group cannot be deleted - the other groups may be deleted if they have no clients.</small>"."\n"; 
		
		if ($GroupName=="DEFAULT")
		{
			$page_content.="<br/>"."\n"; 
			$page_content.="<small>DEFAULT group's settings default price and default currency are used for new clients that do not belong to an existing group.</small>";
		}

		
		########## Edit group button
		$page_content.= '<a href="'.site_url().'groups/zt2016_group_info/'.$GroupName.'" class="btn btn-warning btn-sm pull-right">Cancel Edit</a>'."\n";

		########## Delete group button
		if ($templateData['GroupClientsCount']==0){
			
			$formurl="groups/zt2016_group_trash";
			$attributes='id="group-trash-form"  style="display:inline; float:right;"';
			$page_content.=form_open($formurl,$attributes )."\n";
			$page_content.=form_hidden('GroupName',$FormValues['GroupName'] );
			$data = array(
			  'id' => 'GroupTrashSubmit',
			  'name' => 'GroupTrashSubmit',
			  'class' => 'btn btn-danger btn-sm btn-delete',
			  'value' => 'Delete Group', 
			  'style' => 'margin-bottom:0;',
			);
			$page_content.= form_submit($data);
			$page_content.= form_close();		
			
			
			//$page_content.= '<a href="'.site_url().'groups/zt2016_group_delete/'.$GroupName.'" class="btn btn-warning btn-sm pull-right">Delete Group</a>'."\n";
		}

		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body">'."\n";
		

		
		$page_content .=zt2016_groups_edit_form($FormValues);

		
		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

		$page_content.="</div><!--page content-->";

  		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {
  			
  		}

		return $page_content;

	}	




}

/* End of file editclient.php */
/* Location: ./system/application/controllers/groups/zt2016_group_info.php */
?>