<?php

//Problem online is uri segment number - please read hidden client input 

class Zt2016_post_ogone_ZOWsite extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		

		$this->load->helper(array('userpermissions', 'url', 'file'));
		$this->load->library('session'); //flashdata

		
		$zowuser=_superuseronly(); 
		
		

		#Determine whether form was submitted
		if ($this->input->post('ORDERID'))
		{
			$invoiceinfo['InvoiceNumber']=$this->input->post('ORDERID');
		} 
		else {
			
			die ("Cannot post ogone form - missing ORDERID.<br/>Please use the back button on your browser.");
		}


		if ($this->input->post('AMOUNT'))
		{
			$invoiceinfo['InvoiceTotal']=(float)$this->input->post('AMOUNT')/100;
		} 
		else {
			
			die ("Cannot post ogone form - missing AMOUNT.<br/>Please use the back button on your browser.");
		}

		if ($this->input->post('INVOICEHTML'))
		{
			$invoiceinfo['InvoiceHtml']=$this->input->post('INVOICEHTML');
		} 
		else {
			
			die ("Cannot post ogone form - missing Invoice HTML.<br/>Please use the back button on your browser.");
	    }

		if ($this->input->post('InvoiceVAT'))
		{
			
			if ($this->input->post('InvoiceVAT')==1) {
				$invoiceinfo['InvoiceVAT']=1;
				//$invoiceinfo['InvoiceTotal']=$invoiceinfo['InvoiceTotal']/1.21;
			}
			
		} 

			
		$status=$this->_create_html_invoice($invoiceinfo);



		
	}



	// ################## upload  ##################	
	function _create_html_invoice ($invoiceinfo)
	{
			
		
		
			$save_path = $_SERVER['DOCUMENT_ROOT'].'/zowtempa/etc/temp/';		
			
			// ##### Create invoice file and save it in protected/temp
			
			
			if ( ! write_file($save_path.$invoiceinfo['InvoiceNumber'].'.html', $invoiceinfo['InvoiceHtml']))
			{
			     die("Unable to write the file");
			}
			

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
			
			$filename='/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html';
			

			
			$upload = $sftp->put($filename,$save_path.$invoiceinfo['InvoiceNumber'].'.html', NET_SFTP_LOCAL_FILE );
			//print_r($upload);
			
			$sftp->disconnect();
			
			// ##### Delete invoice file in protected/temp

			unlink($save_path.$invoiceinfo['InvoiceNumber'].'.html');

			
			$this->session->set_flashdata('invoiceinfo', $invoiceinfo);
			
			redirect('invoicing/zt2016_invoice_ogone_form/'.$invoiceinfo['InvoiceNumber'], 'refresh');
			
	}


}

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>