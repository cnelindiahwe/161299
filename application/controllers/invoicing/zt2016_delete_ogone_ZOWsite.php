<?php

//Problem online is uri segment number - please read hidden client input 

class Zt2016_delete_ogone_ZOWsite extends MY_Controller {


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
		if ($this->input->post('InvoiceNumber'))
		{
			$invoiceinfo['InvoiceNumber']=$this->input->post('InvoiceNumber');
			$invoiceinfo['InvoiceVAT']=$this->input->post('InvoiceVAT');
			$invoiceinfo['InvoiceTotal']=$this->input->post('InvoiceTotal');
					
			$status=$this->_delete_html_invoice($invoiceinfo);
		} 
		else {
			
			die("Cannot delete invoice file - missing information");
		}
		
	}



	// ################## upload  ##################	
	function _delete_html_invoice ($invoiceinfo)
	{

		
		$this->load->library('sftp');

			$config['hostname'] = $this->config->item('zowsftphostname');
			$config['username'] = $this->config->item('zowsftpusername');
			$config['password'] = $this->config->item('zowsftppassword');
			//$config['debug']	= TRUE;
		
			$sftp = new Net_SFTP($config['hostname']);
			     if (!$sftp->login($config['username'], $config['password'])) {
			         exit('Login Failed - cannot write Ogone url to ZOW site');
			     }
			

		//$this->ftp->delete_file('/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html');
		
		$sftp->delete('/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html');		
		$sftp->disconnect();

		
		$this->session->set_flashdata('invoiceinfo', $invoiceinfo);
		redirect('invoicing/zt2016_invoice_ogone_form/'.$invoiceinfo['InvoiceNumber'], 'refresh');
	}


}

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>