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
 *  _getEntryForm
 *
 * Displays entry form
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _getEntryForm'))
{

	function  _getEntryForm($Clientlist,$current='',$optionscall = array())
	{
		if (isset($current->id)) {
			if ($current->Status=="BILLED" || $current->Status=="PAID") {
				$BilledPaid=1;
			}
		}

		$attributes = array( 'id' => 'entryForm');
		if (isset($current->id)){
			$attributes['class'] = 'editEntry';
			$entryForm = form_open(site_url().'updateentry/'.$current->id, $attributes)."\n";
			$entryForm .="<fieldset>\n";
			$entryForm .="<input type='hidden' name='id' id='id' value='".$current->id."'/>\n";
			$entryForm .="<input type='hidden' name='clientcode' id='clientcode' value='".$current->Code."'/>\n";
			$entryForm .="</fieldset>\n";
			
			//$subsections = array( 'Status'=>'Status','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','Client'=>'Client','Originator'=>'Originator','WorkType'=>'Work Type','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','ProjectName'=>'Project Name','RealTime'=>'Real Time','TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled','WorkedBy'=>'Worked','ProofedBy'=>'Proofed','CompletedBy'=>'Completed','EntryNotes'=>'Entry Notes','ContactNotes'=>'Contact Notes','ClientNotes'=>'Client Guidelines','ZOWNotes'=>'ZOW Guidelines');
			$subsections = array( 'Status'=>'Status','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','Client'=>'Client','Originator'=>'Originator','WorkType'=>'Work Type','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','ProjectName'=>'Project Name','RealTime'=>'Real Time','TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled','WorkedBy'=>'Worked','ProofedBy'=>'Proofed','CompletedBy'=>'Completed','EntryNotes'=>'Entry Notes','ContactNotes'=>'Contact Notes','ClientNotes'=>'Client Guidelines');
		}
		else {
			$attributes['class'] = 'newEntry';
			$entryForm = form_open(site_url().'addentry',$attributes)."\n";
			$subsections = array( 'Status'=>'Status','Client'=>'Client','Originator'=>'Originator','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','WorkType'=>'Work Type','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','ProjectName'=>'Project Name','RealTime'=>'Real Time','TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled By','WorkedBy'=>'Worked By','ProofedBy'=>'Proofed By','CompletedBy'=>'Completed By','EntryNotes'=>'Entry Notes'
		);
		}
		
		//buttons
		$entryForm .="<fieldset class=\"formbuttons zowtrakui-topbar\">";
		if (isset($current->id)) {
			$entryForm.='<h1>Job ID '.$current->id."</h1>\n";
		} else {
			$entryForm.="<h1> New Job</h1>\n";
		}
		if (isset($current->id) && isset($BilledPaid) ) {
			$entryForm .= "<a href=\"".site_url()."tracking\" class=\"cancelEdit\">Cancel Edit (Cannot edit ".$current->Status." entries)</a>\n";
		}
		else {
			$ndata = array('name' => 'submit','class' => 'submitButton');
			if (isset($current->id)){
				$ndata ['value']='Update Entry';
			}
			else {
				$ndata ['value']='Add Entry';
			}
			$entryForm .= form_submit($ndata)."\n";

			if (isset($current->id)){
				$entryForm .= "<a href=\"".site_url()."trashentry/".$current->id."\" class=\"trashEntry\">Trash Entry</a>\n";
			}
				$entryForm .= "<a href=\"".site_url()."tracking\" class=\"cancelEdit\">Cancel Edit</a>\n";
		}		
		$entryForm .="</fieldset>";
		foreach ($subsections as $key=>$value){
			if ($key=="Originator"){
				$entryForm .="<fieldset  class=\"originfield\">\n";
			}
			else if ($key=="NewSlides"){
				$entryForm .="<fieldset  class=\"workhours\">\n<div>\n";;
			}
			else if ($key=="EditedSlides" || $key=="Hours" ){
				$entryForm .="<div>\n";
			}
			else {
				$entryForm .="<fieldset>\n";
			}
			
			if (isset($current->id)) {
					$entryForm .= form_label($value.':',$key);
				} else {
					$skipboxes=array('TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
					if (!in_array($key,$skipboxes)){
						$entryForm .= form_label($value.':',$key);
					}
				}
				//Regular inputs
			$inputboxes =array( 'TimeIn','TimeOut','Originator','NewSlides','EditedSlides','Hours','RealTime','FileName','ProjectName','TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
	
			if (in_array($key,$inputboxes)){
				$longboxes =array('Originator','FileName','ProjectName');
				$dateboxes =array('DateIn','DateOut');
				$timeboxes =array('TimeIn','TimeOut');
				$tinyboxes =array('NewSlides','EditedSlides','Hours','RealTime');
				if (in_array($key,$longboxes)){
					$size="25";
				}else if (in_array($key,$timeboxes)) {
					$size="4";
				}else if (in_array($key,$tinyboxes)) {
					$size="1";
				}else  {
					$size="3";
				}
				$ndata = array('name' => $key, 'size' => $size, 'class' => $key);
				
				if ($key=="Client") {
					$ndata['class']="EntryClient";
				}
				if ($key=="Originator") {
					$ndata['class']="Origin Originator";
				}
				else if ($key=="DateIn") {
					$ndata['class']="EntryDate";
				}
				else if ($key=="DateOut") {
					$ndata['class']="Deadline";
				}
				else if ($key=="NewSlides" || $key=="EditedSlides" || $key=="Hours") {
					$ndata['class']="workdone ".$key;
				}
 				else {
					$ndata['class']=$key;
				}
				if (isset($current->id)){
					$ndata['value'] =$current->$key;
				}
				if (isset($current->id)) {
					$entryForm .= form_input($ndata)."\n";
				} else {
					$skipboxes=array('TentativeBy','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
					if (!in_array($key,$skipboxes)){
						$entryForm .= form_input($ndata)."\n";
					}
				}
			}
			//Date in / date out
			else if ($key=='DateIn' || $key=='DateOut')
			{
				
					$size="8";
					 if ($key=='DateIn' ){
						$inputclass= 'EntryDate';
						}
					 if ($key=='DateOut' ){
						$inputclass= 'Deadline';
						}
					
					$inputclass.= ' '.$key;	
					$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class' => $inputclass);
					if (isset($current->id)){
						$ndata['value'] =date( 'd-M-Y',strtotime(str_replace("/","-",$current->$key)));
					}
					$entryForm .= form_input($ndata)."\n";
				
			}
			//Timezones
			else if ($key=='TimeZoneIn' || $key=='TimeZoneOut')
			{
				if (isset($current->id)) {
					
					if ($current->$key!='') {
						$entryForm .= TimeZoneDropDown($current->$key,$key);
					}
					else {
						if (!empty($optionscall['contactinfo']->TimeZone)){
							$entryForm .= TimeZoneDropDown($optionscall['contactinfo']->TimeZone,$key);
						}else {
							if (!empty($optionscall['clientinfo']->TimeZone)){
								$entryForm .= TimeZoneDropDown($optionscall['clientinfo']->TimeZone,$key);
							}else{
								$entryForm .= TimeZoneDropDown('',$key);
							}
						}
						
					}
				}
				else {
					$entryForm .= TimeZoneDropDown('',$key);		
				}
			}
			//Status
			else if ($key=='Status')
			{
				if (isset($current->id) && isset($BilledPaid) ) {
					$entryForm .="<p>".$current->Status."</p>";
				}
				else {			
					$options = array('TENTATIVE' => 'Tentative','SCHEDULED' => 'Scheduled', 'IN PROGRESS'=>'In Progress', 'IN PROOFING'=>'In Proofing','COMPLETED'=>'Completed');
					$more = 'id="Status" class="Status"';
					if (isset($current->id)) {
						$selected=$current->Status;
					}
					else{
						$selected='SCHEDULED';
					}
					
					$entryForm .=form_dropdown('Status', $options,$selected,$more);
				}
			}			
			//WorkType
			else if ($key=='WorkType')
			{
				$options = array('Office' => 'Office', 'Non-Office'=>'Non-Office');
				$more = 'id="WorkType" class="WorkType"';	
				if (isset($current->id)) {
					$selected=$current->WorkType;
				}
				else{
					$selected='';
				}
				$entryForm .=form_dropdown('WorkType', $options,$selected,$more);
			}			
			//Client
			else if ($key=='Client')
			{
				$options = array(''  => '');
				foreach($Clientlist as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);		
				$more = 'id="Client" class="EntryClient Client"';			
				
				if (isset($current->id)) {
					$selected=$current->Client;
				}
				else{
					$selected='';
				}
				$entryForm .=form_dropdown('Client', $options,$selected ,$more);
			}			
			//Entry Notes
			else if ($key=='EntryNotes')
			{
				$ndata = array('name' => 'EntryNotes', 'rows' => '5', 'cols' => '68','class' => 'EntryNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$current->EntryNotes;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}	
			//Client Notes		
			else if ($key=='ClientNotes')
			{
				$ndata = array('name' => 'ClientNotes',  'rows' => '5', 'cols' => '68','class' => 'ClientNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ClientGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			
			//ZoW Guidelines	
			/*
			else if ($key=='ZOWNotes')
			{
				$ndata = array('name' => 'ZOWNotes',  'rows' => '5', 'cols' => '68','class' => 'ZOWNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ZOWGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}
			*/			
				//Contact Guidelines	
			else if ($key=='ContactNotes')
			{
				$ndata = array('name' => 'ContactNotes',  'rows' => '5', 'cols' => '68','class' => 'ContactNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['contactinfo']->Notes;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			
			//Contact Guidelines	
			else if ($key=='ZOWNotes')
			{
				$ndata = array('name' => 'ClientNotes',  'rows' => '5', 'cols' => '68','class' => 'ClientNotes notesfield',);
				if (isset($current->id)) {
						$ndata['value']=$optionscall['clientinfo']->ZOWGuidelines;
					}
				$entryForm .= form_textarea($ndata)."\n";

			}			

		if ($key=="NewSlides" || $key=="EditedSlides" ){
				$entryForm .="</div>\n";
			}
			else if ($key=="Hours" ){
				$entryForm .="</div>\n</fieldset>\n";
			}
			else {
				$entryForm .="</fieldset>\n";
			}
			
			
	
		}
		

		$entryForm .= form_close()."\n";

		return $entryForm;

	}
}
// ------------------------------------------------------------------------
/**
 *  _getusertimedata
 *
 * Displays entry form
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _getusertimedata'))
{

	function  _getusertimedata($utz)
	{
		// Get server time
		$timestamp = time();
		 
		 if ($utz=='') {$utz='Europe/Amsterdam';}
	 
		// create the DateTimeZone object for later
		$dtzone = new DateTimeZone($utz);
		 
		// first convert the timestamp into a string representing the local time
		$time = date('r', $timestamp);
		 
		// now create the DateTime object for this time
		$dtime = new DateTime($time);
		 
		// convert this to the user's timezone using the DateTimeZone object
		$dtime->setTimeZone($dtzone);
		 
		// print the time using your preferred format
		$usertimedata = $dtime->format('g:i A m/d/y');
		$usertimedata .= " ".$utz;
		return $usertimedata;

		}
	
}

// ------------------------------------------------------------------------
/**
 *  _getOngoingJobs
 *
 * Builds list of ongoing jobs)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists(' _getOngoingJobs'))
{

	function  _getOngoingJobs($getentries,$ZOWuser)
			{
			$entries ="";
			//$entries="<table>\n<thead>\n<tr><th>New</th><th>Edits</th><th>Hours</th><th>Client</th><th>Originator</th><th>DateOut</th><th>TimeOut</th><th>Team</th><th>Status</th></tr></thead>\n<tbody>\n";
			//$entries.="</tbody></table>";
			
			$totalnew =0;
			$totaledits =0;
			$totalhours =0;
			$totalweb =0;
			
			foreach($getentries as $project)
			{
					//$pendinghours=_getpendinghours($project->DateOut,$project->TimeOut,$project->TimeZoneOut);
					$entries .= "<a href=\"".site_url()."editentry/".$project->id . "\" class=\"";
					$CalendarEntryClass=strtolower($project->Status);
					$CalendarEntryClass=str_replace (" ", "", $CalendarEntryClass);
					//$entries .=$CalendarEntryClass." projectlink\"><span class='sortnumber'>".$project->PendingHours['raw']."</span><strong>";
					$entries .=$CalendarEntryClass." projectlink\"><strong>";
					// Add asterisk if client or contact notes
					if ($project->EntryNotes!="")
							{  $entries .="*"; }
					else {
					$CI =& get_instance();
						$CI->load->model('trakcontacts', '', TRUE);
						$getContacts = $CI->trakcontacts->GetEntry($options =array('CompanyName' => $project->Client));
						if($getContacts) {
							foreach($getContacts as $contactitem)
							{
								if ($contactitem->FirstName." ".$contactitem->LastName==$project->Originator){
									if ($contactitem->Notes!="") {  $entries .="*"; }
								}
							}
						}					
					}
					$items ='';
					if ($project->NewSlides > 0) {
						$items .= $project->NewSlides . "N ";
						$totalnew +=$project->NewSlides;
					}
					if ($project->EditedSlides > 0) {
						$items .= $project->EditedSlides . "E ";
						$totaledits+=$project->EditedSlides;
					}
					if ($project->Hours > 0) {
						$items .= $project->Hours . "H ";
						$totalhours+=$project->Hours;
					}
					$entries .= $items;

					if($project->WorkType=="Non-Office") $totalweb ++;

					
					$entries .= "Due in ";
					if ($project->PendingHours['days']!=0){
						$entries .= $project->PendingHours['days']." days ".$project->PendingHours['hours']." hours ";
					}
					else {
						if ($project->PendingHours['hours']!=0){
							$entries .= $project->PendingHours['hours']." hours ";
						}
					}
					$entries .= $project->PendingHours['mins']." minutes";
					$entries .="</strong> ";

					$entries .= "  |  ".$project->Code;
					$entries .="  |  ".$project->Originator."  |  ";
					switch ( $project->Status){
						case 'TENTATIVE':
							$entries .=" ".$project->TentativeBy;
							Break;
						case 'SCHEDULED':
							$entries .=" ".$project->ScheduledBy;
							Break;
						case 'IN PROGRESS':
							$entries .=" ".$project->WorkedBy;
							Break;
						case 'IN PROOFING':
							$entries .=" ".$project->ProofedBy;
							Break;
					}	
					$entries .="<em>";
					$entries .= " Due at ".date('H:i',strtotime($project->TimeOut));
					//$entries .= " at ".$project->TimeOut;
					$entries .= " on ".date('D, j M',strtotime($project->DateOut));
					



					$TimeZoneOut=preg_split('/\//', $project->TimeZoneOut);
					$entries .=" in ".$TimeZoneOut[1]." (now: ".date('H:i D, j M',strtotime($project->PendingHours['currenttime'])).")";
					
			//$datetimeout = strtotime($project->DateOut." ".$project->TimeOut);
		//  convert due into a string representing the local time
			//$timedue = date('r', $datetimeout);
			//$mumduetime = new DateTime($timedue);
			//$mumduetime ->setTimeZone('Asia/Kolkata');
			//$entries .= strtotime($mumduetime->format('H:i  D j M'));


					$entries .="</em>";

					$entries .= "</a>";

		}

		$ongoingjobs['entries']=$entries;
		$ongoingjobs['totalentries']=count($getentries);
		$ongoingjobs['totalnew']=$totalnew;
		$ongoingjobs['totaledits']=$totaledits;
		$ongoingjobs['totalhours']=$totalhours;
		$ongoingjobs['totalweb']=$totalweb;
		$ongoingdisplay=_displayOngoingJobs($ongoingjobs,$ZOWuser);
		return $ongoingdisplay;
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
	function  _getpendinghours($DateOut,$TimeOut,$TimeZoneOut)
	{

		// create the DateTimeZone object for later
		$dtzone = new DateTimeZone($TimeZoneOut);
		
		// Get server current time
			$timestamp = time();
		//  convert the server timestamp into a string representing the local time
			$timenow = date('r', $timestamp);
		// now create the DateTime object for this time
			$dtime = new DateTime($timenow);
		// convert this to the client's timezone using the DateTimeZone object
			$dtime->setTimeZone($dtzone);	
				
		// Get job's due time
			$datetimeout = strtotime($DateOut." ".$TimeOut);
		//  convert due into a string representing the local time
			$timedue = date('r', $datetimeout);
			$duetime = new DateTime($timedue);
			//$duetime ->setTimeZone($dtzone);


		$diff = strtotime($duetime->format('H:i  m/d/y'))-strtotime($dtime->format('H:i  m/d/y'));
		
		/*echo "Due: ".$duetime->format('H:i  m/d/y e');
		echo "  ";
		echo "Current: ".$dtime->format('H:i  m/d/y e');
		echo "   ";
		echo strtotime($duetime->format('u'));
		echo "   ";
		echo strtotime($dtime->format('U'));
		//echo strtotime($duetime->format('u'));
		echo "   ";
		echo "<br>";*/
		$pendinghours['days'] = intval($diff/24/60/60);
		$remain=$diff%86400; 
		$pendinghours['hours']=intval($remain/3600); 
		
		$remain=$remain%3600;
		$pendinghours['raw']=$diff;
		$pendinghours['mins']=intval($remain/60);
		$pendinghours['currenttime']=$dtime->format('H:i  m/d/y');
		$pendinghours['duetime']=$duetime->format('H:i  m/d/y');

		return $pendinghours;
	}
}

// ------------------------------------------------------------------------
/**
 *  _displayOngoingJobs
 *
 * display ongoing jobs list
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists(' _displayOngoingJobs'))
{

	function  _displayOngoingJobs($OngoingJobsdata=0,$ZOWuser) 
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>".$OngoingJobsdata['totalentries']." Ongoing Jobs : ";
				if ($OngoingJobsdata['totalnew']>0) 	$entries .=" ".$OngoingJobsdata['totalnew']." new";
				if ($OngoingJobsdata['totaledits']>0) 	$entries .=" ".$OngoingJobsdata['totaledits']." edits";
				if ($OngoingJobsdata['totalhours']>0) 	$entries .=" ".$OngoingJobsdata['totalhours']." hours";
			
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
	
	
				$entries .="</em>";
			}
			$entries .="</h1>";
			//Add extra buttons
			$entries .="<a href=\"".site_url()."tracking\" class=\"newjob\">New Job</a></h3>\n";
			$entries .="<a href=\"".site_url()."trash\">View Trash</a>";
			//Add logout buttons
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			//page switcher
			$entries .=_createpageswitcher();
			$entries .="\n</div><!--newjobbuttons-->\n";
				$entries .="<div id='ongoing' >\n";
		if ($OngoingJobsdata!=0) 
				{	$entries .= $OngoingJobsdata['entries']; }
				else
				{$entries .= "No ongoing Jobs";}
				
			$entries .="\n</div><!--ongoing-->\n";
			return $entries;
	}
}


	// ################## create data tables ##################	
if ( ! function_exists(' _entrydatatable'))
{
	function _entrydatatable($getentries)
	{
		$entries="<table>\n<thead>\n<tr><th>New</th><th>Edits</th><th>Hours</th><th>Client</th><th>Originator</th><th>DateOut</th><th>TimeOut</th><th>Team</th><th>Status</th><th>Name</th></tr></thead>\n<tbody>\n";
		
		foreach($getentries as $project){
			$rowlink="<a href=\"".site_url()."editentry/".$project->id . "\" class=\"projectlink\" >";
			$entries.="<tr><td>";
				if ($project->NewSlides !=0){
					$entries.=$rowlink.$project->NewSlides."</a>";
				}
			$entries.="</td><td>";
				if ($project->EditedSlides !=0){
					$entries.=$rowlink.$project->EditedSlides."</a>";
				}
			$entries.="</td><td>";
				if ($project->Hours !=0){
					$entries.=$rowlink.$project->Hours."</a>";
				}
			$entries.="</td><td>".$rowlink.$project->Client."</a></td>";
			$entries.="<td>".$rowlink.$project->Originator."</a></td>";
			$entries.="<td>".$rowlink.date('D d M, Y',strtotime($project->DateOut))."</a></td>";
			$entries.="<td>".$rowlink.$project->TimeOut."</a></td>";
			$entries.="<td>";
			
			$entries.=$rowlink.$project->ScheduledBy;
			if ($project->ScheduledBy != $project->WorkedBy){
				$entries.=", ".$project->WorkedBy;
			}
			if ($project->ProofedBy != $project->ScheduledBy && $project->ProofedBy != $project->WorkedBy){
				$entries.=", ".$project->ProofedBy;
			}
		
			if ($project->CompletedBy != $project->ScheduledBy && $project->CompletedBy != $project->WorkedBy && $project->CompletedBy != $project->ProofedBy){
				$entries.=", ".$project->ProofedBy;
			}
			$entries.="</a></td><td>";
			$entries.="<a href=\"".site_url()."editentry/".$project->id . "\" class=\"projectlink\" >";
			$entries.=$project->Status;
			$entries.="</a>";
			$entries.="</td>";
			$entries.="<td>".$rowlink.$project->FileName."</a></td>";
			$entries.="</tr>\n";
			
		
		}
		$entries.="</tbody>\n</table>";
		return $entries;
	}
}

/* End of file trackingnew_helper.php */
/* Location: ./system/application/helpers/tracking_helper.php */