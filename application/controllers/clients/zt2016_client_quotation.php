<?php

class Zt2016_client_quotation extends MY_Controller {

	
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
		
		# retrieve client ID from POST data
		$SafeclientName = $this->input->post('Current_Client');
		
		# retrieve client ID from url
		if (empty($SafeclientName)){ 
			$SafeclientName=$this->uri->segment(3);
		}
		
		# exit to  clients page if no client ID is provided
			if (empty($SafeclientName)){
			
				$Message="Client name not provided. Unable to provide quotation.";
				$this->session->set_flashdata('ErrorMessage',$Message);
				redirect('clients/zt2016_clients', 'refresh');		
			}

		$clientName=str_replace("_", " ", $SafeclientName);
		$clientName=str_replace("~", "&", $clientName);		
		
		# retrieve client info
		$this->load->model('zt2016_clients_model', '', TRUE);
		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientName));
		
		
		$bug=array($ClientInfo);
		//var_dump($ClientInfo);		

		# exit to  clients page if client ID does not match an existing client
		if (!empty($ClientInfo)){
 			$ClientInfo->SafeclientName=$SafeclientName;
			//bellow gets rid of bg that loads to falshdata the data below
			echo "<b></b>";
		}
		else {
			$Message="Client not found in database. Unable to provide quotation.";
			$this->session->set_flashdata('ErrorMessage',$Message);
			redirect('clients/zt2016_clients', 'refresh');		
		}	
		
		
		
		# retrieve forma values from POST data
		$templateData['New_Slides'] = $this->input->post('New_Slides');		
		if (empty($templateData['New_Slides'] )){ $templateData['New_Slides'] =0;}
		
		$templateData['Edited_Slides'] = $this->input->post('Edited_Slides');		
		if (empty($templateData['Edited_Slides'])){ $templateData['Edited_Slides']=0;}	
		
		$templateData['Additional_Hours'] = $this->input->post('Additional_Hours');		
		if (empty($templateData['Additional_Hours'] )){$templateData['Additional_Hours'] =0;}
		
		
		
		
		
		# retrieve all clients from db		
		$ClientsTable = $this->zt2016_clients_model->GetClient();			
		
		
		$templateData['title'] = 'Client Quotation for '.$clientName;
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _client_quotation_page($templateData,$ClientInfo,$ClientsTable); 

		$this->load->view('admin_temp/main_temp',$templateData,$ClientsTable);

	}
	

	// ################## display quotation page ##################	
	function _client_quotation_page($templateData,$ClientInfo,$ClientsTable)
	{
		

		#form calculations
		
		$templateData['new_billed_hours']=number_format($templateData['New_Slides']/5,2);
		$templateData['edits_billed_hours']=number_format($templateData['Edited_Slides']/10,2);
		$templateData['additional_billed_hours']=number_format($templateData['Additional_Hours'],2);

		
		$templateData['TotalBillableHours']= number_format(($templateData['New_Slides']/5)+($templateData['Edited_Slides']/10)+$templateData['Additional_Hours'],2);
		$templateData['TotalQuotationPrice']= number_format($templateData['TotalBillableHours']*$ClientInfo->BasePrice,2);
		
		
		
		#Create page.
	


		$page_content ="";
		######### client dropdown
		$page_content.=$this->_display_clients_control($ClientsTable,$ClientInfo);	


		########## panel 
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";		
		
		########## panel head
		$page_content.='<div class="panel-heading">'."\n";
		
			########## client name, client code
			$page_content.=' <h3>';
			$page_content.= 'Quotation for '.$ClientInfo->CompanyName;
			$page_content.= ' <small id="client-price">('.$ClientInfo->BasePrice.' '.$ClientInfo->Currency.' per hour)</small>';
			$page_content.= '</h3>'."\n";		

			##########  buttons
			$page_content.= '<div>'."\n";
		
				##### Client info button
				$page_content.='<a href="'.site_url().'clients/zt2016_client_info/'.$ClientInfo->SafeclientName.'" class="btn btn-warning btn-xs " style="float: right;margin-top: -34px;">Client Info</a>';

			$page_content.= '</div><!--//buttons-->'."\n";
		
		$page_content.= '</div><!--//panel-heading-->'."\n";
		########## panel body
		$page_content.='<div class="panel-body">'."\n";
				
			$page_content.='	<div class="row">'."\n";		
		
				########## col 1	
					$page_content.='			<div class="col-sm-6">'."\n";
					########## quotation form
				  	$page_content.=$this->_quotation_form($templateData,$ClientsTable,$ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";	
		
				########## col 2	
					$page_content.='			<div class="col-sm-6">'."\n";
					########## template form
				 	$page_content.=$this->_quotation_template($templateData,$ClientInfo);
					$page_content.='			</div><!--col 4-->'."\n";			
	

	 		$page_content.='</div><!--row--> '."\n";	


		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="panel" -->'."\n";
		return $page_content;
		
	}


	// ################## quotation form ##################	
	function   _quotation_form($templateData,$ClientsTable,$ClientInfo)
	{

		
		# Create quotation form
		$QuotationForm ="\n";
			
		//$subsections = array('Name'=>'Name','ID'=>'ID','GroupName'=>'Group Name','Pricing'=>'Pricing', 'DefaultPrice'=>'Default Price','DefaultCurrency'=>'Default Currency','DefaultPaymentDays'=>'Default Payment Days','Location'=>'Location','DefaultCountry'=>'Default Country','DefaultTimeZone'=>'Default Time Zone');

			
		
		$FormURL="clients/Zt2016_client_quotation";
		
		
		$attributes='id="quotation-data-form"';
		
		$QuotationForm.=form_open($FormURL,$attributes )."\n";
		
		$QuotationForm .=form_hidden('Current_Client',$ClientInfo->CompanyName);
		
		# row 1	
		$QuotationForm.="	<div class=\"col-sm-4\">\n";
		$QuotationForm.="	   ".form_label('New Slides'.":",'New_Slides')."\n";
			$data = array(
						  'name' => 'New_Slides',
						  'id'   => 'New_Slides',
						  'class'=> 'form-control',
						  'type' => 'number',
						  'min' => '0',
						  'value' => $templateData['New_Slides'],
						  'step' => '1',
						  'required' => 'true'
						);
		$QuotationForm.="      ".form_input($data)."\n";
		$QuotationForm.="	</div>\n";
		
		$QuotationForm.="	<div class=\"col-sm-4\">\n";
		$QuotationForm.="	   ".form_label('Edited Slides'.":",'Edited_Slides')."\n";
			$data = array(
						  'name' => 'Edited_Slides',
						  'id'   => 'Edited_Slides',
						  'class'=> 'form-control',
						  'type' => 'number',
						  'min' => '0',
						  'value' => $templateData['Edited_Slides'],
						  'step' => '1',
						  'required' => 'true'
						);		
		$QuotationForm.="      ".form_input($data)."\n";
		$QuotationForm.="	</div>\n";		

		
		$QuotationForm.="	<div class=\"col-sm-4\">\n";
		$QuotationForm.="	   ".form_label('Additional Hours'.":",'Additional_Hours')."\n";
			$data = array(
						  'name' => 'Additional_Hours',
						  'id'   => 'Additional_Hours',
						  'class'=> 'form-control',
						  'type' => 'number',
						  'min' => '0',
						  'step' => '.01',
						  'value' => $templateData['Additional_Hours'],
						  'required' => 'true'
						);		
		$QuotationForm.="      ".form_input($data)."\n";
		$QuotationForm.="	</div>\n";		

		
		
		# row 2	
		$QuotationForm.="	<div id=\"new-billed-hours\" class=\"col-sm-4 col-centered \">\n";
		$QuotationForm.=$templateData['new_billed_hours']." final hours\n";
		$QuotationForm.="	</div>\n";
		
		$QuotationForm.="<div id=\"edits-billed-hours\" class=\"col-sm-4 col-centered\">\n";
		$QuotationForm.=$templateData['edits_billed_hours']." final hours\n";
		$QuotationForm.="	</div>\n";	
		
		$QuotationForm.="	<div id=\"additional-billed-hours\" class=\"col-sm-4 col-centered\">\n";
		$QuotationForm.=$templateData['additional_billed_hours']." final hours\n";
		$QuotationForm.="	</div>\n";

		# row 3	
		
		$TotalBillableHours= number_format(($templateData['New_Slides']/5)+($templateData['Edited_Slides']/10)+$templateData['Additional_Hours'],2);
		$TotalPrice= number_format($TotalBillableHours*$ClientInfo->BasePrice,2);
		
		$QuotationForm.="	<div id=\"total-billed-hours\" class=\"col-sm-12 col-centered \">\n";
		$QuotationForm.=" <h4>".$templateData['TotalBillableHours']." Total hours at ".$ClientInfo->BasePrice." ".$ClientInfo->Currency." = ".$templateData['TotalQuotationPrice']." ".$ClientInfo->Currency."</h4>\n";
		$QuotationForm.="	</div>\n";
	
		# row 4	

		$QuotationForm.="	<div  class=\"col-md-4 col-md-offset-4 col-centered\">\n";
			$data = array(
						  'name' => 'Client_Quotation_Submit',
						  'id'   => 'Client_Quotation_Submit',
						  'class'=> 'form-control',
						  'value' => 'Calculate'
						);	
		$QuotationForm.= form_submit($data);
		$QuotationForm.="	</div>\n";		
		$QuotationForm.="	<div  class=\"col-sm-4 \"></div>\n";
		

		$QuotationForm.= form_close()."\n";
			
		return $QuotationForm;
	
	}
	
	// ################## template form ##################	
	function   _quotation_template($templateData,$ClientInfo)
	{

		$TemplateText="";
		
		if ($templateData['TotalBillableHours']!=0){
			
			$TemplateTextFlag=0;
			$TemplateText="Dear,"."\n\n";
			$TemplateText.="Regarding costs, the deck has:"."\n\n";
			if ($templateData['New_Slides']!=0){
				$TemplateText.=$templateData['New_Slides']. " new or complex slides at 5 slides per hour = ".$templateData['new_billed_hours']." billable hours\n";
				$TemplateTextFlag=1;
			}		
			if ($templateData['Edited_Slides']!=0){
				if ($TemplateTextFlag==1){
					$TemplateText.="and"."\n";
				}
				$TemplateText.=$templateData['Edited_Slides']. " simple slides at 10 slides per hour = ".$templateData['edits_billed_hours']." billable hours\n";
				$TemplateTextFlag=1;
			}				
			if ($templateData['Additional_Hours']!=0){
				if ($TemplateTextFlag==1){
					$TemplateText.="and"."\n";
				}
				$TemplateText.=$templateData['Additional_Hours']. " additional hours = ".$templateData['additional_billed_hours']." billable hours\n";
				$TemplateTextFlag=1;
			}	
			
			$TemplateText.="\n";
			
			$TemplateText.="The total is ".$templateData['TotalBillableHours']." hours at ".number_format($ClientInfo->BasePrice,2)." ".$ClientInfo->Currency." = ".$templateData['TotalQuotationPrice']." ".$ClientInfo->Currency."\n\n";
			
			$TemplateText.="Kindly indicate whether we should go ahead.";
			}		
		
			$QuotationTemplate="";
			$QuotationTemplate.="	<div class=\"col-sm-12\">\n";
			$QuotationTemplate.="	   ".form_label('Message Box'.":",'Message')."\n";
				$data = array(
							  'id'   => 'quotation-template',
							  'name'   => 'quotation-template',
							  'class'=> 'form-control',
							  'rows' => 13,
							  'value' => $TemplateText,
							);
			$QuotationTemplate.="      ".form_textarea($data)."\n";
			$QuotationTemplate.="	</div>\n";


			return $QuotationTemplate;
			

	
	}

// ################## clients control ##################	
	function   _display_clients_control($ClientsTable,$ClientInfo)
	{
		
		#top client dropdown
		$FormInfo['FormURL']="clients/zt2016_client_quotation";
		$FormInfo['labeltext']= 'Company';
		$FormInfo['id'] = 'client_dropdown_form';
		$FormInfo['class'] = 'form-inline';
	
		$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";

		return $clients_top_dropdown;
	
	}

	
	
}
/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/zt2016_client_quotation.php */
?>