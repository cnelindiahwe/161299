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
 * Month_selector
 *
 * Creates top month selector dropdown
 *
 * @access	public
 * @return	string
 */
	if ( ! function_exists('month_selector'))
	{

		function  Month_selector($selectedmonth,$start_month,$form_info) {

			$initial=date( 'M Y', strtotime($start_month->row(0)->DateOut));

			$selected_month =date( 'M Y', strtotime($selectedmonth));

			$EndDate = date( 'M Y', strtotime('now'));
			$i=0;

			do {				
				$i++;
				$running_month=date( 'M Y', strtotime($initial.'+'.$i.'months'));
				$months_list[$running_month]=$running_month;				
				//$monthsarray.= $running_month."<br/>";;
				
			} while ($running_month != $EndDate);
			
			$months_list = array_reverse($months_list,TRUE);
			$month_selector=form_open($form_info['FormURL'],$form_info)."\n";
				$month_selector.='				<div class="form-group">'."\n";
				$month_selector.='					<div class="input-group" style="display:-webkit-inline-box";>'."\n";
				$more = 'id="date_dropdown_selector" class="selector form-control input"';
				$month_selector.=form_dropdown('selected_month', $months_list,$selected_month ,$more);
				$month_selector.='					</div>'."\n";
				$month_selector.='					<div class="input-group" style="display:-webkit-inline-box";> '."\n";
				$more = 'id="date_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
				$month_selector.=form_submit('date_dropdown_selector_submit', 'View Month',$more);			
			
				$month_selector.='					</div>'."\n";
				$month_selector.='				</div>'."\n";
			
			$month_selector.= form_close()."\n";


		 return $month_selector ;

		}		

	}
/* End of file 2016_reports_helper.php */
/* Location: ./system/application/helpers/2016_reports_helper.php */

/**
 * Month_selector
 *
 * Creates top month selector dropdown
 *
 * @access	public
 * @return	string
 */
	if ( ! function_exists('year_selector'))
	{

		function  year_selector($selectedyear,$form_info) {

			//$initial=date( 'Y', strtotime($start_year->row(0)->DateOut));

			$period = new DatePeriod(
				 new DateTime('2011-01-01'),
				 new DateInterval('P1Y'),
				 new DateTime('now')
			);
			foreach ($period as $key => $value) {
				$running_year=$value->format('Y');
				$years_list[$running_year]=$running_year;

			}			

			$years_list = array_reverse($years_list,TRUE);
			$month_selector=form_open($form_info['FormURL'],$form_info)."\n";
				$month_selector.='				<div class="form-group">'."\n";
				$month_selector.='					<div class="input-group" style="display:-webkit-inline-box";>'."\n";
				$more = 'id="date_dropdown_selector" class="selector form-control input"';
				$month_selector.=form_dropdown('selected_year', $years_list,$selectedyear ,$more);
				$month_selector.='					</div>'."\n";
				$month_selector.='					<div class="input-group" style="display:-webkit-inline-box";> '."\n";
				$more = 'id="date_dropdown_selector_submit" class="clientcontrolsubmit form-control"';
				$month_selector.=form_submit('date_dropdown_selector_submit', 'View Year',$more);			
			
				$month_selector.='					</div>'."\n";
				$month_selector.='				</div>'."\n";
			
			$month_selector.= form_close()."\n";


		 return $month_selector ;

		}		

	}
/* End of file 2016_reports_helper.php */
/* Location: ./system/application/helpers/2016_reports_helper.php */