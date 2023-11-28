<?php
class zowindia_invoice_data extends MY_Controller{
    function index(){
        $data = $_POST;

        $this->load->model('zowindiainvoice', '', TRUE);

        if(isset($_POST['submit'])){
            $created_invoice = $this->zowindiainvoice->addinvoice($data); 

            if($created_invoice){
                $Message="Added invoice";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                redirect('/zowindia/zowindia_invoice');
            }
        }
        if(isset($_POST['update'])){
            $created_invoice = $this->zowindiainvoice->updateinvoice($data);
           
                $Message="Updated invoice";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                redirect('/zowindia/zowindia_invoice');
           
        }

        

       


    }
}
?>
