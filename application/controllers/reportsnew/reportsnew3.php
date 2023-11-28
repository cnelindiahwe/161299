<?php

class Reportsnew3 extends MY_Controller {

	function Reportsnew3()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreportsnew', '', TRUE);
		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakcontacts', '', TRUE);


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
			$StartDate = date( 'Y-1-1');
			$EndDate = date( 'Y-11-30');

	$data="<h2>Contact Totals</h2>";
	
	$data.="<h4>EUR DTP</h4>";
	$clients=$this->trakreportsnew->ContactsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Office','Currency'=>'EUR'));	
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{
	$data.="<tr><td>".$thisclient->Originator."</td><td>";
	$company=$this->trakreportsnew->_getcontactcompany($thisclient->Originator);
	$data.="<td><td>".$company."</td><td>";
	$cashtotal=$this->trakreportsnew->_contactcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Originator'=>$thisclient->Originator));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";	
	
	
	$clients=$this->trakreportsnew->ContactsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'WorkType'=>'Office','Currency'=>'USD'));	
	$data.="<h4>USD DTP</h4>";
	$data.="<table>";	
	foreach ($clients as $thisclient)
	{

	$data.="<tr><td>".$thisclient->Originator."</td><td>";
	$company=$this->trakreportsnew->_getcontactcompany($thisclient->Originator);
	$data.="<td><td>".$company."</td><td>";
	$cashtotal=$this->trakreportsnew->_contactcashtotal($options=array ('StartDate'=>$StartDate, 'EndDate'=>$EndDate, 'Originator'=>$thisclient->Originator));
$data.=number_format($cashtotal,2)."</td></tr>";
	}
	$data.="</table>";
	



	return $data;
	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>