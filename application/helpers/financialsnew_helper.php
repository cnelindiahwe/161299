<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2010 - 2009, Zebra On WHeels
 * @since		Version 1.0S
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
// ------------------------------------------------------------------------

/**
 * _MonthPricestable
 *
 * Generates month price table
 *
 * @access	public
 * @return	string
 */

	function _MonthPricestable($monthtotal,$StartDate){
			if ($monthtotal=="") {
				$output="".date( 'M Y', strtotime($StartDate))." - No entries.";
				return $output;
			}
			
			$hourstotal=0;
			$eurtotal=0;
			$usdtotal=0;
			
				
			$monthTotalTable="<table class=\"monthprice\">";
			$monthTotalTable.="<thead><tr><th>Client</th><th>Revenue</th><th>Currency</th><th>Hours</th><th>Price</th></tr></thead><tbody>";
			foreach ($monthtotal as $row) {
					//$cash=$row['Price']*$row['Total'];
					$monthTotalTable.="<tr><td>".$row['Client']."</td>";	
					$monthTotalTable.="<td>".number_format($row['Ammount'],2,".",",")."</td>";	
					$monthTotalTable.="<td>".$row['Currency']."</td>";	
					$monthTotalTable.="<td>".$row['Total']."</td>";	
					$monthTotalTable.="<td>".$row['Price']."</td>";	
					$monthTotalTable.="</tr>";
					$hourstotal=number_format($hourstotal+$row['Total'],2);
					if (strtoupper($row['Currency'])=="EUR"){
						$eurtotal=$eurtotal+$row['Ammount'];
					}
					else {
						$usdtotal=$usdtotal+$row['Ammount'];
					}
			}
			$monthTotalTable.="</tbody></Table>";
			
			$output="<div class=\"content\"><h3 class=\"title\">".number_format($hourstotal, 2)." hours completed in ".Date('F Y', strtotime($StartDate))." = ";
			$output.= number_format($eurtotal, 2)." EUR | ";
			$output.= number_format($usdtotal, 2)." USD</h3>";
			$output.=$monthTotalTable;
			$output.="</div>";
			$tempeuro=$eurtotal*0.6;
			$tempusd=$usdtotal*0.6;
			$output.="<div  class=\"content\"><hr/>60% split: ".number_format($tempeuro, 2)." EUR | ";
			$output.=number_format($tempusd, 2)." USD  </div>";
		 return $output;

	}
// ------------------------------------------------------------------------

/**
 * _buildSplitTables
 *
 * Generates splits table
 *
 * @access	public
 * @return	string
 */
 	function  _buildSplitTables($AllTotals,$StartDate,$EndDate,$Status,$ClientList,$monthtotals,$Partners)
	{
	
				//Build table			
				$split="";	
				$MiguelsplitEUR=0;
				$SunilsplitEUR=0;
				$MiguelsplitUSD=0;
				$SunilsplitUSD=0;
				$TeamIndiaSplitEUR=0;
				$TeamIndiaSplitUSD=0;
				$TotalEUR=0;
				$TotalUSD=0;				
				
				$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
					
				$tableheader="<table class=\"financials\">\n<thead><tr><th>Client</th>\n";
				$tableheader.="<th>Total</th>";
				$tableheader.="<th>Currency</th>";
				$tableheader.="<th>Price</th>";
				$tableheader.="<th>FD</th><th>WK</th><th>PR</th>";
				foreach($StatusType as $Status) {
					$tableheader.="<th>".$Status."</th>";			
				}
				$tableheader.="</tr></thead>\n";
				// echo '<pre>';
				//  print_r($Partners);
				foreach($Partners as $Partner)	{
				    
					$partnertotalUSD=0;
					$partnertotalEUR=0;
					
					/*					
					$tablesplit="<table class=\"financials\">\n<thead><tr><th>Client</th>";
					$tablesplit.="<th>Total</th>";
					$tablesplit.="<th>Currency</th>";
					$tablesplit.="<th>Price</th>";
					$tablesplit.="<th>FD</th><th>WK</th><th>PR</th>";
					foreach($StatusType as $Status) {
						$tablesplit.="<th>".$Status."</th>";			
					}
					$tablesplit.="</tr></thead>\n";
					*/
					
					$tablesplit="";
					$rowtotal=0;
					
					
					foreach ($ClientList as $clientb) {
									if ($Partner == "Miguel"){
										
										if ($monthtotals[$clientb->Client]['Currency']=="EUR"){
										
											  $TotalEUR=$TotalEUR+$monthtotals[$clientb->Client]['Ammount'];

											//$TotalEUR=$TotalEUR+($monthtotals[$clientb->Client]['Total']*$monthtotals[$clientb->Client]['Price']);
										}else {
												 $TotalUSD=$TotalUSD+$monthtotals[$clientb->Client]['Ammount'];
											//$TotalUSD= 	$TotalUSD+($monthtotals[$clientb->Client]['Total']*$monthtotals[$clientb->Client]['Price']);
										}
									}

						
						$rowsubtotals="";
						

						foreach($StatusType as $Status) {
								//if (isset($AllTotals[$clientb->Client][$Status][$Partner]['total'])){
								$aux=$AllTotals[$clientb->Client][$Status];
								
								
								if (array_key_exists($Partner,$aux)){ 
									
									$cell=$AllTotals[$clientb->Client][$Status][$Partner]['total']." hours<br/><span>".$AllTotals[$clientb->Client][$Status][$Partner]['span']."</span>";
									if ($Status=='ScheduledBy'){
										
										 $FD=$AllTotals[$clientb->Client][$Status][$Partner]['cashtotal'];
										
									 	$FDcell=$FD*.1;

										
									}
									else if ($Status=='WorkedBy'){
										 $WK=$AllTotals[$clientb->Client][$Status][$Partner]['cashtotal'];
										$WKcell=$WK*.4;

									}
									else if ($Status=='ProofedBy'){

										$PR=$AllTotals[$clientb->Client][$Status][$Partner]['cashtotal'];
										$PRcell=$PR*.1;

									}
								}
								else{

									$cell="-";
									if ($Status=='ScheduledBy'){
										$FDcell="-";
									}
									else if ($Status=='WorkedBy'){
										$WKcell="-";
									}
																	
									else if ($Status=='ProofedBy'){
										$PRcell="-";
									}
								}
							$rowsubtotals.="<td>".$cell."</td>";
							}
							
							
							$totalssplit="<td>".$FDcell;
							//if ($FDcell!="-") {$totalssplit.=" ".$monthtotals[$clientb->Client]['Currency'];}
							$totalssplit.="</td>";
							
							$totalssplit.="<td>".$WKcell;
							//if ($WKcell!="-") {$totalssplit.=" ".$monthtotals[$clientb->Client]['Currency'];}
							$totalssplit.="</td>";
							
							
							$totalssplit.="<td>".$PRcell;
							//if ($PRcell!="-") {$totalssplit.=" ".$monthtotals[$clientb->Client]['Currency'];}
							$totalssplit.="</td>";
							// echo '<br>';
							// echo $FDcell;
							// echo' FDcell<br>';
							// echo $WKcell;
							// echo' WKcell<br>';
							// echo $PRcell;
							// echo' PRcell<br>';
							
							  $rowtotal = _calculate_split_total($FDcell,$WKcell,$PRcell);
							

							
							
							if ($rowtotal != "-") {
								
								if ($tablesplit==""){
									$tablesplit=$tableheader;
								}
								$tablesplit.="<tr>\n";
								$tablesplit.="<td>".$clientb->Client."</td>";
								$tablesplit.="<td>".number_format($rowtotal, 2)."</td>";
								$tablesplit.="<td>".$monthtotals[$clientb->Client]['Currency']."</td>";
								$tablesplit.="<td>".$monthtotals[$clientb->Client]['Price']."</td>";
								if ($monthtotals[$clientb->Client]['Currency']=='EUR') {
										$partnertotalEUR=$partnertotalEUR+$rowtotal;
								}
								elseif ($monthtotals[$clientb->Client]['Currency']=='USD'){
									$partnertotalUSD=$partnertotalUSD+$rowtotal;
								}
								$tablesplit.=$totalssplit;
								$tablesplit.=$rowsubtotals;
								$tablesplit.="</tr>\n";
							}
							else {
								$tablesplit.="";
							}
							
							
					}		
					if ($tablesplit!=""){
						$tablesplit.="</table>";
					}
					
					
				if (($Partner!="" && number_format($partnertotalEUR, 2) != 0) || ($Partner!="" && number_format($partnertotalUSD, 2) != 0)){
				
					// if ($tablesplit!=""){

						$split.="<div class=\"partnersplit content\" >\n<h4 class=\"title\">".$Partner."  ";
						$split.= number_format($partnertotalEUR, 2)." EUR | ";
						$split.= number_format($partnertotalUSD, 2)." USD</h4>\n";
						$split.=$tablesplit;
						$split.="</div>\n";
					// }
					//|| $Partner=="Jirka"
					if ($Partner=="Miguel"){
						$MiguelsplitEUR=$MiguelsplitEUR+$partnertotalEUR;
						$MiguelsplitUSD=$MiguelsplitUSD+$partnertotalUSD;
						}
					else if ($Partner=="Sunil" ){
						$SunilsplitEUR=$SunilsplitEUR+$partnertotalEUR;
						$SunilsplitUSD=$SunilsplitUSD+$partnertotalUSD;
					}
					// die;
					$CI = get_instance();

					// You may need to load the model if it hasn't been pre-loaded
						$CI->load->model('zt2016_users_model');
						$UsersData = $CI->zt2016_users_model->GetUser_ascfname_nonstatus();
					$new_user_arr = array();
		
		foreach($UsersData as $user_list){
				$name = ucfirst($user_list->fname);
			
			$new_user_arr[]=$name;

		}
		
		 $indiapartners =$new_user_arr;// array ("Agnel", "Arpita", "Ashish", "Dinesh", "Divya", "Ganesh", "Hussain", "Hiren", "Jemema", "Joseph", "Kanchan", "Manali", "Manikandan", "Nainesh",  "Nandhinipriya","Nazima", "Poojari", "Prakash", "Pranjali", "Saakshi", "Seemakaur", "Sharique", "Shital", "Shreyas", "Sijo","Seema", "Sneha","Sowmya", "Subathra", "Suyog" ,"Tarun", "Vaishali");
		foreach($indiapartners as $indiapartners_names){
		if(!in_array($indiapartners_names,$new_user_arr)){
			$new_user_arr[]=$indiapartners_names;
		}
		}
		// print_r($indiapartners);
		
					if (in_array($Partner, $indiapartners )){
						if ($Partner !="Miguel" && $Partner !="Sunil" ){
						    
						 $TeamIndiaSplitEUR=$TeamIndiaSplitEUR+$partnertotalEUR;
						$TeamIndiaSplitUSD=$TeamIndiaSplitUSD+$partnertotalUSD;
						}
					}
				}
			}
			// echo ($TeamIndiaSplitEUR-$SunilsplitEUR)+$SunilsplitEUR.'<br>';
			$split.= "<br/><div  class=\"content\">";
			$split.= "<hr/><p>Final Revenue Split:</p>";
			$split.= "<h3>Miguel: ";
			$split.= number_format(($TotalEUR*.4)+$MiguelsplitEUR, 2)." EUR | ";
			$split.= number_format(($TotalUSD*.4)+$MiguelsplitUSD, 2)." USD</h3>";
			$split.= "<p>(".number_format($TotalEUR*.4, 2)." + ".number_format($MiguelsplitEUR, 2)." EUR | ";
			$split.= number_format($TotalUSD*.4, 2)." + ".number_format($MiguelsplitUSD, 2);
			$split.= " USD)</p>";
			$split.= "<h3>Sunil: ".number_format($SunilsplitEUR+$TeamIndiaSplitEUR, 2)." EUR | ";
			$split.= number_format($SunilsplitUSD+$TeamIndiaSplitUSD, 2)." USD</h3>";
			$split.= "<p>(".number_format($SunilsplitEUR, 2)." + ".number_format($TeamIndiaSplitEUR, 2)." EUR | ";
			$split.= number_format($SunilsplitUSD, 2)." + ".number_format($TeamIndiaSplitUSD, 2);
			$split.= " USD)</p>";
			$split.= "</div>";
			//$output="<h4>".$Partner."</h4>";
			//$output=$split;

			//return $output;
			return $split;
	}


// ------------------------------------------------------------------------

/**
 * _calculate_split_total
 *
 * Generates splits table
 *
 * @access	public
 * @return	string
 */
 	function  _calculate_split_total($FDcell,$WKcell,$PRcell)
	{
			
			$rowtotal=0;	
			if ($FDcell!="-"){
				$rowtotal=$rowtotal+$FDcell;
			}
			if ($WKcell!="-"){
				$rowtotal=$rowtotal+$WKcell;
			}
			// if ($WKcell_2!="-"){
			// 	$rowtotal=$rowtotal+$WKcell_2;
			// }
			// if ($WKcell_3!="-"){
			// 	$rowtotal=$rowtotal+$WKcell_3;
			// }
			if ($PRcell!="-"){
				$rowtotal=$rowtotal+$PRcell;
			}
			if ($rowtotal==0) {$rowtotal="-";}	
			return $rowtotal;
	}



/* End of file invoice_helper.php */
/* Location: ./system/application/helpers/invoice_helper.php */