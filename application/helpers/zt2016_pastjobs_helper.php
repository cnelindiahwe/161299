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
 *   _Past_Jobs_Control
 *
 * display past jobs table controls
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists('_Past_Jobs_Control'))
{



	// ################## past jobs control ##################	
	function   _Past_Jobs_Control($PastJobsFilters,$ClientsData,$ClientContacts)
	{
		$attributes['id'] = 'PastJobsControlForm';
		$attributes['class'] = 'form pull-right';
		
		if (isset($PastJobsFilters['URL'])){
			$formURL = $PastJobsFilters['URL'];
		}else{
			$formURL ="tracking/zt2016_tracking";
		}

		$pastjobscontrol= form_open(site_url().$formURL ,$attributes);
		 $pastjobscontrol.="<div class=\"form-group\">\n";

			
			//Submit
		     $more = 'id="PastJobsSubmit" class="form-control btn"';	
			$pastjobscontrol .=form_submit('PastJobsSubmit', 'Change',$more);

		
			//View type
			$options = array('list' => 'List', 'date' => 'Date',  'calendar'=>'Calendar');
			$more = 'id="PastJobsViewType" class="form-control"';	
			$selected=$PastJobsFilters['PastJobsViewType'];
			$pastjobscontrol .=form_label('View as:','PastJobsViewType');
			$pastjobscontrol .=form_dropdown('PastJobsViewType', $options,$selected,$more);
			
			
			//jobs listed
			if ($PastJobsFilters['PastJobsViewType']=='list'){
				$options = array('10' => '10', '20'=>'20', '50'=>'50', '100'=>'100', '200'=>'200', '400'=>'400');
				$more = 'id="NumberPastJobs" class="form-control"';	
				$selected=$PastJobsFilters['NumberPastJobs'];
				$pastjobscontrol .=form_label('Jobs listed:','NumberPastJobs');
				$pastjobscontrol .=form_dropdown('NumberPastJobs', $options,$selected,$more);
				$options ="";
			}
			
			//Clients
			if ($PastJobsFilters['PastJobsViewType']=='list' || $PastJobsFilters['PastJobsViewType']=='calendar'){
				$options=[];
				foreach($ClientsData as $client)
				{
					$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array(''=>"All")+$options;		
				$more = 'id="PastJobsClient" class="form-control"';	

				$selected=$PastJobsFilters['PastJobsClient'];
				$pastjobscontrol .=form_label('Client:','PastJobsClient');
				$pastjobscontrol .=form_dropdown('PastJobsClient', $options,$selected ,$more);
				$options ="";


				if ($PastJobsFilters['PastJobsClient']!='all'){
					//Originator
					$options=array();
					foreach($ClientContacts as $Contact)
					{
						$originator=$Contact->FirstName." ".$Contact->LastName;
						$options[$originator]=$originator;
					}
					asort($options);
					$options=array(''=>"All")+$options;		
					$more = 'id="PastJobsOriginator" class="form-control"';	

					$selected=$PastJobsFilters['PastJobsOriginator'];
					$pastjobscontrol .=form_label('Originator:','PastJobsOriginator');
					$pastjobscontrol .=form_dropdown('PastJobsOriginator', $options,$selected ,$more);						
				}
			}

			//date box
			if ($PastJobsFilters['PastJobsViewType']=='date'){
				if (empty ($PastJobsFilters['PastJobsDate'])){
					$dateplaceholder=date("d M Y");
				} else{
					$dateplaceholder=date("d M Y",strtotime($PastJobsFilters['PastJobsDate'])); 
				}

				$pastjobscontrol.='<div class="input-append date" id="pastjobsdp" data-date="'.$dateplaceholder.'" data-date-format="dd mm yyyy" style="display:inline;">'."\n";
				$pastjobscontrol.=form_label('Date:','PastJobsDate');
				$pastjobscontrol.='<input type="text" class="form-control datepicker" value="'.$dateplaceholder.'" id="PastJobsDate" name="PastJobsDate" >'."\n";
				$pastjobscontrol.='</div>'."\n";
			}		
		
		
		
		$pastjobscontrol.="</div>\n";//form group
		$pastjobscontrol .= form_close()."\n";
		return $pastjobscontrol;
	
	}


}


// ------------------------------------------------------------------------
/**
 *   _Display_Past_Entries_Table
 *
 * display past jobs table
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists(' _Display_Past_Entries_Table'))
{

		
	// ################## display contacts info ##################	
	function _Display_Past_Entries_List_Table($RawPastEntries)
	{
		
		$EntriesTable="\n";
		
		// print_r($RawPastEntries);
		
		if (!is_array($RawPastEntries)) {
				$EntriesTable .=$RawPastEntries;
		} 
			
		else
		
		{

			$EntriesTable .='<table class="table table-striped  table-condensed responsive dtr-inline" style="width:100%;display:none;" id="past_jobs_table">'."\n";
			
			//$EntriesTable .='<table class="table table-striped  table-condensed responsive dtr-inline" style="width:100%;" id="pastg_jobs_table">'."\n";
		
			$EntriesTable .="<thead><tr><th data-sortable=\"true\">New</th><th data-sortable=\"true\">Edits</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\">Team</th><th data-sortable=\"true\">Date and Time Out</th><th data-sortable=\"true\">File Name</th><th data-sortable=\"true\">Status</th></tr></thead>\n";

			$EntriesTable .="<tbody>\n";
		
		
			if (!empty($RawPastEntries )) {
				foreach($RawPastEntries as $EntryKey => $EntryDetails)
				{


				$EntriesTable .="<tr>\n";	


				$rowlink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails->id."\" class=\"projectlink\" >";

				$EntriesTable .="<td>";			
					if ($EntryDetails->NewSlides !=0){
							$EntriesTable .=$rowlink.$EntryDetails->NewSlides."</a>";
						}
				$EntriesTable .="</td>\n";

				$EntriesTable .="<td>";			
					if ($EntryDetails->EditedSlides !=0){
							$EntriesTable .=$rowlink.$EntryDetails->EditedSlides ."</a>";
						}
				$EntriesTable .="</td>\n";

				$EntriesTable .="<td>";			
					if ($EntryDetails->Hours !=0){
							$EntriesTable .=$rowlink.$EntryDetails->Hours ."</a>";
						}
				$EntriesTable .="</td>\n";			



				$ClientCodeRowLink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails->id."\" data-toggle=\"tooltip\" title=\"".$EntryDetails->Client."\" class=\"projectlink\" >";
					$EntriesTable .="<td>".$ClientCodeRowLink.$EntryDetails->Code."</a></td>\n";					


				$EntriesTable .="<td>";			
							$EntriesTable .=$rowlink.$EntryDetails->Originator."</a>";
				$EntriesTable .="</td>\n";				



				$EntriesTable .="<td>";	
				$CI = get_instance();

					// You may need to load the model if it hasn't been pre-loaded
						$CI->load->model('zt2016_users_model');
						$have_data = 0;
						$EntriesTable.='<small>'.$rowlink;
						if ($EntryDetails->ScheduledBy != $EntryDetails->WorkedBy){
						
						$num_WorkedBy = (int) $EntryDetails->WorkedBy;
						if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
						}else{

							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy);
		
						}
						$have_data = 1;
						$EntriesTable.="".ucfirst($num_WorkedBy->fname);
						if( !empty($EntryDetails->WorkedBy_2) && $EntryDetails->has_multi_worked == 1){
							
							$num_WorkedBy = (int) $EntryDetails->WorkedBy_2;
						if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
						}else{
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy_2);
		
						}
						$have_data = 1;
						$EntriesTable.=", ".ucfirst($num_WorkedBy->fname);
						}else if( !empty($EntryDetails->WorkedBy_3) && $EntryDetails->has_multi_worked == 1){
							$num_WorkedBy = (int) $EntryDetails->WorkedBy_3;
						if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
						}else{
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy_3);
		
						}
						$have_data = 1;
						$EntriesTable.=", ".ucfirst($num_WorkedBy->fname);
						}
						}else{
						$num_WorkedBy = (int) $EntryDetails->ScheduledBy;
						if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
						}else{
							
							$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->ScheduledBy);
		
						}
						$have_data = 1;
						$EntriesTable.="".ucfirst($num_WorkedBy->fname);
					}
					if ($EntryDetails->ProofedBy != $EntryDetails->WorkedBy  && $EntryDetails->ProofedBy != $EntryDetails->WorkedBy_2 && $EntryDetails->ProofedBy != $EntryDetails->WorkedBy_3){
							
						
						$num_ProofedBy = (int) $EntryDetails->ProofedBy;
						if (  is_numeric($num_ProofedBy) && $num_ProofedBy !=0) {
							$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_ProofedBy);
							
						}else{
							$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->ProofedBy);
		
						}
						if($have_data == 1){
							$EntriesTable.=", ".ucfirst($num_ProofedBy->fname);

						}else{
						$EntriesTable.="".ucfirst($num_ProofedBy->fname);

						}

					}

				// $num_ScheduledBy = (int) $EntryDetails->ScheduledBy;
				// if (  is_numeric($num_ScheduledBy) && $num_ScheduledBy !=0) {
				// 	$num_ScheduledBy= $CI->zt2016_users_model->getsuer_name_by_id($num_ScheduledBy);
					
				// }else{
				// 	$num_ScheduledBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->ScheduledBy);

				// }
					// $EntriesTable.='<small>'.$rowlink.$EntryDetails->ScheduledBy;
					// if ($EntryDetails->ScheduledBy != $EntryDetails->WorkedBy){
						
					// 	$num_WorkedBy = (int) $EntryDetails->WorkedBy;
					// 	if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
					// 	}else{
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy);
		
					// 	}
					// 	$EntriesTable.=", ".ucfirst($num_WorkedBy->fname);
					// 	if( !empty($EntryDetails->WorkedBy_2) && $EntryDetails->has_multi_worked == 1){
							
					// 		$num_WorkedBy = (int) $EntryDetails->WorkedBy_2;
					// 	if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
					// 	}else{
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy_2);
		
					// 	}
					// 	$EntriesTable.=", ".ucfirst($num_WorkedBy->fname);
					// 	}else if( !empty($EntryDetails->WorkedBy_2) && $EntryDetails->has_multi_worked == 1){
					// 		$num_WorkedBy = (int) $EntryDetails->WorkedBy_3;
					// 	if (  is_numeric($num_WorkedBy) && $num_WorkedBy !=0) {
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_WorkedBy);
							
					// 	}else{
					// 		$num_WorkedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->WorkedBy_3);
		
					// 	}
					// 	$EntriesTable.=", ".ucfirst($num_WorkedBy->fname);
					// 	}
					// }
					// if ($EntryDetails->ProofedBy != $EntryDetails->ScheduledBy && $EntryDetails->ProofedBy != $EntryDetails->WorkedBy){
						
						
					// 	$num_ProofedBy = (int) $EntryDetails->ProofedBy;
					// 	if (  is_numeric($num_ProofedBy) && $num_ProofedBy !=0) {
					// 		$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_ProofedBy);
							
					// 	}else{
					// 		$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->ProofedBy);
		
					// 	}


					// 	$EntriesTable.=", ".ucfirst($num_ProofedBy->fname);
					// }

					// if ($EntryDetails->CompletedBy != $EntryDetails->ScheduledBy && $EntryDetails->CompletedBy != $EntryDetails->WorkedBy && $EntryDetails->CompletedBy != $EntryDetails->ProofedBy){
						
					// 	$num_ProofedBy = (int) $EntryDetails->ScheduledBy;
					// 	if (  is_numeric($num_ProofedBy) && $num_ProofedBy !=0) {
					// 		$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_id($num_ProofedBy);
							
					// 	}else{
					// 		$num_ProofedBy= $CI->zt2016_users_model->getsuer_name_by_string($EntryDetails->ScheduledBy);
		
					// 	}
					// 	$EntriesTable.=", ".ucfirst($num_ProofedBy->fname);
					// }
					$EntriesTable.="</a></small>";
				$EntriesTable .="</td>\n";


				$duetimestamp= _getpendinghours($EntryDetails->DateOut,$EntryDetails->TimeOut,$EntryDetails->TimeZoneOut,1);	


				$EntriesTable .="<td data-order=\"".$duetimestamp['duetimestamp']."\"><small>";			
				//$EntriesTable .="<td><small>";			

						$DateTimeOut = date('H:i',strtotime($EntryDetails->TimeOut))." ";				
						$DateTimeOut .=date('D, j M Y',strtotime($EntryDetails->DateOut))." ";
						$TimeZoneOut=preg_split('/\//', $EntryDetails->TimeZoneOut);
						$DateTimeOut .= str_replace("_"," ",$TimeZoneOut[1]);	

						$EntriesTable .=$rowlink.$DateTimeOut."</a>";

				$EntriesTable .="</small></td>\n";					

				$EntriesTable .="<td><small>";			
							$EntriesTable .=$rowlink.$EntryDetails->FileName."</a>";
				$EntriesTable .="</small></td>\n";		

				$EntriesTable .="<td><small>";			
							$EntriesTable .=$rowlink.$EntryDetails->Status."</a>";
				$EntriesTable .="</small></td>\n";

				}
			}

			$EntriesTable .="<tfoot><tr><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Status</th></tr></tfoot>\n";
			$EntriesTable .="</table>\n";
		
		}
		return $EntriesTable;

	}
}

// ------------------------------------------------------------------------
/**
 *  _getpendinghours
 *
 * get pending hours between dates
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _getpendinghours'))
{
	function  _getpendinghours($DateOut,$TimeOut,$TimeZoneOut,$NoDiff=0)
	{

		// create the DateTimeZone object for later
		$dtzone = new DateTimeZone($TimeZoneOut);
		
		date_default_timezone_set($TimeZoneOut);
		
		
		// Get job's due time
		$datetimeout = strtotime($DateOut." ".$TimeOut);
		//  convert due into a string representing the local time
		$timedue = date('r', $datetimeout);
		
		//die ($timedue);
		$duetime = new DateTime($timedue, $dtzone);
		//$duetime ->setTimeZone($dtzone);
		$pendinghours['duetimestamp']=date('U', $datetimeout);

		
		
		//$pendinghours['duetimestamp']=date('U', $datetimeout);
		
		if ($NoDiff==0) {
			// Get server current time
			$timestamp = time();
			//  convert the server timestamp into a string representing the local time
			$timenow = date('r', $timestamp);
			// now create the DateTime object for this time
			$dtime = new DateTime($timenow,$dtzone);
			// convert this to the client's timezone using the DateTimeZone object
			//$dtime->setTimeZone($dtzone);	
		
			$diff = strtotime($duetime->format('H:i  m/d/y'))-strtotime($dtime->format('H:i  m/d/y'));

			$pendinghours['days'] = intval($diff/24/60/60);
			$remain=$diff%86400; 
			$pendinghours['hours']=intval($remain/3600); 
		
			$remain=$remain%3600;
			$pendinghours['raw']=$diff;
			$pendinghours['mins']=intval($remain/60);
			$pendinghours['currenttime']=$dtime->format('H:i  m/d/y');
			$pendinghours['duetime']=$duetime->format('H:i  m/d/y');
		}

		return $pendinghours;
	}
}





/* End of file zt2016_pastjobs_helper.php */
/* Location: ./system/application/helpers/zt2016_pastjobs_helper.php */