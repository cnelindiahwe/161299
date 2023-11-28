<?php

class Zt2016_view_invoice extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_invoice','form'));
		
		$zowuser=_superuseronly(); 

		$invoicenumber=$this->uri->segment(3);



		 if (empty ($invoicenumber)) {
			redirect('invoicing/zt2016_existing_invoices', 'refresh');
		 }

		$templateData['title'] = 'View Invoice '.$invoicenumber;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _getinvoiceContent($invoicenumber); 

		$templateData['ZOWuser']=_getCurrentUser();

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    // ################## Retrieves invoice data . ##################	
	function  _getinvoiceContent($invoicenumber)
	{
		# retrieve invoice from db
		$this->load->model('zt2016_invoices_model','','TRUE');
		$invoiceTotals=$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','InvoiceNumber'=>$invoicenumber,));

		$Emailinvoice=$this->zt2016_invoices_model->GetEmailData($options = array('pdf'=>$invoicenumber));
		$discountentry=$this->zt2016_invoices_model->GetdiscountEntry($options = array('Invoice'=>$invoicenumber));

		     # redirect if invoice is not found in DB
			 if (empty ($invoiceTotals)) {
				 
				 if ($invoicenumber!='favicon.ico') { // prevents incorrect setting  of error message on exisiting invoices
					 $Message='Invoice Number '. $invoicenumber." not found in DB.";	
					 $this->session->set_flashdata('ErrorMessage',$Message);
				 	redirect('invoicing/zt2016_existing_invoices/fast', 'refresh');
			 	}
			 }


		# retrieve client from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$clientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $invoiceTotals->Client));

		# retrieve client contacts from db		
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$contactInfoTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $invoiceTotals->Client));
		
		$RawOriginators= explode(",", $invoiceTotals->Originators);
		
		
		$Originators=array_map('trim',$RawOriginators);
		sort($Originators);

		foreach ($Originators as $Originator){

			$Originator=trim($Originator);
			foreach ($contactInfoTable  as $Contact){ 
				$ContactFullName=trim($Contact->FirstName." ".$Contact->LastName);
				
				//echo "#".$Originator."#<br/>#".$ContactFullName."#";
				if ($Originator==$ContactFullName){
					
					$Contact->ContactFullName=$ContactFullName;
					$OriginatorTable[]=$Contact;
					break;	
				}

			}
			//die ("#".$Contact->ContactFullName."#");
		}
		$zowuser =_getCurrentUser();
		
		# call helper
		$pageOutput = $this->zt2016_display_existing_invoice($invoiceTotals,$clientInfo,$OriginatorTable,$Emailinvoice,$zowuser,$discountentry);
		
		return $pageOutput;
	}


    // ################## Generates invoice content . ##################	
	
	function  zt2016_display_existing_invoice($invoiceTotals,$clientInfo,$OriginatorTable,$Emailinvoice,$zowuser,$discountentry)
	{
			
		$pageOutput="";	
	
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
	
		$invoicePanelInfo = zt2016_invoice_paneltype ($invoiceTotals); 
		
		$pageOutput.='<div class="panel '.$invoicePanelInfo->PanelType.'">'."\n";
		$pageOutput.='<div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Invoice '.$invoiceTotals->InvoiceNumber.' for ';

		$safeclientName= str_replace(' ', '_', $invoiceTotals->Client);
		$safeclientName= str_replace('&', '~', $safeclientName);
		
		$pageOutput.='<a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$invoiceTotals->Client.'</a> ('.$invoicePanelInfo->Status.')</h3>'."\n";
		
		
		######### buttons
		$pageOutput.= "<p class='top-buffer-10'>";
		
		
		########## Invoices button
		$pageOutput.='<a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'" class="btn btn-info btn-xs ">Invoices</a>';

		########## Client info button
		$pageOutput.='<a href="'.site_url().'clients/zt2016_client_info/'.$safeclientName.'" class="btn btn-warning btn-xs ">Client Info</a>';
		
		// send email to client
		// $fromAddress = 'deepakcnel@gmail.com'; // Replace with your desired "From" address
		// $message = "Dear " . $clientInfo->ClientContact . ",\n\n";
		// $message .= "Please find attached an invoice for recent support.\n";
		// $message .= "For your convenience, this invoice may be settled via the following URL:\n";
		// $message .= $invoiceTotals->MolliePaymentUrl . "\n\n";
		// $message .= "Thank you for your business!\n\n";
		// $message .= "Cordially,\n\n";
		// $message .= "Jirka Blom - Art Director at Zebra on Wheels BV";
		// $attachmentPath = 'https://zowtrak.com/web/assets/usersprofile/invoice-logo.jpg';
		// $mailtoLink = 'mailto:recipient@example.com,recipient2@example.com?subject=Email%20Subject&attach='.$attachmentPath.'&body=' . urlencode($message);

		$pageOutput.='<a href="'.site_url().'invoicing/zt2016_invoicesendmail/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-success btn-xs ">Send Mail</a>';
		
		$pageOutput.= "</p>"; //buttons		
		
		
		
		$pageOutput.="</div><!--panel-heading-->\n";
		$pageOutput.='<div class="panel-body">'."\n";
		
		
 		################# Tab navigation	
 		
 		
 		$pageOutput.='<ul class="nav nav-tabs " role="tablist" >'."\n";
		$pageOutput.='<li role="presentation" class="active"><a href="#invoice-main" aria-controls="invoice-main" role="tab" data-toggle="tab">Invoice</a></li>'."\n";
		
			
			### Notes count
		
			$notesText ="Notes";
			$notesCount =0;
			
			if ($invoiceTotals->BillingNotes!=""){
				$notesCount++; #= $notesCount++;				
			} 
			
			if ($clientInfo->BillingGuidelines!="") {
				$notesCount++; #= $notesCount++;
			}
		

			foreach ($OriginatorTable as $Originator){
				if (!empty($Originator->ContactBillingGuidelines)){
					$notesCount++;
				}

			}

			if ($notesCount > 0) {
				$notesText.=' <span class="badge badge-primary">'.$notesCount.'</span>';
			}

			
		
		$pageOutput.='<li role="presentation"><a href="#invoice-notes" aria-controls="invoice-notes" role="tab" data-toggle="tab">'.$notesText.'</a></li>'."\n";
		$pageOutput.='</ul>'."\n";

		
		################# Tab panes 
		
		$pageOutput.='<div class="tab-content">'."\n";
  		$pageOutput.='	<div role="tabpanel" class="tab-pane active" id="invoice-main">'."\n";

		
 		$pageOutput.='	<div class="row" style="padding:.5em 0 1.5em;">'."\n";		
		$pageOutput.='		<div class="col-md-4">'."\n";
		
 		###### Invoice status form
		$attributes='class="form-inline" id="invoice-status-form"';
		$pageOutput.=form_open(site_url().'invoicing/zt2016_invoice_status',$attributes )."\n";
 		$pageOutput.='				<div class="form-group">'."\n";
      	$pageOutput.='					<div class="input-group input-group-sm pb-4">'."\n";
      	$pageOutput.='						<span class="input-group-addon" id="basic-addon1">Status</span>'."\n";
		if ($invoiceTotals->Status=='PAID') {
			$options = array('BILLED'=>'Billed', 'PAID'=>'Paid');
		}
		else if ($invoiceTotals->Status=='WAIVED') {
			$options = array('BILLED'=>'Billed', 'WAIVED'=>'Waived');
		}
		else if ($invoiceTotals->Status=='MARKETING') {
			$options = array('BILLED'=>'Billed', 'MARKETING'=>'Marketing');
		}
		else if ($invoiceTotals->Status=='DISPUTED') {
			$options = array('BILLED'=>'Billed', 'DISPUTED'=>'Disputed',);
		}		
		else { //BILLED
			$options = array('CANCEL'=>'Cancel','BILLED'=>'Billed', 'PAID'=>'Paid', 'MARKETING'=>'Marketing','WAIVED'=>'Waived','DISPUTED'=>'Disputed','Partially Paid'=>'Partially Paid');
		}
		$more = 'id="InvoiceStatus" class="Status form-control" aria-describedby="basic-addon1"';	
		$pageOutput .=form_dropdown('Status', $options,$invoiceTotals->Status,$more)."\n";
		
 		$pageOutput.='					</div>'."\n";
		
		//$dateplaceholder=date("d M Y");
	 	$dateplaceholder=date("d M Y",strtotime($invoiceTotals->BilledDate)); 
 		if ($invoiceTotals->Status=='BILLED' || $invoiceTotals->Status=='Partially Paid') {


			$pageOutput.='<div class="input-append date" id="dp3" data-date="'.$dateplaceholder.'" data-date-format="dd mm yyyy" style="display:inline;">'."\n";
	      	$pageOutput.='<input type="text" class="form-control datepicker" value="'.$dateplaceholder.'" id="InvoiceDate" name="InvoiceDate" >'."\n";
			$pageOutput.='</div>'."\n";
		}  else{
			$pageOutput.=form_hidden('InvoiceDate',$dateplaceholder);
		}		
		$pageOutput.=form_hidden('Invoice',$invoiceTotals->InvoiceNumber);
		$pageOutput.=form_hidden('Client',$invoiceTotals->Client);
		$pageOutput.=form_hidden('DueDays',$clientInfo->PaymentDueDate);

		if ($invoiceTotals->Status=='Partially Paid') {
			$pageOutput.='<input class="form-control" type="text" name="paidAmount" id="paidamount" placeholder="Enter Amount" value="'.$invoiceTotals->paidAmount.'">';
		}else{
			$pageOutput.='<input class="form-control" type="text" name="paidAmount" id="paidamount" placeholder="Enter Amount" style="display:none;">';
		}

		
		
 		$pageOutput.='					<button type="submit" class="btn btn-sm btn-success m-2">Change</button>'."\n";
 		$pageOutput.='				</div>'."\n";
 		$pageOutput.='			</form>'."\n";
	 	$pageOutput.='		</div>'."\n";
		
		#discont field
		$pageOutput.='		<div class="col-md-4">'."\n";
		$attributes='class="form-inline" id="discount-status-form"';
		$pageOutput.=form_open(site_url().'invoicing/zt2016_invoice_discount',$attributes )."\n";
		$pageOutput.=form_hidden('Invoice',$invoiceTotals->InvoiceNumber);
		$pageOutput.=form_hidden('zowuser',$zowuser);
		$pageOutput.=form_hidden('currency',$clientInfo->Currency);
		$pageOutput.='				<div class="form-group">'."\n";
		$pageOutput.='					<div class="input-group input-group-sm pb-4">'."\n";
		$pageOutput.='						<span class="input-group-addon" id="basic-addon1">Discount</span>'."\n";
		$pageOutput.=($invoiceTotals->discount == 0)?'<input class="form-control" id="basic-addon1" name="discount">'."\n":'<input class="form-control" id="basic-addon1" name="discount" value="'.$invoiceTotals->discount.'">'."\n";
		// $pageOutput.='						<input class="form-control" id="basic-addon1" name="discount" value="'.$invoiceTotals->discount.'">'."\n";
 		$pageOutput.='				</div>'."\n";

			if($invoiceTotals->discount == 0){
				$pageOutput.='<button type="submit" class="btn btn-sm btn-success m-2" name="add_dis">Add discount</button>'."\n";
			}
			else{
				$pageOutput.='<button type="submit" class="btn btn-sm btn-success m-2" name="add_dis">Update discount</button>'."\n";
				$pageOutput.='<button type="submit" class="btn btn-sm btn-success m-2" name="delete_dis">Remove discount</button>'."\n";
			}
		
 		$pageOutput.='			</div></form></div>'."\n";




		

		$pageOutput.='		<div class="col-md-4 btn-toolbar">'."\n";
		
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_invoice_ogone_form/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-default btn-sm pull-right" style="height: fit-content;">Ogone</a>'."\n";

		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_invoice_mollie_form/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-info btn-sm pull-right" style="height: fit-content;">Mollie</a>'."\n";		
		
		
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_csv_existing_invoice/'.$safeclientName.'/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-sm btn-primary pull-right" style="height: fit-content;">Export</a>'."\n";
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_pdf_existing_invoice/'.$safeclientName.'/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-sm btn-primary pull-right" style="height: fit-content;"  target="_blank">PDF</a>'."\n";
		$pageOutput.='		</div>'."\n";		
  		$pageOutput.='	</div>'."\n";		

 		############ Data row
 		$pageOutput.='	<div class="row">'."\n";	
 		
 		########## CASH TOTALS 
		$pageOutput.='<div class="col-sm-4">'."\n";
				
		$pageOutput.='<ul class="list-group totals-column">'."\n";

		$discount = 0;
		if($invoiceTotals->discount){
			$discount = $invoiceTotals->discount;
			$invoiceTotals->InvoiceTotal = $invoiceTotals->InvoiceTotal - $discount;

		}
		// if($discount > 0){
		// 	$pageOutput.='	<li class="list-group-item">'."\n";
		// 	$pageOutput.='	Subtotal after discount'."\n";
		// 	$pageOutput.='	<span class="badge badge-info">'.number_format($invoiceTotals->InvoiceTotal, 2, '.', '' ) .'</span>'."\n";
		// 	$pageOutput.='	</li>'."\n";
		// }
		
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Total'."\n";
		//$pageOutput.='	<span class="badge badge-success">'. round($invoiceTotals->InvoiceTotal.'</span> '.$clientInfo->Currency."\n";
		$pageOutput.='	<span class="badge badge-success">';
		
		$pageOutput.=number_format($invoiceTotals->InvoiceTotal, 2);
		
		if (strtolower($clientInfo -> Country)=="the netherlands" || strtolower($clientInfo -> Country)=="netherlands" ) {
			$vatTotal = number_format((($invoiceTotals->InvoiceTotal*.21)+ $invoiceTotals->InvoiceTotal),2);
			$pageOutput.=' ( '.$vatTotal.' )';
		}
		$pageOutput.= '</span> '.$clientInfo->Currency."\n";
		if (strtolower($clientInfo -> Country)=="the netherlands" || strtolower($clientInfo -> Country)=="netherlands" ) {
			$pageOutput.=' (inc. 21% VAT)';
		}
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Price per hour'."\n";
		$pageOutput.='	<span class="badge badge-info">'.number_format($invoiceTotals->PricePerHour, 2, '.', '' ) .'</span>'.$clientInfo->Currency."\n";
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Billed hours'."\n";
		$pageOutput.='	<span class="badge badge-warning">'.$invoiceTotals->BilledHours.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";
		$pageOutput.='</ul>'."\n";
		$pageOutput.='</div><!--col-->'."\n";

 		######### DATES 

 					
		$pageOutput.='<div class="col-sm-4">'."\n";

		$pageOutput.='	<ul class="list-group">'."\n";
		
		$pageOutput.='		<li class="list-group-item">'."\n";
		$pageOutput.='		Billed date'."\n";
		$pageOutput.='		<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->BilledDate)).'</span> '."\n";
		$pageOutput.='		</li>'."\n";

		$pageOutput.='		<li class="list-group-item">'."\n";
		$pageOutput.='		Payment period (days)'."\n";
		$pageOutput.='		<span class="badge badge-primary">'.$clientInfo->PaymentDueDate.'</span> '."\n";
		$pageOutput.='		</li>'."\n";

		$pageOutput.='		<li class="list-group-item">'."\n";
		if ($invoicePanelInfo->Status=="PAID") {
			$pageOutput.='		Paid date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
		} else if ($invoiceTotals->Status=="BILLED") {
			$pageOutput.='		Due date'."\n";

			if ($invoicePanelInfo->Status=="BILLED - OVERDUE") {
				$pageOutput.='		<span class="badge badge-danger">';
			} ELSE {
				$pageOutput.='		<span class="badge badge-warning">';
			}
			$pageOutput.=date('d M Y',strtotime($invoiceTotals->DueDate)).'</span>' ."\n";		
		}	 else if ($invoiceTotals->Status=="WAIVED") {
			$pageOutput.='		Waived date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
		
		}	 else if ($invoiceTotals->Status=="MARKETING") {
			$pageOutput.='		Closed date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
			
		}	 else if ($invoiceTotals->Status=="DISPUTED") {
			$pageOutput.='		Disputed date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
			
		}	
		$pageOutput.='		</li>'."\n";
		$pageOutput.='	</ul>'."\n";
		$pageOutput.='</div><!--col-->'."\n";

 		##########  VOLUME TOTALS 

		
		$pageOutput.='<div class="col-sm-4">'."\n";
		
		$pageOutput.='<ul class="list-group">'."\n";
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	New Slides'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumNewSlides.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Edited Slides'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumEditedSlides.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";
		
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Hours'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumHours.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";

		
		$pageOutput.='</ul>'."\n";
 		$pageOutput.='</div><!--col-->'."\n";		
 		$pageOutput.='</div><!--row--> '."\n";		



 		###########  Originators row
 		$pageOutput.='	<div class="well">';	


		$pageOutput.= "Originators:";
		$pageOutput.= "<pre class='pre-scrollable' id='originators_list'>";
		$FinalOriginators="";
		foreach ($OriginatorTable as $Originator){
			$FinalOriginators.= $Originator->ContactFullName.", ";	
		}
		$pageOutput.=substr($FinalOriginators,0,-2);
		
 		$pageOutput.='</pre>'."\n";
 		$pageOutput.='</div><!--well--> '."\n";		
 		
 		############  Invoice data
		$pageOutput.= zt2016_getInvoiceTableByNumber($invoiceTotals->InvoiceNumber,$clientInfo);

		$pageOutput.= zt2016_getEmailTableByNumber($Emailinvoice);
		
		$pageOutput.= zt2016_getDisountEntry($discountentry);
		

		$pageOutput.="</div><!--tabpanel invoice-main-->\n";
 		
		
		#################   Tab 2 - Invoice notes 
		
  		$pageOutput.='	<div role="tabpanel" class="tab-pane" id="invoice-notes">'."\n";

			##########  Notes row
			$pageOutput.='	<div class="row" style="padding:1.5em 0;">'."\n";	
				
			##########  Invoice notes
				$pageOutput.='		<div class="col-md-4">'."\n";

				$attributes='id="invoice-billing-notes-form"';
				$formurl=site_url().'invoicing/zt2016_invoice_billing_notes_update/';
				$pageOutput.=form_open($formurl,$attributes )."\n";
				$pageOutput .=form_hidden('InvoiceNumber', $invoiceTotals->InvoiceNumber);
			 	$pageOutput .= "			".form_label("Invoice Billing Notes")."\n";
				$pageOutput .= "			".form_textarea('InvoiceBillingNotes',$invoiceTotals->BillingNotes,'id="InvoiceBillingNotes" class="form-control" style="min-width: 100%"')."\n";
				$ndata = array('class' => 'Notes-Submit-Button btn btn-sm','value' => 'Update Invoice Notes');
				$pageOutput .= "<p>".form_submit($ndata)."</p>\n";	
				$pageOutput.=form_close("\n");
				
	

	 			$pageOutput.='		</div><!--col-->'."\n";		
	
			######### Contact notes		
			$pageOutput.='		<div class="col-md-4">'."\n";
			
				foreach ($OriginatorTable as $Originator){

					$OriginatorFullName=$Originator->FirstName.' '.$Originator->LastName;
					if (count($OriginatorTable)<6){
						
							$attributes='class="contact-billing-guidelines-form"';
							$formurl=site_url().'contacts/zt2016_contact_billing_info_update/';
							$pageOutput.=form_open($formurl,$attributes )."\n";
							$pageOutput .=form_hidden('ID', $Originator->ID);
							$pageOutput .=form_hidden('InvoiceNumber', $invoiceTotals->InvoiceNumber);
							$pageOutput .= "			".form_label($OriginatorFullName." Billing Guidelines")."\n";
							$pageOutput .= "			".form_textarea('ContactBillingGuidelines',$Originator->ContactBillingGuidelines,'class="ClientBillingGuidelines form-control" style="min-width: 100%"')."\n";
							$ndata = array('class' => 'Notes-Submit-Button btn btn-sm','value' => 'Update Contact Guidelines');
							$pageOutput .= "<p>".form_submit($ndata)."</p>\n";	
							$pageOutput.=form_close("\n");
						
					} else{
						#if more than 5 originators, list names linked to contact info page
						$pageOutput.='<a href="'.site_url().'contacts/zt2016_contact_info/'.$Originator->ID.'">'.$OriginatorFullName;
						if (!empty($Originator->ContactBillingGuidelines))
						{
							$pageOutput.=" **";
						}
						$pageOutput.="</a><br/>\n";
					}
				}

		
					
	 		$pageOutput.='		</div><!--col-->'."\n";		
	
			######### Client notes		
			$pageOutput.='		<div class="col-md-4">'."\n";
			
				$attributes='id="client-billing-guidelines-form"';
				$formurl=site_url().'clients/zt2016_client_billing_info_update/';
				$pageOutput.=form_open($formurl,$attributes )."\n";
				$pageOutput .=form_hidden('ID', $clientInfo->ID);
				$pageOutput .=form_hidden('InvoiceNumber', $invoiceTotals->InvoiceNumber);
			 	$pageOutput .= "			".form_label("Client Billing Guidelines")."\n";
				$pageOutput .= "			".form_textarea('BillingGuidelines',$clientInfo->BillingGuidelines,'id="ClientBillingGuidelines" class="form-control" style="min-width: 100%"')."\n";
				$ndata = array('class' => 'Notes-Submit-Button btn btn-sm','value' => 'Update Client Guidelines');
				$pageOutput .= "<p>".form_submit($ndata)."</p>\n";	
				$pageOutput.=form_close("\n");

	 		$pageOutput.='		</div><!--col-->'."\n";		
			
	 		$pageOutput.='	</div><!--row--> '."\n";	
		#################  end Tab 2 - Invoice notes 

  		$pageOutput.="</div><!--tabpanel invoice-notes-->\n";
  		$pageOutput.="</div><!--tab content-->\n";
 				

		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		//

		 
		//$pageOutput ="test"; 
		return $pageOutput;/**/

	}


}

/* End of file Zt2016_view_invoice */
/* Location: ./system/application/controllers/invoicing/Zt2016_view_invoice.php */
?>