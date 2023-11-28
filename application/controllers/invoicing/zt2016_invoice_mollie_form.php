<?php

class Zt2016_invoice_mollie_form extends MY_Controller {

	
	public function index()
	{

		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->library('session'); //flashdata
		$this->load->helper(array( 'userpermissions','url','zt2016_invoice','form','security'));

		
		$zowuser=_superuseronly(); 


		#Determine whether there is flashdata related to invoice
		#(this means that it is a redirect zt2016_finalize_mollie_url)
		
		$invoiceinfo = new \stdClass();
		
		if($this->session->flashdata('invoiceinfo'))
		
		{

			$forminvoiceinfo = new \stdClass();
			$forminvoiceinfo=(object)$this->session->flashdata('invoiceinfo'); 	
			
			 if (!isset($forminvoiceinfo->InvoiceNumber)) {
				 die ('Flashdata does not include invoice number');
			 } else{
 				 $invoiceinfo->InvoiceNumber=$forminvoiceinfo->InvoiceNumber;
			 }
			
		}
		else 
		# URL
		{
			# retrieve invoice number from URL			
			$invoiceinfo->InvoiceNumber=$this->uri->segment(3);
			 if (empty ($invoiceinfo->InvoiceNumber)) {
				 //redirect('invoicing/zt2016_pending_invoices', 'refresh');
				 die ('No invoice number');
			 }
		}
		
		# retrieve invoice data from db
		$this->load->model('zt2016_invoices_model','','TRUE');
		$invoiceinfo=$this->zt2016_invoices_model->GetInvoice($options = array('Trash' => '0', 'InvoiceNumber' => $invoiceinfo->InvoiceNumber,));

		#Check if data has been submitted (via flashdata akaka 'form')
		#this means that it is a redirect zt2016_finalize_mollie_url)
		if (isset($forminvoiceinfo)) {
	
			# Add form data flag
			$invoiceinfo->FormSubmitted="1";

			
			#update $invoiceinfo as per form data
			if (isset($forminvoiceinfo->InvoiceTotal)) {
				$invoiceinfo->InvoiceTotal =number_format($forminvoiceinfo->InvoiceTotal,2,".","");
			}

			#update $invoiceinfo as per form data
			if (isset($forminvoiceinfo->VATCheck)) {
				$invoiceinfo->VATCheck =$forminvoiceinfo->VATCheck;
				
			}
		
			
			if (isset($forminvoiceinfo->ZOWPaymentURL)) {

				#update Mollie checkout URL in $invoiceinfo as per form data
				$invoiceinfo->ZOWPaymentURL=$forminvoiceinfo->ZOWPaymentURL;

				#update MollieCheckoutURL in DB as per form data
				$invoiceMollieURLUpdate=$this->zt2016_invoices_model->UpdateInvoice($options = array('Trash' => '0', 'InvoiceNumber' => $invoiceinfo->InvoiceNumber, 'MolliePaymentUrl'=>''));
				
				#update MollieCheckoutURL in DB as per form data
				$invoiceMollieURLUpdate=$this->zt2016_invoices_model->UpdateInvoice($options = array('Trash' => '0', 'InvoiceNumber' => $invoiceinfo->InvoiceNumber, 'MolliePaymentUrl'=>$invoiceinfo->ZOWPaymentURL));

				if (!$invoiceMollieURLUpdate){
					die ('Mollie Payment URL and / or Mollie ID insertion into DB failed');
				}
				
			}		
		
		}

		#If no data was received via flashdata)	
		else {
			#check if there is a an existing ZOW payment URL in the db
			if (!$invoiceinfo->MolliePaymentUrl==""){
				$invoiceinfo->ZOWPaymentURL=$invoiceinfo->MolliePaymentUrl;
			}
		}

		#Build view	
		$templateData['title'] = 'Mollie form '.$invoiceinfo->InvoiceNumber;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _getinvoiceContent($invoiceinfo); 
		$templateData['ZOWuser']=_getCurrentUser();
		
		#Load View
		$this->load->view('admin_temp/main_temp',$templateData);
		
	}

    ################## Retrieve invoice data ##################	
	function  _getinvoiceContent($invoiceinfo)
	{
		# retrieve client data from db		
		$this->load->model('trakclients', '', TRUE);
		$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $invoiceinfo->Client));
 
		#discont added
		if($invoiceinfo->discount > 0){
			$invoiceinfo->InvoiceTotal = $invoiceinfo->InvoiceTotal - $invoiceinfo->discount;
		}
		# determine if client is a Dutch company
		#if so, VAT needs to be added
		if (strtolower($clientInfo->Country)=="the netherlands" || strtolower($clientInfo->Country)=="netherlands" ) {
				
				if (isset($invoiceinfo->FormSubmitted)){
					$invoiceinfo->PostInvoiceVAT= number_format($invoiceinfo->InvoiceTotal,2,".","");
				
				} else {
					$temptotal = number_format($invoiceinfo->InvoiceTotal + ($invoiceinfo->InvoiceTotal*.21),2,".","");
					$invoiceinfo->PostInvoiceVAT=$temptotal;		
				}
		}

		 
		$pageOutput = $this->_display_mollie_page($invoiceinfo,$clientInfo);
		
		return $pageOutput;
	}

    ################## Generates invoice content . ##################	
	function  _display_mollie_page($invoiceinfo,$clientInfo)
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
			
			$pageOutput.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$pageOutput.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('ErrorMessage');
			$pageOutput.='</div>'."\n";
		}
		
	
		########## panel head
		$invoicePanelInfo = zt2016_invoice_paneltype ($invoiceinfo); 
		$invoiceinfo=(object)$invoiceinfo;
		$pageOutput.='<div class="panel '.$invoicePanelInfo->PanelType.'"><div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Mollie form for invoice '.$invoiceinfo->InvoiceNumber.' for '.$invoiceinfo->Client.' ('.$invoicePanelInfo->Status.')</h3>'."\n";
		$pageOutput.="</div><!--panel-heading-->\n";

		########## panel body
		$pageOutput.='<div class="panel-body">'."\n";

 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">'."\n";	

		$pageOutput.='		<div class="col-md-7" >'."\n";	

		
		//$pageOutput.=$this-> _existing_payment_url ($invoiceinfo);
		$pageOutput.=$this->_get_mollie_well($invoiceinfo);
		$pageOutput.=$this->_get_mollie_form($invoiceinfo);
		$pageOutput.="		</div>"."\n";//col-md-6;
		
		$pageOutput.='		<div class="col-md-5 ">'."\n";
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_view_invoice/'.$invoiceinfo->InvoiceNumber.'" class="btn btn-info btn-b pull-right" >Invoice Details</a>'."\n";
		$pageOutput.='		</div>'."\n";		
				
		$pageOutput.="	</div>"."\n";//row;
		$pageOutput.="	</div>"."\n";//panel body;
		$pageOutput.="</div>"."\n";//panel
		
		return $pageOutput;
	}

	################## get Well page element content   ##################
	function _get_mollie_well ($invoiceinfo){

		#Build VAT title if needed
		if (isset($invoiceinfo->PostInvoiceVAT)) {
			$invoicetotal=$invoiceinfo->PostInvoiceVAT;	
		} else {
			$invoicetotal=$invoiceinfo->InvoiceTotal;	
		}

		#Build well content
		$content='		<div class="well">';
		
			if (!empty($invoiceinfo->ZOWPaymentURL)) {

				#Add Mollie checkout URL
				$ZOWPaymentURL="https://www.zebraonwheels.com/paymentsm/".$invoiceinfo->InvoiceNumber.".html";

				$content.='<a href='.$ZOWPaymentURL.' target="_blank">'.$ZOWPaymentURL.'</a>';
				
				################### delete form
				//$content.="<div>";
				$formurl=site_url().'invoicing/zt2016_invoice_mollie_delete';
				$attributes='"class="form-inline" id="deletemollieform" name="deletemollieform" style="margin-top:1em;"';
				$content.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8">';
				$content.='	<input type="hidden" name="InvoiceNumber" value="'.$invoiceinfo->InvoiceNumber.'">'."\n";
				$content.='	<input type="submit" name="molliedeletesubmit" value="Delete" class="btn btn-xs btn-danger" >'."\n";
				$content.="</form>"."\n";
				
			} else{
				$content.="Please create payment link below";	
			}
		
		$content.="		</div>";	
		return $content;
	
	}
	
	// ################## Generate mollie form ##################	
	function _get_mollie_form($invoiceinfo){
		
		
		$attributes='class="form-inline" id="mollieform" name="mollieform"';
		$formurl=site_url().'invoicing/zt2016_invoice_mollie_pagemaker';
		
		$mollieform="";
		$mollieform.='<div class="form-group">'."\n";	

		$mollieform.= '<form action="'.$formurl.'" '.$attributes.' method="post" accept-charset="utf-8">';

		$mollieform.='				<div class="form-group">'."\n";
      	$mollieform.='					<div class="input-group input-group-sm">'."\n";
      	$mollieform.='						<span class="input-group-addon" id="basic-addon1">Invoice #</span>'."\n";
		$mollieform.='						<input type="text" class="form-control" name="InvoiceNumber" value="'.$invoiceinfo->InvoiceNumber.'">'."\n";
 		$mollieform.='					</div>'."\n";
 		$mollieform.='				</div>'."\n";

		$mollieform.='				<div class="form-group">'."\n";
		$mollieform.='					<input type="hidden" id="DbCurrency" name="DbCurrency" value="'.$invoiceinfo->Currency.'">'."\n";
      	$mollieform.='					<div class="input-group input-group-sm">'."\n";
      	$mollieform.='						<span class="input-group-addon" id="basic-addon1">Currency</span>'."\n";
		$mollieform.='						<input type="text" class="form-control" name="Currency" value="'.$invoiceinfo->Currency.'"  style="width:4em;">';
 		$mollieform.='					</div>'."\n";
 		$mollieform.='				</div>'."\n";		
		
 		$mollieform.='				<div class="form-group">'."\n";
      	$mollieform.='					<div class="input-group input-group-sm" style="padding:0 10px;">'."\n";
      	$mollieform.='						<span class="input-group-addon" id="basic-addon1">Amount</span>'."\n";
		
		if (isset($invoiceinfo->PostInvoiceVAT)) {

			$mollieform.='							<input type="text" class="form-control" name="InvoiceTotal" id="InvoiceTotal" value="'.$invoiceinfo->PostInvoiceVAT.'" style="width:7em;font-size:14px;font-weight:bold;">'."\n";
			$mollieform.='							<input type="hidden" id="DbPostInvoiceVAT" name="DbPostInvoiceVAT" value="'.$invoiceinfo->PostInvoiceVAT.'">'."\n";

		} else {
			$mollieform.='							<input type="hidden" id="DbInvoiceTotal" name="DbInvoiceTotal" value="'.$invoiceinfo->InvoiceTotal.'">'."\n";
			$mollieform.='							<input type="text" class="form-control" name="InvoiceTotal" id="InvoiceTotal" value="'.$invoiceinfo->InvoiceTotal.'" style="width:7em;font-size:14px;font-weight:bold;" >'."\n";
		}
 		$mollieform.='					</div>'."\n";
 		$mollieform.='				</div>'."\n";
      	$mollieform.='					<div class="checkbox-inline">'."\n";

		if (isset($invoiceinfo->VATCheck)) {
			if ($invoiceinfo->VATCheck==1) {
				$mollieform.='							<label ><input type="checkbox" name="VATCheck" id="VATCheck" value="1" checked> <span style="font-size:12px;color:#555;">21% VAT</a></label>'."\n";
			} else {
				$mollieform.='							<label ><input type="checkbox" name="VATCheck" id="VATCheck" value="0"> <span style="font-size:12px;color:#555;">21% VATxx</a></label>'."\n";
			}			
		}
		else{ 
			if (isset($invoiceinfo->PostInvoiceVAT)) {
				$mollieform.='							<label ><input type="checkbox" name="VATCheck" id="VATCheck" value="1" checked> <span style="font-size:12px;color:#555;">21% VAT</a></label>'."\n";
			} else {
				$mollieform.='							<label ><input type="checkbox" name="VATCheck" id="VATCheck" value="O"> <span style="font-size:12px;color:#555;">21% VAT</a></label>'."\n";
			}
		}
 		$mollieform.='					</div>'."\n";


  		$mollieform.='				<div class="form-group pull-right" style="padding-left: 20px;">'."\n";
		

		if (!empty($invoiceinfo->ZOWPaymentURL)) {
			$SubmitMessage="Update";
			$mollieform.=form_hidden('ZOWPaymentURL', $invoiceinfo->ZOWPaymentURL);
			
		} else{
			$SubmitMessage="Create";
		}
		
		
 		$mollieform.='					<input type="submit" name="molliesubmit" value="'.$SubmitMessage.'" class="btn btn-sm btn-warning" >'."\n";
 		$mollieform.='				</div>'."\n";
		$mollieform.="			</form>"."\n";
		
		$mollieform.="		</div>"."\n";
		return $mollieform;
	}

} 


/* End of file editclient.php */
/* Location: ./system/application/controllers/invoicing/zt2016_invoice_mollie_form.php */
?>