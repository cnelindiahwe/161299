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
		// echo '<pre>';
		// print_r($ActiveEntry);
		// echo '</pre>';
		// die;
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
							$JobDetailsForm .= $ActiveEntry->Status." entries are not editable.\n";				
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
				if($Key == 'Hours'){
					$padding_hsd = "style=\"padding:0 15px 0 1px; \"";			

					}else if($Key == 'EditedSlides'){
					$padding_hsd = "style=\"padding:0 10px 0 10px; \"";			
						
					}else{
					$padding_hsd = "style=\"padding:0 1px 0 15px; \"";			

					}
					$JobDetailsForm .="		<div class=\"col-sm-4 ".$Key."_hwe'\" $padding_hsd >\n";


				} else {
					$JobDetailsForm .="		<div class=\"col-sm-12\">\n";
				}			
				$JobDetailsForm .="		<div class=\"form-group ".$Key."\">\n";
				if ($Key=='NewSlides' || $Key=='EditedSlides' || $Key=='Hours') {		
					$JobDetailsForm .="<div class=\"".$Key."_clone_meta_hwe\">\n";

				}
				if ($Key == 'WorkedBy') {
					$JobDetailsForm .="		<div class=\"clone_meta_hwe\">\n";
				}
				$JobDetailsForm .= "			".form_label($Value, $Key);	
				
				### dropdowns
				
				## ZOWStaff
				$ZOWStaffInputs = array('TentativeBy','ScheduledBy', 'WorkedBy', 'ProofedBy','CompletedBy');
				if (in_array($Key,$ZOWStaffInputs)) {
					$more = 'id="'.$Key.$ActiveEntry->$Key.'" class="form-control"';
					
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					    $CI = get_instance();
						$CI->load->model('zt2016_users_model');
					        $num = (int) $ActiveEntry->$Key;
							if (  is_numeric($num) && $num !=0) {
								
								$ActiveEntry_Key_id = $ActiveEntry->$Key;
							}else{
								$id_get_modal= $CI->zt2016_users_model->getsuer_id_by_name($ActiveEntry->$Key);
								$ActiveEntry_Key_id = $id_get_modal->user_id;
							}
					$JobDetailsForm .= form_dropdown($Key, $ZOWstaff, $ActiveEntry_Key_id ,$more )."\n";
					if ($Key == 'WorkedBy') {
					$JobDetailsForm .= "</div>";
					
					if(($ActiveEntry->has_multi_worked == 1) && (!empty($ActiveEntry->WorkedBy_2))){
						
						$more = 'id="WorkedBy_2" class="form-control"';
						 $JobDetailsForm .= '<div class="clone_success_Worked_2 mt-3"><button class="btn btn-danger remove_clone float-end mt-1 " type="button" data-remove="2" style="font-size: 10px;padding: 1px 1px;background: transparent;border: navajowhite;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size: 16px;color: #000;"></i></button>';
						$JobDetailsForm .="<label for=\"WorkedBy_2\">Worked_2</label>\n";
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$JobDetailsForm .= form_dropdown('WorkedBy_2', $ZOWstaff, $ActiveEntry->WorkedBy_2 ,$more )."\n";
					$JobDetailsForm .= "</div>";
					}
					if(($ActiveEntry->has_multi_worked == 1) && (!empty($ActiveEntry->WorkedBy_3))){
						$more = 'id="WorkedBy_3" class="form-control"';
						$JobDetailsForm .= '<div class="clone_success_Worked_3 mt-3"><button class="btn btn-danger remove_clone float-end mt-1 " type="button" data-remove="3" style="font-size: 10px;padding: 1px 1px;background: transparent;border: navajowhite;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size: 16px;color: #000;"></i></button>';

						$JobDetailsForm .="<label for=\"WorkedBy_3\">Worked_3</label>\n";
					if ($ActiveEntry->BilledPaid==1){$more.=" disabled";}
					$JobDetailsForm .= form_dropdown('WorkedBy_3', $ZOWstaff, $ActiveEntry->WorkedBy_3 ,$more )."\n";
					$JobDetailsForm .= "";
					}
					if(($ActiveEntry->has_multi_worked == 1 && $ActiveEntry->WorkedBy_2 !=0 && $ActiveEntry->WorkedBy_3 !='') || ($ActiveEntry->has_multi_worked == 1 && $ActiveEntry->WorkedBy_3 !=0 && $ActiveEntry->WorkedBy_3 !='')){
						$count_workedby =1;
						if(isset($ActiveEntry->WorkedBy_3) && !empty($ActiveEntry->WorkedBy_3)){
							$count_workedby = 4;
							$JobDetailsForm .= "
						</div><button class='btn btn-info float-end clone_WorkedBy_btn mt-3 d-none' type='button' data-workedby=".$count_workedby." style='font-size: 10px;padding: 4px 9px;'><i class='fa fa-plus' aria-hidden='true'></i></button>\n";
					
						}else if(isset($ActiveEntry->WorkedBy_2) && !empty($ActiveEntry->WorkedBy_2)){
							$count_workedby = 3;
							$JobDetailsForm .= "<div class='Worked_clone_content mt-2'>
						</div><button class='btn btn-info float-end clone_WorkedBy_btn mt-3' type='button' data-workedby=".$count_workedby." style='font-size: 10px;padding: 4px 9px;'><i class='fa fa-plus' aria-hidden='true'></i></button>\n";
					
						}else{
							
						}
						
					}else{
						$JobDetailsForm .= "<div class='Worked_clone_content mt-2'>
						</div><button class='btn btn-info float-end clone_WorkedBy_btn mt-3' type='button' data-workedby='2' style='font-size: 10px;padding: 4px 9px;'><i class='fa fa-plus' aria-hidden='true'></i></button>\n";
					}
					}
					
			}
				
				## Status					
				else if ($Key=='Status') {
					$options = array('TENTATIVE'=>'Tentative','SCHEDULED'=>'Scheduled','IN PROGRESS'=>'In Progress','IN PROOFING'=>'In Proofing','COMPLETED'=>'Completed',);
					$more = 'id="Status" class="Status form-control" ';	
					if ($ActiveEntry->BilledPaid==1){
						$more.=" disabled";
						$FinalStatus='COMPLETED';
					} else{
						$FinalStatus=$ActiveEntry->Status;
					}
					$JobDetailsForm  .=form_dropdown('Status', $options,$FinalStatus,$more)."\n";
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
					$JobDetailsForm .= "</div>";
					if ($Key=='NewSlides'){
						$total_NewSlides =  $ActiveEntry->$Key;
					$extra_NewSlides = $ActiveEntry->NewSlides_2 + $ActiveEntry->NewSlides_3?:0;
					$balance_newslides = (intval($total_NewSlides) - $extra_NewSlides);
					

					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->NewSlides_2))  && (!empty($ActiveEntry->WorkedBy_2))){
						$extra="readonly";
					$more = array('name' => 'NewSlides_1', 'id' =>$Key, 'class'=>'form-control NewSlides_1', 'value'=>$balance_newslides,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_1 NewSlides_1_clone_meta_hwe mt-2\">\n <label for=\"NewSlides_1\">New 1</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
						$extra="required ";
					$more = array('name' => 'NewSlides_2', 'id' =>$Key, 'class'=>'form-control NewSlides_2', 'value'=>$ActiveEntry->NewSlides_2,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_2 NewSlides_2_clone_meta_hwe mt-2\">\n <label for=\"NewSlides_2\">New 2</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->NewSlides_3)) && (!empty($ActiveEntry->WorkedBy_3))){
						$extra="required ";
					$more = array('name' => 'NewSlides_3', 'id' =>$Key, 'class'=>'form-control NewSlides_3', 'value'=>$ActiveEntry->NewSlides_3,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_3 NewSlides_3_clone_meta_hwe mt-2\">\n <label for=\"NewSlides_3\">New 3</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
				}else if ($Key=='EditedSlides'){
					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->EditedSlides_2))  && (!empty($ActiveEntry->WorkedBy_2))){
					$total_EditedSlides = $ActiveEntry->$Key;
					$extra_EditedSlides_one = $ActiveEntry->EditedSlides_2 + $ActiveEntry->EditedSlides_3?:0;
					$balance_EditedSlides =$total_EditedSlides-$extra_EditedSlides_one;
						$extra="readonly ";
					$more = array('name' => 'EditedSlides_1', 'id' =>$Key, 'class'=>'form-control EditedSlides_1', 'value'=>$balance_EditedSlides,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_1 EditedSlides_1_clone_meta_hwe mt-2\">\n <label for=\"EditedSlides_1\">Edit 1</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";	
						$extra="required ";
					$more = array('name' => 'EditedSlides_2', 'id' =>$Key, 'class'=>'form-control EditedSlides_2', 'value'=>$ActiveEntry->EditedSlides_2,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_2 EditedSlides_2_clone_meta_hwe mt-2\">\n <label for=\"EditedSlides_2\">Edit 2</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->EditedSlides_3))  && (!empty($ActiveEntry->WorkedBy_3))){
						$extra="required ";
					$more = array('name' =>'EditedSlides_3', 'id' =>$Key, 'class'=>'form-control EditedSlides_3', 'value'=>$ActiveEntry->EditedSlides_3,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_3 EditedSlides_3_clone_meta_hwe mt-2\">\n <label for=\"EditedSlides_3\">Edit 3</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
				}
					$JobDetailsForm .= "<div class='".$Key."_put_content_hwe mt-2'>
						</div>\n";
				}

				## hours	
				else if ($Key=='Hours') {
					

					$extra="required ";
					$more = array('name' => $Key, 'id' =>$Key, 'class'=>'form-control', 'value'=>$ActiveEntry->$Key,'type' => 'number','min'=>'0','step'=>'0.01' );		

					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->Hours_2)) && (!empty($ActiveEntry->WorkedBy_2))){
						$total_Hours = $ActiveEntry->$Key;
					$extra_Hours_one = $ActiveEntry->Hours_2 + $ActiveEntry->Hours_3?:0;
					$balance_Hours =$total_Hours-$extra_Hours_one;
						$extra="readonly ";
					$more = array('name' => 'Hours_1', 'id' =>$Key, 'class'=>'form-control Hours_1' , 'value'=>$balance_Hours,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_1  Hours_1_clone_meta_hwe mt-2\">\n <label for=\"Hours_2\">Hours 1</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";

						$extra="required ";
					$more = array('name' => 'Hours_2', 'id' =>$Key, 'class'=>'form-control Hours_2', 'value'=>$ActiveEntry->Hours_2,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_2 Hours_2_clone_meta_hwe mt-2\">\n <label for=\"Hours_2\">Hours 2</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
					if(($ActiveEntry->has_multi_worked == 1) && (isset($ActiveEntry->Hours_3)) && (!empty($ActiveEntry->WorkedBy_3))){
						$extra="required ";
					$more = array('name' => 'Hours_3', 'id' =>$Key, 'class'=>'form-control Hours_3', 'value'=>$ActiveEntry->Hours_3,'type' => 'number','min'=>'0','step'=>'0.01' );	
					
					$JobDetailsForm .="<div class=\"".$Key." clone_success_Worked_3 Hours_3_clone_meta_hwe mt-2\">\n <label for=\"Hours_3\">Hours 3</label>\n";
					if ($ActiveEntry->BilledPaid==1){$extra.=" disabled";}
					$JobDetailsForm.= form_input($more,'',$extra)."\n";
					$JobDetailsForm .= "</div>";
					}
					$JobDetailsForm .= "<div class='".$Key."_put_content_hwe mt-2'>
					</div>\n";
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
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\" style=\"overflow-y: auto; resize: vertical; height: 67px;background: rgb(223, 223, 223);\">";
		$AdditionalJobDetails .="	<a  class=\"p-2 \" href=\"".Base_Url()."clients/zt2016_manageclientmaterials/".$EntryClientData->ClientCode."\">Materials Page</a>\n";
		$AdditionalJobDetails .="	</div><!--well-->\n";	
		$AdditionalJobDetails .="	</div><!--col-sm-12-->\n";	
		$AdditionalJobDetails .="	</div><!--row item-group-->\n";			
		$AdditionalJobDetails .="	</div><!--col-sm-4-->\n";	
		
		### Client Guidelines
		$AdditionalJobDetails .="	<div class=\"col-sm-4\">\n";
		$AdditionalJobDetails .="	<div class=\"row item-group\">\n";
		$AdditionalJobDetails .="	<div class=\"col-sm-12\"><h5 class=\"text-uppercase text-primary\">".$EntryClientData->CompanyName." Guidelines</h5></div>\n";		
		$AdditionalJobDetails .="	<div class=\"col-sm-12\">\n";
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\" style=\"overflow-y: auto; resize: vertical; height: 67px;background: rgb(223, 223, 223);\">";
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
		$AdditionalJobDetails .="	<div class=\"well well-sm contacts-well\" style=\"overflow-y: auto; resize: vertical; height: 67px;background: rgb(223, 223, 223);\">\n";
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