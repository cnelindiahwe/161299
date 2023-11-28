<?php

class Createreportdb extends MY_Controller {

	function Createreportdb ()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports','financials'));

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		echo "working";

 		$now = strtotime(date('Y-m-15'));
		for ($i = 0; $i <=24; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$monthtotal= $this-> _monthData($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			$this->_createdb($monthtotal);
			
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
			$bookeddata = $this->trakreports->_getAllClientTotalsByDate($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Office'));
			
			foreach ($bookeddata as $row) {
				foreach ($row as $key => $value) {
					//transfer new, edits, hours
					
					$monthtotal[$row['Client']]['OfficeNewSlides']= $row['NewSlides'];
					$monthtotal[$row['Client']]['OfficeEditedSlides']= $row['EditedSlides'];
					$monthtotal[$row['Client']]['OfficeHours']= $row['Hours'];
					//number of Jobs
					$monthtotal[$row['Client']]['OfficeJobsCount']= $this->trakreports->_NumJobsByDate($options = array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'Client'=>$row['Client']));
					//Calculate total
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row['Client']));
					//Apply edit price
					$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
					//Add slides and divide by slides per hour
					$subtotalbooked= $row['NewSlides']+$subtotalbooked;
					$subtotalbooked= $subtotalbooked/5;
					//Add hours to get the total
					$bookedtotal= $subtotalbooked+$row['Hours'];
					$monthtotal[$row['Client']]['OfficeHoursBilled']= $bookedtotal;/**/
					
					//price and currency
					$monthtotal[$row['Client']]['MonthPrice']= _fetchClientMonthPrice($clientdata,$bookedtotal);/**/
					$monthtotal[$row['Client']]['MonthCurrency']=$clientdata->Currency;/**/
				}
			}
			//web
				$bookeddata = $this->trakreports->_getAllClientTotalsByDate($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Non-Office'));
			
			foreach ($bookeddata as $row) {
				foreach ($row as $key => $value) {
					//transfer new, edits, hours
					$monthtotal[$row['Client']]['NonOfficeNewSlides']= $row['NewSlides'];
					$monthtotal[$row['Client']]['NonOfficeEditedSlides']= $row['EditedSlides'];
					$monthtotal[$row['Client']]['NonOfficeHours']= $row['Hours'];
					//number of Jobs
					$monthtotal[$row['Client']]['NonOfficeJobsCount']= $this->trakreports->_NumJobsByDate($options = array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'Client'=>$row['Client']));
					//Calculate total
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row['Client']));
					//Apply edit price
					$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
					//Add slides and divide by slides per hour
					$subtotalbooked= $row['NewSlides']+$subtotalbooked;
					$subtotalbooked= $subtotalbooked/5;
					//Add hours to get the total
					$bookedtotal= $subtotalbooked+$row['Hours'];
					$monthtotal[$row['Client']]['NonOfficeHoursBilled']= $bookedtotal;/**/
					
					
					//Price
					if (isset($monthtotal[$row['Client']]['OfficeHoursBilled'])) {
							$bookedtotal=$bookedtotal+$monthtotal[$row['Client']]['OfficeHoursBilled'];
					}
					$monthtotal[$row['Client']]['MonthPrice']= _fetchClientMonthPrice($clientdata,$bookedtotal);/**/
					$monthtotal[$row['Client']]['MonthCurrency']=$clientdata->Currency;/**/				}
			}

					
		 return $monthtotal;
	}
	
	
	

	
	
	
			
	function _createdb($monthtotal)
	{

		$qualificationArray = array('Client','Date', 'NonOfficeHoursBilled','OfficeHoursBilled','NonOfficeJobsCount','OfficeJobsCount','MonthPrice','MonthCurrency');				
			foreach ($monthtotal as $monthrow) {
			// qualification (make sure that we're not allowing the site to insert data that it shouldn't)
					foreach($qualificationArray as $qualifier)
					{
						if(isset($monthrow[$qualifier])) $this->db->set($qualifier, $monthrow[$qualifier]);
					}
					
					// Execute the query
					$this->db->insert('zowtrakreports');
			}

	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>