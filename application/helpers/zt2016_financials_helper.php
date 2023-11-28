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
 * _fetchClientMonthPrice'
 *
 * Provides discount price for a given client for a given volume
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_fetchClientMonthPrice'))
{
	function _fetchClientMonthPrice($clientdata,$bookedtotal){
		$price =0;
		if ($clientdata->VolDiscount1Trigger!=0) {
			if ($bookedtotal>$clientdata->VolDiscount1Trigger) {
				if ($clientdata->VolDiscount2Trigger!=0) {
					if ($bookedtotal>$clientdata->VolDiscount2Trigger) {
						if ($clientdata->VolDiscount3Trigger!=0) {
							if ($bookedtotal>$clientdata->VolDiscount3Trigger) {
								if ($clientdata->VolDiscount4Trigger!=0) {
									if ($bookedtotal>$clientdata->VolDiscount4Trigger) {
										$price =$clientdata->VolDiscount4Price;										
									}
									else{$price =$clientdata->VolDiscount3Price;}
								}
								else {$price =$clientdata->VolDiscount3Price;}
							}
							else {$price =$clientdata->VolDiscount2Price;}
						}
						else {$price =$clientdata->VolDiscount2Price;}
					}
					else{$price =$clientdata->VolDiscount1Price;}
				}
				else {$price =$clientdata->VolDiscount1Price;}
			}
			else {$price =$clientdata->BasePrice;}
		}
		else {$price =$clientdata->BasePrice;}

		return $price;

	}

}



/* End of file zt2016_invoice_helper.php */
/* Location: ./system/application/helpers/invoice_helper.php */