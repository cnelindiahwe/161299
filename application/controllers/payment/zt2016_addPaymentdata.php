<?php

class Zt2016_addPaymentdata extends MY_Controller {

	
	public function index()
	{
		$payment =$_POST;
		$this->load->model('zt2016_payment_model');
			
        if(isset($payment['submit'])){
            
            $paymentDBinfo=$this->zt2016_payment_model->addpayment($payment);

            if($paymentDBinfo){
                $Message="Added Payment";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                redirect('/payment/zt2016_addpayment');
            }else{
                $Message="There was an error add payment, which has not been added.";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/payment/zt2016_addpayment');
            }
            
            
            
        }
                    	
		

	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>