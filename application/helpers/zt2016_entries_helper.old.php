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
 * ZOWTRAK 2016 Entries Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */



// ------------------------------------------------------------------------
/**
 *  _Generate_Job_Deta	ils_Form
 *
 * Creates basic form entries)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_Generate_Job_Details_Form'))
{


	function  _Generate_Job_Details_Form ($ActiveEntry='',$ClientsData,$EntryClientData,$EntryContactsData,$EntryOriginatorData, $TimezonesList,$ZOWstaff)
	{
		
		$subsections = array(
			'JobDetails'=>'Job Details', 'Status'=>'Status','NewSlides'=>'New','EditedSlides'=>'Edits', 'Hours'=>'Hours', 'FileName'=>'FileName', 'WorkType'=>'Work Type',
			'DateTimeOut'=>'Time Out','DateOut'=>'Date Out','TimeOut'=>'Time Out','TimeZoneOut'=>'Time Zone Out',
			'ProductionNotes'=>'Production Notes','EntryNotes'=>'Instructions, etc.',
			'ZOWStaff'=>'ZOW Staff', 'TentativeBy'=>'Tentative','ScheduledBy'=>'Scheduled', 'WorkedBy'=>'Worked','ProofedBy'=>'Proofed', 'CompletedBy'=>'Completed',
			'DateTimeIn'=>'Time In','DateIn'=>'Date In','TimeIn'=>'Time In','TimeZoneIn'=>'Time Zone In',
			'BillingNotes'=>'Billing Notes','ProjectName'=>'P.O., etc.'
		);
							 
	
		$JobDetailsForm ="\n";
		
		foreach ($subsections as $Key=>$Value){
			
			### Form Headers
			$Headers = array(  'JobDetails','DateTimeOut','ProductionNotes','ZOWStaff','DateTimeIn','BillingNotes');
			$ColHeaders = array(  'JobDetails', 'DateTimeOut','ZOWStaff','DateTimeIn');
			if (in_array($Key,$Headers)) {
					
				#### close inner item group
				if ($Key!='JobDetails')	{
					$JobDetailsForm .="	</div>\n";
				}
				
				$currentheader=strtolower(str_replace(" ","",$Key));
				if (in_array($Key,$ColHeaders)) {
					
					#### close column
					if ($Key!='JobDetails')	{
						$JobDetailsForm .="</div>\n";
					}
					$JobDetailsForm .="<div class=\"col-sm-3\">\n";
					
					if ($Key=='JobDetails')	{
						
					####### submit button
					$JobDetailsForm .="	<div class=\"row item-group\">\n";
					$JobDetailsForm .="<div class=\"col-sm-12\">\n";
					
					# new job	
					if (!isset($ActiveEntry->id)) {
							$ndata = array('class' => 'submitButton btn btn-success btn-xs form-control','value' => 'Create Job','name' => 'JobCreateubmit','id' => 'JobCreateSubmit');
							$JobDetailsForm .= form_submit($ndata)."\n";				
					} 
						
					#edited job
					else {
						if ($ActiveEntry->BilledPaid==0){
							$ndata = array('class' => 'submitButton btn btn-success btn-xs form-control','value' => 'Update Job','name' => 'JobUpdateSubmit','id' => 'JobUpdateSubmit');
							$JobDetailsForm .= form_submit($ndata)."\n";
						} else{
							$JobDetailsForm .= "Billed entries are not editable.\n";				
						}							
					}

					
	
					$JobDetailsForm .="</div><!--col-sm-12-->\n";
					$JobDetailsForm .="</div><!--row item-group-->\n";
					}
					
				}

				$JobDetailsForm .="	<div class=\"row item-group\">\n";
				
				if ($Key=='JobDetails' || $Key=='Pricing' ) {
					$JobDetailsForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$Value."</h5></div>\n";
				}else{
					$JobDetailsForm .="		<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary ".$currentheader."\">".$Value."</h5></div>\n";
				}
			}
			
			### Form inputs
			else{
				
				## build input containers	
				if ($Key=='NewSlides' || $Key=='EditedSlides' || $Key=='Hours') {				
					$JobDetailsForm .="		<div class=\"col-sm-4\">\n";
				} else {
					$JobDetailsForm .="		<div class=\"col-sm-12\">\n";
				}			
				$JobDetailsForm .="		<div class=\"form-group\">\n";
				$JobDetailsForm .= "			".form_label($Value, $Key);	
				
				### dropdowns
				
				## ZOWStaff
				$ZOWStaffInputs = array('TentativeBy','ScheduledBy', 'WorkedBy', 'ProofedBy','CompletedBy');
				if (in_array($Key,$ZOWStaffInputs)) {
					$more = 'id="'.$Key.'" class="form-control"';
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$JobDetailsForm .= form_dropdown($Key, $ZOWstaff, $ActiveEntry->$Key ,$more )."\n";
				}
				
				## Status					
				else if ($Key=='Status') {
					$options = array('TENTATIVE'=>'Tentative','SCHEDULED'=>'Scheduled','IN PROGRESS'=>'In Progress','IN PROOFING'=>'In Proofing','COMPLETED'=>'Completed',);
					$more = 'id="Status" class="Status form-control" ';	
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$JobDetailsForm  .=form_dropdown('Status', $options,$ActiveEntry->Status,$more)."\n";
				}
				
				
				## Originator					
				else if ($Key=='Originator') {
					$JobDetailsForm .=$this->_display_originator_dropdown($EntryContactsData,$EntryOriginatorData);	
				}				

				## Work Type					
				else if ($Key=='WorkType') {
					$more = 'id="WorkType" class="form-control"';
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$WorkTypes=array ("Office"=>"Office","Non-Office"=>"Non-Office");
					$JobDetailsForm.= form_dropdown('WorkType', $WorkTypes, $ActiveEntry->WorkType ,$more )."\n";					
				}
				
				## time zone	
				else if ($Key=='TimeZoneIn'||$Key=='TimeZoneOut') {
					$more = 'id="'.$Key.'" class="form-control" required';
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$JobDetailsForm.= form_dropdown($Key, $TimezonesList, $ActiveEntry->TimeZoneOut ,$more )."\n";
				}
				
				## New, edits	
				else if ($Key=='NewSlides'||$Key=='EditedSlides') {
					$extra="required";
					$more = array('name' => $Key, 'id' =>$Key, 'class'=>'form-control', 'value'=>$ActiveEntry->$Key,'type' => 'number','min'=>'0' );		
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
				}

				## hours	
				else if ($Key=='Hours') {
					$extra="required ";
					$more = array('name' => $Key, 'id' =>$Key, 'class'=>'form-control', 'value'=>$ActiveEntry->$Key,'type' => 'number','min'=>'0','step'=>'0.01' );		

					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
				}
				
				
				### Textareas
				else if ($Key=='FileName' || $Key=='EntryNotes' || $Key=='ProjectName') {
					$extra="'style=\"min-width: 100%\"";
					$rows=2;
					$cols=35;
					$ndata = array('name' => $Key, 'id' => $Key, 'rows' => $rows, 'cols' => $cols, 'class'=>'form-control');
					$ndata['value']= $ActiveEntry->$Key;
					if ($Key=='FileName'){
						 $extra .=" required";
					} 
					if ($ActiveEntry->BilledPaid==1){
						 $extra .=" disabled";
					} 
					$JobDetailsForm .=form_textarea($ndata,'',$extra)."\n";				
				}	
				## regular inputs
				else{
					$extra="";
					$more = array('name' => $Key, 'id' =>$Key, 'class'=>'form-control', 'value'=>$ActiveEntry->$Key);	
					
					if ($Key=='ScheduledDateOut'||$Key=='DateIn'|| $Key=='ScheduledTimeOut'||$Key=='TimeIn') {
						$extra= ' required';
					}
					
					if ($Key=='ScheduledDateOut'||$Key=='DateIn'||$Key=='DateOut') {
						$more += ['type'=> 'date' ];
					}
					else if ($Key=='ScheduledTimeOut'||$Key=='TimeIn'||$Key=='TimeOut') {
						$more += ['type'=> 'time'];
					}	
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
				}
				
				$JobDetailsForm .="</div><!--form-group-->\n";
				$JobDetailsForm .="</div><!--col-sm-12-->\n";	
			}
				
		}
		$JobDetailsForm .="</div><!--col-sm-12-->\n";	
		$JobDetailsForm .="</div><!--col-sm-12-->\n";
		return $JobDetailsForm;
	}		
		
}

// ------------------------------------------------------------------------
/**
 *  _generate_Additional_Form_Details
 *
 * Displays additional job details form items
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists('_Generate_Additional_Form_Details'))
{

	function  _Generate_Additional_Form_Details($ActiveEntry,$EntryClientData,$EntryOriginatorData){
		
		$AdditionalJobDetails="\n";

		### Client Materials
		$AdditionalJobDetails .="	<div class=\"col-sm-4\">\n";
		$AdditionalJobDetails .="	<div class=\"row item-group\">\n";
		$AdditionalJobDetails .="	<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary\">".$EntryClientData->CompanyName." Materials</h5></div>\n";
		$AdditionalJobDetails .="	<div class=\"col-sm-12\">\n";
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\">";
		$AdditionalJobDetails .="	<a href=\"".Base_Url()."clients/zt2016_manageclientmaterials/".$EntryClientData->ClientCode."\">Materials Page</a>\n";
		$AdditionalJobDetails .="	</div><!--well-->\n";	
		$AdditionalJobDetails .="	</div><!--col-sm-12-->\n";	
		$AdditionalJobDetails .="	</div><!--row item-group-->\n";			
		$AdditionalJobDetails .="	</div><!--col-sm-4-->\n";	
		
		### Client Guidelines
		$AdditionalJobDetails .="	<div class=\"col-sm-4\">\n";
		$AdditionalJobDetails .="	<div class=\"row item-group\">\n";
		$AdditionalJobDetails .="	<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary\">".$EntryClientData->CompanyName." Guidelines</h5></div>\n";		
		$AdditionalJobDetails .="	<div class=\"col-sm-12\">\n";
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\">";
		$AdditionalJobDetails .=	nl2br($EntryClientData->ClientGuidelines);
		$AdditionalJobDetails .="	</div><!--well-->\n";	
		$AdditionalJobDetails .="	</div><!--col-sm-12-->\n";	
		$AdditionalJobDetails .="	</div><!--row item-group-->\n";	
		$AdditionalJobDetails .="	</div><!--col-sm-4-->\n";	
		
		### Contact Guidelines
		$AdditionalJobDetails .="	<div class=\"col-sm-4\">\n";
		$AdditionalJobDetails .="	<div class=\"row item-group\">\n";
		if (isset($EntryOriginatorData->ContactProductionGuidelines)) {	
			$AdditionalJobDetails .="	<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary\">".$ActiveEntry->Originator." Guidelines</h5></div>\n";	
		} else{
			$AdditionalJobDetails .="	<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary\"> Originator Guidelines</h5></div>\n";	
		}
		
		$AdditionalJobDetails .="	<div class=\"col-sm-12\">\n";
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\">\n";
		if (isset($EntryOriginatorData->ContactProductionGuidelines)) {	
			$AdditionalJobDetails .=	nl2br($EntryOriginatorData->ContactProductionGuidelines);
		}
		$AdditionalJobDetails .="	</div><!--well-->\n";
		$AdditionalJobDetails .="	</div><!--col-sm-12-->\n";	
		$AdditionalJobDetails .="	</div><!--row item-group-->\n";	
		$AdditionalJobDetails .="	</div><!--col-sm-4-->\n";	

	
		return $AdditionalJobDetails;
	}

}


/* End of file zt2016_job_entries_helper.php */
/* Location: ./system/application/helpers/zt2016_tracking_helper.php */