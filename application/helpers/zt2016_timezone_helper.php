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
 * CodeIgniter TimeZone Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */


// ################## clients control ##################	
	if ( ! function_exists('generate_timezone_array'))
	{
		function generate_timezone_array()
		{


			$temp_TimezonesList = array();
			$running_List = array();
			$top_times = array('Europe/Amsterdam','America/New_York','Asia/Dubai','Asia/Kolkata','Asia/Shanghai','Europe/London','Australia/Sydney','Asia/Tokyo','America/Los_Angeles');
			foreach(timezone_abbreviations_list() as $abbr => $timezone){
				foreach($timezone as $val){
					if(
						isset($val['timezone_id']) 
						&& !in_array($val['timezone_id'],$running_List) 
						&& strpos($val['timezone_id'],"/")
						&& substr($val['timezone_id'],0, 10)!='Antarctica'
					)	{
						$running_List[]=$val['timezone_id'];	
						$dtz = new DateTimeZone($val['timezone_id']);	
						$time_now = new DateTime('now', $dtz);
						$tz_offset = $dtz->getOffset( $time_now ) / 3600;
						$ti_val = $val['timezone_id'];
						if(in_array($ti_val,$top_times)){
							$temp_TimezonesList []=array('TimeZone'=>$val['timezone_id'],'Offset'=>$tz_offset ,'custom_order'=>-99);

						}else{
							$temp_TimezonesList []=array('TimeZone'=>$val['timezone_id'],'Offset'=>$tz_offset ,'custom_order'=>$tz_offset );
						}
					}
				}
			}
			$temp_TimezonesList[] = array('TimeZone'=>'Asia/Dubai','Offset'=>0 ,'custom_order'=>-99) ;

			empty($running_List);
			array_multisort(array_column($temp_TimezonesList, 'Offset'), SORT_ASC, array_column($temp_TimezonesList,'TimeZone'), SORT_STRING, $temp_TimezonesList);
			array_multisort(array_column($temp_TimezonesList, 'custom_order'), SORT_ASC, array_column($temp_TimezonesList,'TimeZone'), SORT_STRING, $temp_TimezonesList);

			foreach($temp_TimezonesList as $finalTZ){

				if ($finalTZ['Offset'] > 0) {
					$current_offset="GMT + ".$finalTZ['Offset'];
				}
				else if  ($finalTZ['Offset'] == 0) {
					$current_offset="GMT"; 	
				}
				else{
					$current_offset="GMT ".$finalTZ['Offset'];
				}
				$TimezonesList[$finalTZ['TimeZone']]=$finalTZ['TimeZone']." (".$current_offset.")";
				

			}
			return $TimezonesList;

		}

	}



/* End of file zt2016_timezone_helper.php */
/* Location: ./system/application/helpers/zt2016_timezone_helper */