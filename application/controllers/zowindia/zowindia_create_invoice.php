<?php

class Zowindia_create_invoice extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
			 
		$templateData['title'] = 'ZowIndia New Invoice';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page(); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	


// ################## create page ##################	
	function   _display_page ()
	{

		$page_content ='<div class="page_content">'."\n";

		
        if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}
		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}


				
		########## panel head
		 		//Invoice status form
		//$attributes='class="form-inline" id="invoice-status-form"';
		$attributes='id="zowindia-form"';
		$formurl=site_url().'zowindia/zowindia_invoice_data/';

        $InvoiceNumber = $this->getinvoicenumber();
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h3 class="panel-title">';
		$page_content.= "ZowIndia Invoice (".$InvoiceNumber.")";
		$page_content.=' </h3>';
		
		######### buttons
		$page_content.= "<p class='top-buffer-10'>";
		
        # submit button
        $ndata = array('class' => 'submitButton btn btn-success btn-sm','value' => 'Submit','name'=>'submit');
        $page_content .= form_submit($ndata)."\n";

		$page_content.= '</p>'."\n";
		
		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
			
       
   

		$page_content .='<div class="row">'; //row 
        $page_content.='<div><label for="client">Client </label> <input class="form-control" id="client" type="text" name="client" required="true"/></div>';
        // $page_content.='<div><label for="client">Address </label> <textarea class="form-control" id="client" type="text" name="address" required="true"></textarea></div>';
        $page_content.='<div><label for="client">DESCRIPTION </label> <textarea class="form-control" id="description" type="text" name="description" required="true"></textarea></div>';
        $page_content.='<div><label for="client">Currency </label> <select class="form-control" id="currency" type="text" name="currency" required="true"><option>INR</option><option>USD</option><option>EUR</option></select></div>';
        $page_content.='<div><label for="client">HOUR</label> <input class="form-control" id="hour" type="number" name="hour" step="0.1" /></div>';
        $page_content.='<div><label for="client">RATE</label> <input class="form-control" id="rate" type="number" name="rate" step="0.1"/></div>';
        $page_content.='<div><label for="client">AMOUNT</label> <input class="form-control" id="amount" readonly type="text" name="amount" required="true"/></div>';
        $page_content.='<div><input class="form-control" id="client" type="hidden" name="invoiceNumber" value="'.$InvoiceNumber.'" /></div>';
        $page_content.='</div>';


		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}
    function getinvoicenumber(){
        $finished = false;
        $quotationCount = 100;
        
        while (!$finished)
        { 
            $quotationCount+=1;
            $formattedResult = str_pad($quotationCount, 4, '0', STR_PAD_LEFT);
            $Year = date("y");
            
           $quotationnumber = $Year.'-'.$formattedResult.'-ZOW';
    
            $this->db->select('invoiceNumber');
            $this->db->from('zowindiainvoice');
            //$this->db->limit(1);
            $this->db->where("invoiceNumber ='".$quotationnumber ."'"); 
            $query = $this->db->get();
            if ($query->num_rows()==0) { $finished = true;   }
        }
        $InvoiceNumberhwe=$quotationnumber;
        
    
        return $InvoiceNumberhwe;
    }
	
	
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>