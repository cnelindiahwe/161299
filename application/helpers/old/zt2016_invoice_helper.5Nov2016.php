<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2010 - 2009, Zebra On WHeels
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Client Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */


// ------------------------------------------------------------------------

/**
zt2016_zt2016_displayInvoice($invoice,$clientName)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('zt2016_InvoiceTotalsByNumber'))
{

	function  zt2016_InvoiceTotalsByNumber($invoiceTotals,$clientInfo)
	{
	
		/**/
		//Determine invoice status
		if ($invoiceTotals->Status=="PAID") {
			$invoiceStatus = "PAID";
			$PanelType = "panel-success";
		} 
		else if ($invoiceTotals->Status=="BILLED") {
		 	$invoiceStatus = "BILLED";
			$PanelType = "panel-primary";
		 	
		 	$duedate = new DateTime($invoiceTotals->DueDate);
			$now = new DateTime();
			
			if($duedate < $now) {
				 $invoiceStatus .= " - OVERDUE";
				 $PanelType = "panel-danger";
			}
		}
		else if ($invoiceTotals->Status=="WAIVED") {
			$invoiceStatus = "WAIVED";
			$PanelType = "panel-warning";
		}

		else if ($invoiceTotals->Status=="MARKETING") {
			$invoiceStatus = "MARKETING";
			$PanelType = "panel-info";
		}

		
		$pageOutput='<div class="panel '.$PanelType.'"><div class="panel-heading">'."\n"; 
		

		$pageOutput.='<h3 class="panel-title">Invoice '.$invoiceTotals->InvoiceNumber.' for '.$invoiceTotals->Client.' ('.$invoiceStatus.')</h3>';
		$pageOutput.="</div><!--panel-heading-->\n";
		$pageOutput.='<div class="panel-body">'."\n";
		
		
 		$pageOutput.='	<div class="row" style="padding-bottom:1em;">';		
		$pageOutput.='		<div class="col-md-4">';
		
 		//Invoice status form
		$attributes='class="form-inline" id="invoice-status-form"';
		$pageOutput.=form_open(site_url().'invoicing/zt2016_invoicestatus',$attributes )."\n";
 		$pageOutput.='				<div class="form-group">';
      	$pageOutput.='					<div class="input-group input-group-sm">';
      	$pageOutput.='						<span class="input-group-addon" id="basic-addon1">Status</span>';
		if ($invoiceTotals->Status=='PAID') {
			$options = array('BILLED'=>'Billed', 'PAID'=>'Paid');
		}
		else if ($invoiceTotals->Status=='WAIVED') {
			$options = array('BILLED'=>'Billed', 'WAIVED'=>'Waived');
		}
		else if ($invoiceTotals->Status=='MARKETING') {
			$options = array('BILLED'=>'Billed', 'MARKETING'=>'Marketing');
		}
		else { //BILLED
			$options = array('CANCEL'=>'Cancel','BILLED'=>'Billed', 'PAID'=>'Paid', 'MARKETING'=>'Marketing','WAIVED'=>'Waived',);
		}
		$more = 'id="InvoiceStatus" class="Status form-control" aria-describedby="basic-addon1"';	
		$pageOutput .=form_dropdown('Status', $options,$invoiceTotals->Status,$more);
		
 		$pageOutput.='					</div>';
 		
 		if ($invoiceTotals->Status=='BILLED') {
	 		$dateplaceholder=date("d M Y");
			$pageOutput.='<div class="input-append date" id="dp3" data-date="'.$dateplaceholder.'" data-date-format="dd mm yyyy" style="display:inline;">'."\n";
	      	$pageOutput.='<input type="text" class="form-control datepicker" value="'.$dateplaceholder.'" id="invoicedateinput" name="InvoiceDate" >'."\n";
			$pageOutput.='</div>';
		} 		
		$pageOutput.=form_hidden('Invoice',$invoiceTotals->InvoiceNumber);
		$pageOutput.=form_hidden('Client',$invoiceTotals->Client);
 		$pageOutput.='					<button type="submit" class="btn btn-sm">Change</button>';
 		$pageOutput.='				</div>';
 		$pageOutput.='			</form>';
	 	$pageOutput.='		</div>';		
		$pageOutput.='		<div class="col-md-8 btn-toolbar">';
		$pageOutput.='				<a href="'.site_url().'invoicing/invoiceogoneform/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-info btn-b pull-right">Ogone</a>';
		$clientName= str_replace(' ', '_', $invoiceTotals->Client);
		$pageOutput.='				<a href="'.site_url().'invoicing/zt2016_csvpastinvoice/'.$clientName.'/'.$invoiceTotals->InvoiceNumber.'" class="btn btn-primary pull-right">Export</a>';
		$pageOutput.='		</div>';		
  		$pageOutput.='	</div>';		

 		//Data row
 		$pageOutput.='	<div class="row">';	
			
		$pageOutput.='<div class="col-sm-4">'."\n";

		$pageOutput.='	<ul class="list-group">'."\n";
		
		$pageOutput.='		<li class="list-group-item">'."\n";
		$pageOutput.='		Billed date'."\n";
		$pageOutput.='		<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->BilledDate)).'</span> '."\n";
		$pageOutput.='		</li>'."\n";

		$pageOutput.='		<li class="list-group-item">'."\n";
		$pageOutput.='		Payment period (days)'."\n";
		$pageOutput.='		<span class="badge badge-primary">'.date('d M Y',strtotime($clientInfo->PaymentDueDate)).'</span> '."\n";
		$pageOutput.='		</li>'."\n";

		$pageOutput.='		<li class="list-group-item">'."\n";
		if ($invoiceStatus=="PAID") {
			$pageOutput.='		Paid date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
		} else if ($invoiceTotals->Status=="BILLED") {
			$pageOutput.='		Due date'."\n";

			if ($invoiceStatus=="BILLED - OVERDUE") {
				$pageOutput.='		<span class="badge badge-danger">';
			} ELSE {
				$pageOutput.='		<span class="badge badge-warning">';
			}
			$pageOutput.=date('d M Y',strtotime($invoiceTotals->DueDate)).'</span>' ."\n";		
		}	 else if ($invoiceTotals->Status=="WAIVED") {
			$pageOutput.='		Waived date'."\n";
			$pageOutput.='	<span class="badge badge-primary">'.date('d M Y',strtotime($invoiceTotals->PaidDate)).'</span>' ."\n";
			
		}	
		$pageOutput.='		</li>'."\n";
		$pageOutput.='	</ul>'."\n";
		$pageOutput.='</div><!--col-->'."\n";

		$pageOutput.='<div class="col-sm-4">'."\n";
				
		$pageOutput.='<ul class="list-group">'."\n";
		
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Total'."\n";
		$pageOutput.='	<span class="badge badge-success">'.$invoiceTotals->InvoiceTotal.'</span> '.$clientInfo->Currency."\n";
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Price per hour'."\n";
		$pageOutput.='	<span class="badge badge-info">'.$invoiceTotals->PricePerHour .'</span>'.$clientInfo->Currency."\n";
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Billed hours'."\n";
		$pageOutput.='	<span class="badge badge-warning">'.$invoiceTotals->BilledHours.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";
		$pageOutput.='</ul>'."\n";
		$pageOutput.='</div><!--col-->'."\n";

		
		$pageOutput.='<div class="col-sm-4">'."\n";
		
		$pageOutput.='<ul class="list-group">'."\n";
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	New Slides'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumNewSlides.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";

		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Edited Slides'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumEditedSlides.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";
		
		$pageOutput.='	<li class="list-group-item">'."\n";
		$pageOutput.='	Hours'."\n";
		$pageOutput.='	<span class="badge badge-default">'.$invoiceTotals->SumHours.'</span>' ."\n";
		$pageOutput.='	</li>'."\n";

		
		$pageOutput.='</ul>'."\n";
 		$pageOutput.='</div><!--col-->'."\n";		
 		$pageOutput.='</div><!--row--> '."\n";		
	
 		
 		//Invoice data
		$pageOutput.= zt2016_getInvoiceTableByNumber($invoiceTotals->InvoiceNumber,$clientInfo);

		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		//

		 
		//$pageOutput ="test"; 
		return $pageOutput;/**/

	}

}




// ------------------------------------------------------------------------

/**
 * zt2016_getInvoiceByNumber($invoice,$clientName='')
 *
 * Retrieves all entries for a given invoice number
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('zt2016_getInvoiceTableByNumber'))
{

	function  zt2016_getInvoiceTableByNumber($invoice,$clientInfo)
	{
		if ($invoice==""){
			return false;
		}
		else
		{
			$CI =& get_instance();
			$CI->load->model('trakentries', '', TRUE);
			$getentries = $CI->trakentries->GetEntry($options = array('Invoice' => $invoice,'sortBy'=> 'id','sortDirection'=> 'asc'));
			if($getentries)
			{
				$InvoiceTotals=InvoiceTotalsByNumber($invoice,$clientInfo);
				//$entries=$InvoiceTotals['page'];
				$entries="";
				$entries.= "<p>Total Jobs: <span class=\"badge\">".count($getentries)."</span></p>";
				$entries.= "<div class=\"table-responsive\">\n";
				$entries.= "	<table id=\"currententries\" class=\"table\">\n";
				$entries .= "		<thead>\n";
				$entries .= "			<tr><th class=\"header\">Client</th><th class=\"header\">Date</th><th class=\"header\">Originator</th><th class=\"header\"># New Slides</th><th class=\"header\"># Edited Slides</th><th class=\"header\"># Hours</th><th>File Name</th><th>Team</th></tr>\n";//<th class=\"button\"></th>
				$entries .= "		</thead>\n";
				
				$entries .= "		<tbody>\n";
				foreach($getentries as $project)
				{
					$entries .= "			<tr>";
					$entries .= "<td>".$project->Client . "</td>";
					//Converts MySQL date
					$mysqldate = date( 'd/M/Y',strtotime($project->DateIn));
					$entries .= "<td class=\"date\">".$mysqldate. "</td>";
					$entries .= "<td>".$project->Originator . "</td>";
					if ($project->NewSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->NewSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->EditedSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->EditedSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->Hours > 0) {
						$entries .= "<td class=\"slides\">".$project->Hours . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
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
					
					//$entries .= "<td class=\"button edit\"><a href=\"".base_url()."editentry/".$project->id . "\" class=\"edit\">Edit</a></td>";

					$entries .= "</tr>\n";
				}
				$entries .= "		</tbody>\n";
				$entries .= "	</table>\n";
				$entries .= "</div>\n";
			}
			else
			{
				$entries = "<p>ERROR - No entries found for invoice ".$invoice.".</p>\n";


			}
		return $entries;
		}
	}


}


// ------------------------------------------------------------------------

/**
 * InvoiceTotalsByNumber($client,$StartDate,$EndDate)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('InvoiceTotalsByNumber'))
{

	function  InvoiceTotalsByNumber($invoice,$clientInfo)
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
		  //$CI->db->count_all_results();
		  $CI->db->from('zowtrakentries');
		  $CI->db->where('Invoice',$invoice);
		  $CI->db->where('Trash =',0);
		  $query = $CI->db->get();

		  //Get client details from db
		  //$CI->load->model('trakclients', '', TRUE);
		  
		  
		  //$query2 = $CI->trakclients->GetEntry($options = array('CompanyName' => $clientName));

		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$clientInfo->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

		  //$total = "<p>".$this->db->last_query()."</p>";

		  $total ="<div id=\"reportTotals\">\n";
		  $total .="   <div id=\"reportMain\">\n";
		  $total .="      <h3>\n";
		  $total .="      <a href=\"".site_url()."invoicing/clientinvoices/".$clientInfo->ID."\">".$clientInfo->CompanyName."</a> invoice number: ".$invoice;
		  //$total .= " <br/> from  ".$StartDate." to  ".$EndDate.".\n ";
		  $total .= "      <br/>\n" ;
		  $total .= "      Total hours billed: ".$htotal;
		 // if ($query2['0']->Retainer!=0){
		//		if (date("m",strtotime($StartDate))==date("m",strtotime($EndDate))){
		//			$Retainerleft=$query2->Retainer-$htotal;
		//			$total .="      <br />Hours left in retainer: ".$Retainerleft;
		//		}
		 // }
		 
		  $total .= " | Price: ";
		  
		  $CI =& get_instance();

				  //Get entry totals from db

				  
				  $CI->db->select('PricePerHour');
				  $CI->db->from('zowtrakinvoices');
				  $CI->db->where('InvoiceNumber',$invoice);
				  $CI->db->where('Trash =',0);
		  		  $query3 = $CI->db->get();

		 	 $tprice =$query3->row()->PricePerHour;

			 $total .=number_format($tprice, 2, '.', ',')." ".$clientInfo->Currency;
			 $invoicetotal=number_format($tprice*$htotal, 2, '.', ',');
			 $total .= " | Total: ".number_format($tprice*$htotal, 2, '.', ',')." ".$clientInfo->Currency;
		  $total .= "      </h3>\n" ;
		  $total .= "  </div>\n" ;

		  $total .="  <div id=\"totals\"><p>\n";
		  //if ($query->row()->Hours!=0){ 
		  	$total .=$query->row()->Hours." Hours | \n";
		 //}
		  //if ($query->row()->NewSlides!=0){ 
		  		$total .=$query->row()->NewSlides." New | \n";
			//}
		  //if ($query->row()->EditedSlides!=0){ 
		  		$total .=$query->row()->EditedSlides." Edits \n";
			//}


		  $total .=" </p>\n";
		  //If client has retainer, show numbers
		  //if ($query2['0']->Retainer!=0){
		//	  $total .="      <p id=\"retainer\">Retainer: ".$query2->Retainer."</p>\n";
		 // }
		$total .="  </div>\n";
		//$total .=getPastInvoices($client);		
		$total .="  </div>\n";
	//return $total;
		$invoicedata ['page']=$total ;
		$invoicedata ['price']=$tprice ;
		$invoicedata ['billedhours']=$htotal ;
		$invoicedata ['invoicetotal']=$invoicetotal ;
		$invoicedata ['invoicejobs']=$query->row()->EditedSlides ;
		$invoicedata ['SumHours']=$query->row()->Hours ;
		$invoicedata ['SumEditedSlides']=$query->row()->NewSlides ;
		$invoicedata ['SumNewSlides']=$query->row()->EditedSlides ;
		
	return $invoicedata;

	}

}


/* End of file zt2016_invoice_helper.php */
/* Location: ./system/application/helpers/invoice_helper.php */