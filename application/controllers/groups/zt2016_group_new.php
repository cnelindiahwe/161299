<?php

class zt2016_group_new extends MY_Controller {

	
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
		
		

		
		$templateData['title'] ='New Group';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_group_new_page($templateData); 

		$this->load->view('admin_temp/main_temp',$templateData); 

	}
	

	// ################## display clients info ##################	
	function  _group_new_page($templateData)
	{

		# retrieve all clients from db		
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupData = $this->zt2016_groups_model->GetGroup($options = array('GroupName'=>'DEFAULT'));		
		
		$formdata['ZOWuser']=$templateData['ZOWuser'];
		$formdata['DefaultPrice']=$GroupData->DefaultPrice;
		$formdata['DefaultCurrency']=$GroupData->DefaultCurrency;
		$formdata['DefaultPaymentDays']=$GroupData->DefaultPaymentDays;
		$formdata['DefaultCountry']=$GroupData->DefaultCountry;
		$formdata['DefaultTimeZone']=$GroupData->DefaultTimeZone;
		
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
		$page_content.='<div class="panel panel-info" ><div class="panel-heading" >'."\n"; 
		$page_content.="<h4>New group\n";

		########## Edit group button
		$page_content.= '<a href="'.site_url().'groups/zt2016_groups" class="btn btn-info btn-sm pull-right">Existing Groups</a>'."\n";
		
		
		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body"><div class="row">'."\n";

		
		$page_content .=zt2016_groups_edit_form($formdata);


		$page_content.="</div></div><!--panel body-->\n</div><!--panel-->\n";

		$page_content.="</div><!--page content-->";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {
  			
  		//}

		return $page_content;

	}	


}

/* End of file zt2016_group_new.php */
/* Location: ./system/application/controllers/groups/zt2016_group_new.php */
?>