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

	function  _getEntryForm($Clientlist,$current='')
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
		}
		else {
			$attributes['class'] = 'newEntry';
			$entryForm = form_open(site_url().'addentry',$attributes)."\n";
		}
		$subsections = array( 'Status'=>'Status','Client'=>'Client','Originator'=>'Originator','NewSlides'=>'New','EditedSlides'=>'Edits','Hours'=>'Hours','RealTime'=>'Real Time','FileName'=>'FileName','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out','EntryNotes'=>'Entry Notes','ProjectName'=>'Project Name','WorkType'=>'Work Type','ScheduledBy'=>'Scheduled By','WorkedBy'=>'Worked By','ProofedBy'=>'Proofed By','CompletedBy'=>'Completed By'
		);
		foreach ($subsections as $key=>$value){




			if ($key=="Originator"){
				$entryForm .="<fieldset  id=\"originfield\">\n";
			}
			else if ($key=="NewSlides"){
				$entryForm .="<fieldset  id=\"workhours\">\n<div>\n";;
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
					$skipboxes=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
					if (!in_array($key,$skipboxes)){
						$entryForm .= form_label($value.':',$key);
					}
				}
				//Regular inpuits
			$inputboxes =array( 'TimeIn','TimeOut','Originator','NewSlides','EditedSlides','Hours','RealTime','FileName','ProjectName','ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
	
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
				$ndata = array('name' => $key, 'id' => $key, 'size' => $size, 'class' => $key);
				
				if ($key=="Client") {
					$ndata['class']="EntryClient";
				}
				if ($key=="Originator") {
					$ndata['class']="Origin";
				}
				else if ($key=="DateIn") {
					$ndata['class']="EntryDate";
				}
				else if ($key=="DateOut") {
					$ndata['class']="Deadline";
				}
				else if ($key=="NewSlides" || $key=="EditedSlides" || $key=="Hours") {
					$ndata['class']="workdone";
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
					$skipboxes=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy');
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
								if ($current->TimeZoneIn!='') {
									$entryForm .= TimeZoneDropDown($current->$key,$key);
								}
								else {
									$entryForm .= TimeZoneDropDown($current->$key,$key);
								}
				}
				else {
					if (isset($CurrentClient->ID)) {
						$entryForm .= TimeZoneDropDown($CurrentClient->TimeZone,$key);
					}
					else
					{
						$entryForm .= TimeZoneDropDown('',$key);
					}			
				}
			}
			//Status
			else if ($key=='Status')
			{
				if (isset($current->id) && isset($BilledPaid) ) {
					$entryForm .="<p>".$current->Status."</p>";
				}
				else {			
					$options = array('SCHEDULED' => 'Scheduled', 'IN PROGRESS'=>'In Progress', 'IN PROOFING'=>'In Proofing','COMPLETED'=>'Completed');
					$more = 'id="Status" class="Status"';
					if (isset($current->id)) {
						$selected=$current->Status;
					}
					else{
						$selected='';
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
				$more = 'id="Client" class="EntryClient"';			
				
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
				$ndata = array('name' => 'EntryNotes', 'id' => 'EntryNotes', 'rows' => '10', 'cols' => '55');
				if (isset($current->id)) {
						$ndata['value']=$current->EntryNotes;
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
		
		$entryForm .="<fieldset class=\"formbuttons\">";
			

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
				$entryForm .= "<a href=\"".site_url()."trashentry/".$current->id."\" class=\"cancelEdit\">Trash Entry</a>\n";
				$entryForm .= "<a href=\"".site_url()."tracking\" class=\"cancelEdit\">Cancel Edit</a>\n";
			}
		}		
		$entryForm .="</fieldset>";
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

	function  _getOngoingJobs($getentries)
			{
			
			$entries="<div id='ongoing'>";
			$entries.="<h3>Ongoing Jobs:</h3>";
			
			foreach($getentries as $project)
			{
				if ($project->Status!="COMPLETED"){
					$entries .= "<a href=\"".site_url()."editentry/".$project->id . "\" class=\"";
					$CalendarEntryClass=strtolower($project->Status);
					$CalendarEntryClass=str_replace (" ", "", $CalendarEntryClass);
					$entries .=$CalendarEntryClass."\">";
					$items ='';
					if ($project->NewSlides > 0) {
						$items .= $project->NewSlides . "N ";
					}
					if ($project->EditedSlides > 0) {
						$items .= $project->EditedSlides . "E ";
					}
					if ($project->Hours > 0) {
						$items .= $project->Hours . "H ";
					}
					$entries .= $items;
					$entries .= " ".$project->Code;
					switch ( $project->Status){
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
					$pendinghours=_getpendinghours($project->DateOut,$project->TimeOut,$project->TimeZoneOut);
					
					$entries .= " | Due in ";
					if ($pendinghours['days']!=0){
						$entries .= $pendinghours['days']." days ".$pendinghours['hours']." hours ";
					}
					else {
						if ($pendinghours['hours']!=0){
							$entries .= $pendinghours['hours']." hours ";
						}
					}
					$entries .= $pendinghours['mins']." minutes";
					$entries .= " at ".$pendinghours['duetime']." (".$project->TimeZoneOut." current: ".$pendinghours['currenttime'].")";

					$entries .= "</a>";
					
				}
		}
		$entries .= "</div>";
		return $entries;
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
		$pendinghours['mins']=intval($remain/60);
		$pendinghours['currenttime']=$dtime->format('H:i  m/d/y');
		$pendinghours['duetime']=$duetime->format('H:i  m/d/y');

		return $pendinghours;
	}
}
/* End of file tracking_helper.php */
/* Location: ./system/application/helpers/tracking_helper.php */