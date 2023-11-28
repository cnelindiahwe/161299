<?php

class Reportsnew1 extends MY_Controller {

	function Reportsnew1()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreportsnew', '', TRUE);


		$templateVars['pageSidebar'] ="";
		$templateVars['pageOutput'] =$this->_monthData();
		$templateVars['pageInput'] ="";

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "reports";
		$templateVars['pageType'] = "reports";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');
				 
	}

	function  _monthData($options=array())
	{
				$dtpeurtotal=0;
				$dtpusdtotal=0;
				$nondtpeurtotal=0;
				$nondtpusdtotal=0;

	$data="<h2>Year Totals</h2>";
	$data.="<table>";
	$data.="<tr><th>Month</th><th>DTP Euros</th><th>DTP Dollars</th><th>Non-DTP Euros</th><th>Non-DTP Dollars</th><th>Total Euros</th><th>Total Dollars</th></tr>";
	$now = strtotime(date('Y-m-15'));
		for ($i = 1; $i <=11; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			
				$dtpeur=$this->trakreportsnew->_cashtotals($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Currency'=>'EUR', 'WorkType'=>'Office'));
				$dtpusd=$this->trakreportsnew->_cashtotals($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Currency'=>'USD', 'WorkType'=>'Office'));
				$nondtpeur=$this->trakreportsnew->_cashtotals($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Currency'=>'EUR', 'WorkType'=>'Non-Office'));
				$nondtpusd=$this->trakreportsnew->_cashtotals($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Currency'=>'USD', 'WorkType'=>'Non-Office'));			
				$data.= "<tr><td>";
				$data.= date( 'M Y', strtotime($StartDate ));
				$data.= "</td><td>";
				$data.= number_format($dtpeur,2);
				$data.= "</td><td>";
				$data.= number_format($dtpusd,2);
				$data.= "</td><td>";
				$data.= number_format($nondtpeur,2);
				$data.= "</td><td>";
				$data.=number_format( $nondtpusd,2);
				$data.= "</td><td>";
				$data.= number_format($dtpeur+$nondtpeur,2);
				$data.= "</td><td>";
				$data.=number_format( $dtpusd+$nondtpusd,2);
				$data.= "</td></tr>";
				$dtpeurtotal=$dtpeurtotal+$dtpeur;
				$dtpusdtotal=$dtpusdtotal+$dtpusd;
				$nondtpeurtotal=$nondtpeurtotal+$nondtpeur;
				$nondtpusdtotal=$nondtpusdtotal+$nondtpusd;
			}
				$data.= "<tr><td>";
				$data.= "Totals";
				$data.= "</td><td>";
				$data.= number_format($dtpeurtotal,2);
				$data.= "</td><td>";
				$data.= number_format($dtpusdtotal,2);
				$data.= "</td><td>";
				$data.= number_format($nondtpeurtotal,2);
				$data.= "</td><td>";
				$data.= number_format($nondtpusdtotal,2);
				$data.= "</td><td>";
				$data.= number_format($dtpeurtotal+$nondtpeurtotal,2);
				$data.= "</td><td>";
				$data.= number_format($dtpusdtotal+$nondtpusdtotal,2);
				$data.= "</td></tr>";
		$data.="</table>";
		
		return $data;
	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>