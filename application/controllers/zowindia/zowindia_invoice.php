<?php

class zowindia_invoice extends MY_Controller {

	public function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));

		$zowuser=_superuseronly(); 

		$this->load->model('zt2016_invoices_model','','TRUE');

	
	
	
		$templateData['title'] = 'Zow India Invoice';	

        $this->load->model('zt2016_retainersmodal', '', TRUE);
        #get client data
        $this->load->model('zowindiainvoice', '', TRUE);
        $invoicedata = $this->zowindiainvoice->getinvoice();
		
        $templateData['Fastflag']='';

		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_page($templateData,$invoicedata); 
		$templateData['ZOWuser']=_getCurrentUser();
        
		
		$this->load->view('admin_temp/main_temp',$templateData); 

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contact lists content
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_contact_lists_content
	 * Gather content
	 */
	function _display_page($templateData,$clientdata) {
		

		$page_content ='<div class="page_content">';

		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}		
		$page_content .= $this->_get_existing_invoices_table($templateData,$clientdata)."\n";
		$page_content .='</div>'."\n";
		return 	$page_content;		
		}





	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get existing invoice table
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_existing_invoices_table
	 * build existing (past) invoices table
	 */
    function _get_existing_invoices_table($templateData,$clientdata) {
				
        // print_r($clientdata);
        // die;
        
        $page_content='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="invoices_table">'."\n";
        $page_content.="<thead>"."\n";
        $page_content.="<tr><th data-sortable=\"true\">InvoiceNumber</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Rate</th><th data-sortable=\"true\">Amount</th>"."\n";
        $page_content.="</thead>"."\n";
        // $page_content.="<tfoot>"."\n";
        // $page_content.="<tr><th data-sortable=\"true\">InvoiceNumber</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Rate</th><th data-sortable=\"true\">Amount</th></tr>"."\n";
        // $page_content.="</tfoot>"."\n";		
        $page_content.="<tbody>"."\n";
        
    
        
        if ($templateData['Fastflag']==''){
        
            foreach ($clientdata as $client){
              
               
                $page_content.= '  <tr>';

                $page_content.= '<td>  <a href=" '.site_url().'zowindia/zowindia_view_invoice/'.$client->invoiceNumber.'">'.$client->invoiceNumber.'</a></td>'."\n";
                $page_content.= '<td>'.$client->client.'</td>'."\n";
                $page_content.= '<td>'.$client->date.'</td>'."\n";
                $page_content.= '<td>'.$client->currency.'</td>'."\n";
                $page_content.= '<td>'.$client->hour.'</td>'."\n";
                $page_content.= '<td>'.$client->rate.'</td>'."\n";
                $page_content.= '<td>'.$client->amount.'</td>'."\n";

                $page_content.= '</tr>'."\n";
            }
        }
        
        $page_content.="</tbody>"."\n";
        $page_content.="</table>"."\n";
        $page_content.= ' <script>
        var BaseUrl = "'.base_url().'"
        </script>';
    
        $page_header='<div class="panel panel-info"><div class="panel-heading">'."\n";
		$page_header.='<div class="row align-items-center">
		<div class="col">
			<h3 class="panel-title">Zow India Invoice</h3>
		</div>
		<div class="col-auto float-end ms-auto">
			<a href="'.site_url().'zowindia/zowindia_create_invoice/" class="btn add-btn btn-info"><i class="fa fa-plus"></i> Create Invoice</a>
			
		</div>
	</div>'; 
        // $page_header.='<h3 class="panel-title">tesdt</h3>';
        $page_header.="</div><!--panel-heading-->\n";
        $page_header.='<div class="panel-body">'."\n";
        $page_header.='<div id="table_loading_message">Loading ... </div>'."\n";
        $page_header.='<div class="date-filter"></div>';
        $page_header.='<div class="filter-row"></div>';
    
        $page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";
    
        
        return 	$page_content;
    }


	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>