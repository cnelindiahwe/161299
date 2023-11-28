<?php

class Zt2016_paidamount extends MY_Controller {

	
	public function index()
	{
		$payment =$_POST;
		$InvoiceDate= $payment['PaidDate'];
		$paidAmount= $payment['paidAmount'];
		$invoice= $payment['invoice'];
		$this->load->model('zt2016_payment_model');
		

        $this->load->model('zt2016_invoices_model');

        $paidstatus = $this->zt2016_invoices_model->paidamount($options =  array('invoice'=>$invoice,'status' => 'Partially Paid','invoicedate' => $InvoiceDate, 'paidAmount'=>$paidAmount));
        if($paidstatus){
            return true;
        }
		

	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>