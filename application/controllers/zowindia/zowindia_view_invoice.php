<?php

class Zowindia_view_invoice extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
			 
		$invoicenumber=$this->uri->segment(3);

		$templateData['title'] = 'ZowIndia View Invoice';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($invoicenumber); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	


// ################## create page ##################	
	function   _display_page ($invoicenumber)
	{
		$this->load->model('zowindiainvoice', '', TRUE);
        $invoicedata = $this->zowindiainvoice->getinvoice($options = array('invoiceNumber' => $invoicenumber));
		$page_content ='<div class="page_content">'."\n";
// print_r($invoicedata);die;
		
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

  
		
		$page_content.=form_open($formurl,$attributes )."\n";
		
		$page_content.='<div id="client_info_panel" class="panel panel-primary"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		$page_content.=' <h3 class="panel-title">';
		$page_content.= "Invoice ".$invoicedata->invoiceNumber." for ".$invoicedata->client;
		$page_content.=' </h3>';
		
		######### buttons
		$page_content.= "<div class='d-flex justify-content-between'><p class='top-buffer-10'>";
		
        # submit button
        $ndata = array('class' => 'submitButton btn btn-success btn-sm','value' => 'Update','name' =>'update');
        $page_content .= form_submit($ndata)."\n";

		$page_content.= '</p>'."\n";

		$page_content.='<div class="btn-toolbar top-buffer-10"><a href="'.site_url().'zowindia/zowindia_pdf_existing/'.$invoicenumber.'" class="btn btn-sm btn-success pull-right" style="height: fit-content;"  target="_blank">PDF</a></div>'."\n";
		
		$page_content.= '</div></div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
			
       
	
		$currency = array('INR','USD','EUR');
		$page_content .='<div class="row">'; //row 
        $page_content.='<div><label for="client">Client </label> <input class="form-control" id="client" type="text" name="client" required="true" readonly value="'.$invoicedata->client.'"/></div>';
        // $page_content.='<div><label for="client">Address </label> <textarea class="form-control" id="client" type="text" name="address" required="true">'.$invoicedata->address.'</textarea></div>';
        $page_content.='<div><label for="client">DESCRIPTION </label> <textarea class="form-control" id="description" type="text" name="description" required="true">'.$invoicedata->description.'</textarea></div>';
        $page_content.='<div><label for="client">Currency </label> <select class="form-control" id="currency" type="text" name="currency" required="true">';
		foreach($currency as $curr){
			if($invoicedata->currency == $curr){
				$page_content.='<option selected>'.$curr.'</option>';
			}
			else{
				$page_content.='<option>'.$curr.'</option>';
			}
		}
		$page_content.='</select></div>';
		$page_content.='<div><label for="client">HOUR</label> <input class="form-control" id="hour" type="number" name="hour" value="'.$invoicedata->hour.'" step="0.1" /></div>';
        $page_content.='<div><label for="client">RATE</label> <input class="form-control" id="rate" type="number" name="rate" value="'.$invoicedata->rate.'" step="0.1" /></div>';
        $page_content.='<div><label for="client">AMOUNT</label> <input class="form-control" id="amount" readonly type="text" name="amount" required="true" value="'.$invoicedata->amount.'"/></div>';
        $page_content.='<div><input class="form-control" id="client" type="hidden" name="invoiceNumber" value="'.$invoicedata->invoiceNumber.'" /></div>';
        $page_content.='</div>';


		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";
		$page_content .='</div><!-- // class="page_content" -->'."\n";


		return $page_content;
		
	}
	
	
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>