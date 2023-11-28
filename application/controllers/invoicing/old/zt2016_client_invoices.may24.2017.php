<?php

class Zt2016_client_invoices extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_clients','form'));
		
		$zowuser=_superuseronly(); 

		$safeclientName=$this->uri->segment(3);


		 if (empty ($safeclientName)) {
		 	if ($this->input->post('Current_Client')){
		 		$safeclientName=$this->input->post('Current_Client');
				if ($safeclientName == "all") {
					redirect('invoicing/zt2016_existing_invoices', 'refresh');
				}
				
		 	} else{
					//die ("no client name");
					redirect('invoicing/zt2016_existing_invoices', 'refresh');
		 	}
			
			 
		 }
		$clientName=str_replace("_", " ", $safeclientName);
		$clientName=str_replace("~", "&", $clientName);
		$templateData['title'] = 'Client Invoices for '.$clientName;

		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _get_Client_Invoices($clientName,$safeclientName); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    // ################## Retrieves invoice data . ##################	
	function  _get_Client_Invoices($clientName,$safeclientName)
	{
		# retrieve invoice from db
		$this->load->model('zt2016_invoices_model','','TRUE');
		$clientInvoicesTable=$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','Client'=>$clientName,));

		# retrieve current client from db		
		$this->load->model('trakclients', '', TRUE);
		$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $clientName));

		# retrieve all clients from db		
		$this->load->model('trakclients', '', TRUE);
		$clientsTable = $this->trakclients->GetEntry();


		# call main routine
		$pageOutput = $this->_display_existing_client_invoices($clientInvoicesTable,$clientInfo,$clientsTable,$safeclientName);
		
		
		
		return $pageOutput;
	}


    // ################## Generates invoice content . ##################	
	
	function  _display_existing_client_invoices($clientInvoicesTable,$clientInfo,$clientsTable,$safeclientName)
	{
	
	

		$FormURL="invoicing/zt2016_client_invoices";
		$attributes['id'] = 'client_dropdown_form';
		$attributes['class'] = 'form-inline';

		$pageOutput=form_open(site_url().$FormURL,$attributes);
	 	$pageOutput.='				<div class="form-group">'."\n";
      	$pageOutput.='					<div class="input-group ">'."\n";
      	$pageOutput.='						<span class="input-group-addon" id="basic-addon1">Invoices for </span>'."\n";
		$pageOutput.= zt2016_clients_dropdown_control($clientsTable,$clientInfo,$FormURL);
 		$pageOutput.='					</div>';
 		$pageOutput.='				</div>';
	 	$pageOutput.='				<div class="form-group">'."\n";
      	$pageOutput.='					<div class="input-group">'."\n";
		$more = 'id="client_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
		$pageOutput.=form_submit('client_dropdown_selector_submit', 'Go',$more);
		$pageOutput.= form_close()."\n";
 		$pageOutput.='					</div>'."\n";
 		$pageOutput.='				</div>'."\n";
 		$pageOutput.='			</form>'."\n";	
	
		$pageOutput.='<div class="row">'."\n";
		$pageOutput.='<div class="col-md-12">'."\n";
		$pageOutput.='<p>'; 
		$pageOutput.='More: '; 
		$pageOutput.='<a href="'.site_url().'invoicing/zt2016_new_client_invoice/'. $safeclientName.'">New Invoice</a>'."\n";
		$pageOutput.=' | ';
		$pageOutput.='<a href="'.site_url().'clients/zt2016_manageclientmaterials/'.$clientInfo->ClientCode.'">Materials</a>'."\n";
		$pageOutput.='</p>'."\n"; 
		$pageOutput.='</div>'."\n";
		$pageOutput.='</div>'."\n";
		
			
		$pageOutput.= $this->_get_existing_client_invoices_table($clientInvoicesTable,$clientInfo);
		
		return $pageOutput;/**/

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get existing invoices table
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_existing_invoices_table
	 * build existing (past) invoices table
	 */
	function _get_existing_client_invoices_table($clientInvoicesTable,$clientInfo) {

		if (empty($clientInvoicesTable)) {
			
			$InvoiceTableRaw = "No previous invoices.";
			
		}
		else{
				
			//$clientInvoicesTable =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','sortBy'=>'BilledDate','sortDirection'=>'DESC'));
			//$InvoiceTableRaw='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="invoices_table">'."\n";
			$InvoiceTableRaw='<table class="table table-striped table-condensed responsive"  style="display:none;" id="client_invoices_table">'."\n";
			$InvoiceTableRaw.="<thead>"."\n";
			$InvoiceTableRaw.="<tr><th data-sortable=\"true\">Invoice Number</th><th data-sortable=\"true\">Billed</th><th data-sortable=\"true\">Due</th><th data-sortable=\"true\">Amount (".$clientInfo->Currency.")</th><th data-sortable=\"true\">Status</th><th data-sortable=\"true\">Originators</th></tr>"."\n";
			$InvoiceTableRaw.="</thead>"."\n";
			$InvoiceTableRaw.="<tfoot>"."\n";
			$InvoiceTableRaw.="<tr><th >Invoice Number</th><th>Billed</th><th>Due</th><th class=\"text-right\">Amount</th><th>Status</th><th></th></tr>"."\n";
			$InvoiceTableRaw.="</tfoot>"."\n";		
			$InvoiceTableRaw.="<tbody>"."\n";
			foreach ($clientInvoicesTable as $row){
				//$InvoiceTableRaw.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
	
				 if ($row->Status=="BILLED"){
				 		
				 	$duedate = new DateTime($row->DueDate);
					$now = new DateTime();
					
					if($duedate <  $now) {
					    $InvoiceStatus = "OVERDUE";
					$InvoiceTableRaw.= "<tr class=\"danger overdue_invoice\">";
	
					} else{
						$InvoiceStatus = $row->Status;		
						$InvoiceTableRaw.= "<tr>";
					}					
								 	
				 } else if ($row->Status=="PAID"){
				 	$InvoiceStatus = $row->Status;				
					$InvoiceTableRaw.= "<tr class=\"success paid_invoice\">";
				 }			
				else if ($row->Status=="WAIVED"){
				 	$InvoiceStatus = $row->Status;				
					$InvoiceTableRaw.= "<tr class=\"warning paid_invoice\">";
				 }			
				else if ($row->Status=="MARKETING"){
				 	$InvoiceStatus = $row->Status;				
					$InvoiceTableRaw.= "<tr class=\"info paid_invoice\">";
				 }			
							
				
				$InvoiceTableRaw.= '<td><a href="'.site_url().'invoicing/zt2016_view_invoice/'.$row->InvoiceNumber.'">'.$row->InvoiceNumber."</a></td>";
				$InvoiceTableRaw.= "<td>".date('j-M-Y', strtotime($row->BilledDate))."</td><td>".date('j-M-Y', strtotime($row->DueDate))."</td>";
				$InvoiceTableRaw.= "<td class=\"text-right\">".number_format($row->InvoiceTotal,2)."</td>";
				 
				$InvoiceTableRaw.= "<td>".$InvoiceStatus."</td>";
				
				$InvoiceTableRaw.= "<td style='width:30%;'>".$row->Originators."</td>";
				
				$InvoiceTableRaw.= "</tr>\n";
	
				//$InvoiceTableRaw.= "<td>".$row->FirstName. " ". $row->LastName."</td><td>".$row->Email1."</td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
							
				
			}
			$InvoiceTableRaw.="</tbody>"."\n";
			$InvoiceTableRaw.="</table>"."\n";
			
			$InvoiceTableRaw_header='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
			$InvoiceTableRaw_header.='<h3 class="panel-title">'.count ($clientInvoicesTable)." existing invoices (all time)</h3>";
			$InvoiceTableRaw_header.="</div><!--panel-heading-->\n";
			$InvoiceTableRaw_header.='<div class="panel-body">'."\n";
			$InvoiceTableRaw_header.='<div id="table_loading_message">Loading ... </div>'."\n";
			
			$InvoiceTableRaw=$InvoiceTableRaw_header.$InvoiceTableRaw."</div><!--panel body-->\n</div><!--panel-->\n";
		}
		
		return 	$InvoiceTableRaw;
	}

}

/* End of file zt2016_client_invoices.php */
/* Location: ./system/application/controllers/invoicing/zt2016_client_invoices.php */
?>