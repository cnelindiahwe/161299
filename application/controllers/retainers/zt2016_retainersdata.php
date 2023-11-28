<?php
class Zt2016_retainersdata extends MY_Controller {
    public function index()
	{
        $retainersData = $_POST;

        $this->load->model('zt2016_retainersmodal', '', TRUE);

        if(isset($_POST['submit'])){
            $RetainersDBinfo=$this->zt2016_retainersmodal->addretainersdate($retainersData);
    
            if($RetainersDBinfo == 'insert'){
                $Message = 'Added Successfully';
            }
            else{
                $Message = 'Update Successfully';
            }
            $this->session->set_flashdata('SuccessMessage',$Message);
            redirect('/retainers/zt2016_retainers');
        }
        else if(isset($_POST['report']))
        {
            $this->session->set_flashdata('Reportdata',$retainersData);
            redirect('/retainers/zt2016_csv_create_report');
        }

        
    }
}
?>