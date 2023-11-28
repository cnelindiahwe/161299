<?php

class Zt2016_estimateDataSave extends MY_Controller {

	
	public function index()
	{
		$estimateData =$_POST;
      

        if(isset($estimateData['submit'])){
           
            $allInputValues = array();
            $rowCount = $estimateData['rowcount'];
            $inputValues = [];

            for ($i=0; $i < $rowCount; $i++) { 
                $estimateData['items'][$i]['date'] = $estimateData['date'][$i];
                $estimateData['items'][$i]['originator'] = $estimateData['originator'][$i];
                $estimateData['items'][$i]['filename'] = $estimateData['filename'][$i];
                $estimateData['items'][$i]['newslides'] = $estimateData['newslides'][$i];
                $estimateData['items'][$i]['editslides'] = $estimateData['editslides'][$i];
                $estimateData['items'][$i]['hour'] = $estimateData['hour'][$i];
            }
        //   print_r($estimateData);
        //   die;
            $estimateData['estimateTotal'] = $estimateData['PerHourvalue'];
            $this->load->model('zt2016_estimateModal');
            $estimateDBinfo=$this->zt2016_estimateModal->addestimate($estimateData);
        
    
                if($estimateDBinfo){
                    $Message="Added Estimate";  
                    $this->session->set_flashdata('SuccessMessage',$Message);
                    redirect('/estimate/zt2016_estimate');
                }else{
                    $Message="There was an error add payment, which has not been added.";  
                    $this->session->set_flashdata('ErrorMessage',$Message);
                    redirect('/estimate/zt2016_estimate');
                }
                            
        }
        else if(isset($estimateData['update'])){
            $allInputValues = array();
            $rowCount = $estimateData['rowcount'];
            $inputValues = [];
            $h=0;
            for ($i=0; $i < $rowCount; $i++) { 
                $estimateData['items'][$i]['date'] = $estimateData['date'][$i];
                $estimateData['items'][$i]['originator'] = $estimateData['originator'][$i];
                $estimateData['items'][$i]['filename'] = $estimateData['filename'][$i];
                $estimateData['items'][$i]['newslides'] = $estimateData['newslides'][$i];
                $estimateData['items'][$i]['editslides'] = $estimateData['editslides'][$i];
                $estimateData['items'][$i]['hour'] = $estimateData['hour'][$i];
            }
            foreach($estimateData['id'] as $item){
                $estimateData['items'][$h]['id'] = $item;
                $h++;
            }
            $estimateData['estimateTotal'] = $estimateData['PerHourvalue'];
            // print_r($estimateData);
            // die;
            $this->load->model('zt2016_estimateModal');
            $estimateDBinfo=$this->zt2016_estimateModal->updateestimate($estimateData);
        

            if($estimateDBinfo){
                $Message="Updated Estimate";  
                $this->session->set_flashdata('SuccessMessage',$Message);
                redirect('/estimate/zt2016_edit_estimate/'.$estimateData['quotationNumber']);
            }else{
                $Message="There was an error updated, which has not been updated.";  
                $this->session->set_flashdata('ErrorMessage',$Message);
                redirect('/estimate/zt2016_edit_estimate/'.$estimateData['quotationNumber']);
            }
        }
        else if(isset($estimateData['action'])){
            $code = $estimateData['code'];
            
            $estimateArray = array();
            # retrieve active client contacts from db
            $this->load->model('zt2016_contacts_model', '', true);
            $ActiveClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $estimateData['client'], 'Active' => '1', 'sortBy' => 'FirstName', 'sortDirection' => 'Asc'));
              
            // $contacts = array();
            foreach($ActiveClientContacts as $contacts){
                $contactslist[] = $contacts->FirstName." ".$contacts->LastName;
            }
           
            $finished = false;
            $quotationCount = 100;
            
        
            $quotationCount += 1;
            $formattedResult = str_pad($quotationCount, 4, '0', STR_PAD_LEFT);
            $Year = date("y");

            $lastQuotationNumber = 'QTN-' . $Year . '-' . $formattedResult . '-'.$code;

            $this->db->select('quotationNumber');
            $this->db->from('zowestimate');
            $this->db->order_by('id', 'DESC'); // Assuming 'id' is the primary key
            $this->db->limit(1);

            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                $lastRow = $query->row();
                $lastQuotationNumber = $lastRow->quotationNumber;
                $lastCounter = (int)substr($lastQuotationNumber, 8, 4); // Extract the counter from the lastQuotationNumber
                $quotationCount = $lastCounter + 1;
            }

            $nextFormattedResult = str_pad($quotationCount, 4, '0', STR_PAD_LEFT);
            $quotationnumber = 'QTN-' . $Year . '-' . $nextFormattedResult . '-'.$code;

            
            
        
            // $InvoiceNumberhwe=$quotationnumber;

            $estimateArray['contactlist']= $contactslist;
            $estimateArray['quotation']= $quotationnumber;

            echo json_encode($estimateArray);
        

        }

            
            
        // }
                    	
		

	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>