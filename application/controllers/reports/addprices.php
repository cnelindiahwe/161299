<?php

class Addprices extends MY_Controller {

	function  Addprices ()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports','financials'));

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakentries', '', TRUE);
		echo "working";

 		$now = strtotime(date('Y-m-15'));
		for ($i = 1; $i <=24; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$monthtotal= $this-> _monthData($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			if ($monthtotal) {
				echo $StartDate."<br/>";
				$this->_insertPrices($monthtotal,$StartDate,$EndDate);
			}
			
			}
		
		echo "done";
		
				 
	}

	function  _monthData($options=array())
	{
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
			unset ($options);	


			$allclients = $this->trakreports->ClientsByDate($initialoptions=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			foreach ($allclients as $clientrow) {
				$monthtotal[$clientrow->Client]['Client']= $clientrow->Client;
				$monthtotal[$clientrow->Client]['Date']= $StartDate;
			}
			//dtp
			$bookeddata = $this->trakreports->_getAllClientTotalsByDate($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			foreach ($bookeddata as $row) {
				foreach ($row as $key => $value) {
					//transfer new, edits, hours
					
					$monthtotal[$row['Client']]['OfficeNewSlides']= $row['NewSlides'];
					$monthtotal[$row['Client']]['OfficeEditedSlides']= $row['EditedSlides'];
					$monthtotal[$row['Client']]['OfficeHours']= $row['Hours'];

					//Calculate total
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row['Client']));
					//Apply edit price
					$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
					//Add slides and divide by slides per hour
					$subtotalbooked= $row['NewSlides']+$subtotalbooked;
					$subtotalbooked= $subtotalbooked/5;
					//Add hours to get the total
					$bookedtotal= $subtotalbooked+$row['Hours'];
					$monthtotal[$row['Client']]['HoursBilled']= $bookedtotal;/**/
					
					//price and currency
					$monthtotal[$row['Client']]['Price']= _fetchClientMonthPrice($clientdata,$bookedtotal);/**/
					$monthtotal[$row['Client']]['Currency']=$clientdata->Currency;/**/
				}
			}

		if ($monthtotal) return $monthtotal;
	}
	
	
	

	
	
	
			
	function _insertPrices($monthtotal,$StartDate,$EndDate)
	{

			foreach ($monthtotal as $monthrow) {
					
					$entriestoupdate=$this->trakentries->GetEntryRange($options = array("Client"=>$monthrow['Client'],"Trash"=>0),$StartDate,$EndDate);
					if ($entriestoupdate) {
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $monthrow['Client']));
						foreach($entriestoupdate as $entry)
						{
							$jobtotal=($entry->NewSlides+($entry->EditedSlides*$clientdata->PriceEdits))/5;
							$jobtotal=$jobtotal+$entry->Hours;
							$JobPrice=$monthrow['Price']*$jobtotal;
							$done=$this->trakentries->UpdateEntry($options = array("id"=>$entry->id,"MonthPrice"=>$monthrow['Price'],"Currency"=>$monthrow['Currency'],"HoursBilled"=>$jobtotal,"JobPrice"=>$JobPrice));
						}
					}

					
			}

	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>