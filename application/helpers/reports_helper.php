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
 * _getClientSelectorForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_getClientReportList'))
{
	function  _getClientReportList($ClientList,$Selected="" )
	{
		$ClientReportList ="<h3>Active Clients</h3>";

		foreach($ClientList as $client)
		{
			if ($client->CompanyName==$Selected){ $ClientReportList .="<strong>";}
			$ClientReportList .="<a href=\"".base_url()."reports/clientreport/$client->ID\">".$client->CompanyName."</a>";
			if ($client->CompanyName==$Selected){ $ClientReportList .="</strong>";}
		}
		return $ClientReportList;	


	}
}

// ------------------------------------------------------------------------

/**
 * _getClientSelectorForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_getClientReportDropDown'))
{
	function  _getClientReportDropDown($ClientList,$Selected="",$timeframe="Last 12 months" )
	{
		$attributes['id'] = 'clientcontrol';
			$ClientReportList= form_open(site_url()."reports/zt2016_annual_client_figures",$attributes);

			
			//Clients
				$options=array();
				foreach($ClientList as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;

				}
				asort($options);	
				$more = 'id="Current_Client" class="reportsclient"';			
				$selected=$Selected;
				$options=array(''=>"All")+$options;

				$ClientReportList .=form_label('Client:','reportsclient');
				$ClientReportList .=form_dropdown('Current_Client', $options,$selected ,$more);
				
				
				$date=date('Y-n-j',strtotime("now"));
		 		$startdate=date('Y-n-1',strtotime("- 12 months"));
		  		$options=array($date=>"Last 12 months");
				$more = 'id="reportclientsubmit" class="reportclientsubmit"';
				
				
				/*$options['2011-12-15']="2011";
				$options['2012-12-15']="2012";
		
				
				$date=date('Y-12-15',strtotime("now"));
				$options[$date]=date('Y',strtotime("now"));
				
				$options['2010-6-15']="All Time";
				
		 		$attributes['id'] = 'timeframecontrol';
				
				$more = 'id="Timeframe" class="Timeframe"';
				$ClientReportList .=form_dropdown('Timeframe', $options,$timeframe,$more);	*/			
							
				$ClientReportList .=form_submit('reportclientsubmit', 'View',$more);
				$ClientReportList .= form_close()."\n";

		return $ClientReportList;	


	}
}

/* End of file client_helper.php */
/* Location: ./system/application/helpers/client_helper.php */