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
 *  _Display_Ongoing_Entries_Table
 *
 * Displays final ongoing job table)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _Display_Ongoing_Entries_Table'))
{
	// ################## create entries table ##################	
	function _Display_Ongoing_Entries_Table($OngoingEntries)
	{
 			//echo "_Display_Entries_Table<br/>";
			//var_dump($OngoingEntries['entries']);
			//die();	
		$EntriesTable="\n";
		// table-condensed
		$EntriesTable ='<table class="table table-striped table-hover  responsive dtr-inline" style="width:100%;display:none;" id="ongoing_jobs_table">'."\n";
		
		$EntriesTable .="<thead><tr><th data-sortable=\"true\">NEW</th><th data-sortable=\"true\">EDITS</th><th data-sortable=\"true\">HOURS</th>"."\n";
		
		$EntriesTable .="<th data-sortable=\"true\">DUE IN</th>"."\n";
			
		$EntriesTable .="<th data-sortable=\"true\" >STATUS</th><th data-sortable=\"true\">CLIENT</th><th data-sortable=\"true\">ORIGINATOR</th><th data-sortable=\"true\">ZOW</th><th data-sortable=\"true\">DEADLINE</th><th data-sortable=\"true\">FILE NAME</th></tr></thead>"."\n";
		

		$EntriesTable .="<tbody>\n";
		
		
		if (!empty ($OngoingEntries['entries'])) {

			
			foreach($OngoingEntries['entries'] as $EntryKey => $EntryDetails)
			{
			

				# STATUS
				$classes= " btn-xs btn-";

				 if ($EntryDetails['Status']=='TENTATIVE'){
							$classes.="default";
					 		$dataorder=1;
				} elseif ($EntryDetails['Status']=='SCHEDULED') {
							$classes.="success";
					 		$dataorder=2;
				} elseif ($EntryDetails['Status']=='IN PROGRESS'){
							$classes.="warning";
						 	$dataorder=3;
				} elseif ($EntryDetails['Status']=='IN PROOFING'){
								$classes.="danger";
								$dataorder=4;
				}
				// if(){

				// }

				$EntriesTable .="<tr>\n";	

					$rowlink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails['id'] . "\" class=\"projectlink\" >";
				
				##### New, edits hours
				$EntriesTable .="<td>";			
					if ($EntryDetails['NewSlides'] !=0){
							$EntriesTable .=$rowlink.$EntryDetails['NewSlides']."</a>";
						}
				$EntriesTable .="</td>\n";

				$EntriesTable .="<td>";			
					if ($EntryDetails['EditedSlides'] !=0){
							$EntriesTable .=$rowlink.$EntryDetails['EditedSlides'] ."</a>";
						}
				$EntriesTable .="</td>\n";

				$EntriesTable .="<td>";			
					if ($EntryDetails['Hours'] !=0){
							$EntriesTable .=$rowlink.$EntryDetails['Hours'] ."</a>";
						}
				$EntriesTable .="</td>\n";
				
				##### pending
				$EntriesTable .="<td class=\"text-right\" data-order=\"".$EntryDetails['DateTimeOutTimestmap']."\" style=\"text-align: right;\" >\n";
				
				$PendingRowLink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails['id']." \" data-toggle=\"tooltip\" title=\"".$EntryDetails['DateTimeOutTimestmap']."\" class=\"projectlink\" style=\"font-weight: 300 !important;\" \>";

				$EntriesTable .="<strong>".$PendingRowLink.$EntryDetails['Pending']."</a></strong>\n";
				$EntriesTable .="</td>\n";

				##### Status
				$StatusRowLink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails['id'] . "\" class=\"projectlink btn ".$classes."\"  style=\"width: 9em;padding: 4px 15px 2px 15px;\"> ";
				$EntriesTable .="<td data-order=\"".$dataorder."\" style=\"text-align: center;\">".$StatusRowLink.$EntryDetails['Status']."</a></td>\n";			

				##### Client code
				$ClientCodeRowLink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails['id'] . "\" data-toggle=\"tooltip\" title=\"".$EntryDetails['Client']."\" class=\"projectlink\" >";
				$EntriesTable .="<td>".$ClientCodeRowLink.$EntryDetails['ClientCode']."</a></td>\n";	
				
				##### Originator, ZOWmember
				
				$EntriesTable .="<td>".$rowlink.$EntryDetails['Originator']."</a></td>\n";
				
				$EntriesTable .="<td>".$rowlink.$EntryDetails['ZOWMember']."</a></td>\n";

				##### Deadline
				$DateTimeOutRowLink="<a href=\"".site_url()."zt2016_edit_job/".$EntryDetails['id'] . "\" data-toggle=\"tooltip\" title=\"".$EntryDetails['DateTimeOutNow']."\" class=\"projectlink\" >";
				$EntriesTable .="<td data-order=\"".$EntryDetails['DateTimeOutTimestmap']."\">".$DateTimeOutRowLink.$EntryDetails['DateTimeOut']."</a></td>\n";
				
				##### filename
				$EntriesTable .="<td>".$rowlink.$EntryDetails['FileName']."</a></td>\n";			

				$EntriesTable .="</tr>\n";

			}
			
		}
		
		$EntriesTable .="</tbody>\n";
		$EntriesTable .="<tfoot><tr><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\">ZOW Member</th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th></tr></tfoot>\n";		
		$EntriesTable .="</table>\n";

		return $EntriesTable;
	
	}
}




// ------------------------------------------------------------------------
/**
 * _process_ongoing_jobs
 *
 * ensures key ongoing hjobs fields are filled before displaying anything (to avoid errors)
 *
 * @access	public
 * @return	string
 */

	// ################## Get Ongoing Jobs Data ##################	
	function  _process_ongoing_jobs($ZOWuser,$RawOngoingEntries,$ContactsData)
	{
		if($RawOngoingEntries)
		{
			foreach($RawOngoingEntries as $project)
			{
				
				if	(empty($project->TimeZoneOut))
				{
					foreach($ContactsData as $ContactInfo){
						$ContactOriginator= $ContactInfo->FirstName." ".$ContactInfo->LastName;
						if ($project->Originator==$ContactOriginator and $project->Client == $ContactInfo_>CompanyName)
							{
								$project->TimeZoneOut =$ContactInfo->TimeZone;
							}
					}

					if	(empty($project->TimeZoneIn)){
						$project->TimeZoneIn =$ContactInfo->TimeZone;	
					}
				}
				
				########## add additional contact info
				foreach($ContactsData as $ContactInfo){
					$ContactOriginator= $ContactInfo->FirstName." ".$ContactInfo->LastName;
					if ($project->Originator==$ContactOriginator and $project->Client == $ContactInfo->CompanyName)	{
						
						### add timezonein if empty
						if (empty($project->TimeZoneIn)) {
							$project->TimeZoneOut =$ContactInfo->TimeZone;
						}
						### add contact's local town
						$project->TownOut="";
						if ($project->TimeZoneOut==$ContactInfo->TimeZone) {
							$project->TownOut=$ContactInfo->OfficeCity;
						}
					}
				}
				
				
				
				$pendinghours=_getpendinghours($project->DateOut,$project->TimeOut,$project->TimeZoneOut);	
				$project->PendingHours =$pendinghours;
			}
			$OngoingJobsData=_Generate_Ongoing_Entries_Data($RawOngoingEntries,$ZOWuser);
		} 
		else {
			$OngoingJobsData=0;
		}	
	
		
		return $OngoingJobsData;
	}


// ------------------------------------------------------------------------
/**
 *  _displayOngoingJobsSummary
 *
 * display ongoing jobs number and breakdown
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists(' _displayOngoingJobsSummary'))
{

	function  _displayOngoingJobsSummary($OngoingJobsdata=0,$ZOWuser) 
	{

			$entries =$OngoingJobsdata['totalentries']." Ongoing Jobs";

			
			if ($OngoingJobsdata!=0)
				{
				$entries .="<em>";
				if ($OngoingJobsdata['totalweb']>0) {

					$entries .=" (";
					$entries .=$OngoingJobsdata['totalweb']." Non-Office, ";
					$entries .=$OngoingJobsdata['totalentries']-$OngoingJobsdata['totalweb']."  Office";
					$entries .=")";

					}
					else {
						$entries .=" (";
						$entries .= $OngoingJobsdata['totalentries']." Office";
						$entries .=")";

					}

				$entries .="</em> :";
			} else{
				$entries .=": 0";
			}
		
			if ($OngoingJobsdata['totalnew']>0) 	$entries .=" ".$OngoingJobsdata['totalnew']." new";
			if ($OngoingJobsdata['totaledits']>0) 	$entries .=" ".$OngoingJobsdata['totaledits']." edits";
			if ($OngoingJobsdata['totalhours']>0) 	$entries .=" ".$OngoingJobsdata['totalhours']." hours";
		
			return $entries;
	}
}






// ------------------------------------------------------------------------
/**
 *  _Generate_Ongoing_Entries_Data
 *
 * Builds final table and count data of ongoing jobs)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _Generate_Ongoing_Entries_Data'))
{

	function _Generate_Ongoing_Entries_Data($RawOngoingEntries,$ZOWuser)
			{
			$entries ="";
			//$entries="<table>\n<thead>\n<tr><th>New</th><th>Edits</th><th>Hours</th><th>Client</th><th>Originator</th><th>DateOut</th><th>TimeOut</th><th>Team</th><th>Status</th></tr></thead>\n<tbody>\n";
			//$entries.="</tbody></table>";
			
			$totalnew =0;
			$totaledits =0;
			$totalhours =0;
			$totalweb =0;
		
			$entries= array();
		
			foreach($RawOngoingEntries as $project)
			{
				
					unset ($ProjectData);
					$ProjectData=array();
					$pendinghours=_getpendinghours($project->DateOut,$project->TimeOut,$project->TimeZoneOut);
					

					$CalendarEntryClass=strtolower($project->Status);
					$CalendarEntryClass=str_replace (" ", "", $CalendarEntryClass);

					$ProjectData['id']=$project->id;
					$ProjectData['Client']=$project->Client;
				    $ProjectData['ClientCode']=$project->Code;
				    $ProjectData['Status']=$project->Status;
				    $ProjectData['FileName']=$project->FileName;
					$ProjectData['Originator']=$project->Originator;
			
						$ProjectData['NewSlides']=$project->NewSlides;
						$totalnew +=$project->NewSlides;

						$ProjectData['EditedSlides']=$project->EditedSlides;
						$totaledits+=$project->EditedSlides;
				
						$ProjectData['Hours']=$project->Hours;
						$totalhours+=$project->Hours;

					
					if($project->WorkType=="Non-Office") $totalweb ++;

					//####### Pending ####### 
					$pending="";
				
					if ($project->PendingHours['days']!=0){
						$pending.= $project->PendingHours['days']."D ".abs($project->PendingHours['hours'])."H ".abs( $project->PendingHours['mins'])."M";
					}
					else {
						if ($project->PendingHours['hours']!=0){
							$pending.= $project->PendingHours['hours']."H ".abs( $project->PendingHours['mins'])."M";
						}
						else{
							$pending.= $project->PendingHours['mins']."M";
						}
					}
				
					
				
				   $ProjectData['Pending']=$pending; 
				
				
				    // ####### Date, Time and TimeZoneOut ####### 
				
					$DateTimeOut = date('H:i',strtotime($project->TimeOut))." ";				
				    $DateTimeOut .=date('D, j M',strtotime($project->DateOut))." ";
				
					if ($project->TownOut!=""){
						$DateTimeOut .= $project->TownOut;
					} else{
						$TimeZoneOut=preg_split('/\//', $project->TimeZoneOut);
						$DateTimeOut .= str_replace("_"," ",$TimeZoneOut[1]);						
					}

				    
					$ProjectData['DateTimeOut']=$DateTimeOut;
				
				    $ProjectData['DateTimeOutNow']="now: ".date('H:i, j M',strtotime($project->PendingHours['currenttime']));
				
					$ProjectData['DateTimeOutTimestmap']=trim($pendinghours['duetimestamp']);
				
				
					// ####### ZOW Member ####### 
					$CI = get_instance();

					// You may need to load the model if it hasn't been pre-loaded
						$CI->load->model('zt2016_users_model');
					switch ( $project->Status){
						case 'TENTATIVE':
							$num = (int) $project->TentativeBy;
							if (  is_numeric($num) && $num !=0) {
								$name= $CI->zt2016_users_model->getsuer_name_by_id($num);
								
							}else{
								$name= $CI->zt2016_users_model->getsuer_name_by_string($project->TentativeBy);

							}
			
							$ProjectData['ZOWMember']=ucfirst($name->fname);
							Break;
						case 'SCHEDULED':
							$num = (int)  $project->ScheduledBy;
							if (  is_numeric($num) && $num !=0) {
								$name= $CI->zt2016_users_model->getsuer_name_by_id($num);
								
							}else{
								$name= $CI->zt2016_users_model->getsuer_name_by_string($project->ScheduledBy);

							}
							
							$ProjectData['ZOWMember']=ucfirst($name->fname);
							
							Break;
						case 'IN PROGRESS':
						
							 $num = (int) $project->WorkedBy;
							  
							// die;
							if (  is_numeric($num) && $num !=0) {
								
								$name= $CI->zt2016_users_model->getsuer_name_by_id($num);
							}else{
								$name= $CI->zt2016_users_model->getsuer_name_by_string($project->WorkedBy);
							}
							$make_name = ucfirst($name->fname);

							if( $project->has_multi_worked == 1){
								if(!empty($project->WorkedBy_2)){
									$num_2 = $project->WorkedBy_2;
									$name= $CI->zt2016_users_model->getsuer_name_by_id($num_2);
									$make_name .=', '.ucfirst($name->fname);
								}
								if(!empty($project->WorkedBy_3)){
									$num_3 = $project->WorkedBy_3;
									$name= $CI->zt2016_users_model->getsuer_name_by_id($num_3);
									$make_name .=', '.ucfirst($name->fname);
								}
							}
							$ProjectData['ZOWMember']=$make_name;
							
							Break;
						case 'IN PROOFING':
							  $num = (int) $project->ProofedBy;
							
							if (  is_numeric($num) && $num !=0) {
								$name= $CI->zt2016_users_model->getsuer_name_by_id($num);
								
							}else{
								$name= $CI->zt2016_users_model->getsuer_name_by_string($project->ProofedBy);

							}
							$ProjectData['ZOWMember']=ucfirst($name->fname);
							Break;
					}	


				$entries[]= $ProjectData;
		}
		
	
		$ongoingjobs['entries']=$entries;
		
		

		$ongoingjobs['totalentries']=count($RawOngoingEntries);
		$ongoingjobs['totalnew']=$totalnew;
		$ongoingjobs['totaledits']=$totaledits;
		$ongoingjobs['totalhours']=$totalhours;
		$ongoingjobs['totalweb']=$totalweb;

		return $ongoingjobs;
	}
}






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
			$options = array('list' => 'List', 'calendar'=>'Calendar');
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

			$EntriesTable .='<table class="table table-striped  table-condensed responsive dtr-inline" style="width:100%;display:none;" id="past_jobs_table">'."\n";
			
			//$EntriesTable .='<table class="table table-striped  table-condensed responsive dtr-inline" style="width:100%;" id="pastg_jobs_table">'."\n";
		
			$EntriesTable .="<thead><tr><th data-sortable=\"true\">New</th><th data-sortable=\"true\">Edits</th><th data-sortable=\"true\">Hours</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\">Team</th><th data-sortable=\"true\">Date and Time Out</th><th data-sortable=\"true\">File Name</th><th data-sortable=\"true\">Status</th></tr></thead>\n";

			$EntriesTable .="<tbody>\n";
		
		
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
				$EntriesTable.='<small>'.$rowlink.$EntryDetails->ScheduledBy;
				if ($EntryDetails->ScheduledBy != $EntryDetails->WorkedBy){
					$EntriesTable.=", ".$EntryDetails->WorkedBy;
				}
				if ($EntryDetails->ProofedBy != $EntryDetails->ScheduledBy && $EntryDetails->ProofedBy != $EntryDetails->WorkedBy){
					$EntriesTable.=", ".$EntryDetails->ProofedBy;
				}

				if ($EntryDetails->CompletedBy != $EntryDetails->ScheduledBy && $EntryDetails->CompletedBy != $EntryDetails->WorkedBy && $EntryDetails->CompletedBy != $EntryDetails->ProofedBy){
					$EntriesTable.=", ".$EntryDetails->ProofedBy;
				}
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

			$EntriesTable .="<tfoot><tr><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Originator</th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Status</th></tr></tfoot>\n";
			$EntriesTable .="</table>\n";
		
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





/* End of file zt2016_tracking_helper.php */
/* Location: ./system/application/helpers/zt2016_tracking_helper.php */