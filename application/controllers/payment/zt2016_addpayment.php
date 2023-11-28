<?php

class zt2016_addpayment extends MY_Controller {

	
	function index()
	{
        
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		


		$templateData['title'] = 'Add Payment';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _new_client_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _new_client_page($ZOWuser)
	{

		$this->load->model('globalSettingModal', '', TRUE);
		$globalData = $this->globalSettingModal->GetGlobalSetting();

        $this->load->model('trakclients', '', TRUE);
        $clientInfo = $this->trakclients->GetEntry();

       

		$FormData = array();

		$FormData['ID']= 1;	
		
		$CountriesList= array();
		

		$TimezonesList = generate_timezone_array();

		#Create page.
			$page_content=$this->_display_page($ZOWuser,$clientInfo,$TimezonesList,$globalData,$FormData);

		return $page_content;


	
	}
    function   _display_page ($ZOWuser,$clientInfo,$TimezonesList,$globalData,$FormData)
	{
		$GroupData=$FormData;

		$page_content ='<div class="page_content">'."\n";

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" >'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";

		}

        ######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}
				
		########## panel head
		 //Payment 
         $formurl=site_url().'payment/zt2016_addPaymentdata/';
        //  $formurl='';
       
         $page_content.=form_open($formurl,$attributes )."\n";
         
         
         $page_content.='<div id="client_info_panel" class="panel panel-default" >'."\n";
         $page_content.='<div class="panel-heading">'."\n";
         $page_content.=' <h4>';
         
         $page_content.= "Add Payment";
         
         $page_content.=' </h4><div class="row p-3"><div class="col-sm-4 ">';
         
         $ndata = array('class' => 'submitButton btn btn-primary col-sm-8 mt-3 clinet_submit_button','value' => 'Add','name'=>'submit');
     
         $page_content .= form_submit($ndata)."\n";
         $page_content.= '</div></div></div>'."\n";
         $page_content.= form_hidden('invoice', $this->createPaymnetInvoice())."\n";
 
         ########## panel body
         $page_content.='<div class="panel-body"><div class="row" style="justify-content: center;">'."\n";
         $page_content.='<div class="row clinet p-1">'."\n";
 
         $page_content.= '<label>Client</label><select name="client" required="true" style="height: 44px;">';
         foreach($clientInfo as $client){
            $clientName = $client->ClientContact;
            if(!$clientName){
                $clientName = $client->CompanyName;
                
            }
            $page_content.= '<option>'.$clientName.'</option>';
         }
         $page_content .= '</select>';
         $page_content.= '<label>Payment Type</label>';
         $page_content.= '<select style="height: 44px;" name="payment_type"><option>Paypel</option><option>Bank Transfer</option><option>Online Banking</option><option>Others Method\'s</option></select>';
         $page_content.= '<label>Payment Status</label>';
         $page_content.= '<select style="height: 44px;" name="paymnet_status"><option>Fully</option><option>Pastlie</option></select>';
         $page_content.= '<label>Amount</label><input style="height: 44px;" type="text" name="amount"></input>';
      
 
         // $page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo,$GroupData);
 
         $page_content.='</div></div></div>'.'<!-- // class="panel-body" -->'."\n";
         $page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}	

	function createPaymnetInvoice(){
		$finished = false;
		$InvoiceCount = 0;
		
		while (! $finished)
		{ 
			$InvoiceCount+=1;
			$formattedResult = str_pad($InvoiceCount, 4, '0', STR_PAD_LEFT);
			
		    $TestInvoiceNumber = 'INV-'.$formattedResult;

			$this->db->select('invoice');
			$this->db->from('zowpaymententries');
			//$this->db->limit(1);
			$this->db->where("invoice ='".$TestInvoiceNumber ."'"); 
			$query = $this->db->get();
			if ($query->num_rows()==0) { $finished = true;   }
		}
		$InvoiceNumberhwe=$TestInvoiceNumber;
		

		return $InvoiceNumberhwe;
	}
	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>