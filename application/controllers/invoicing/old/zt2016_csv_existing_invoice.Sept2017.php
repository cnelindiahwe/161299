<?php

class Zt2016_csv_existing_invoice extends MY_Controller {


	
	function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form','url','invoice','reports','financials'));
		
		//Read form input values
		$client= $this->uri->segment(3);
			$client= str_replace('_', ' ', $client);
			//$client= str_replace('%20', ' ', $client);
			$client=str_replace("~","&",$client);
			$this->load->model('trakinvoices', '', TRUE);
		$invoice= $this->uri->segment(4);

		
		$StartDate='2010-06-01';
		
		$StartDate = date('Y-m-d', strtotime('+1 day'.$StartDate));
		$EndDate = date('Y-m-d', strtotime('now'));
				
		
			$this->load->dbutil();
			$this->load->model('trakentries', '', TRUE);
			$invoicedata = $this->trakentries->GetEntry($options = array('Invoice' => $invoice,'sortBy'=> 'DateOut','sortDirection'=> 'asc'));

			if($invoicedata)
			{
				$this->load->model('trakclients', '', TRUE);
				$clientdata = $this->trakclients->GetEntry($options = array('CompanyName' => $client));
				
				$delimiter = ",";
				$newline = "\n";
				
				$data ="";

				//############### Invoice Number
				$InvoiceNumber = $invoice;
				


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

				$invoicetotals=csvPastInvoiceTotals($clientdata,$InvoiceNumber);
				$data.="Subtotals".$delimiter.$delimiter.$delimiter.'"'.round($invoicetotals['newtotal'],2).'"'.$delimiter;
				$data.='"'.$invoicetotals['editstotal'].'"'.$delimiter.'"'.round($invoicetotals['hourstotal'],2).'"'.$delimiter;	
				$data.=$newline;
				$data.="Subtotal hours".$delimiter.$delimiter.$delimiter.'"'.round($invoicetotals['newslidehours'],2).'"'.$delimiter;
				$data.='"'.$invoicetotals['editshours'].'"'.$delimiter.'"'.round($invoicetotals['hourstotal'],2).'"'.$delimiter;	
				$data.=$delimiter.'"'.round($invoicetotals['billablehourstotal'],2).'"'.$delimiter."Total hours";
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