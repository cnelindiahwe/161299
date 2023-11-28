<?php

class Zt2016_invoice_discount extends MY_Controller {

	
	public function index()
	{
		$invoice =$_POST;


			
			$this->load->model('zt2016_invoices_model');
			
                    if(isset($invoice['add_dis'])){
                        $invoiceDBinfo=$this->zt2016_invoices_model->AddDiscount($invoice);
                        $currency = $invoice['currency'];
                        if($currency == "USD"){
                            $currencySymbol = '$';
                        }else if($currency == 'EUR'){
                            $currencySymbol = '€';
                        }

                        //add dicount entry
                        $invoice['discount'] = $currencySymbol." ".number_format($invoice['discount'],2);
                        $invoice['status'] = 'Discount applies';
                        $invoiceDBEntry=$this->zt2016_invoices_model->AddDiscountEntery($invoice);

                        if($invoiceDBinfo)	{
                
                            $Message='Added Discount ';						
                            $this->session->set_flashdata('SuccessMessage',$Message);
                            //die( $Message);
                                
                                
                            redirect('/invoicing/zt2016_view_invoice/'.$invoice['Invoice']);
                        }
                        else{
                            $Message="There was an error add discount, which has not been added.";  
                            $this->session->set_flashdata('ErrorMessage',$Message);
                            redirect('/invoicing/zt2016_view_invoice/'.$invoice['Invoice']);
                        }
                    }
                    else if(isset($invoice['delete_dis'])){
                       
                        $currency = $invoice['currency'];
                        if($currency == "USD"){
                            $currencySymbol = '$';
                        }else if($currency == 'EUR'){
                            $currencySymbol = '€';
                        }
                        //add dicount entry
                        $invoice['discount'] = "-".$currencySymbol." ".$invoice['discount'];
                        $invoice['status'] = 'Discount removed';
                        $invoiceDBEntry=$this->zt2016_invoices_model->AddDiscountEntery($invoice);

                        $invoice['discount'] = 0;
                        $invoiceDBinfo=$this->zt2016_invoices_model->AddDiscount($invoice);

                        if($invoiceDBinfo)	{
                
                            $Message='Remove Discount ';						
                            $this->session->set_flashdata('SuccessMessage',$Message);
                            //die( $Message);
                                
                                
                            redirect('/invoicing/zt2016_view_invoice/'.$invoice['Invoice']);
                        }
                        else{
                            $Message="There was an error add discount, which has not been added.";  
                            $this->session->set_flashdata('ErrorMessage',$Message);
                            redirect('/invoicing/zt2016_view_invoice/'.$invoice['Invoice']);
                        }

                    }

					
            
                    
                   

					
				
	
		
		
		

	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>