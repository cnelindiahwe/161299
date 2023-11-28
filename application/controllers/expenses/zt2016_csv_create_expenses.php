<?php

class Zt2016_csv_create_expenses extends MY_Controller {


	
	function index(){


		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		
		//$this->load->helper(array('form','url','invoice','reports','financials'));
		$this->load->helper(array('form','url','invoice','reports'));
		
		$this->load->model('zt2016_expenses_model', '', TRUE);
		$expensesData = $this->zt2016_expenses_model->GetExpensesEntrie();

		
		$this->load->dbutil();
				
		$delimiter = ",";
		$newline = "\n";
		$data ="";

		// Data for the left section
		$leftSection = '';
		
		$data .= 'item,reference,date,category,amount,paidBy,status,Payment Amount,Payment Date,Remark'.$newline;
		
		// print_r($invoiceentries);
		foreach($expensesData as $expenses){
			$currency = $expenses->currency;
			if($currency == "USD"){
				$currencySymbol = '$';
			}else if($currency == 'EUR'){
				 $currencySymbol = 'EUR';
			}
			else if($currency == 'INR'){
				 $currencySymbol = 'INR';
			}
			$currencySymbol = mb_convert_encoding($currencySymbol, 'UTF-8');
			$data .= '"' . $expenses->item . '"' . $delimiter;
			$data .= '"' . $expenses->Reference . '"' . $delimiter;
			$data .= '"' . $expenses->purchaseDate . '"' . $delimiter;
			$data .= '"' . $expenses->Category . '"' . $delimiter;
			$data .= '"' . $currencySymbol." ".$expenses->amount. '"' . $delimiter;
			$data .= '"' . $expenses->paidBy . '"' . $delimiter;
			$data .= '"' . $expenses->status . '"' . $delimiter;
			$data .= '"' . $currencySymbol." ".$expenses->paymentAmount . '"' . $delimiter;
			$data .= '"' . $expenses->paymentDate . '"' . $delimiter;
			$data .= '"' . $expenses->Remark . '"' . $delimiter;
			$data  .= $newline;
			
		}

		$name = "ZOW_Expenses_Report.csv";

		$this->load->helper('download');
		// "\xEF\xBB\xBF" sets download to UTF encoding
		force_download($name, "\xEF\xBB\xBF" . $data);


	}	
	


}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>