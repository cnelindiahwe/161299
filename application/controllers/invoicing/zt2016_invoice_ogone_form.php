<?php

class Zt2016_invoice_ogone_form extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_invoice','form','security'));
		
		$zowuser=_superuseronly(); 
		$this->load->library('session'); //flashdata


		#Determine whether form was submitted

		if ($this->input->post('InvoiceNumber'))
		{

		
			 if ($this->input->post('InvoiceTotal')) {
				$InvoiceNumber=	$this->input->post('InvoiceNumber');
				$PostInvoiceTotal=$this->input->post('InvoiceTotal');
				$Formsubmitted=true;	
				if ($this->input->post('VATCheck')) {
					$PostInvoiceVAT=$this->input->post('VATCheck');
				}
			 } else {
				//print_r($PostInvoiceTotal);    
				//exit();	
				redirect('invoicing/zt2016_view_invoice'/$this->input->post('invoicenumber'), 'refresh');
			 }
		} 
		
		else if($this->session->flashdata('invoiceinfo'))
		# flahsdata
		{
			$invoiceinfo=$this->session->flashdata('invoiceinfo'); 	
			

			 if (isset($invoiceinfo['InvoiceNumber'])) {
				$InvoiceNumber=	$invoiceinfo['InvoiceNumber'];
			 } else {
				 die ('no invoice number');
			 }
			 
			if (isset($invoiceinfo['InvoiceTotal'])) {
				$PostInvoiceTotal=	$invoiceinfo['InvoiceTotal'];
			 } else{
				 die ('no invoice number');
			 }
			
			if (isset($invoiceinfo['InvoiceVAT'])) {
				if ($invoiceinfo['InvoiceVAT']==1) {
					$PostInvoiceVAT=$invoiceinfo['InvoiceVAT'];
					$PostInvoiceTotal=$PostInvoiceTotal/1.21;
				}
			}
			//else{
			//	die ('no VAT');
				//redirect('invoicing/zt2016_invoice_ogone_form/'.$InvoiceNumber, 'refresh');
			// }
			
		}
		else 
		# URL
		{
			# retrieve invoice number from URL
			$InvoiceNumber=$this->uri->segment(3);
			 if (empty ($InvoiceNumber)) {
				redirect('invoicing/zt2016_existing_invoices', 'refresh');
			 }
		
		}

		# retrieve invoice data from db
		$this->load->model('zt2016_invoices_model','','TRUE');
		$invoiceTotals=$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','InvoiceNumber'=>$InvoiceNumber,));
		
		#if form has new total, update the total in $invoiceTotals
		if (isset($PostInvoiceTotal)) {
			 $invoiceTotals->InvoiceTotal =$PostInvoiceTotal; 
			 //print_r($PostInvoiceTotal);    
			 //exit();	
		}
		
		#if form VAT checked, update the total in $invoiceTotals		
		if (isset($PostInvoiceVAT)) {
				$temptotal = number_format($invoiceTotals->InvoiceTotal + ($invoiceTotals->InvoiceTotal*.21),2,".","");
				$invoiceTotals->PostInvoiceVAT=$temptotal;			
		}

		if (isset($Formsubmitted)) {
				$invoiceTotals->Formsubmitted=$Formsubmitted;	
		}


		$templateData['title'] = 'Ogone form '.$InvoiceNumber;
		$templateData['sidebar_content']='sidebar';
		

		$templateData['main_content'] =$this-> _getinvoiceContent($invoiceTotals); 

		$templateData['ZOWuser']=_getCurrentUser();

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    // ################## Retrieves invoice data ##################	
	function  _getinvoiceContent($invoiceTotals)
	{

		# retrieve client data from db		
		$this->load->model('trakclients', '', TRUE);
		$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $invoiceTotals->Client));
 
		if (strtolower($clientInfo -> Country)=="the netherlands" || strtolower($clientInfo -> Country)=="netherlands" ) {
				if (isset($invoiceTotals->Formsubmitted)){
					if (isset($invoiceTotals->Formsubmitted)  && isset($invoiceTotals->PostInvoiceVAT)){
						$temptotal = number_format($invoiceTotals->InvoiceTotal + ($invoiceTotals->InvoiceTotal*.21),2,".","");
						$invoiceTotals->PostInvoiceVAT=$temptotal;
					}	
				} else {
					$temptotal = number_format($invoiceTotals->InvoiceTotal + ($invoiceTotals->InvoiceTotal*.21),2,".","");
					$invoiceTotals->PostInvoiceVAT=$temptotal;		
				}
		}

		# 
		$pageOutput = $this->_display_ogone_page($invoiceTotals,$clientInfo);
		
		return $pageOutput;
	}

    // ################## Generates invoice content . ##################	
	
	function  _display_ogone_page($invoiceTotals,$clientInfo)
	{
	
		$invoicePanelInfo = zt2016_invoice_paneltype ($invoiceTotals); 
		
		$pageOutput='<div class="panel '.$invoicePanelInfo->PanelType.'"><div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Ogone form for invoice '.$invoiceTotals->InvoiceNumber.' for '.$invoiceTotals->Client.' ('.$invoicePanelInfo->Status.')</h3>'."\n";
		$pageOutput.="</div><!--panel-heading-->\n";
		$pageOutput.='<div class="panel-body">'."\n";



 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">'."\n";	

		$pageOutput.='		<div class="col-md-7" >'."\n";	
		$pageOutput.=$this-> _existing_payment_url ($invoiceTotals);
		$pageOutput.=$this->_get_ogone_panels($invoiceTotals);
		$pageOutput.=$this->_get_ogone_form($invoiceTotals);
		$pageOutput.="		</div>"."\n";//col-md-6;
		
		$pageOutput.='		<div class="col-md-5 ">'."\n";
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_view_invoice/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-info btn-b pull-right" >Invoice Details</a>'."\n";
		$pageOutput.='		</div>'."\n";		
				
		$pageOutput.="	</div>"."\n";//row;
		$pageOutput.="	</div>"."\n";//panel body;
		$pageOutput.="</div>"."\n";//panel
		
		return $pageOutput;
	}

	// ################## Check if (ogone) payment url exists ##################	
	function _existing_payment_url ($invoiceTotals){

		//http://stackoverflow.com/questions/11916429/check-if-url-is-exist-or-not
		
		$testurl = "https://www.zebraonwheels.com/payments/".$invoiceTotals->InvoiceNumber.".html";
		
		$headers = @get_headers($testurl);
		if (strpos($headers[0],'200')===false)
		{
			//return false;
		}
		else {
			

			$existing_ogone_url='			<div class="bs-callout bs-callout-warning" style="margin:5px 0 15px;">	'."\n";
			$existing_ogone_url.='			<h4>Existing URL</h4>'."\n";
			$testurl = "https://www.zebraonwheels.com/payments/".$invoiceTotals->InvoiceNumber.".html";	
			$existing_ogone_url.='			<p><a href="'.$testurl.'" target="_blank">'.$testurl.'</a></p>'."\n";
			$existing_ogone_url.='			<div class="form-group">'."\n";
			//<a href="#" class="btn btn-xs btn-danger">Delete</a>
			$attributes='class="form-inline" id="ogonedeleteform" name="ogonedeleteform"';
			$existing_ogone_url.=form_open(base_url().'invoicing/zt2016_delete_ogone_ZOWsite',$attributes )."\n";	
			$existing_ogone_url.='						<input type="hidden" name="InvoiceNumber" value="'.$invoiceTotals->InvoiceNumber.'">'."\n";
//			$existing_ogone_url.='						<input type="hidden" name="Client" value="'.$invoiceTotals->Client.'">'."\n";
//			$existing_ogone_url.='						<input type="hidden" name="Status" value="'.$invoiceTotals->Status.'">'."\n";
			if (isset($invoiceTotals->PostInvoiceVAT)) {
				$existing_ogone_url.='							<input type="hidden" name="InvoiceTotal"  value="'.$invoiceTotals->PostInvoiceVAT.'">'."\n";
				$existing_ogone_url.='						<input type="hidden" name="InvoiceVAT" value="1">'."\n";
			} else {
				$existing_ogone_url.='							<input type="hidden" name="InvoiceTotal" value="'.$invoiceTotals->InvoiceTotal.'">'."\n";
				$existing_ogone_url.='							<input type="hidden" name="InvoiceVAT" value="0">'."\n";
			}
 			$existing_ogone_url.='					<input type="submit" name="ogonesubmit" value="Delete" class="btn btn-xs btn-danger">'."\n";
			$existing_ogone_url.='			</form>';	
			$existing_ogone_url.='			</div>'."\n";
			$existing_ogone_url.='			</div>'."\n";//bs-callout;


			return $existing_ogone_url;
		}	
	}
	
	// ################## Gnerate Ogone form ##################	
	function _get_ogone_form($invoiceTotals){
		
		
		$ogoneform='<div class="form-group">'."\n";	
		//$attributes='class="form-inline" id="ogoneform" name="ogoneform"';
		$attributes='class="form-inline" id="ogoneform" name="ogoneform"';
		$ogoneform.=form_open(base_url().'invoicing/zt2016_invoice_ogone_form',$attributes )."\n";
	
		//$ogoneform='			<form method="post" action="'.base_url().'invoicing/zt2016_invoice_ogone_form/'.$invoiceTotals->InvoiceNumber.'" id="form1" name="form1">';
 		$ogoneform.='				<div class="form-group">'."\n";
      	$ogoneform.='					<div class="input-group input-group-sm">'."\n";
      	$ogoneform.='						<span class="input-group-addon" id="basic-addon1">Invoice #</span>'."\n";
		$ogoneform.='						<input type="hidden" name="Client" value="'.$invoiceTotals->Client.'">'."\n";
		$ogoneform.='						<input type="hidden" name="Status" value="'.$invoiceTotals->Status.'">'."\n";
		$ogoneform.='						<input type="text" class="form-control" name="InvoiceNumber" value="'.$invoiceTotals->InvoiceNumber.'">'."\n";
 		$ogoneform.='					</div>'."\n";
 		$ogoneform.='				</div>'."\n";
 		$ogoneform.='				<div class="form-group">'."\n";
      	$ogoneform.='					<div class="input-group input-group-sm" style="padding:0 10px;">'."\n";
      	$ogoneform.='						<span class="input-group-addon" id="basic-addon1">Amount ('.$invoiceTotals->Currency.')</span>'."\n";
		if (isset($invoiceTotals->PostInvoiceVAT)) {
			//$temptotal = number_format($invoiceTotals->InvoiceTotal + ($invoiceTotals->InvoiceTotal*.21),2,".","");
			$ogoneform.='							<input type="text" class="form-control" name="InvoiceTotal" id="InvoiceTotal" value="'.$invoiceTotals->PostInvoiceVAT.'" style="width:7em;font-size:14px;font-weight:bold;">'."\n";
		} else {
			$ogoneform.='							<input type="text" class="form-control" name="InvoiceTotal" id="InvoiceTotal" value="'.$invoiceTotals->InvoiceTotal.'" style="width:7em;font-size:14px;font-weight:bold;" >'."\n";
		}
 		$ogoneform.='					</div>'."\n";
 		$ogoneform.='				</div>'."\n";
      	$ogoneform.='					<div class="checkbox-inline">'."\n";
		if (isset($invoiceTotals->PostInvoiceVAT)) {
			$ogoneform.='							<label ><input type="checkbox" name="VATCheck" id="VATCheck" value="1" checked> <span style="font-size:12px;color:#555;">21% VAT</a></label>'."\n";
		} else {
			$ogoneform.='							<label ><input type="checkbox" name="VATCheck"  id="VATCheck" value="1"> <span style="font-size:12px;color:#555;">21% VAT</a></label>'."\n";
		}
 		$ogoneform.='					</div>'."\n";
  		$ogoneform.='				<div class="form-group pull-right" style="padding-left: 20px;">'."\n"; 
 		$ogoneform.='					<input type="submit" name="ogonesubmit" value="Update HTML" class="btn btn-sm btn-warning" >'."\n";
 		$ogoneform.='				</div>'."\n";
		$ogoneform.="			</form>"."\n";
		$ogoneform.="		</div>"."\n";
	

		return $ogoneform;
	}


	// ################## get page content   ##################	
	function _get_ogone_panels($invoiceTotals){
			
		if (isset($invoiceTotals->PostInvoiceVAT)) {
			$invoicetotal=$invoiceTotals->PostInvoiceVAT;	
		} else {
			$invoicetotal=$invoiceTotals->InvoiceTotal;	
		}

		$invoicenumber=$invoiceTotals->InvoiceNumber;	
		$shasig="5f<&!~7aT=cnfdsafds4123ds";
		$shatotal=$invoicetotal*100;
		$shastring="AMOUNT=".$shatotal.$shasig;
		$shastring.="CURRENCY=EUR".$shasig;
		$shastring.="LANGUAGE=en_US".$shasig;
		$shastring.="ORDERID=".$invoicenumber.$shasig;
		$shastring.="PSPID=ZebraOnWheels".$shasig;
		$shacode = do_hash($shastring); // SHA1
		
		
		$content='		<div class="well">';
		$content.="				<form method=\"post\" id=\"postogoneform\" action=\"".base_url()."invoicing/zt2016_post_ogone_ZOWsite"."\">"."\n";	
		
		$content.='				<input type="submit" class="btn btn-sm btn-success" name="postogonetoZOWsite" id="postogonetoZOWsite" value="Post to ZOW website" style="padding:.25em;margin:0 0 1em;" >';
		
		$content.='				<div class="form-group code-hide">'."\n";;
		$content.="					<label for=\"shastring\"  style=\"font-size:12px;\">SHA string:</label>"."\n";
		$content.="					<textarea rows=\"3\" cols=\"150\" class=\"form-control\" id=\"shastring\" style=\"font-size:12px;margin-bottom:1em;\">"."\n";
		$content.=$shastring;
		$content.="					</textarea>"."\n";
		$content.="				</div>"."\n";	//form-group
		
		$content.='				<div class="form-group code-hide">'."\n";
		$content.="					<label for=\"INVOICEHTML\" style=\"font-size:12px;\">HTML:</label>"."\n";
				
		$content.="					<textarea rows=\"10\" cols=\"150\" name=\"INVOICEHTML\" id=\"INVOICEHTML\"style=\"border:1px solid #a9a9a9;margin:.25em 0 0;font-size:12px\" class=\"form-control\">";
		$content.= 					$this->_getheaderhtml($invoicenumber,$invoicetotal);
		$content.='<input type="hidden" name="ORDERID" value="'.$invoicenumber.'">'."\n";
		$content.='		<input type="hidden" name="AMOUNT" value="'.$shatotal.'">'."\n";
		$content.='		<!-- check before the payment: see Security: Check before the Payment -->'."\n";
		$content.='		<input type="hidden" name="SHASIGN" value="'.$shacode.'">';
		$content.= $this->_getfooterhtml();
		$content.="					</textarea>";
		$content.='					<input type="hidden" name="ORDERID" value="'.$invoicenumber.'">'."\n";
		$content.='					<input type="hidden" name="AMOUNT" value="'.$shatotal.'">'."\n";
		$content.='					<input type="hidden" name="InvoiceVAT" value="'.isset($invoiceTotals->PostInvoiceVAT).'">'."\n";
		$content.="				</div>";	//form-group
		$content.="			</form>";	
		$content.="		</div>";	
		return $content;
	
	}
	

	// ################## Generate HTML header ##################		
	function  _getheaderhtml($invoicenumber,$invoicetotal)
	{	
	$headerhtml ='<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ZOW Invoice '.$invoicenumber.'</title>
	<meta name="description" content="Zebra on Wheels - Creative Services for Corporate Managers">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
	
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

	<link href="https://fonts.googleapis.com/css2?family=Jockey+One&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Spinnaker&display=swap" rel="stylesheet">	
	
	<link rel="stylesheet" href="https://www.zebraonwheels.com/web/css/zow.css">
	
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="50">

<!-- nav bar -->

<nav class="navbar navbar-expand-lg navbar-dark fixed-top black">
  <a class="navbar-brand" href="https://www.zebraonwheels.com/" alt="Zebra on Wheels"><img src="https://www.zebraonwheels.com/web/img/ZOWlogo.svg"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse flex-grow-1 text-right" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto flex-nowrap">
      <li class="nav-item">
        <a class="nav-link" href="https://www.zebraonwheels.com/#intro">Intro</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://www.zebraonwheels.com/#services">Services</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://www.zebraonwheels.com/#approach">Approach</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://www.zebraonwheels.com/#contact">Contact</a>
      </li>
    </ul>

  </div>
</nav>
	<div class="container invoice">
   		<h1 class="section-intro">Invoice<br />'.$invoicenumber.'<br />'.number_format($invoicetotal,2,".",",").' EUR</h1>
		<form method="post" action="https://secure.ogone.com/ncol/prod/orderstandard.asp" id="form1" name="form1" class="mb-5">
		<input type="hidden" name="PSPID" value="ZebraOnWheels">
		';
		return $headerhtml;
		} 

	// ################## Generate HTML footer ##################		
	function  _getfooterhtml()
	{	
	$headerhtml ='
		<input type="hidden" name="CURRENCY" value="EUR">
		<input type="hidden" name="LANGUAGE" value="en_US">
		<input type="hidden" name="CN" value="">
		<input type="hidden" name="EMAIL" value="">
		<input type="hidden" name="OWNERZIP" value="">
		<input type="hidden" name="OWNERADDRESS" value="">
		<input type="hidden" name="OWNERCTY" value="">
		<input type="hidden" name="OWNERTOWN" value="">
		<input type="hidden" name="OWNERTELNO" value="">
		<!-- layout information: see Look and Feel of the Payment Page -->
		<input type="hidden" name="TITLE" value="">
		<input type="hidden" name="BGCOLOR" value="">
		<input type="hidden" name="TXTCOLOR" value="">
		<input type="hidden" name="TBLBGCOLOR" value="">
		<input type="hidden" name="TBLTXTCOLOR" value="">
		<input type="hidden" name="BUTTONBGCOLOR" value="">
		<input type="hidden" name="BUTTONTXTCOLOR" value="">
		<input type="hidden" name="LOGO" value="">
		<input type="hidden" name="FONTTYPE" value="">
		<!-- post payment redirection: see Transaction Feedback to the Customer -->
		<input type="hidden" name="ACCEPTURL" value="">
		<input type="hidden" name="DECLINEURL" value="">
		<input type="hidden" name="EXCEPTIONURL" value="">
		<input type="hidden" name="CANCELURL" value="">
		<input type="submit" value="Pay with American Express" id="submit2" name="submit2"  class="btn btn-sm btn-info">
		</form>
		
		<p>For security reasons, only the invoice number and due amount are shown on this page.</p>
		<p>Please ensure that they match your copy before proceeding with payment.</p>
		<p>Should you have any questions or comments, please contact <a href="mailto:invoices@zebraonwheels.com">invoices@zebraonwheels.com</a></p>
	</div>
</body>
</html>';
		return $headerhtml;
		} 
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/invoicing/zt2016_invoice_ogone_form.php */
?>