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
zt2016_invoice_paneltype ($invoiceTotals)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('zt2016_invoice_paneltype'))
{

	function  zt2016_invoice_paneltype ($invoiceTotals)
	{
	
		/**/
		//Determine invoice status
		
		if (!isset($invoicePanelInfo)) $invoicePanelInfo = new stdClass();
		
		if ($invoiceTotals->Status=="PAID") {
			$invoicePanelInfo->Status = "PAID";
			$invoicePanelInfo->PanelType = "panel-success";
		} 
		else if ($invoiceTotals->Status=="BILLED") {
		 	$invoicePanelInfo->Status = "BILLED";
			$invoicePanelInfo->PanelType = "panel-primary";
		 	
		 	$duedate = new DateTime($invoiceTotals->DueDate);
			$now = new DateTime();
			
			if($duedate < $now) {
				 $invoicePanelInfo->Status .= " - OVERDUE";
				 $invoicePanelInfo->PanelType = "panel-danger";
			}
		}
		else if ($invoiceTotals->Status=="WAIVED") {
			$invoicePanelInfo->Status = "WAIVED";
			$invoicePanelInfo->PanelType = "panel-warning";
		}

		else if ($invoiceTotals->Status=="MARKETING") {
			$invoicePanelInfo->Status = "MARKETING";
			$invoicePanelInfo->PanelType = "panel-info";
		}

		
		
		return $invoicePanelInfo;

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
			$getentries = $CI->trakentries->GetEntry($options = array('Invoice' => $invoice,'sortBy'=> 'DateOut','sortDirection'=> 'asc'));
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
					$mysqldate = date( 'd/M/Y',strtotime($project->DateOut));
					$entries .= "<td class=\"date\">".$mysqldate. "</td>";
					
					
					//Linked Originator
					
					$SafeContactName=str_replace( "&","~", $project->Originator);
					$SafeContactName=str_replace( " ","_", $SafeContactName);
					
					$entries .= "<td><a href='".site_url()."contacts/zt2016_contact_info/".$SafeContactName."'>".$project->Originator. "</a></td>";
					
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
/* Location: ./system/application/helpers/zt2016_invoice_helper.php */