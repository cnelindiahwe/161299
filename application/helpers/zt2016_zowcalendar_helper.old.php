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
if ( ! function_exists('getZOWCalendarData'))
{

	// ################## Load invoice list ##################	
	function  getZOWCalendarData($getentries,$CalendarMonth,$CalendarYear)
	{
		if ($getentries!="") {
		foreach($getentries as $project)
			{
				$EntryDate=$project->DateOut;

					if ($project->Status!="COMPLETED" && $project->Status!="BILLED" && $project->Status!="PAID"){
						$EntryDay=date( 'd',strtotime($EntryDate));
						//remove leading 0s;
						$EntryDay= ltrim($EntryDay, "0");
						if (!isset($CalendarData[$EntryDay])){
							$CalendarData[$EntryDay]="";
						}
	//					$CalendarData[$EntryDay].="<li><a href=\"".site_url()."editentry/".$project->id."\" class=\"";
						$CalendarData[$EntryDay].="<li><a href=\"".site_url()."zt2016_edit_job/".$project->id."\" class=\"";

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
						$CalendarData[$EntryDay].="</a></li>\n";
						
					}
					//Completed projects count
					else {
						$EntryDay=date( 'd',strtotime($EntryDate));

						$EntryDay= ltrim($EntryDay, "0");
						if (!isset($CalendarData[$EntryDay])){
							$CalendarData[$EntryDay]="";
						}
						$CalendarData[$EntryDay].="<li><a href=\"".site_url()."zt2016_edit_job/".$project->id."\" class=\"";
							$CalendarEmtryClass=strtolower($project->Status);
							$CalendarEmtryClass=str_replace (" ", "", $CalendarEmtryClass);
						$CalendarData[$EntryDay].=$CalendarEmtryClass." projectlink\" ";
						
						$CalendarData[$EntryDay].=" data-toggle=\"tooltip\"";
						$CalendarData[$EntryDay].=" title=\"".$project->Client." ".$project->Originator."\"";
						$CalendarData[$EntryDay].=">\n";
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
						$CalendarData[$EntryDay].="</a></li>\n";
						
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


// ------------------------------------------------------------------------
/**
 *   _Set_Calendar_Prefs
 *
 * display past jobs table
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists('_Set_Past_Entries_Calendar_Prefs'))
{

		
	// ################## display contacts info ##################	
	function  _Set_Past_Entries_Calendar_Prefs($suffix,$navtargeturl="zt2016_tracking")
	{
		
		
		$navurl=site_url().'tracking/'.$navtargeturl.'/monthview/';
		
		$prefs = array (
        	'show_next_prev'  => TRUE,
			'next_prev_url' => $navurl
        );
		
		
		
		$prefs['template'] = '

		   {table_open}<table id="monthview" class="table ZOWmonth-table">{/table_open}

		   {heading_row_start}<tr>{/heading_row_start}

		   {heading_previous_cell}<th style="width:15%;"><a href="{previous_url}'.$suffix.'#bottom-panel" class="monthviewnav">&lt;&lt;</a></th>{/heading_previous_cell}
		   {heading_title_cell}<th colspan="{colspan}" style="text-align:center;">{heading}</th>{/heading_title_cell}
		   {heading_next_cell}<th style="width:15%;"><a href="{next_url}'.$suffix.'#bottom-panel"  class="monthviewnav pull-right">&gt;&gt;</a></th>{/heading_next_cell}

		   {heading_row_end}</tr>{/heading_row_end}

		   {week_row_start}<tr class="weekdays">{/week_row_start}
		   {week_day_cell}<td style="width:14%;"><div>{week_day}</div></td>{/week_day_cell}
		   {week_row_end}</tr>{/week_row_end}

		   {cal_row_start}<tr>{/cal_row_start}
		   {cal_cell_start}<td>{/cal_cell_start}

		   {cal_cell_content}<div class="CalendarDay"><strong>{day}</strong></div><div><ul>{content}</ul></div>{/cal_cell_content}
		   {cal_cell_content_today}<div class="CalendarDay"><strong>{day}</strong></div><div><ul>{content}</ul></div>{/cal_cell_content_today}

		   {cal_cell_no_content}<div class="CalendarDay">{day}</div>{/cal_cell_no_content}
		   {cal_cell_no_content_today}<div class="CalendarDay">{day}</div>{/cal_cell_no_content_today} 

		   {cal_cell_start_today}<td class="today">{/cal_cell_start_today} 
		   {cal_cell_end_today}</td>{/cal_cell_end_today} 

		   {cal_cell_blank}&nbsp;{/cal_cell_blank}

		   {cal_cell_end}</td>{/cal_cell_end}
		   {cal_row_end}</tr>{/cal_row_end}

		   {table_close}</table>{/table_close}
		';

		return $prefs;

	}
}/* End of file zowcalendar_helper.php */
/* Location: ./system/application/helpers/zowcalendar_helper.php */