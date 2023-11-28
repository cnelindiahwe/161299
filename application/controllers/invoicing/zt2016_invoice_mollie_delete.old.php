<?php

class Zt2016_invoice_mollie_delete extends MY_Controller {



	function index()
	{
		
		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		

		$this->load->helper(array('userpermissions', 'url','file'));
		//$this->load->library('session'); //flashdata

		$zowuser=_superuseronly(); 

		
		#Determine whether form was submitted
		
			
		if ($this->input->post('InvoiceNumber'))
		{
			$invoiceinfo['InvoiceNumber']=$this->input->post('InvoiceNumber');
		} 
		else {
		
			die ("Cannot create Mollie page due to missing Invoice Number.<br/>Please use the back button on your browser.");
		}
		
		
		
		
		$status =$this->_delete_zow_url($invoiceinfo);
		
		
		$status = $this->_delete_zow_url_db($invoiceinfo);
		
		//$this->session->set_flashdata('invoiceinfo', $invoiceinfo);
		$this->session->set_flashdata('SuccessMessage', 'Payment URL deleted.');


		redirect('invoicing/zt2016_invoice_mollie_form/'.$invoiceinfo['InvoiceNumber'], 'refresh');
	}


	
	// ################## upload  final Mollie page to ZOW website##################	
	function _delete_zow_url ($invoiceinfo)	{
			
			// ##### Upload invoice file to ZOW site
			
			$this->load->library('sftp');

			$config['hostname'] = $this->config->item('zowsftphostname');
			$config['username'] = $this->config->item('zowsftpusername');
			$config['password'] = $this->config->item('zowsftppassword');
			$config['debug']	= TRUE;
			
			$sftp = new Net_SFTP($config['hostname']);
			     if (!$sftp->login($config['username'], $config['password'])) {
			         exit('Login Failed - cannot delete Mollie url to ZOW site');
			     }
			
			//$this->ftp->delete_file('/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html');

			$sftp->delete('/www/paymentsm/'.$invoiceinfo['InvoiceNumber'].'.html');		
			$sftp->disconnect();

	}
	
	// ################## Generate HTML header ##################		
	function  _delete_zow_url_db($invoiceinfo)
	{	

		# retrieve invoice data from db
		$this->load->model('zt2016_invoices_model','','TRUE');

		#update MollieCheckoutURL in DB as per form data
		$invoiceMollieURLDelete=$this->zt2016_invoices_model->UpdateInvoice($options = array('Trash' => '0', 'InvoiceNumber' => $invoiceinfo['InvoiceNumber'], 'MolliePaymentUrl'=>''));

		if (!$invoiceMollieURLDelete){
			die ('Mollie Payment URL delettion from DB failed');
		}

	}
}

/* End of file zt2016_invoice_mollie_deletel.php */
/* Location: ./system/application/controllers/invoicing/zt2016_invoice_mollie_delete.php */
?>