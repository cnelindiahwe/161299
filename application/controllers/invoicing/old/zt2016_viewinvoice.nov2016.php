<?php

class Zt2016_viewinvoice extends MY_Controller {

	
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
			redirect('invoicing/zt2016_invoices', 'refresh');
		 }

		$templateData['title'] = 'View Invoice '.$invoicenumber;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _getinvoiceContent($invoicenumber); 

		$templateData['ZOWuser']=_getCurrentUser();

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    // ################## Provides invoice content . ##################	
	function  _getinvoiceContent($invoicenumber)
	{
		$clientInfo= $this->_getinvoiceclientname($invoicenumber);

		$this->load->model('zt2016_invoices_model','','TRUE');
		$invoiceTotals=$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','InvoiceNumber'=>$invoicenumber,));

		$pageOutput = zt2016_InvoiceTotalsByNumber($invoiceTotals,$clientInfo);
		
		
		return $pageOutput;/**/
	}
	
    // ################## Provides client name from invoice. ##################	
	function  _getinvoiceclientname($invoicenumber)
	{
		echo " ";
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


		$this->load->model('trakclients', '', TRUE);
		$clientinfo = $this->trakclients->GetEntry($options = array('ClientCode' => $clientcode));
		
		return $clientinfo ;
	}

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/invoicing/zt2016_viewinvoice .php */
?>