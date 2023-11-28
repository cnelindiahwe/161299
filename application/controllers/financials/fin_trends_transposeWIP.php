<?php

class Fin_trends extends MY_Controller {


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
		
		$EndDate = date('Y-n-j',strtotime("now"));
		$StartDate=date('Y-n-1',strtotime("3 month ago"));
		$timeframe=$StartDate.','.$EndDate;

		if ( isset($_POST['Timeframe']))
		{
			$timeframe=	$_POST['Timeframe'];
			$timeframedates=preg_split('/\,/', $_POST['Timeframe']);		
			$StartDate = $timeframedates[0];
			$EndDate = $timeframedates[1];
		}
		
		//Month totals

		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";
		$templateVars['pageOutput'] .= $this->_getTopMenu($timeframe);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
			$templateVars['pageOutput'] .= $this->_totalstables();
		
		
		
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "fin_trends";
		$templateVars['pageType'] = "fin_trends";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _getTopMenu($timeframe="")

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Trends</h1>";
			//Add splits button
			$entries .="<a href=\"".site_url()."financials\" >Monthly Splits</a>";
			//Add totals button
			$entries .="<a href=\"".site_url()."financials/fin_totals\" >Totals</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}

// ------------------------------------------------------------------------

/**
 * _totalstables
 *
 * Provides number of clients per month
 *
 * @access	public
 * @return	string
 */
 	function _totalstables($options=array()){
 		
 		$reportresults ="<h3>Historical Data</h3><div class=\"clientreportlayout\">";
		
 		$now = strtotime(date('Y-m-15'));
 		//for ($i =12; $i >=0; $i--) {
		
		$StartDate = date( 'Y-m-1', strtotime('2010-10-1'));
		$EndDate = date( 'Y-m-t', strtotime('-1 month',$now ));
		
		$rawresults =$this->trakmonthsummaries->GetEntrybyDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));


	    //http://stackoverflow.com/questions/797251/transposing-multidimensional-arrays-in-php
		   
		   
		    $transposedresults = array();
		    foreach ($rawresults as $key => $subarr) {
		    	foreach ($subarr as $subkey => $subvalue) {
		    		$transposedresults[$subkey][$key] = $subvalue;
		    	}
		    }
		//########## table
		$reportresults .="<table id=\"graphtable\">";
		
		$row = array_slice($transposedresults,1,1);

		$reportresults .="<thead><tr>";
		foreach($row  as $item=> $value)
		{
			foreach($value  as $key=> $subvalue) {	
			$reportresults .="<th>".date( 'M - Y', strtotime($subvalue))."</th>";
			}
		}
		$reportresults .="</tr></thead>";
		
		//########## tbody
		$reportresults .="<tbody>";
		$rowstoread = array (3,8,9,10,11);
		foreach($rowstoread  as $row => $subrow)
		{
				
			$reportresults .= "<tr>";	
			$currentrow = array_slice($transposedresults,$subrow,1);
			foreach($currentrow  as $item=> $value)
			{
				foreach($value  as $key=> $subvalue) {	
					$reportresults .="<td>".$subvalue."</td>";
				}
			}
		}
	
		$reportresults .= "</tr>";	
			
		
		 //$reportresults .= "<tr><th scope=\"row\">".date( 'M Y', strtotime($row['Date']))."</th></tr>";
			
			//$reportresults .= "<tr><th scope=\"row\">".date( 'M Y', strtotime($row->Date))."</th><td>".$row->Jobs."</td><td>".$row->Clients."</td>";
			//$reportresults .="<td>".$row->Contacts."</td><td>".$row->NewClients."</td><td>".$row->NewContacts."</td></tr>";
		//}			
		//}*/
		$reportresults .="</tbody></table></div>";
		return $reportresults;
	}	

// ------------------------------------------------------------------------

 	
 		


/* End of file fin_trends.php */
/* Location: ./system/application/controllers/financials/fin_totals.php */
}

?>