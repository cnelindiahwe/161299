<?php

class Zt2016_expensesdata extends MY_Controller {

	
	public function index()
	{
        $this->load->model('zowindia_expenses_model');

	    $expenses =$this->input->post();
       
        if($_FILES['attach']['tmp_name']){
            $randomFileName = $this->generateRandomFileName($_FILES["attach"]["name"]);
            $expenses['attch'] = $randomFileName;
        }
       

       
		
			
        if(isset($expenses['submit'])){

            $expensesDBinfo=$this->zowindia_expenses_model->addexpenses($expenses);

            if($expensesDBinfo){
                $Message="Added Expenses";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                #image upload 
                if (isset($_FILES['attach']) && !empty($_FILES['attach']['name'])) {
                    $config['upload_path'] = './pdfs/expenses/'; 
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf'; 
                    $config['max_size'] = 2048;
                    $config['file_name'] = $randomFileName;
            
                    $this->load->library('upload', $config);
            
                    if ($this->upload->do_upload('attach')) {
                        $uploadData = $this->upload->data();
                        $expenses['file'] = $uploadData['file_name'];
                    } else {
                        $error = array('error' => $this->upload->display_errors());
                        $this->session->set_flashdata('ErrorMessage', $error['error']);
                        redirect('/zowindia_expenses/zt2016_expenses');
                    }
                }
                redirect('/zowindia_expenses/zt2016_expenses');
            }else{
                $Message="There was an error add expenses, which has not been added.";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/zowindia_expenses/zt2016_expenses');
            }
            
            
            
        }
        if(isset($expenses['update'])){
           
            $expensesDBinfo=$this->zowindia_expenses_model->UpdateExpensesEntrie($expenses);

            if($expensesDBinfo){
                $Message="Update Expenses";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                #image upload 
                if (isset($_FILES['attach']) && !empty($_FILES['attach']['name'])) {
                    $config['upload_path'] = './pdfs/expenses/'; 
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf'; 
                    $config['max_size'] = 2048;
                    $config['file_name'] = $randomFileName;
            
                    $this->load->library('upload', $config);
            
                    if ($this->upload->do_upload('attach')) {
                        $uploadData = $this->upload->data();
                        $expenses['file'] = $uploadData['file_name'];
                    } else {
                        $error = array('error' => $this->upload->display_errors());
                        $this->session->set_flashdata('ErrorMessage', $error['error']);
                        redirect('/zowindia_expenses/zt2016_expenses');
                    }
                }
                redirect('/zowindia_expenses/zt2016_expenses');
            }else{
                $Message="The data in the form below is the same as the one on the DB. Expenses not updated.";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/zowindia_expenses/zt2016_expenses');
            }
            
            
            
        }
        if(isset($expenses['delete'])){
           

            $expensesDBinfo=$this->zowindia_expenses_model->DeleteExpensesEntrie($expenses);

            if($expensesDBinfo){
                $Message="Delete Expenses";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                redirect('/zowindia_expenses/zt2016_expenses');
            }else{
                $Message="The data in the form below is the same as the one on the DB. Status not updated."; 
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/zowindia_expenses/zt2016_expenses');
            }
            
            
            
        }
        if(isset($expenses['action'])){
           

            $expensesDBinfo=$this->zowindia_expenses_model->UpdateExpensesEntrie($expenses);

            if($expensesDBinfo){
                $Message="Status Updated";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                // redirect('/expenses/zt2016_expenses');
            }else{
                $Message="There was an error delete expenses, which has not been delete.";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                // redirect('/expenses/zt2016_expenses');
            }
            
            
            
        }
        if(isset($expenses['filter'])){
           
            if($expenses['paidBy']== 'Paid By'){
                $expenses['paidBy'] = '';
            }
           
            if($expenses['Category']== 'Category'){
                $expenses['Category'] = '';
            }
            $expensesDBinfo=$this->zowindia_expenses_model->GetExpensesEntrie($expenses);
         

            if($expensesDBinfo){
                $Message="Filter records Show";  
                $this->session->set_flashdata('expensesData',$expensesDBinfo);
                redirect('/zowindia_expenses/zt2016_expenses');
            }else{
                $Message="no data found";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/zowindia_expenses/zt2016_expenses');
            }
            
            
            
        }
                    	
		

	}
    function generateRandomFileName($originalFileName) {
        $finished = true;
        while($finished){
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $randomName = uniqid() . '_' . mt_rand(1000, 9999);
            $filename = $randomName.'.'.$extension;
            $this->db->select('attch');
			$this->db->from('zowexpensesentries');
			$this->db->where("attch ='".$filename."'"); 
			$query = $this->db->get();
			if ($query->num_rows()==0) { $finished = false;   }
        }
      
        return $filename;
    }

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>