<?php

class Csvinvoice extends MY_Controller {


	
	function index()
	{
		
		$this->load->helper(array('form','url','invoice','reports','financials'));
		
		//Read form input values
		$client= $this->uri->segment(3);
		$client= str_replace('_', ' ', $client);
		$client= str_replace('%20', ' ', $client);
		$client=str_replace("~","&",$client);
		$this->load->model('trakinvoices', '', TRUE);
		
		//$StartDate =$this->trakinvoices->_getDateLastInvoice($client);
		
		//If no previous invoice
		//if ($StartDate==""){$StartDate='2010-06-01';}
		
		$StartDate='2010-06-01';
		
		$StartDate = date('Y-m-d', strtotime('+1 day'.$StartDate));
		$EndDate = date('Y-m-d', strtotime('now'));
		
		//Load excluded entries list
		$excludelist = array();
		if ($this->session->flashdata('excludelist')){
			$excludelist = explode(",",$this->session->flashdata('excludelist'));
		} 		
		
			$this->load->dbutil();
			$this->load->model('trakentries', '', TRUE);
			$invoicedata = $this->trakentries->GetEntryRange(array('Client'=>$client,'Status'=>'COMPLETED'),$StartDate,$EndDate);
			if($invoicedata)
			{
				$this->load->model('trakclients', '', TRUE);
				$clientdata = $this->trakclients->GetEntry($options = array('CompanyName' => $client));
				
				$delimiter = ",";
				$newline = "\n";
				
				$data ="";

				//############### Invoice Number
				$InvoiceNumber = $clientdata->ClientCode;
//$InvoiceNumber = $query->row()->ClientCode;
		//$InvoiceNumber .= date("Ym");
		//$TestInvoiceNumber .= $InvoiceNumber.'01';
		
		$InvoiceCount=0;
		$finished = false;
		while (! $finished)
		{ 
			$InvoiceCount+=1;
			if ($InvoiceCount<10) {
				$TestInvoiceNumber = $InvoiceNumber.date("mY").'0'.$InvoiceCount;
			}
			else{
				$TestInvoiceNumber = $InvoiceNumber.date("mY").$InvoiceCount;
			}
			$this->db->select('Invoice');
			$this->db->from('zowtrakentries');
			$this->db->limit(1);
			$this->db->where("Invoice ='".$TestInvoiceNumber ."'"); 
			$query = $this->db->get();
			if ($query->num_rows()==0) { $finished = true;   }
		}
		$InvoiceNumber=$TestInvoiceNumber;

				//############### header
				$data .='"Invoice "'.$delimiter.'"'.$InvoiceNumber.'"'.$delimiter.'"for "'.$delimiter.'"'.$clientdata->CompanyName.'"';
				$data.=$newline;
				$data .='"From "'.$delimiter.'"'.$StartDate.'"'.$delimiter.'"to"'.$delimiter.'"'.$EndDate.'"';
				$data.=$newline;
				$data .='"Total Jobs "'.$delimiter.'"'.count($invoicedata).'"';
				$data.=$newline;

				//############### Table
				 $data .='"Date"'.$delimiter;
				 $data .='"Originator"'.$delimiter;
				 $data .='"File Name"'.$delimiter;
				 $data .='"New Slides"'.$delimiter;
				 $data .='"Edited Slides"'.$delimiter;
				 $data .='"Hours"'.$delimiter;
				 $data.=$newline;

					foreach ($invoicedata as $row)
					{
						if (!in_array( $row->id,$excludelist))	{
							 $data .='"'.$row->DateOut.'"'.$delimiter;
							 $data .='"'.$row->Originator.'"'.$delimiter;
							 $data .='"'.$row->FileName.'"'.$delimiter;
							 $data .='"'.$row->NewSlides.'"'.$delimiter;
							 $data .='"'.$row->EditedSlides.'"'.$delimiter;
							 $data .='"'.$row->Hours.'"'.$delimiter;
							 $data.=$newline;
						}
					}
					$data.=$newline;
					$data.=$newline;
				//############### Totals

					$invoicetotals=csvInvoiceTotals($clientdata,$StartDate,$EndDate,$excludelist);

				$data.="Subtotals".$delimiter.$delimiter.$delimiter.'"'.$invoicetotals['newtotal'].'"'.$delimiter;
				$data.='"'.$invoicetotals['editstotal'].'"'.$delimiter.'"'.$invoicetotals['hourstotal'].'"'.$delimiter;	
				$data.=$newline;
				$data.="Subtotal hours".$delimiter.$delimiter.$delimiter.'"'.$invoicetotals['newslidehours'].'"'.$delimiter;
				$data.='"'.$invoicetotals['editshours'].'"'.$delimiter.'"'.$invoicetotals['hourstotal'].'"'.$delimiter;	
				$data.=$delimiter.'"'.$invoicetotals['billablehourstotal'].'"'.$delimiter."Total hours";
				$data.=$newline;					
				$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$invoicetotals['price'].'"'.$delimiter."Price".$delimiter.'"'.$invoicetotals['currency'].'"';
				$data.=$newline;
				$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$invoicetotals['revenue'].'"'.$delimiter."Subtotal".$delimiter.'"'.$invoicetotals['currency'].'"';
				$data.=$newline;
				
				if ($invoicetotals['VAT'])	{
					$vatrevenue = (float)str_replace(',', '', $invoicetotals['revenue']);
					$VAT=$vatrevenue *21/100;
					$VATFormatted=number_format($VAT, 2, '.', ',');
					$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$VATFormatted.'"'.$delimiter."VAT".$delimiter.'"'.$invoicetotals['currency'].'"';
					$data.=$newline;
					$totalwithVAT=number_format($VAT+$vatrevenue, 2, '.', ',');
					$data.=$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.$delimiter.'"'.$totalwithVAT.'"'.$delimiter."Total with VAT".$delimiter.'"'.$invoicetotals['currency'].'"';			
				}		
				
				
				
					
				//echo $data;
				$name = "ZOW_Invoice_".$InvoiceNumber.".csv";

				$this->load->helper('download');
				force_download($name, $data);
			}
			else {
				echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			}
	

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