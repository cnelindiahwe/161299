<?php

//Problem online is uri segment number - please read hidden client input 

class Invoiceogoneform extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('clients','general','form','userpermissions', 'url','invoice','reports','financials','security'));
		
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
			if ($invoicenumber=="") {
				redirect('invoicing');
			}
		
			$this->load->model('trakclients', '', TRUE);
			$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
		
		$clientName=$this-> _getclient($invoicenumber);
		$invoiceinfo=InvoiceTotalsByNumber($invoicenumber,$clientName);
		}
		
		#Create page
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .=  $this->_gettopmenu($invoicenumber,$invoiceinfo['invoicetotal'],$clientName);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .=$this->_getinvoiceinfo($invoicenumber,$invoiceinfo['invoicetotal']);
		
		$templateVars['pageOutput'] .= "</div><!-- content -->";

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Ogone";
		$templateVars['pageType'] = "invoice";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');
	}

	// ################## top ##################	
	function _gettopmenu($invoicenumber,$invoicetotal,$clientName)
	{
			$topmenu ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$topmenu .="<h1>Ogone form for invoice ".$invoicenumber."</h1>";
			$topmenu .="<a href=\"".site_url()."invoicing/viewinvoice/".$invoicenumber."\" >View Details</a>";
			$topmenu .= $this->_getogoneform($invoicenumber,$invoicetotal,$clientName);

			//Add logout button
			$topmenu .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$topmenu .="</div>";
		
			
			return $topmenu;

	}

	// ################## View client name from invoice ##################	
	function 	_getogoneform($invoicenumber,$invoicetotal,$clientName){
	
		$ogoneform='<form method="post" action="'.base_url().'invoicing/invoiceogoneform/'.$invoicenumber.'" id="form1" name="form1">';
		$ogoneform.='<input type="hidden" name="clientname" value="'.$clientName.'">';
		$ogoneform.="<fieldset>";
		$ogoneform.='<label for="invoicenumber"> Invoice Number:</label>';
		$ogoneform.='<input type="text" name="invoicenumber" value="'.$invoicenumber.'">';
		$ogoneform.='<label for="invoicetotal"> Invoice Total:</label>';
		$ogoneform.='<input type="text" name="invoicetotal" value="'.$invoicetotal.'">';
		$ogoneform.="</fieldset>";
		$ogoneform.="<fieldset>";
		$ogoneform.='<input type="submit" name="ogonesubmit" value="Generate">';
		$ogoneform.="</fieldset>";
		$ogoneform.="</form>";
		return $ogoneform;
	}
	
	// ################## View client name from invoice ##################	
	function 	_getinvoiceinfo($invoicenumber,$invoicetotal){
			
		$shasig="5f<&!~7aT=cnfdsafds4123ds";
		$shatotal=$invoicetotal*100;
		$shastring="AMOUNT=".$shatotal.$shasig;
		$shastring.="CURRENCY=EUR".$shasig;
		$shastring.="LANGUAGE=en_US".$shasig;
		$shastring.="ORDERID=".$invoicenumber.$shasig;
		$shastring.="PSPID=ZebraOnWheels".$shasig;
		$shacode = do_hash($shastring); // SHA1
		
		$content="<textarea rows=\"3\" cols=\"150\">";
		$content.=$shastring;
		$content.="</textarea>";
		$content.="<textarea rows=\"40\" cols=\"150\">";
		$content.= $this->_getheaderhtml($invoicenumber,$invoicetotal);
		$content.='<input type="hidden" name="ORDERID" value="'.$invoicenumber.'">
		<input type="hidden" name="AMOUNT" value="'.$shatotal.'">
		<!-- check before the payment: see Security: Check before the Payment -->';
		
		$content.='<input type="hidden" name="SHASIGN" value="'.$shacode.'">';
		$content.= $this->_getfooterhtml();
		$content.="</textarea>";
		return $content;
	
	}
	
		// ################## View client name from invoice ##################	
	function  _getclient($invoicenumber)
	{

		$this->load->model('trakclients', '', TRUE);
		


		$i = 0;
		$clientcode ='';
		$longcode='';
		while ($clientcode =='') {
			$longcode=substr($invoicenumber, $i,1);
			if (is_numeric($longcode)){
				$clientcode=substr($invoicenumber,0, $i);
			}
		    $i++;
		}
		$currentclientcode = $this->trakclients->GetEntry($options = array('ClientCode' => $clientcode));
		return $currentclientcode['0']->CompanyName;


	}
	
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

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>