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


// ------------------------------------------------------------------------

/**
 * _createpageswitcher
 *
 * Remote FTP server dir & file list
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_createpageswitcher'))
{
	function  _createpageswitcher(){
		$pageswitcher ="<form id=\"pageswitcher\" class=\"logout\">";
		$pageswitcher .="<select id=\"pageswitcherselect\" ><option value=''>More ..</option>";
		$pageswitcher .="<option value='contacts/zt2016_contacts_search'>Search</option>";
		$pageswitcher .="<option value='reports'>Reports</option>";
		$pageswitcher .="<option value='limbo'>Limbo</option>";
		$pageswitcher .="</select>";
		$pageswitcher .="<input type=\"submit\" value=\"Go\" id=\"pageswitchersubmit\">";
		$pageswitcher .="</form>";		
		return $pageswitcher;
	}
}
/* End of file zowtrakiu_helper.php */
/* Location: ./system/application/helpers/limbo_helper.php  */
 ?>
