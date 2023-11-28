<?php

class Zt2016_csv_existing_invoice extends MY_Controller {


	
	function index(){


		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		
		//$this->load->helper(array('form','url','invoice','reports','financials'));
		$this->load->helper(array('form','url','invoice','reports'));
		
		//Read form input values
		$clientname= $this->uri->segment(3);
		
		 if (empty ($clientname)) {
			redirect('invoicing/zt2016_existing_invoices', 'refresh');
		 }
		
		$invoicenumber= $this->uri->segment(4);

		 if (empty ($invoicenumber)) {
			redirect('invoicing/zt2016_client_invoices/'.$clientname, 'refresh');
		 } 
		$clientname= str_replace('_', ' ', $clientname);
		$clientname=str_replace("~","&",$clientname);


		
		
		#get client data
		$this->load->model('zt2016_clients_model', '', TRUE);
		$clientdata = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientname));
		
		if(empty ( $clientdata)) {
			echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }
		
		
		#get invoice data
		$this->load->model('zt2016_invoices_model', '', TRUE);
		$invoicedata = $this->zt2016_invoices_model->GetInvoice($options = array('InvoiceNumber' => $invoicenumber));
		
		if(empty ($invoicedata)) {
			echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }
		

		#get invoice entries
		$this->load->model('trakentries', '', TRUE);
		$invoiceentries = $this->trakentries->GetEntry($options = array('Invoice' => $invoicenumber,'sortBy'=> 'DateOut','sortDirection'=> 'asc'));
		if(empty ($invoiceentries)) {
			echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }

		
		$this->load->dbutil();
					
		$StartDate=$invoicedata->StartDate;
		$EndDate=$invoicedata->EndDate;
		
		$StartDate = date('Y-m-d', strtotime('+1 day'.$StartDate));
		$EndDate = date('Y-m-d', strtotime($EndDate));
				
		
		$this->load->dbutil();

				
		$delimiter = ",";
		$newline = "\n";
		
		$data ="";


		//############### header
		$data .='"Invoice "'.$delimiter.'"'.$invoicenumber.'"'.$delimiter.'"for "'.$delimiter.'"'.$clientdata->CompanyName.'"';
		$data.=$newline;
		$data .='"From "'.$delimiter.'"'.$invoicedata->StartDate.'"'.$delimiter.'"to"'.$delimiter.'"'.$invoicedata->EndDate.'"';
		$data.=$newline;
		$data .='"Total Jobs "'.$delimiter.'"'.count($invoiceentries).'"';
		$data.=$newline;

		//############### Table
		$data .='"Date"'.$delimiter;
		$data .='"Originator"'.$delimiter;
		$data .='"File Name"'.$delimiter;
		$data .='"New Slides"'.$delimiter;
		$data .='"Edited Slides"'.$delimiter;
		$data .='"Hours"'.$delimiter;
		$data.=$newline;

		foreach ($invoiceentries as $row)
		{
			 $data .='"'.$row->DateOut.'"'.$delimiter;
			 $data .='"'.$row->Originator.'"'.$delimiter;
			 $data .='"'.$row->FileName.'"'.$delimiter;
			 $data .='"'.$row->NewSlides.'"'.$delimiter;
			 $data .='"'.$row->EditedSlides.'"'.$delimiter;
			 $data .='"'.$row->Hours.'"'.$delimiter;
			 $data.=$newline;
		}
		$data.=$newline;
		$data.=$newline;
		//############### Totals

		$data.="Subtotals".$delimiter.$delimiter.$delimiter.'"'.$invoicedata->SumNewSlides.'"'.$delimiter;
		$data.='"'.$invoicedata->SumEditedSlides.'"'.$delimiter.'"'.round($invoicedata->SumHours,2).'"'.$delimiter;	
		$data.=$newline;
		$data.="Subtotal hours".$delimiter.$delimiter.$delimiter.'"'.round($invoicedata->SumNewSlides/5,2).'"'.$delimiter;
		
		
		
		$data.='"'.round($invoicedata->SumEditedSlides/(5/$invoicedata->PriceEdits),2).'"'.$delimiter.'"'.round($invoicedata->SumHours,2).'"'.$delimiter;	
		$data.=$delimiter.'"'.round($invoicedata->BilledHours,2).'"'.$delimiter."Total hours";
		$data.=$newline;					
		$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$invoicedata->PricePerHour.'"'.$delimiter."Price".$delimiter.'"'.$invoicedata->Currency.'"';
		$data.=$newline;
		$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$invoicedata->InvoiceTotal.'"'.$delimiter."Subtotal".$delimiter.'"'.$invoicedata->Currency.'"';
		$data.=$newline;
		

		
		if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
			$vatrevenue = (float)str_replace(',', '', $invoicedata->InvoiceTotal);
			$VAT=$vatrevenue *21/100;
			$VATFormatted=number_format($VAT, 2, '.', ',');
			$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$VATFormatted.'"'.$delimiter."VAT".$delimiter.'"'.$invoicedata->Currency.'"';
			$data.=$newline;
			$totalwithVAT=number_format($VAT+$vatrevenue, 2, '.', ',');
			$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$totalwithVAT.'"'.$delimiter."Total with VAT".$delimiter.'"'.$invoicedata->Currency.'"';			
		}		

		//echo $data;
		$name = "ZOW_Invoice_".$invoicenumber.".csv";

		$this->load->helper('download');
		//"\xEF\xBB\xBF" sets downdload to utf encoding
		//https://stackoverflow.com/questions/33592518/how-can-i-setting-utf-8-to-csv-file-in-php-codeigniter
		force_download($name, "\xEF\xBB\xBF" . $data);

	}
	


	// ################## Get last invoice date ##################	
	function  _getDateLastInvoice($client)
	{

		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Invoice <>'NOT BILLED'");
		$query = $this->db->get();
		//echo $this->db->last_query();

		if ($query->row()->DateOut=="") {
			$this->db->select_min('DateOut');
			$this->db->from('zowtrakentries');
			$this->db->where("Client ='".$client."'"); 
			$query = $this->db->get();
			$StartDate = date('Y-m-d', strtotime('-1 day'.$query->row()->DateOut));
		}
		else
		{
		$StartDate =  $query->row()->DateOut;
		}
		return $StartDate;
	}


}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>