<?php

class Reportsnew2 extends MY_Controller {

	function Reportsnew2()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreportsnew', '', TRUE);
		$this->load->model('trakreports', '', TRUE);


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
			$StartDate = date( 'Y-01-01');
			$EndDate = date( 'Y-11-30');

	$data="<h2>Client Totals</h2>";
	
	$data.="<h4>EUR DTP</h4>";
	$clients=$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Office','Currency'=>'EUR'));	
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{
	$data.="<tr><td>".$thisclient->Client."</td><td>";
	$cashtotal=$this->trakreportsnew->_clientcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Client'=>$thisclient->Client));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";	
	
	
	$clients=$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Office','Currency'=>'USD'));	
	$data.="<h4>USD DTP</h4>";
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{
	$data.="<tr><td>".$thisclient->Client."</td><td>";
	$cashtotal=$this->trakreportsnew->_clientcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Client'=>$thisclient->Client));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";
			
	$data.="<h4>EUR NON-DTP</h4>";
	$clients=$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Non-Office','Currency'=>'EUR'));	
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{
	$data.="<tr><td>".$thisclient->Client."</td><td>";
	$cashtotal=$this->trakreportsnew->_clientcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Client'=>$thisclient->Client));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";	

	$clients=$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Non-Office','Currency'=>'USD'));	
	$data.="<h4>USD NON-DTO</h4>";
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{
	$data.="<tr><td>".$thisclient->Client."</td><td>";
	$cashtotal=$this->trakreportsnew->_clientcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Client'=>$thisclient->Client));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";		



	return $data;
	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>