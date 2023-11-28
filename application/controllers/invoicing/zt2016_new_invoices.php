<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_new_invoices extends MY_Controller {

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
		
		$this->load->helper(array('url','form','userpermissions', 'zt2016_financials'));
		
		
		

		$zowuser=_superuseronly(); 
		
		
		$this->load->model('zt2016_invoices_model','','TRUE');
		
		if ($this->input->post('CutoffDate'))
		 	{
		 		//$CutoffDate=$this->input->post('CutoffDate');
				$CutoffDate =date( 'Y-m-d', strtotime($this->input->post('CutoffDate')));
		 	} else{
				$CutoffDate = date( 'Y-m-d', strtotime('now'));
		 	}

  		// IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded! 
 		// It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
		//$this->auth = new stdClass;
		
		// Load 'standard' flexi auth library by default.
		//$this->load->library('flexi_auth');		
		
		//$this->load->model('zowtrak_auth_model');

		//if (!$this->flexi_auth->is_logged_in()) {
		//	redirect('auth/logout/auto');
		//}	
		
		$templateData['title'] = 'New Invoices';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_new_invoices_page($CutoffDate); 
		$templateData['ZOWuser']=_getCurrentUser();
		
		$this->load->view('admin_temp/main_temp',$templateData); 

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Display main content in new invices page
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 *  _display_new_invoices_page
	 *  provide main content data
	 */
	function _display_new_invoices_page($CutoffDate) {
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		$EndDate = date( 'Y-m-d', strtotime('now'));

		$page_content ='<div class="page_content">'."\n";

		$page_content .=		$this->_cutoff_date_form($CutoffDate)."\n";
		
		$page_content .=		$this->_get_new_invoices_table($ClientList,$CutoffDate)."\n";
		//$page_content .='	</div>'."\n";		
		//$page_content .='</div>'."\n";
		$page_content .='</div><!-- "page_content" -->'."\n";
		return 	$page_content;		
		}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Cut off date form
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 *  _display_new_invoices_page
	 *  provide main content data
	 */

	function _cutoff_date_form($EndDate) {

		
 		###### New Invoices Cutoff Date
		$pageOutput='<div style="margin-bottom:1em;">'."\n";
		
			$attributes='class="form-inline" id="cutoff-date-form" ';
		$pageOutput.=form_open(site_url().'invoicing/zt2016_new_invoices',$attributes )."\n";
 		
		$pageOutput.='				<div class="form-group mb-3">'."\n";
      	$pageOutput.='					<div class="input-group input-group-sm">'."\n";

 	 		//$dateplaceholder=date("d M Y");
	 		$dateplaceholder=date("d M Y",strtotime($EndDate));

		$pageOutput.='						<span class="input-group-addon" id="basic-addon1">Cut off date</span>'."\n";
		$pageOutput.='						<div class="input-append date" id="dp3" data-date="'.$dateplaceholder.'" data-date-format="dd mm yyyy" style="display:inline;">'."\n";
		$pageOutput.='							<input type="text" class="form-control datepicker" value="'.$dateplaceholder.'" id="CutoffDate" name="CutoffDate" >'."\n";
 		$pageOutput.='						</div>'."\n";
		$pageOutput.='						<div class="input-append " style="display:inline;margin-left:1em;">'."\n";
 		$pageOutput.='							<button type="submit" class="btn " id="cutoff-submit">Change</button>'."\n";
 		$pageOutput.='						</div>'."\n";
		$pageOutput.='					</div>'."\n";
  		$pageOutput.='				</div>'."\n";		
		$pageOutput.='			</form>'."\n";
		
	 	$pageOutput.='		</div>'."\n";		

		
		return $pageOutput;
		
		
	}
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get existing invoice table
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_existing_invoices_table
	 * build existing (past) invoices table
	 */
	// ################## Generate client totals ##################	
	function  _get_new_invoices_table($Clientlist,$EndDate)
	{

		$totalusd=0;
		$totaleuro=0;
		$totalhours=0;
		$total ='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="invoices_table">'."\n";
		$total .="<thead><tr><th data-sortable=\"true\">Client <span class=\"glyphicon glyphicon-asterisk\" aria-hidden=\"true\" style=\"color:#B0C4DE;font-size:.8em\"></span><span style=\"font-size:.9em; font-style: italic; font-weight: normal;\"\>= (Partially?) invoiced before cut-off date</span></th><th data-sortable=\"true\">Revenue</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Total Jobs</th><th data-sortable=\"true\">Total Hours</th><th data-sortable=\"true\">New</th><th data-sortable=\"true\">Edits</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Last Invoice</th><th class='no-sort'></th></tr></thead>\n";
		$total .="<tfoot><tr><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Revenue</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">Total Jobs</th><th data-sortable=\"true\">Total Hours</th><th data-sortable=\"true\">New</th><th data-sortable=\"true\">Edits</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Last Invoice</th><th class='no-sort'></th></tr></tfoot>\n";
		$total .="<tbody>\n";

		$this->load->model('trakinvoices', '', TRUE);
		
		 $DateLastInvoice='2010-01-01';
		 $StartDate = date( 'Y-m-d', strtotime('+1 day '.$DateLastInvoice));

		
		foreach($Clientlist as $client)
		{
		  
		  //Get entry totals from db
		  
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  
		  //countjobs
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);		  
		  $totaljobs=$this->db->count_all_results();
			
			
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client->CompanyName));
 
	  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;
		  //$total = "<p>".$this->db->last_query()."</p>";
		  if ($htotal!=0){
			 	
			 //$total.="<tr><td>".$client->CompanyName."</td>\n";
			 $safeclientName=str_replace("&", "~", $client->CompanyName);
			 $safeclientName=str_replace(" ", "_", $safeclientName);
			  
			 $DateLastInvoice =$this->zt2016_invoices_model->_getDateLastInvoice($client->CompanyName); 
			  
			 
			 $CompanyName= $client->CompanyName;
		
			 if ($DateLastInvoice!='') {
				// if (date('M'), strtotime($DateLastInvoice))==(date('M'), strtotime('now')){
				 

												  
				//if (date('M', strtotime($DateLastInvoice))==date('M', strtotime('now'))){
				if (date('M', strtotime($DateLastInvoice))==date('M', strtotime($EndDate))){
				
				 	$CompanyName .= " <span class=\"glyphicon glyphicon-asterisk\" aria-hidden=\"true\" style=\"color:#B0C4DE;font-size:.8em\"></span>";
				 }
			 } 			 
			  
			  $total.='<tr><td><a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'. $CompanyName.'</a></td>'."\n";
			  

			  
			  
			  
			 $tprice =_fetchClientMonthPrice($query2,$htotal);
			 $invoicerevenue=$tprice*$htotal;
			 $total .= "<td>".number_format($invoicerevenue, 2, '.', ',')."</td>\n";
			 $total.="<td>".$query2->Currency."</td>\n";
			 $total.="<td>".$totaljobs."</td>\n";
			 $total.="<td>".number_format($htotal, 2, '.', ',')."</td>\n";
			 
			 //add to totals
			 $totalhours=number_format($htotal+$totalhours, 2, '.', ',');
			 if ($query2->Currency=="EUR"){
				$totaleuro=$totaleuro+$invoicerevenue;
			 }
			 else if ($query2->Currency=="USD"){
			 	
				$totalusd=$totalusd+$invoicerevenue;
				
			 }	 
			  
			 if ($query->row()->NewSlides!=0){
				$total.="<td>".$query->row()->NewSlides."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 if ($query->row()->EditedSlides!=0){
				$total.="<td>".$query->row()->EditedSlides."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 if ($query->row()->Hours!=0){
				$total.="<td>".number_format($query->row()->Hours, 1, '.', ',')."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 

			 $total.="<td>";
			 if ($DateLastInvoice=='') {
				 $total.='-';
		 	 } else{
				$total.= date('d-M-Y', strtotime($DateLastInvoice));
			 } 
			 $total.="</td>\n";

			 $total.="<td>\n";
			  
			 //$total.="<td><a href=\"".site_url()."invoicing/zt2016_new_client_invoice/".$cleanclientname. "\">New Invoice</a></td>\n";
					$attributes='class="form-inline" ';
				$total.=form_open(site_url().'invoicing/zt2016_new_client_invoice',$attributes )."\n";
			  	
			    $cleanclientname=str_replace("&","~",$client->CompanyName);
			  	$cleanclientname=str_replace(" ","_",$cleanclientname);
			  
			    $total.=form_hidden('Current_Client',$cleanclientname);
				$total.=form_hidden('InvoiceEndDate',$EndDate);
			    $total.='<button type="submit" class="btn btn-xs btn-info">New Invoice</button>'."\n";
				$total.='</form>'."\n";			  
			  
	 		 $total.="</td>\n";
			
			$total .="</tr>\n";
		}	
	}
	$total.="</tbody></table></div>\n";
	$totaleuro="&euro;".number_format($totaleuro,2);
	$totalusd="$".number_format($totalusd,2);

		$page_header='<div class="panel panel-default"><div class="panel-heading">'."\n"; 
		$page_header.='<h4>Create New Invoice</h4><h3 class="panel-title">'.$totalhours." hours pending invoices (".$totaleuro." and ". $totalusd.")</h3>";
		$page_header.="</div><!--panel-heading-->\n";
		$page_header.='<div class="panel-body">'."\n";
		$page_header.='<div id="table_loading_message">Loading ... </div>'."\n";

		$page_content=$page_header.$total."</div><!--panel body-->\n</div><!--panel-->\n";

	
	
	return $page_content;
		

	}

	

}

/* End of file zt2016_new_invoices.php */
/* Location: ./application/controllers/invoicing/zt2016_new_invoices.php */

?>
