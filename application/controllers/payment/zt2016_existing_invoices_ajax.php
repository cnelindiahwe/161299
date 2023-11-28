<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_existing_invoices_ajax extends MY_Controller {


	public function index()
	{
 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions'));

		$zowuser=_superuseronly(); 

 
		$this->load->model('zt2016_invoices_model','','TRUE');
		//$InvoiceTableRaw =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','sortBy'=>'BilledDate','sortDirection'=>'DESC','Client' =>'Canidae'));
		$InvoiceTableRaw =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','sortBy'=>'BilledDate','sortDirection'=>'DESC'));
	
		$InvoiceTableFinal = array();
		
		foreach ($InvoiceTableRaw as $index=>$Invoice){
			$InvoiceTableFinal[$index][]='<a href="'.site_url().'invoicing/zt2016_view_invoice/'.$Invoice->InvoiceNumber.'">'.$Invoice->InvoiceNumber.'</a>';

			$safeclientName=str_replace(" ", "_", $Invoice->Client);
			$safeclientName=str_replace("&", "~", $safeclientName);

			$InvoiceTableFinal[$index][]='<a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$Invoice->Client.'</a>';
			$InvoiceTableFinal[$index][]=date('j-M-Y', strtotime($Invoice->BilledDate));
			$InvoiceTableFinal[$index][]=date('j-M-Y', strtotime($Invoice->DueDate));
			$InvoiceTableFinal[$index][]=date('j-M-Y', strtotime($Invoice->PaidDate));
			$InvoiceTableFinal[$index][]=number_format($Invoice->InvoiceTotal,2);
			$InvoiceTableFinal[$index][]=number_format($Invoice->Paidamount,2);
			$InvoiceTableFinal[$index][]=$Invoice->Status;
			$InvoiceTableFinal[$index][]=$Invoice->sendingStatus;
		}
		
		echo "{";
		echo '"data": ';
		echo json_encode($InvoiceTableFinal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		echo"}";
    	
	}
	
}