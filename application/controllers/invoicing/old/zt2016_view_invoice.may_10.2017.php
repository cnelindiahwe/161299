<?php

class Zt2016_view_invoice extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
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

		# retrieve client from db		
		$this->load->model('trakclients', '', TRUE);
		$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $invoiceTotals->Client));

		# call helper
		$pageOutput = $this->zt2016_display_existing_invoice($invoiceTotals,$clientInfo);
		
		return $pageOutput;
	}


    // ################## Generates invoice content . ##################	
	
	function  zt2016_display_existing_invoice($invoiceTotals,$clientInfo)
	{
	
		$invoicePanelInfo = zt2016_invoice_paneltype ($invoiceTotals); 
		
		$pageOutput='<div class="panel '.$invoicePanelInfo->PanelType.'">'."\n";
		$pageOutput.='<div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Invoice '.$invoiceTotals->InvoiceNumber.' for ';

		$safeclientName= str_replace(' ', '_', $invoiceTotals->Client);
		$safeclientName= str_replace('&', '~', $safeclientName);
		
		$pageOutput.='<a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$invoiceTotals->Client.'</a> ('.$invoicePanelInfo->Status.')</h3>'."\n";
		$pageOutput.="</div><!--panel-heading-->\n";
		$pageOutput.='<div class="panel-body">'."\n";
		
		
 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">'."\n";		
		$pageOutput.='		<div class="col-md-4">'."\n";
		
 		//Invoice status form
		$attributes='class="form-inline" id="invoice-status-form"';
		$pageOutput.=form_open(site_url().'invoicing/zt2016_invoice_status',$attributes )."\n";
 		$pageOutput.='				<div class="form-group">'."\n";
      	$pageOutput.='					<div class="input-group input-group-sm">'."\n";
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
		else { //BILLED
			$options = array('CANCEL'=>'Cancel','BILLED'=>'Billed', 'PAID'=>'Paid', 'MARKETING'=>'Marketing','WAIVED'=>'Waived',);
		}
		$more = 'id="InvoiceStatus" class="Status form-control" aria-describedby="basic-addon1"';	
		$pageOutput .=form_dropdown('Status', $options,$invoiceTotals->Status,$more)."\n";
		
 		$pageOutput.='					</div>'."\n";
 		
 		if ($invoiceTotals->Status=='BILLED') {
	 		//$dateplaceholder=date("d M Y");
	 		$dateplaceholder=date("d M Y",strtotime($invoiceTotals->BilledDate));
	
			$pageOutput.='<div class="input-append date" id="dp3" data-date="'.$dateplaceholder.'" data-date-format="dd mm yyyy" style="display:inline;">'."\n";
	      	$pageOutput.='<input type="text" class="form-control datepicker" value="'.$dateplaceholder.'" id="InvoiceDate" name="InvoiceDate" >'."\n";
			$pageOutput.='</div>'."\n";
		} 		
		$pageOutput.=form_hidden('Invoice',$invoiceTotals->InvoiceNumber);
		$pageOutput.=form_hidden('Client',$invoiceTotals->Client);
		$pageOutput.=form_hidden('DueDays',$clientInfo->PaymentDueDate);
		
 		$pageOutput.='					<button type="submit" class="btn btn-sm">Change</button>'."\n";
 		$pageOutput.='				</div>'."\n";
 		$pageOutput.='			</form>'."\n";
	 	$pageOutput.='		</div>'."\n";		
		$pageOutput.='		<div class="col-md-8 btn-toolbar">'."\n";
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_invoice_ogone_form/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-info btn-b pull-right">Ogone</a>'."\n";

		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_csv_existing_invoice/'.$safeclientName.'/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-primary pull-right">Export</a>'."\n";
		$pageOutput.='		</div>'."\n";		
  		$pageOutput.='	</div>'."\n";		

 		//Data row
 		$pageOutput.='	<div class="row">'."\n";	
 		
 		// CASH TOTALS 
		$pageOutput.='<div class="col-sm-4">'."\n";
				
		$pageOutput.='<ul class="list-group totals-column">'."\n";
		
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Total'."\n";
		//$pageOutput.='	<span class="badge badge-success">'. round($invoiceTotals->InvoiceTotal.'</span> '.$clientInfo->Currency."\n";
		$pageOutput.='	<span class="badge badge-success">';
		$pageOutput.=number_format($invoiceTotals->InvoiceTotal, 2);
		if (strtolower($clientInfo -> Country)=="the netherlands" || strtolower($clientInfo -> Country)=="netherlands" ) {
			$vatTotal = number_format(($invoiceTotals->InvoiceTotal*.21)+ $invoiceTotals->InvoiceTotal,2);
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

 		// DATES 

 					
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
			
		}	
		$pageOutput.='		</li>'."\n";
		$pageOutput.='	</ul>'."\n";
		$pageOutput.='</div><!--col-->'."\n";

 		// VOLUME TOTALS 

		
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



 		//Originators row
 		$pageOutput.='	<div class="well">';	
		//$pageOutput.='<div class="col-sm-4">'."\n";

		$pageOutput.= "Originators:";
		$pageOutput.= "<pre class='pre-scrollable' id='originators_list'>".$invoiceTotals->Originators."</pre>";

		//$pageOutput.= ;

 		//$pageOutput.='</div><!--col-->'."\n";		
	
 		$pageOutput.='</div><!--well--> '."\n";		
 		
 		//Invoice data
		$pageOutput.= zt2016_getInvoiceTableByNumber($invoiceTotals->InvoiceNumber,$clientInfo);

		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		//

		 
		//$pageOutput ="test"; 
		return $pageOutput;/**/

	}


}

/* End of file Zt2016_view_invoice */
/* Location: ./system/application/controllers/invoicing/Zt2016_view_invoice.php */
?>