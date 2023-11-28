<?php

class Csv_trends extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','financialsnew','form','reports'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array());

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakcontacts', '', TRUE);
		$this->load->model('trakmonthsummaries', '', TRUE);
		
		/*$EndDate = date('Y-n-j',strtotime("now"));
		$StartDate=date('Y-n-1',strtotime("3 month ago"));
		$timeframe=$StartDate.','.$EndDate;

		if ( isset($_POST['Timeframe']))
		{
			$timeframe=	$_POST['Timeframe'];
			$timeframedates=preg_split('/\,/', $_POST['Timeframe']);		
			$StartDate = $timeframedates[0];
			$EndDate = $timeframedates[1];
		}
		*/
		$now = strtotime(date('Y-m-15'));
		$this->load->dbutil();
		$StartDate = date( 'Y-m-1', strtotime('2010-10-1'));
		$EndDate = date( 'Y-m-t', strtotime('-1 month',$now ));
		
		$rawresults =$this->trakmonthsummaries->GetEntrybyDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'csv'=>1));

		$now = date('Y-m-15');
		$CurrentDate=date( 'M', strtotime($now));
		$CurrentDate.="_".date( 'Y', strtotime($now));
		echo $CurrentDate;
		$name = "ZOW_Trends_".$CurrentDate.".csv";
	
		$this->load->helper('download');
		force_download($name, $rawresults);

	}
 	
 		


/* End of file csv_trends.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>