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
	 $Owner_na= explode("@", $Owner_em);
	 return  $Owner_na[0];
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
		if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {

		//Manager pages
			$menuarray=array('Tracking','Reports','Clients','Contacts','Invoicing','Financials','Export');

			$managerbar="<div  class='zowtrakui-managerbar'>";
			foreach ( $menuarray as $menuitem) {
				if ($menuitem!='Export') {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\">".$menuitem."</a>";
				} else {
					$managerbar.="<a href=\"".site_url().strtolower($menuitem)."\" class=\"logout\">".$menuitem."</a>";
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
		$ZOWuser=_getCurrentUser();

		if ($ZOWuser!="miguel" && $ZOWuser!="sunil.singal" && $ZOWuser!="alvaro.ollero") {
				redirect('main');
		} 
		else {
			return $ZOWuser;
		}
	}
}


/* End of file userpermissions_helper.php */
/* Location: ./system/application/helpers/client_helper.php */