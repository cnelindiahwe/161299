<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_pending_invoices extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
		

		$zowuser=_superuseronly(); 
  		// Helpers
		//$this->load->helper(array("url","form"));	
		//$this->load->helper(array("url"));
		$this->load->model('zt2016_invoices_model','','TRUE');
		//$this->load->model('trakclients','','TRUE');
		
  		// IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded! 
 		// It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
		//$this->auth = new stdClass;
		
		// Load 'standard' flexi auth library by default.
		//$this->load->library('flexi_auth');		
		
		//$this->load->model('zowtrak_auth_model');

		//if (!$this->flexi_auth->is_logged_in()) {
		//	redirect('auth/logout/auto');
		//}	
		
		$templateData['title'] = 'Pending Invoices';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_get_invoices_table(); 
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
	function _get_invoices_table() {
		

		$page_content ='<div class="page_content">';
		//$page_content .= $this->_get_contact_lists_tabs();
		$page_content .='<div class="tab-content">'."\n";
		$page_content .='	<div id="sectionA" class="tab-pane fade in active">'."\n"; 
		$page_content .=		$this->_get_existing_invoices_table()."\n";
		$page_content .='	</div>'."\n";		
		$page_content .='</div>'."\n";
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
	function _get_existing_invoices_table() {
				
		//$InvoiceTableRaw =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','Status'=>'BILLED','sortBy'=>'BilledDate','sortDirection'=>'DESC'));
		$InvoiceTableRaw =$this->zt2016_invoices_model->GetPendingInvoices();
		$page_content='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="invoices_table">'."\n";
		$page_content.="<thead>"."\n";
		$page_content.="<tr><th data-sortable=\"true\">Invoice Number</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Billed</th><th data-sortable=\"true\">Due</th>.<th data-sortable=\"true\">Amount</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Status</th></tr>"."\n";
		$page_content.="</thead>"."\n";	
		$page_content.="<tbody>"."\n";
		foreach ($InvoiceTableRaw as $row){
			//$page_content.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";

			if ($row->Status=="BILLED"){
			 		
			 	$duedate = new DateTime($row->DueDate);
				$now = new DateTime();
				
				if($duedate <  $now AND $row->Status!="DISPUTED") {
				    $InvoiceStatus = "OVERDUE";
					$page_content.= "<tr class=\"danger overdue_invoice\">";

				} else{
					$InvoiceStatus = $row->Status;		
					$page_content.= "<tr>";
				}	
				 
			 } else if ($row->Status=="DISPUTED"){
			 	$InvoiceStatus = $row->Status;				
				$page_content.= "<tr class=\"danger disputed_invoice\">";
						
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
						
			/*if ($row->Active==1) {
				$page_content.= "<tr>";
			} 
			else {
				$page_content.= "<tr class=\"inactive-contact\">";
			}*/
			
			
			/*$clientinfo= $this->trakclients->GetEntry($options = array('CompanyName'=>$row->CompanyName));
			$clientmaterialslink ='<a href="'.Base_Url().'clients/zt2016_manageclientmaterials/'.$clientinfo->ClientCode.'">'.$row->CompanyName.'</a>';
			*/
			
			$page_content.= '<td><a href="'.site_url().'invoicing/zt2016_view_invoice/'.$row->InvoiceNumber.'">'.$row->InvoiceNumber."</a></td>\n";
			
			$safeclientName=str_replace(" ", "_", $row->Client);
			$safeclientName=str_replace("&", "~", $safeclientName);
			$page_content.= '<td><a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$row->Client.'</a></td>'."\n";
			$page_content.= "<td data-order=\"".strtotime($row->BilledDate)."\">".date('j-M-Y', strtotime($row->BilledDate))."</td>\n";
			$page_content.=	"<td data-order=\"".strtotime($row->DueDate)."\">".date('j-M-Y', strtotime($row->DueDate))."</td>\n";

			$page_content.= "<td class=\"text-right\">".number_format($row->InvoiceTotal,2)."</td>";
			$page_content.="<td>".$row->Currency."</td>\n";
			 

			
			$page_content.="<td>".$InvoiceStatus."</td></tr>\n";

			//$page_content.= "<td>".$row->FirstName. " ". $row->LastName."</td><td>".$row->Email1."</td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
						
			
		}
		$page_content.="</tbody>"."\n";
		
		$page_content.="<tfoot>"."\n";
		$page_content.="<tr><th data-sortable=\"true\"></th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Billed</th><th data-sortable=\"true\">Due</th><th data-sortable=\"true\" class=\"text-right\">Amount</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Status</th></tr>"."\n";
		$page_content.="</tfoot>"."\n";	
		$page_content.="</table>"."\n";
		
		$page_header='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_header.='<h3 class="panel-title">'.count ($InvoiceTableRaw)." pending invoices\n";
			
			########## all invoices button
			$page_header.= '<a href="'.site_url().'invoicing/zt2016_existing_invoices" class="btn btn-info btn-sm pull-right">All Existing Invoices</a></p>'."\n";
		$page_header.="</h3>\n";
		$page_header.="<div class='clearfix'></div>\n";
		$page_header.="</div><!--panel-heading-->\n";
		$page_header.='<div class="panel-body">'."\n";
		$page_header.='<div id="table_loading_message">Loading ... </div>'."\n";
		
		$page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";
		
		
		return 	$page_content;
	}

	

}

/* End of file zt2016_existing_invoices.php */
/* Location: ./application/controllers/invoicing/zt2016_existing_invoices.php */

?>
