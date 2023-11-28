<?php

class globalemailsetting extends MY_Controller {

	
	function index()
	{
        
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		


		$templateData['title'] = 'Global Setting';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _new_client_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _new_client_page($ZOWuser)
	{

		$this->load->model('globalSettingModal', '', TRUE);
		$globalData = $this->globalSettingModal->GetGlobalSetting();


		$FormData = array();

		$FormData['ID']= 1;	
		

		$TimezonesList = generate_timezone_array();

		#Create page.
			$page_content=$this->_display_page($ZOWuser,$TimezonesList,$globalData,$FormData);

		return $page_content;


	
	}
    function   _display_page ($ZOWuser,$TimezonesList,$globalData,$FormData)
	{
		$GroupData=$FormData;

		$page_content ='<div class="page_content">'."\n";

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" >'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";

		}

        ######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}
				
		########## panel head
		 //global setting form
		//$attributes='class="form-inline" id="invoice-status-form"';
		$attributes='id="client-information-form"';
		$formurl=site_url().'settings/globalsettingsave';
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-default" >'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h4>';
		
		$page_content.= "Global Email Setting";
		
		$page_content.=' </h4><div class="row p-3"><div class="col-sm-4 ">';
		
		$ndata = array('class' => 'submitButton btn btn-primary col-sm-8 mt-3 clinet_submit_button','value' => 'Save');
	
		$page_content .= form_submit($ndata)."\n";
		$page_content.= '</div></div></div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body"><div class="row" style="justify-content: center;">'."\n";
		$page_content.='<div class="row clinet p-1">'."\n";
        
        $page_content.= '<label>Send CC mail</label><textarea name="CCmail" required="true">'.$globalData[0]->CCmail.'</textarea><input type="hidden" value=1 name="ID"><input type="hidden" value=1 name="ID">';
        $page_content.= '<label>Email Body</label><textarea style="height:300px;" name="emailBody" required="true">'.$globalData[0]->emailBody.'</textarea>';
		// $page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo,$GroupData);

		$page_content.='</div></div></div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}	

	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>