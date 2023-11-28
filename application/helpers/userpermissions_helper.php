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
 * _getCurrentUser()
 *
 * Retrieves current user name from session info
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_getCurrentUser'))
{

	function  _getCurrentUser()
	{
	 $CI =& get_instance();
	 $Owner_em= $CI->session->userdata('user_email');
	 
	 if ($Owner_em=='sunil.poojari@zebraonwheels.com'){
	 	$Owner_em='poojari.sunil@zebraonwheels.com';
	 }
	 $Owner_na= explode("@", $Owner_em);

	 return  $Owner_na[0];

	}
}

if ( ! function_exists('_getCurrentUser_id'))
{

	function  _getCurrentUser_id()
	{
	 $CI =& get_instance();
	 $user_id= $CI->session->userdata('user_id');

	 return  $user_id;

	}
}

// ------------------------------------------------------------------------

/**
 * _superuseronly()
 *
 * Allows access only to superusers
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_getmanagerbar'))
{
	function _getmanagerbar($ZOWuser)
	{
		if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" || $ZOWuser=="jirka.blom"|| $ZOWuser=="invoices") {

		//Manager pages
			$menuarray=array('Tracking','Reports','Clients','Contacts','Invoicing','Financials','Export');

			$managerbar="<div  class='zowtrakui-managerbar'>";
			foreach ( $menuarray as $menuitem) {
				if ($menuitem=='Export') {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\" class=\"logout\">".$menuitem."</a>";
				
				} else if ($menuitem=='Invoicing') {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\zt2016_new_invoices\">".$menuitem."</a>";
					
				} else if ($menuitem=='Clients') {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\zt2016_clients\">".$menuitem."</a>";
				
				} else if ($menuitem=='Contacts') {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\zt2016_contacts\">".$menuitem."</a>";
				
				}  else {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\">".$menuitem."</a>";
				}	
			}
			$managerbar.="</div>";
		} else {$managerbar="";}
		
		return $managerbar;
	}
}

// ------------------------------------------------------------------------

/**
 * _superuseronly()
 *
 * Allows access only to superusers
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_superuseronly'))
{

	function _superuseronly()
	{
		$CI = get_instance();
		$user_id =  $CI->session->userdata('user_id');
		
		// You may need to load the model if it hasn't been pre-loaded
		  $CI->load->model('zt2016_users_model');
		  $id_data =  $CI->zt2016_users_model->getsuer_visibility($user_id);
		
		$ZOWuser=_getCurrentUser();
		// if ($ZOWuser!="miguel" && $ZOWuser!="sunil.singal" && $ZOWuser!="jirka.blom" && $ZOWuser!="invoices") {
			 if ($id_data->user_type != 2) { 

				redirect('main');
		} 
		else {
			return $ZOWuser;
		}
	}
}


/* End of file userpermissions_helper.php */
/* Location: ./system/application/helpers/client_helper.php */