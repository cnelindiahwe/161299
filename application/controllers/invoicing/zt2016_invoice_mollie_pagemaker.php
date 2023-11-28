<?php

//Problem online is uri segment number - please read hidden client input 

class Zt2016_invoice_mollie_pagemaker extends MY_Controller {


	function index()
	{
		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		

		$this->load->helper(array('userpermissions', 'url','file'));
		$this->load->library('session'); //flashdata

		$zowuser=_superuseronly(); 


		#Determine whether form was submitted
		# and load values if it was
		
		
		if ($this->input->post('InvoiceNumber'))
		{
			$invoiceinfo['InvoiceNumber']=$this->input->post('InvoiceNumber');
		} 
		else {
			
			die ("Cannot create Mollie page due to missing Invoice Number.<br/>Please use the back button on your browser.");
		}


		if ($this->input->post('Currency'))
		{
			$invoiceinfo['Currency']=$this->input->post('Currency');
		} 
		else {
			
			die ("Cannot create Mollie page due to missing Currency.<br/>Please use the back button on your browser.");
		}

		if ($this->input->post('InvoiceTotal'))
		{
			$invoiceinfo['InvoiceTotal']=number_format($this->input->post('InvoiceTotal'),2,".","");
		} 
		else {
			
			die ("Cannot create Mollie page due to missing Invoice Total.<br/>Please use the back button on your browser.");
	    }

		if ($this->input->post('VATCheck'))
		{
			$invoiceinfo['VATCheck']=$this->input->post('VATCheck');
		} else{
			$invoiceinfo['VATCheck']=0;
		}	

		
		
		##################### Main sequence
		
		$invoiceinfo['ZOWpaypageHTML']= $this->_getmolliehtml($invoiceinfo);
		
		$invoiceinfo =$this->_upload_html_invoice ($invoiceinfo);

			
		$this->session->set_flashdata('invoiceinfo', $invoiceinfo);
		$this->session->set_flashdata('SuccessMessage', 'New payment URL created.');

		redirect('invoicing/zt2016_invoice_mollie_form/'.$invoiceinfo['InvoiceNumber'], 'refresh');

	
	}

	
	// ################## Generate HTML header ##################		
	function  _getmolliehtml($invoiceinfo)
	{	
	$molliehtml ='<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />

	<title>ZOW Invoice '.$invoiceinfo['InvoiceNumber'].'</title>
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
  <a class="navbar-brand" href="https://www.zebraonwheels.com/"><img src="https://www.zebraonwheels.com/web/img/ZOWlogo.svg" alt="Zebra on Wheels"></a>
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
   		<h1 class="section-intro">Invoice<br />'.$invoiceinfo['InvoiceNumber'].'<br />'.number_format($invoiceinfo['InvoiceTotal'],2,".",",").' '.$invoiceinfo['Currency'].'</h1>

		<form action="https://www.zebraonwheels.com/createmollieurl" id="mcheckoutform" name="mcheckoutform" method="post" accept-charset="utf-8" class="mb-5">
			<input type="hidden" id="InvoiceTotal" name="InvoiceTotal" value="'.$invoiceinfo['InvoiceTotal'].'">
			<input type="hidden" id="Currency" name="Currency" value="'.$invoiceinfo['Currency'].'">
			<input type="hidden" id="InvoiceNumber" name="InvoiceNumber" value="'.$invoiceinfo['InvoiceNumber'].'">
			<input type="submit" name="molliesubmit" value="Click here to pay" class="btn btn-sm btn-info">
		</form>
			
		<p>For security reasons, only the invoice number and due amount are shown on this page.</p>
		<p>Please ensure that they match your copy before proceeding with payment.</p>
		<p>Should you have any questions or comments, please contact <a href="mailto:invoices@zebraonwheels.com">invoices@zebraonwheels.com</a></p>
	</div>
</body>
</html>';
		return $molliehtml;
		} 	
	
	// ################## upload  final Mollie page to ZOW website##################	
	function _upload_html_invoice ($invoiceinfo)	{
		

			$save_path = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/temp/";
			
			// ##### Create invoice file and save it in protected/temp
		
			
			if ( ! write_file($save_path.$invoiceinfo['InvoiceNumber'].'.html', $invoiceinfo['ZOWpaypageHTML']))
			{
			     die("Unable to write the file");
			}
		
			// die ($save_path.$invoiceinfo['InvoiceNumber'].'.html');
			
			// ##### Upload invoice file to ZOW site
			
			$this->load->library('sftp');
	
			$config['hostname'] = $this->config->item('zowsftphostname');
			$config['username'] = $this->config->item('zowsftpusername');
			$config['password'] = $this->config->item('zowsftppassword');
			$config['debug']	= TRUE;

			$sftp = new Net_SFTP($config['hostname']);
			     if (!$sftp->login($config['username'], $config['password'])) {
			         exit('Login Failed - cannot write Ogone url to ZOW site');
			     }
			//$this->sftp->upload($_SERVER['NFSN_SITE_ROOT']  . 'protected/temp/'.$invoiceinfo['InvoiceNumber'].'.html', '/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html', 'ascii');
			$filename='/data/sites/web/zebraonwheelscom/www/paymentsm/'.$invoiceinfo['InvoiceNumber'].'.html';
			

			
			$upload = $sftp->put($filename,$save_path.$invoiceinfo['InvoiceNumber'].'.html', NET_SFTP_LOCAL_FILE );
			//print_r($upload);
			
			$sftp->disconnect();
			
			// ##### Delete invoice file in protected/temp

			//unlink($save_path.$invoiceinfo['InvoiceNumber'].'.html');

			
			$invoiceinfo['ZOWPaymentURL']="https://www.zebraonwheels.com/paymentsm/".$invoiceinfo['InvoiceNumber'].'.html';
			
			return $invoiceinfo;
			
	}
}

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/invoicing/zt2016_create_mollie_url.php */
?>