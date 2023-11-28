<?php

class zt2016_payment extends MY_Controller {

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

		$Fastflag=$this->uri->segment(3);
		// $Fastflag='fast';
		if ($Fastflag=="fast") {
			$templateData['title'] = 'Existing Invoices Fast';

		}else{
			$templateData['title'] = 'Invoices Payment';			
		}
		
		$templateData['Fastflag']=$Fastflag;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_page($templateData); 
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
	function _display_page($templateData) {
		

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
		
		$page_content .=		$this->_get_existing_invoices_table($templateData)."\n";
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
	function _get_existing_invoices_table($templateData) {
				
		
		$InvoiceTableRaw =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','sortBy'=>'BilledDate','sortDirection'=>'DESC'));
		$page_content='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="invoices_table">'."\n";
		$page_content.="<thead>"."\n";
		$page_content.="<tr><th data-sortable=\"true\">Invoice Number</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Invoice Date</th><th data-sortable=\"true\">Due Date</th><th data-sortable=\"true\">Payment Date</th><th data-sortable=\"true\">Amount</th><th data-sortable=\"true\">Paid Amount</th><th data-sortable=\"true\">Status</th><th data-sortable=\"true\">Vat</th><th data-sortable=\"true\">Discount</th><th data-sortable=\"true\">Total Amount</th><th data-sortable=\"true\">Sending Status</th></tr>"."\n";
		$page_content.="</thead>"."\n";
		$page_content.="<tfoot>"."\n";
		$page_content.="<tr><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Billed</th><th data-sortable=\"true\">Due</th><th data-sortable=\"true\">Paid</th><th data-sortable=\"true\" class=\"text-right\">Amount</th><th data-sortable=\"true\">Paid Amount</th><th data-sortable=\"true\">Status</th><th data-sortable=\"true\">Vat</th><th data-sortable=\"true\">Discount</th><th data-sortable=\"true\">Total Amount</th><th data-sortable=\"true\">Sending Status</th></tr>"."\n";
		$page_content.="</tfoot>"."\n";		
		$page_content.="<tbody>"."\n";

		

		
		

		
		if ($templateData['Fastflag']==''){
		
			foreach ($InvoiceTableRaw as $row){
				//$page_content.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";

				 if ($row->Status=="BILLED"){

					$duedate = new DateTime($row->DueDate);
					$now = new DateTime();

					if($duedate <  $now) {
						$InvoiceStatus = "OVERDUE";
					$page_content.= "<tr class=\"danger overdue_invoice\">";

					} else{
						$InvoiceStatus = $row->Status;		
						$page_content.= "<tr>";
					}					

				 } else if ($row->Status=="PAID"){
					$InvoiceStatus = $row->Status;				
					$page_content.= "<tr class=\"success paid_invoice\">";
				 }			
				else if ($row->Status=="WAIVED"){
					$InvoiceStatus = $row->Status;				
					$page_content.= "<tr class=\"warning paid_invoice\">";
				 }			
				else if ($row->Status=="MARKETING"){
					$InvoiceStatus = $row->Status;				
					$page_content.= "<tr class=\"info paid_invoice\">";
				 }
				else if ($row->Status=="DISPUTED"){
					$InvoiceStatus = $row->Status;				
					$page_content.= "<tr class=\"danger disputed_invoice\">";
				 }
				else if ($row->Status=="Partially Paid"){
					$InvoiceStatus = $row->Status;				
					$page_content.= "<tr class=\"info partially_paid_invoice\">";
				 }
				$vat = number_format(0,2);
				$discount = $row->discount;
				$invoiceTotal = $row->InvoiceTotal;
				$invoiceTotal = $invoiceTotal - $discount;
				$this->load->model('zt2016_clients_model', '', TRUE);
				$clientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row->Client));
				if($clientInfo->Country == 'Netherlands' || $clientInfo->County == 'The Netherlands'){
					$vatrevenue = (float)str_replace(',', '', $invoiceTotal);
					$VAT=$vatrevenue *21/100;
					$VATFormatted=number_format($VAT, 2, '.', ',');
					$vat = $VATFormatted;
					$invoiceTotal += $vat;
				}
				/*if ($row->Active==1) {
					$page_content.= "<tr>";
				} 
				else {
					$page_content.= "<tr class=\"inactive-contact\">";
				}


				$clientinfo= $this->trakclients->GetEntry($options = array('CompanyName'=>$row->CompanyName));
				$clientmaterialslink ='<a href="'.Base_Url().'clients/zt2016_manageclientmaterials/'.$clientinfo->ClientCode.'">'.$row->CompanyName.'</a>';
				*/

				$page_content.= '<td><a href="'.site_url().'invoicing/zt2016_view_invoice/'.$row->InvoiceNumber.'">'.$row->InvoiceNumber."</a></td>\n";

				$safeclientName=str_replace(" ", "_", $row->Client);
				$safeclientName=str_replace("&", "~", $safeclientName);
				$page_content.= '<td><a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$row->Client.'</a></td>'."\n";
				$page_content.= "<td>".date('j-M-Y', strtotime($row->BilledDate))."</td><td>".date('j-M-Y', strtotime($row->DueDate))."</td>\n";

				if ($row->PaidDate=="0000-00-00") {
					$PaidDate= "-";
				} else{
					$PaidDate= date('j-M-Y', strtotime($row->PaidDate));

				}
				$page_content.= "<td>".$PaidDate."</td>";
				$page_content.= "<td class=\"text-right\">".number_format($row->InvoiceTotal,2)."</td>\n";
				if($InvoiceStatus == 'Partially Paid'){
					$page_content.= "<td class=\"text-center\"><input class=\"paidAmount\" type=\"text\" value=\"".$row->paidAmount."\" data-date=\"".date('Y-m-d')."\" data-invoice=\"".$row->InvoiceNumber."\"></td>\n";
				}else if($InvoiceStatus == "PAID"){
					$page_content.= "<td class=\"text-center\">".number_format($row->InvoiceTotal,2)."</td>\n";
				}
				else{
					$page_content.= "<td class=\"text-center\">".number_format($row->paidAmount,2)."</td>\n";
				}
				

				$page_content.= "<td>".$InvoiceStatus."</td><td>".$vat."</td><td>".$row->discount."</td><td>".$invoiceTotal."</td>\n";
				$page_content.= "<td>".$row->sendingStatus."</td></tr>\n";

				//$page_content.= "<td>".$row->FirstName. " ". $row->LastName."</td><td>".$row->Email1."</td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";

			}
		}
		//$page_content.="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>"."\n";
		$page_content.="</tbody>"."\n";
		$page_content.="</table>"."\n";
		$page_content.= ' <script>
        var BaseUrl = "'.base_url().'"
        </script>';

		$page_header='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_header.='<h3 class="panel-title">'.count ($InvoiceTableRaw)." existing invoices (all time)</h3>";
		$page_header.="</div><!--panel-heading-->\n";
		$page_header.='<div class="panel-body">'."\n";
		$page_header.='<div id="table_loading_message">Loading ... </div>'."\n";
		$page_header.='<div class="date-filter"></div>';
		$page_header.='<div class="header-hwe"></div>';

		$page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";

		
		return 	$page_content;
	}


	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>