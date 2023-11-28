<?php

class Zt2016_invoice_ogone_form extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_invoice','form','security'));
		
		$zowuser=_superuseronly(); 


		#Determine whether form was submitted
		if ($this->input->post('invoicenumber'))
		{
			$invoicenumber=$this->input->post('invoicenumber');
			$invoiceinfo['invoicetotal']=$this->input->post('invoicetotal');
			$clientName=$this->input->post('clientname');
		} 
		else {
			$invoicenumber=$this->uri->segment(3);
			 if (empty ($invoicenumber)) {
				redirect('invoicing/zt2016_invoices', 'refresh');
			 }
		
		}



		$templateData['title'] = 'Ogone '.$invoicenumber;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _getinvoiceContent($invoicenumber); 

		$templateData['ZOWuser']=_getCurrentUser();

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    // ################## Retrieves invoice data ##################	
	function  _getinvoiceContent($invoicenumber)
	{
		# retrieve invoice data from db
		$this->load->model('zt2016_invoices_model','','TRUE');
		$invoiceTotals=$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','InvoiceNumber'=>$invoicenumber,));

		# retrieve client data from db		
		$this->load->model('trakclients', '', TRUE);
		$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $invoiceTotals->Client));

		# call helper
		$pageOutput = $this->_display_ogone_page($invoiceTotals,$clientInfo);
		
		return $pageOutput;
	}

    // ################## Generates invoice content . ##################	
	
	function  _display_ogone_page($invoiceTotals,$clientInfo)
	{
	
		$invoicePanelInfo = zt2016_invoice_paneltype ($invoiceTotals); 
		
		$pageOutput='<div class="panel '.$invoicePanelInfo->PanelType.'"><div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Ogone form for invoice '.$invoiceTotals->InvoiceNumber.' for '.$invoiceTotals->Client.' ('.$invoicePanelInfo->Status.')</h3>';
		$pageOutput.="</div><!--panel-heading-->\n";
		$pageOutput.='<div class="panel-body">'."\n";

		$pageOutput.='		<div class="col-md-6 pull-right">';
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_viewinvoice/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-info btn-b pull-right">Invoice Details</a>';
		$pageOutput.='		</div>';		


		$pageOutput.=$this-> _existing_payment_url ($invoiceTotals);
		
		
 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">';		
		$pageOutput.=$this->_get_ogone_form($invoiceTotals);
		$pageOutput.="	</div>";//row;

				
 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">';		
		$pageOutput.=$this->_get_ogone_panels($invoiceTotals);
		$pageOutput.="	</div>";//row;
		$pageOutput.="	</div>";//panel body;
		$pageOutput.="</div>";//panel
		
		return $pageOutput;
	}

	// ################## Check if (ogone) payment url exists ##################	
	function _existing_payment_url ($invoiceTotals){

		//http://stackoverflow.com/questions/11916429/check-if-url-is-exist-or-not
		
		$testurl = "http://www.zebraonwheels.com/payments/".$invoiceTotals->InvoiceNumber.".html";
		
		$headers = @get_headers($testurl);
		if (strpos($headers[0],'200')===false)
		{
			//return false;
		}
		else {
			
	 		$existing_ogone_url='	<div class="row" style="padding-bottom:1em;">';	
	 		$existing_ogone_url.='		<div class="col-md-6">';
			$existing_ogone_url.='			<div class="bs-callout bs-callout-warning" style="margin:5px 0;">	';
			$existing_ogone_url.='			<h4>Existing URL</h4>';
			$testurl = "http://www.zebraonwheels.com/payments/".$invoiceTotals->InvoiceNumber.".html";	
			$existing_ogone_url.='			<p><a href="'.$testurl.'">'.$testurl.'</a></p>';
			$existing_ogone_url.='			<div class="form-group"><a href="#" class="btn btn-xs btn-danger">Delete</a></div>';
			$existing_ogone_url.="			</div>";//bs-callout;
			$existing_ogone_url.="		</div>";//col;
			$existing_ogone_url.="	</div>";//row;

			return $existing_ogone_url;
		}	
	}
	
	// ################## Gnerate Ogone form ##################	
	function _get_ogone_form($invoiceTotals){
		
		
		$ogoneform='		<div class="col-md-6">';	
		$attributes='class="form-inline" id="ogoneform" name="ogoneform"';
		$ogoneform.=form_open(base_url().'invoicing/zt2016_invoice_ogone_form',$attributes )."\n";
	
		//$ogoneform='			<form method="post" action="'.base_url().'invoicing/zt2016_invoice_ogone_form/'.$invoiceTotals->InvoiceNumber.'" id="form1" name="form1">';
 		$ogoneform.='				<div class="form-group">';
      	$ogoneform.='					<div class="input-group input-group-sm">';
      	$ogoneform.='						<span class="input-group-addon" id="basic-addon1">Invoice Number</span>';
		//$ogoneform.='						<input type="hidden" name="clientname" value="'.$invoiceTotals->Client.'">';
		$ogoneform.='						<input type="text" class="form-control" name="invoicenumber" value="'.$invoiceTotals->InvoiceNumber.'">';
 		$ogoneform.='					</div>';
 		$ogoneform.='				</div>';
 		$ogoneform.='				<div class="form-group">';
      	$ogoneform.='					<div class="input-group input-group-sm">';
      	$ogoneform.='						<span class="input-group-addon" id="basic-addon1">Invoice Total ('.$invoiceTotals->Currency.')</span>';
		$ogoneform.='						<input type="text" class="form-control" name="invoicetotal" value="'.$invoiceTotals->InvoiceTotal.'">';
 		$ogoneform.='					</div>';
 		$ogoneform.='				</div>';
 		$ogoneform.='				<div class="form-group pull-right">';
 		$ogoneform.='					<input type="submit" name="ogonesubmit" value="Generate" class="btn btn-sm btn-success">';
 		$ogoneform.='				</div>';
		$ogoneform.="			</form>";
		$ogoneform.='		</div>'; //class="col-md-6"	
	

		return $ogoneform;
	}


	// ################## get page content   ##################	
	function _get_ogone_panels($invoiceTotals){
			
			
		$invoicetotal=$invoiceTotals->InvoiceTotal;	
		$invoicenumber=$invoiceTotals->InvoiceNumber;	
		$shasig="5f<&!~7aT=cnfdsafds4123ds";
		$shatotal=$invoicetotal*100;
		$shastring="AMOUNT=".$shatotal.$shasig;
		$shastring.="CURRENCY=EUR".$shasig;
		$shastring.="LANGUAGE=en_US".$shasig;
		$shastring.="ORDERID=".$invoicenumber.$shasig;
		$shastring.="PSPID=ZebraOnWheels".$shasig;
		$shacode = do_hash($shastring); // SHA1
		
		
		$content='		<div class="col-md-6">';
		$content.="				<form method=\"post\" action=\"".base_url()."invoicing/postogoneZOWsite"."\">";	
		$content.='				<div class="form-group>';
		$content.="					<label for=\"shastring\"  style=\"font-size:12px\">shastring:</label>";
		$content.="					<textarea rows=\"3\" cols=\"150\" class=\"form-control\" id=\"shastring\" style=\"font-size:12px\">";
		$content.=$shastring;
		$content.="					</textarea>";
		$content.="				</div>";	//form-group
		
		$content.='				<div class="form-group">';
		$content.="					<label for=\"\"INVOICEHTML\"\" style=\"font-size:12px\">HTML:</label>";
				
		$content.="					<textarea rows=\"10\" cols=\"150\" name=\"INVOICEHTML\" id=\"INVOICEHTML\"style=\"border:1px solid #a9a9a9;margin:.25em 0 0;font-size:12px\" class=\"form-control\">";
		$content.= 					$this->_getheaderhtml($invoicenumber,$invoicetotal);
		$content.='					<input type="hidden" name="ORDERID" value="'.$invoicenumber.'">
									<input type="hidden" name="AMOUNT" value="'.$shatotal.'">
									<!-- check before the payment: see Security: Check before the Payment -->';
		
		$content.='					<input type="hidden" name="SHASIGN" value="'.$shacode.'">';
		$content.= $this->_getfooterhtml();
		$content.="</textarea>";
		$content.='<input type="hidden" name="ORDERID" value="'.$invoicenumber.'">';
		$content.='<input type="hidden" name="AMOUNT" value="'.$shatotal.'">';

		$content.='<input type="submit" class="btn btn-sm btn-warning" name="postogonetoZOWsite" value="Post to ZOW website" style="padding:.25em;margin:1em 0 0;" >';
		$content.="</div>";	//form-group
		$content.="</div>";	//input-group
		$content.="</form>";	
		$content.="</div>";	
		return $content;
	
	}
	

	// ################## Generate HTML header ##################		
	function  _getheaderhtml($invoicenumber,$invoicetotal)
	{	
	$headerhtml ='
	<!DOCTYPE HTML>
<html>
	<head>
	<title>Zebra on Wheels - Corporate Presentation & Visualization Services</title>
	<meta name="description" content="We are a creative team with a strong background in global corporate strategy engagements. Our service and pricing model has been designed to provide  the service and flexibility you need.">
	<meta name="keywords" content="presentation, PowerPoint, Ms-Office, macro, strategy, corporate, graphs, charts,graphics, marimekko, gant, data visualization, information design">
	<meta name="robots" content="index,follow">
	<script type="text/javascript" src="http://www.zebraonwheels.com/web/js/libs/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="web/js/thankyouinvoice.js"></script>
	<link type="text/css" rel="stylesheet" href="http://www.zebraonwheels.com/web/css/site.css" />
	</head>
	<body class="paymentsinvoice">
		<div id="site-wrapper">
			<header>
			<a href="../home"><img id="logo" src="http://www.zebraonwheels.com/web/media/ui/logo.png" height="64" width="294" alt="Zebra on Wheels"></a>
			<h1 id="slogan"><a href="../home">Corporate Presentation & Visualization Services</a></h1>

			<ul id="menu">
				<li class="contactlink"><a href="contact">Contact Us</a></li>
				<li><a href="http://www.zebraonwheels.com/home"  >Home</a></li>
				<li><a href="http://www.zebraonwheels.com/services"  >Services</a></li>
				<li><a href="http://www.zebraonwheels.com/approach"  >Approach</a></li>
				<li><a href="http://www.zebraonwheels.com/credentials" >Credentials</a></li>
			</ul>
			</header>
   		<h1 class="section-intro">Invoice Number: '.$invoicenumber.' - '.$invoicetotal.' EUR</h1>
			<p>For security reasons, only the invoice number and due amount are shown on this page. Please ensure that they match your copy before proceeding with payment.</p>
			<p>Should you have any questions or comments, please contact <a href="mailto:miguel@zebraonwheels.com">miguel@zebraonwheels.com</a></p>
		<form method="post" action="https://secure.ogone.com/ncol/prod/orderstandard.asp" id="form1" name="form1">
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
		<input type="submit" value="Pay with American Express" id="submit2" name="submit2">
		</form>
		<div id="footer">
		<p>&amp;copy; Zebra on Wheels</p>
		</div><!-- footer -->
		</div> <!-- site-wrapper -->	
		</body>
		</html>
		';
		return $headerhtml;
		} 
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/invoicing/zt2016_viewinvoice .php */
?>