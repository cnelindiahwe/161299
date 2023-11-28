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
 * CodeIgniter group Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */


// ################## groups control ##################	
	if ( ! function_exists('_display_groups_control'))
	{
		function   _display_groups_control($GroupsTable,$GroupInfo,$FormURL)
		{

			#top group dropdown
			$FormInfo['FormURL']=$FormURL;
			$FormInfo['labeltext']= 'Group';
			$FormInfo['id'] = 'groups_dropdown_form';
			$FormInfo['class'] = 'form-inline';


			$groups_top_dropdown=zt2016_create_group_selector($GroupsTable,$GroupInfo,$FormInfo)."\n";


			return $groups_top_dropdown;

		}
	}
		
	// ------------------------------------------------------------------------
	/**
	* zt2016_create_group_selector 
	*
	*/
	if ( ! function_exists('zt2016_create_group_selector'))
	{
	// ################## Generate group selector ##################	
		function zt2016_create_group_selector($GroupsTable,$GroupInfo,$FormInfo){
			
			$FormURL =$FormInfo['FormURL'];
			unset($FormInfo['FormURL']); 
			$Labeltext=$FormInfo['labeltext'];
			unset($FormInfo['labeltext']); 
			$group_selector=form_open(site_url().$FormURL,$FormInfo)."\n";
		 	$group_selector.='				<div class="form-group">'."\n";
	      	$group_selector.='					<div class="input-group ">'."\n";
	      	$group_selector.='						<span class="input-group-addon" id="group-addon1">'.$Labeltext.'</span>'."\n";
			$group_selector.= zt2016_groups_dropdown_control($GroupsTable,$GroupInfo)."\n";
	 		$group_selector.='					</div>'."\n";
	 		$group_selector.='				</div>'."\n";
		 	$group_selector.='				<div class="form-group">'."\n";
	      	$group_selector.='					<div class="input-group">'."\n";
			$more = 'id="group_dropdown_selector_submit" class="groupcontrolsubmit form-control"';
			$group_selector.=form_submit('group_dropdown_selector_submit', 'Go',$more);
	 		$group_selector.='					</div>'."\n";
	 		$group_selector.='				</div>'."\n";
			$group_selector.= form_close()."\n";
			
 			return 	$group_selector;
		}
	
	}

	// ------------------------------------------------------------------------
	/**
	* zt2016_groups_dropdown_control 
	*
	*/

	if ( ! function_exists('zt2016_groups_dropdown_control'))
	{
	
		// ################## groups control ##################	
		function   zt2016_groups_dropdown_control($GroupsTable,$GroupInfo)
		{
	
		//groups
	
			$options=array();
			foreach($GroupsTable  as $GroupDetails)
			{
				$options[$GroupDetails->GroupName]=$GroupDetails->GroupName;
			}
			asort($options);
			$options=array(""=>"All")+$options;
			
			#if ($Currentgroup->CompanyName=="All") {
			#	$options=array('all'=>"All")+$options;
			#}
			$more = 'id="group_dropdown_selector" class="selector form-control input"';

			$selected=$GroupInfo->GroupName;
			
			//$groupscontrol .=form_label('Manage group materials:','group');
			//$more = 'id="group_dropdown_submit" class="groupcontrolsubmit form-control"';			
			$groupscontrol =form_dropdown('Current_Group', $options,$selected ,$more);
	
			return $groupscontrol;
		
		}
	}


 



/* End of file zt2016_groups_helper.php */
/* Location: ./system/application/helpers/zt2016_groups_helper */