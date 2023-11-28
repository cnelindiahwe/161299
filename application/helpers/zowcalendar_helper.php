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
 * @author		Zebra On Wheels

 */



// ------------------------------------------------------------------------

/**
 * getCalendarData($getentries,$CalendarMonth)
 *
 * Gets calendar data for tracking page
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getCalendarData'))
{

	// ################## Load invoice list ##################	
	function  getCalendarData($getentries,$CalendarMonth,$CalendarYear)
	{
		if ($getentries!="") {
		foreach($getentries as $project)
			{
				$EntryDate=$project->DateOut;
				//$EntryMonth=date( 'm',strtotime($EntryDate));
				//$EntryYear=date( 'Y',strtotime($EntryDate));
				//if ($EntryMonth==$CalendarMonth && $EntryYear==$CalendarYear ){
					if ($project->Status!="COMPLETED" && $project->Status!="BILLED" && $project->Status!="PAID"){
						$EntryDay=date( 'd',strtotime($EntryDate));
						//remove leading 0s;
						$EntryDay= ltrim($EntryDay, "0");
						if (!isset($CalendarData[$EntryDay])){
							$CalendarData[$EntryDay]="";
						}
						$CalendarData[$EntryDay].="<a href=\"".site_url()."editentry/".$project->id."\" class=\"";
							$CalendarEmtryClass=strtolower($project->Status);
							$CalendarEmtryClass=str_replace (" ", "", $CalendarEmtryClass);
						$CalendarData[$EntryDay].=$CalendarEmtryClass." projectlink\" >";
						$CalendarData[$EntryDay].=$project->Code;

						if ($project->NewSlides !=0){
							$CalendarData[$EntryDay].=" ".$project->NewSlides.'N ';
							}
						if ($project->EditedSlides !=0){
							$CalendarData[$EntryDay].=" ".$project->EditedSlides.'E ';
							}
						if ($project->Hours !=0){
							$CalendarData[$EntryDay].=" ".$project->Hours.'H ';
							}
						if ($project->Status =="SCHEDULED")
						{
							$CalendarData[$EntryDay].=" ".$project->ScheduledBy;
						}
						elseif ($project->Status =="IN PROGRESS")
						{
							$CalendarData[$EntryDay].=" ".$project->WorkedBy;
						}
						elseif ($project->Status =="IN PROOFING")
						{
							$CalendarData[$EntryDay].=" ".$project->ProofedBy;
						}
						$CalendarData[$EntryDay].="</a>\n";
						
					}
					//Completed projects count
					else {
						$EntryDay=date( 'd',strtotime($EntryDate));

						$EntryDay= ltrim($EntryDay, "0");
						if (!isset($CalendarData[$EntryDay])){
							$CalendarData[$EntryDay]="";
						}
						$CalendarData[$EntryDay].="<a href=\"".site_url()."editentry/".$project->id."\" class=\"";
							$CalendarEmtryClass=strtolower($project->Status);
							$CalendarEmtryClass=str_replace (" ", "", $CalendarEmtryClass);
						$CalendarData[$EntryDay].=$CalendarEmtryClass." projectlink\">";
						$CalendarData[$EntryDay].=$project->Code;
						if ($project->NewSlides !=0){
							$CalendarData[$EntryDay].=" ".$project->NewSlides.'N ';
							}
						if ($project->EditedSlides !=0){
							$CalendarData[$EntryDay].=" ".$project->EditedSlides.'E ';
							}
						if ($project->Hours !=0){
							$CalendarData[$EntryDay].=" ".$project->Hours.'H ';
							}
						$CalendarData[$EntryDay].="</a>\n";
						
					}
				//}
			
			}
		}

	if (!isset($CalendarData)) {
				$CalendarData='';
			}

	return $CalendarData;

	}
}
/* End of file zowcalendar_helper.php */
/* Location: ./system/application/helpers/zowcalendar_helper.php */