<?php

class Zt2016_invoice_sendemail extends MY_Controller {

	
	public function index()
	{
        $this->load->library('session');
        $data = $this->session->flashdata('sendemail');
        $this->load->model('zt2016_invoices_model');

        if ($data) {
        //   $invoiceEmail = array('status' => 'Not Send', 'invoice' => 'INV-23-0102-PHU', 'zowuser' => '','recipient' => 'invoices@zebraonwheels.com', 'cc' => 'demo@gmail.com,demo2@gmail.com ','pdf' => 'INV-23-0102-PHU' ) ;
		    $invoiceDBinfo=$this->zt2016_invoices_model->AddEmailData($data);
            if($data['status'] == 'send'){
                $this->session->set_flashdata('SuccessMessage', 'Email sent successfully to '.$data['cc']);
            }else{
                $this->session->set_flashdata('ErrorMessage', 'Email not sent successfully to '.$data['cc']);
            }
            
            redirect('invoicing/zt2016_invoicesendmail/'.$data['invoice'], 'refresh');
        }


	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>