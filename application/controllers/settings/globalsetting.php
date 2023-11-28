<?php

class globalsetting extends MY_Controller {

	
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
		$page_content.='<div class="row"> <div class="col-6 p-3"><h4>';
		
		$page_content.= "Global Setting";
		
		$page_content.=' </h4></div><div class="col-6 p-3 text-end">';
		
		$ndata = array('class' => 'submitButton btn btn-primary clinet_submit_button w-50','value' => 'Save');
	
		$page_content .= form_submit($ndata)."\n";
		$page_content.= '</div></div></div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body"><p>Email invoice setting</p><div class="row" style="justify-content: center;">'."\n";
		$page_content.='<div class="row clinet p-1">'."\n";

        $page_content.= '<div class="col-md-2 text-end"><label>Form Address</label></div><div class="col-md-10"><textarea class="w-100" name="fromAddress" required="true">'.$globalData[0]->fromAddress.'</textarea></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>Contact Name</label></div><div class="col-md-3 mt-3"><input type="text" name="contactName" required="true" value="'.$globalData[0]->contactName.'"></input></div>';
        $page_content.= '<div class="col-md-4 mt-3"><label class="col-4">Mobile No.</label><input class="col-8" type="text" name="mobNumber" required="true" value="'.$globalData[0]->mobNumber.'"></input></div>';
        $page_content.= '<div class="col-md-3 mt-3"><label class="col-4">Email</label><input class="col-8" type="email" name="email" required="true" value="'.$globalData[0]->email.'"></input></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>Bank / Tax info / Footer</label></div><div class="mt-3 col-md-10"><textarea class="w-100" name="bankAccount" required="true">'.$globalData[0]->bankAccount.'</textarea></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>Footer</label></div><div class="mt-3 col-md-10"><textarea class="w-100" name="footer" required="true">'.$globalData[0]->footer.'</textarea></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>Standard Subject</label></div><div class="mt-3 col-md-4"><textarea class="w-100" name="subject" required="true">'.$globalData[0]->subject.'</textarea><input type="hidden" value=1 name="ID"></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>Reminder Subject</label></div><div class="mt-3 col-md-4"><textarea class="w-100" name="reminderSubject" required="true">'.$globalData[0]->reminderSubject.'</textarea></div>';
		
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>2nd Reminder Subject</label></div><div class="mt-3 col-md-4"><textarea class="w-100" name="secondReminderSubject" required="true">'.$globalData[0]->secondReminderSubject.'</textarea><input type="hidden" value=1 name="ID"></div>';
        $page_content.= '<div class="col-md-2 text-end mt-3"><label>CC Mail To:</label></div><div class="mt-3 col-md-4"><textarea class="w-100" name="ccMailto" required="true">'.$globalData[0]->ccMailto.'</textarea></div>';
		
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Standard Email</label></div><div class="col-md-10 mt-3"><textarea class="w-100" style="height:200px;" name="StandardEmail" required="true">'.$globalData[0]->StandardEmail.'</textarea></div>';
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Reminder Email</label></div><div class="col-md-10 mt-3"><textarea class="w-100" style="height:200px;" name="ReminderEmail" >'.$globalData[0]->ReminderEmail.'</textarea></div>';
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>2nd Reminder Email</label></div><div class="col-md-10 mt-3"><textarea class="w-100" style="height:200px;" name="SecondReminderEmail">'.$globalData[0]->SecondReminderEmail.'</textarea></div>';

		//zowindia
		$page_content.='<p>Zowindia invoice setting</p>'."\n";
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Form Address</label></div><div class="col-md-10 mt-3"><textarea class="w-100" name="zowIndiafromAddress">'.$globalData[0]->zowIndiafromAddress.'</textarea></div>';
		$page_content.= '<div class="col-md-2 text-end"><label>To Address</label></div><div class="col-md-10"><textarea class="w-100 mt-3" name="toAddress">'.$globalData[0]->toAddress.'</textarea></div>';
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Bank</label></div><div class="col-md-10 mt-3"><textarea class="w-100" name="zowIndiaBank"">'.$globalData[0]->zowIndiaBank.'</textarea></div>';
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Mobile</label></div><div class="col-md-10 mt-3"><input class="w-100" type="text" name="zowIndiaMobile" value='.$globalData[0]->zowIndiaMobile.'></div>';
		$page_content.= '<div class="col-md-2 text-end mt-3"><label>Footer</label></div><div class="col-md-10 mt-3"><textarea class="w-100" type="text" name="zowIndiafooter">'.$globalData[0]->zowIndiafooter.'</textarea></div>';

		// $page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo,$GroupData);

		$page_content.='</div></div></div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}	

	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>