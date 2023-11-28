<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_new_client_invoice extends MY_Controller {

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
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_clients','zt2016_financials','form'));
		
		$zowuser=_superuseronly(); 

		$safeclientName=$this->uri->segment(3);


		 if (empty ($safeclientName)) {
		 	if ($this->input->post('Current_Client'))
		 		{
		 		$safeclientName=$this->input->post('Current_Client');
				if ($safeclientName == "all") {
					//die ("all");
					redirect('invoicing/zt2016_new_invoices', 'refresh');
				}
				
		 	} else{
					die ("no client name");
					redirect('invoicing/zt2016_new_invoices', 'refresh');
		 	}

			 
		 }			


		//Check for excluded entries
		$excludearray =$this->input->post();
		

		$excludelist=array();
		$excludeflat="";
		if ($excludearray) {
			
			$excludelist=array();
			foreach (array_keys($excludearray)as $key) {
				if (substr($key, 0,8)=='exclude-') {
				    $excludelist []= str_replace("exclude-","",$key);
				    if($excludeflat!=""){$excludeflat.=","; }
				    $excludeflat.= $excludelist [count($excludelist)-1];
				}
			}
			$this->session->set_flashdata('excludelist',$excludeflat);
		} 


		# client name
		$clientName=str_replace("~","&",$safeclientName);
		$clientName=str_replace("_"," ",$clientName);

		$safeclientName=str_replace("&","~",$safeclientName);
		$safeclientName=str_replace(" ","_",$safeclientName);


		# Invoice dates
		if ($this->input->post('InvoiceStartDate')) {
			$StartDate=$this->input->post('InvoiceStartDate');
			//echo $StartDate;
			//die;
		} else{
			$StartDate='01-01-2010';		
		}

		

		$StartDate = date('d M Y', strtotime($StartDate));
				
		if ($this->input->post('InvoiceEndDate')) {
			$EndDate=$this->input->post('InvoiceEndDate');
		} else{
			$EndDate='now';		
		}	
						
		$EndDate = date('d M Y', strtotime($EndDate));
		
		# Invoice number
		if ($this->input->post('InvoiceNumber')) {
			$InvoiceNumber=$this->input->post('InvoiceNumber');
			//echo $StartDate;
			//die;
		} else{
			$InvoiceNumber="";		
		}
				
			
			
		# Build page

		$templateData['title'] = 'New Invoice for '.$clientName;
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _get_new_invoice_content($clientName,$safeclientName,$StartDate,$EndDate,$excludelist);

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
	function _get_new_invoice_content($clientName,$safeclientName,$StartDate,$EndDate, $excludelist=array()) {
		
		

		# retrieve all clients from db		
		$this->load->model('trakclients', '', TRUE);
		$clientsTable = $this->trakclients->GetEntry();


		# retrieve current client from db		
		//$this->load->model('trakclients', '', TRUE);
		//$clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $clientName));
		foreach($clientsTable as $client) {
		    if ($clientName ==  $client->CompanyName) {
		        $clientInfo = $client;
		        break;
		    }
		};

		if (empty($clientInfo)){
			die ("Missing client info.");
		}

		# retrieve new invoice data from db
		$this->load->model('zt2016_entries_model','','TRUE');
		$InvoiceData = $this->zt2016_entries_model->Get_entries_between_dates($options = array('Client'=>$clientName,'Status'=>'COMPLETED'),$StartDate,$EndDate);

		# call main routine
		$pageOutput = $this->_display_new_client_invoice($InvoiceData,$clientInfo,$clientsTable, $safeclientName, $StartDate,$EndDate,$excludelist);
		
		return 	$pageOutput;	
		}
	
	

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Display completed page
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _display_new_client_invoice
	 * Display completed page
	 */
	// ################## Generate client totals ##################	
	function  _display_new_client_invoice($InvoiceData,$clientInfo,$clientsTable, $safeclientName,$StartDate,$EndDate,$excludelist=array())
	{
		#top dropdown	
		$client_selector=$this->_create_clientselector($clientsTable,$clientInfo);
		
		#more client pages



		#invoice panel
		$invoice_header='<div class="panel panel-default"><div class="panel-heading">'."\n"; 
		$invoice_header.='<h3 class="panel-title"> New invoice for <a href="'.site_url().'invoicing/zt2016_client_invoices/'.$safeclientName.'">'.$clientInfo->CompanyName." (".$clientInfo->ClientCode.")</a></h3>\n";
		
		######### buttons
		$invoice_header.= "<p class='top-buffer-10'>";
		
		 	
			########## Client info button
		 	$invoice_header.='<a href="'.site_url().'clients/zt2016_client_info/'.$safeclientName.'" class="btn btn-warning btn-xs ">Client Info</a>';

		$invoice_header.= "</p>"; //buttons
		
		$invoice_header.="</div><!--panel-heading-->\n";
		$invoice_header.='<div class="panel-body">'."\n";
		//$page_header.='<div id="table_loading_message">Loading ... </div>'."\n";

		#new invoice form
		if (empty($InvoiceData)) {
			
			$invoice_content = "No entries since last invoice.";
			
		}
		else{
				
			$invoice_content = $this->_create_new_invoice_table($InvoiceData,$clientInfo,$clientsTable, $safeclientName, $StartDate,$EndDate,$excludelist);
		}
		
		#final page assembly
		$page_content=$client_selector;
		$page_content.=$invoice_header;
		$page_content.=$invoice_content;
		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";
		
		return $page_content;
		

	}


	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get top client selector
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _create_clientselector
	 * build top client selector
	 */
	// ################## Generate client selector ##################	
		function _create_clientselector($clientsTable,$clientInfo){
		
		#top dropdown
		$FormURL="invoicing/zt2016_new_client_invoice";
		$attributes['id'] = 'client_dropdown_form';
		$attributes['class'] = 'form-inline';

		$client_selector=form_open(site_url().$FormURL,$attributes);
	 	$client_selector.='				<div class="form-group">'."\n";
      	$client_selector.='					<div class="input-group ">'."\n";
      	$client_selector.='						<span class="input-group-addon" id="basic-addon1">New invoice for</span>'."\n";
		$client_selector.= zt2016_clients_dropdown_control($clientsTable,$clientInfo,$FormURL);
 		$client_selector.='					</div>';
 		$client_selector.='				</div>';
	 	$client_selector.='				<div class="form-group">'."\n";
      	$client_selector.='					<div class="input-group">'."\n";
		$more = 'id="client_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
		$client_selector.=form_submit('client_dropdown_selector_submit', 'Go',$more);
		$client_selector.= form_close()."\n";
 		$client_selector.='					</div>'."\n";
 		$client_selector.='				</div>'."\n";
 		$client_selector.='			</form>'."\n";
 		return 	$client_selector;
	}

	
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get new invoice table
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 *_create_new_invoice_table
	 * Get new invoice table
	 */
	
	function _create_new_invoice_table($InvoiceData,$ClientInfo,$clientsTable, $safeclientName, $StartDate,$EndDate,$ExcludeList=array()){
		
		$excludelist[]="";
		$Originators='';				
		$InvoiceTotals['SumNewSlides']=0;
		$InvoiceTotals['SumEditedSlides']=0;
		$InvoiceTotals['SumHours']=0;
						
		$entries="";

		$attributes = array('id' => 'excludeform');
		$entries.= form_open('invoicing/zt2016_new_client_invoice/'.$safeclientName, $attributes);
		
		$NetJobs =count($InvoiceData)-count($ExcludeList);

		$entries .= "<p>Completed and unbilled jobs: <span class=\"badge\">".$NetJobs."</span>";
		if ($NetJobs !=count($InvoiceData)){
			$entries .= " (".count($ExcludeList)." excluded)";
		}
		$entries .= "</p>";
		$entries .= "<div>\n";
		$entries .= "	<table id=\"new-invoice-entries\" class=\"table table-striped table-condensed responsive dataTable dtr-inline\">\n";

		$entries .="		<thead><tr><th class=\"no-sort\">Code</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Originator</th><th  class=\"no-sort\">New</th><th class=\"no-sort\">Edits</th><th  class=\"no-sort\">Hours</th><th  class=\"no-sort\">File Name</th><th  class=\"no-sort\">Team</th><th id=\"exclude-header\" style=\"width:4em\"  class=\"no-sort\">Exc.</th>";
	//	$entries .="		<tfoot><tr><th data-sortable=\"true\">Code</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\">New</th><th data-sortable=\"true\">Edits</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">File Name</th><th data-sortable=\"true\">Team</th><th>Trash</th><th id=\"exclude-header\">Exclude</th></tr></tfoot>\n";		

		//$entries .= "		<tr><th class=\"header\">Code</th><th class=\"header\">Date</th><th class=\"header\">Originator</th><th class=\"header\"># New Slides</th>";
		//$entries .= "		<th class=\"header\"># Edited Slides</th><th class=\"header\"># Hours</th><th>File Name</th><th>Team</th><th class=\"button\">Trash</th><th id=\"exclude-header\">Exclude</th></tr>\n";
		$entries .= "		</thead>\n";
		
		$entries .= "<tbody>\n";
		
		//ignore exclude list if all entries are excluded;
		if (sizeof($InvoiceData)==sizeof($ExcludeList)){
			$ExcludeList=array();
			$excludelist[]="";			
		}
		
		foreach($InvoiceData as $project)
		{
			if (!in_array($project->id, $ExcludeList)) {
				$entries .= "<tr>";
				$entries .= "<td>".$project->Code. "</td>";
				//Converts MySQL date
				$mysqldate = date( 'd/M/Y',strtotime($project->DateOut));
				$entries .= "<td class=\"date\">".$mysqldate. "</td>";
				
				
				$SafeContactName=str_replace( "&","~", $project->Originator);
				$SafeContactName=str_replace( " ","_", $SafeContactName);
					
				$entries .= "<td><a href='".site_url()."contacts/zt2016_contact_info/".$SafeContactName."'>".$project->Originator. "</a></td>";
				
				
				if (strpos($Originators, $project->Originator) == FALSE) {
					$Originators.=",".trim($project->Originator);
				}
				$entries .= "<td class=\"slides\">";
				if ($project->NewSlides > 0) {
					$entries .= $project->NewSlides;
					$InvoiceTotals['SumNewSlides']=$InvoiceTotals['SumNewSlides']+$project->NewSlides;
				}
				$entries .= "</td>";

				$entries .= "<td class=\"slides\">";
				if ($project->EditedSlides > 0) {
					$entries .= $project->EditedSlides;
					$InvoiceTotals['SumEditedSlides']=$InvoiceTotals['SumEditedSlides']+$project->EditedSlides;
				}
				$entries .= "</td>";

				$entries .= "<td class=\"slides\">";
				if ($project->Hours > 0) {
					$entries .= $project->Hours;
					$InvoiceTotals['SumHours']=$InvoiceTotals['SumHours']+$project->Hours;
				}
				$entries .= "</td>";


				$entries .= "<td>".$project->FileName . "</td>";
				
				$entries .= "<td>".$project->ScheduledBy;
				if ($project->ScheduledBy != $project->WorkedBy){
				$entries.=", ".$project->WorkedBy;
				}
				if ($project->ProofedBy != $project->ScheduledBy && $project->ProofedBy != $project->WorkedBy){
					$entries.=", ".$project->ProofedBy;
				}
				if ($project->CompletedBy != $project->ScheduledBy && $project->CompletedBy != $project->WorkedBy && $project->CompletedBy != $project->ProofedBy){
					$entries.=", ".$project->ProofedBy;
				}
				
				$entries .= "</td>";
				//$entries .= "<td class=\"button edit\"><a href=\"".site_url()."editentry/".$project->id . "\" class=\"edit\">Edit</a></td>";
				//$entries .= "<td class=\"button delete\"><a href=\"".$project->id . "\" class=\"delete\">Trash</a></td>";

								$data = array(
	              'name'        => 'exclude-'.$project->id,
	              'value'       => 'exclude',
	              'class'       => 'exclude-checkbox',

	            );					
				$entries .= "<td class=\"button exclude\">".form_checkbox($data)."</td>";
				$entries .= "</tr>\n\n";
			}
		}
		$entries .= "</tbody>\n";
		$entries .= "</table>\n";
		$entries .= "</div>\n";
		

		$entries.=form_hidden('InvoiceStartDate',$StartDate);
		$entries.=form_hidden('InvoiceEndDate',$EndDate);
		
		$data = array(
		  'id'        => 'excludeSubmit',
          'name'        => 'excludeSubmit',
          'value'       => 'Exclude ticked entries from invoice',
          'class'       => 'btn btn-danger btn-xs',
        );
		$entries .= form_submit($data);
		
		if ($NetJobs !=count($InvoiceData)){
			$entries .= '<a href="'.site_url().'invoicing/zt2016_new_client_invoice/'. $safeclientName.'"class="btn btn-default btn-sm" role="button" style="margin-left:1em;">View all'."</a>\n";
		}

		$entries .= "</form>";					
		$originator_box='	<div class="well">';	
		$originator_box.= "Originators:";
		$Originators= substr($Originators, 1);
		
		$RawOriginators= explode(",",$Originators);
		$OriginatorsSorted=array_map('trim',$RawOriginators);
		sort($OriginatorsSorted);
		
		$Originators= implode (", ",$OriginatorsSorted);
		$originator_box.= "	<pre class='pre-scrollable' id='originators_list'>".$Originators."</pre>";
		$originator_box.='</div><!--well--> '."\n";		
	
		$InvoiceTotals['Originators']=$Originators;
	    
	    
		# Basic calculations
		
		$InvoiceTotals['Total_Billed_Hours']= $InvoiceTotals['SumHours'];
		$InvoiceTotals['Total_Billed_Hours']= $InvoiceTotals['Total_Billed_Hours'] + ($InvoiceTotals['SumNewSlides']/5);
		$InvoiceTotals['Total_Billed_Hours']= $InvoiceTotals['Total_Billed_Hours'] + ($InvoiceTotals['SumEditedSlides'] / 5 * $ClientInfo->PriceEdits);
		

		$InvoiceTotals['Invoice_Price'] = _fetchClientMonthPrice($ClientInfo,$InvoiceTotals['Total_Billed_Hours']);

		$InvoiceTotals['Invoice_Cash_Total']=  round($InvoiceTotals['Total_Billed_Hours'] * $InvoiceTotals['Invoice_Price'] ,2);
		
		if (strtolower($ClientInfo->Country)=="the netherlands" || strtolower($ClientInfo -> Country)=="netherlands"  ) {
			$InvoiceTotals['Vat_Total'] = round(($InvoiceTotals['Invoice_Cash_Total']*.21)+ $InvoiceTotals['Invoice_Cash_Total'],2);
		}
		
		
		$InvoiceTotals['Invoice_Number'] = $this->_generate_invoice_number($ClientInfo, $EndDate);
		
	
	
	
		
		
		$invoiceform = $this->_create_new_invoice_dates_form($InvoiceTotals,$ClientInfo,$InvoiceData, $StartDate,$EndDate,$ExcludeList);
		
		$entries=$invoiceform.$originator_box.$entries;
		
		return $entries;
				
	}


	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Create new invoice dates form
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _create_new_invoice_dates_form
	 * build new invoice dates form
	 */
	
	function _create_new_invoice_dates_form($InvoiceTotals,$ClientInfo,$InvoiceData, $StartDate,$EndDate,$ExcludeList=array()){
		
		
		
		$new_invoice_form='';
		
 		# display dates and invoice number	
		$new_invoice_form.='<div class="row">'."\n";
 		

		$attributes='class="form-inline" id="invoice-dates-form"';
		$new_invoice_form.=form_open(site_url().'invoicing/zt2016_new_client_invoice',$attributes )."\n";

 		$new_invoice_form.='<div class="col-sm-4">'."\n";

		$new_invoice_form.='<ul class="list-group  dates-column">'."\n";
		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Invoice Number'."\n";
		//$new_invoice_form.='<input type="text" class="form-control r" value="'.$InvoiceNumber.'" id="InvoiceDate" name="InvoiceNumber" >'."\n";
		$new_invoice_form.='<span id="invoice-number">'.$InvoiceTotals['Invoice_Number']."</span>\n";

		//$new_invoice_form.='	<span class="badge badge-default">'."XXX".'</span>' ."\n";
		
		//$new_invoice_form.='	<span class="badge badge-default">'.$InvoiceTotals['SumNewSlides'].'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";

		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Start Date'."\n";
		$new_invoice_form.='<input type="text" class="form-control datepicker" value="'.$StartDate.'" id="InvoiceDate" name="InvoiceStartDate" >'."\n";
		//$new_invoice_form.='	<span class="badge badge-default">'.$StartDate.'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";
		
		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	End Date'."\n";
		$new_invoice_form.='	<input type="text" class="form-control datepicker" value="'.$EndDate.'" id="InvoiceDate" name="InvoiceEndDate" >'."\n";
		//$new_invoice_form.='	<span class="badge badge-default">'.$EndDate.'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";
		$new_invoice_form.='</ul>'."\n";
 		
		$new_invoice_form.=form_hidden('Current_Client',$ClientInfo->CompanyName);
 		$new_invoice_form.='<button type="submit" id="Dates-Refresh-Submit" class="btn btn-sm">Refresh</button>'."\n";
 		
		$new_invoice_form.='</div><!--col-->'."\n";
		
		$new_invoice_form.='			</form>'."\n";
				
		
 		# display cash totals

 		$new_invoice_form.='<div class="col-sm-4">'."\n";
				
		$new_invoice_form.='<ul class="list-group totals-column">'."\n";
		
		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Total'."\n";
		//$pageOutput.='	<span class="badge badge-success">'. round($invoiceTotals->InvoiceTotal.'</span> '.$clientInfo->Currency."\n";
		$new_invoice_form.='	<span class="badge badge-success">';
		$new_invoice_form.=number_format(round($InvoiceTotals['Invoice_Cash_Total']), 2, '.', '' );
		if (!empty($vatTotal)) {
			$new_invoice_form.=' ( '.$InvoiceTotals['Vat_Total'].' )';
		}
		$new_invoice_form.= '</span> '.$ClientInfo->Currency."\n";
		if (!empty($vatTotal)) {
			$new_invoice_form.=' (inc. 21% VAT)';
		}
		$new_invoice_form.='	</li>'."\n";

		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Price per hour'."\n";
		$new_invoice_form.='	<span class="badge badge-info">'.number_format($InvoiceTotals['Invoice_Price'], 2) .'</span>'.$ClientInfo->Currency."\n";
		$new_invoice_form.='	</li>'."\n";

		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Billed hours'."\n";
		$new_invoice_form.='	<span class="badge badge-warning">'.$InvoiceTotals['Total_Billed_Hours'].'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";
		$new_invoice_form.='</ul>'."\n";
		$new_invoice_form.='</div><!--col-->'."\n";


		

 		# display slide count		
		$new_invoice_form.='<div class="col-sm-4">'."\n";
		
		$new_invoice_form.='<ul class="list-group">'."\n";
		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	New Slides'."\n";
		$new_invoice_form.='	<span class="badge badge-default">'.$InvoiceTotals['SumNewSlides'].'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";

		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Edited Slides'."\n";
		$new_invoice_form.='	<span class="badge badge-default">'.$InvoiceTotals['SumEditedSlides'].'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";
		
		$new_invoice_form.='	<li class="list-group-item">'."\n";
		$new_invoice_form.='	Hours'."\n";
		$new_invoice_form.='	<span class="badge badge-default">'.$InvoiceTotals['SumHours'].'</span>' ."\n";
		$new_invoice_form.='	</li>'."\n";

		
		$new_invoice_form.='</ul>'."\n";
 		$new_invoice_form.='</div><!--col-->'."\n";		
 		$new_invoice_form.='</div><!--row--> '."\n";	
		
		$new_invoice_form.= $this->_New_Invoice_Create_Form($ClientInfo,$InvoiceData,$InvoiceTotals,$StartDate,$EndDate,$ExcludeList);
		

		
		return $new_invoice_form;
	
	}

	// ################## New Invoice Create Form ##################	

	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// New Invoice Create Form
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _New_Invoice_Create_Form
	 * Generates a new invoice creation form
	 */
		
	function  _New_Invoice_Create_Form($ClientInfo,$invoicedata,$InvoiceTotals,$StartDate,$EndDate,$excludelist=array())
	{
		$excludecommas="";	
		foreach($excludelist as $excludename){
			if ($excludecommas!="") {
				$excludecommas.=",";				
			}
			$excludecommas.=$excludename;
		}
		$temptotal=str_replace(",","",$InvoiceTotals['Invoice_Cash_Total']);
		$newtotal= number_format(floatval($temptotal),2, '.', '');
		$attributes = array( 'id' => 'ReportFilter');
		$hidden = array('Client' => $ClientInfo->CompanyName,
						'DueDays' => $ClientInfo->PaymentDueDate,
						'Currency' => $ClientInfo->Currency,
						'PriceEdits' => $ClientInfo->PriceEdits,
						'DueDays' => $ClientInfo->PaymentDueDate,
						'InvoiceNumber' => $InvoiceTotals['Invoice_Number'] ,
						'PricePerHour' => $InvoiceTotals['Invoice_Price'] ,
						'BilledHours' => $InvoiceTotals['Total_Billed_Hours'],
						'InvoiceTotal' => $InvoiceTotals['Invoice_Cash_Total'],								
						'SumHours' => $InvoiceTotals['SumHours'],										
						'SumEditedSlides' => $InvoiceTotals['SumEditedSlides'],										
						'SumNewSlides' => $InvoiceTotals['SumNewSlides'],
						'Originators' => $InvoiceTotals['Originators'],
						'ExcludeList' => $excludecommas,											
						'StartDate' => $StartDate,								
						'EndDate' => $EndDate								
						);
		
		$NewInvoiceForm ="";
		

		$NewInvoiceForm .=form_open(site_url().'invoicing/zt2016_create_invoice',$attributes,$hidden)."\n";

		$NewInvoiceForm .="<fieldset>";

		$ndata = array('name' => 'submit','value' => 'Create','class' => 'submitButton btn btn-success btn-b');
		$NewInvoiceForm .= form_submit($ndata)."\n";
		$NewInvoiceForm .="</fieldset>";
		$NewInvoiceForm .= form_close()."\n";

		return $NewInvoiceForm;
	}

	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Generate invoice number
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _generate_invoice_number
	 * Provides first available invoice number for the month (and client)
	 */
	
	function _generate_invoice_number($ClientInfo, $EndDate){


		$InvoiceNumber = $ClientInfo->ClientCode;
		//$InvoiceNumber .= date("Ym");
		//$TestInvoiceNumber .= $InvoiceNumber.'01';
		$InvoiceCount=0;
		$finished = false;
		while (! $finished)
		{ 
			$InvoiceCount+=1;
			if ($InvoiceCount<10) {
				$TestInvoiceNumber = $InvoiceNumber.date("mY",strtotime($EndDate)).'0'.$InvoiceCount;
			}
			else{
				$TestInvoiceNumber = $InvoiceNumber.date("mY",strtotime($EndDate)).$InvoiceCount;
			}
			$this->db->select('InvoiceNumber');
			$this->db->from('zowtrakinvoices');
			//$this->db->limit(1);
			$this->db->where("InvoiceNumber ='".$TestInvoiceNumber ."'"); 
			$query = $this->db->get();
			if ($query->num_rows()==0) { $finished = true;   }
		}
		$InvoiceNumber=$TestInvoiceNumber;

		return $InvoiceNumber;
	}

}

/* End of file zt2016_new_client_invoice.php */
/* Location: ./application/controllers/invoicing/zt2016_new_client_invoice.php */

?>
