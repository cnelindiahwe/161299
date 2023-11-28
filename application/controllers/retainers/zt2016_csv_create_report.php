<?php

class Zt2016_csv_create_report extends MY_Controller {


	
	function index(){


		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		
		//$this->load->helper(array('form','url','invoice','reports','financials'));
		$this->load->helper(array('form','url','invoice','reports'));
		
		$this->load->model('zt2016_retainersmodal', '', TRUE);


        if($this->session->flashdata('Reportdata')){		
			
			$reportData = $this->session->flashdata('Reportdata');
			
			$startDate = $reportData['startDate'];
            $endDate = $reportData['endDate'];
            $client = $reportData['client'];
			$timestamp = strtotime(str_replace('/', '-', $startDate));
			$start = date("Y-m-d", $timestamp);
			$timestamp1 = strtotime(str_replace('/', '-', $endDate));
			$end = date("Y-m-d", $timestamp1);

			#get client data
			$this->load->model('zt2016_clients_model', '', TRUE);
			$clientdata = $this->zt2016_clients_model->GetClient($options = array('CompanyName'=>$client));
			// print_r($clientdata);
			// die;
			$invoice = $this->zt2016_retainersmodal->gettotalhours($options = array('client'=>$client,'startDate'=>$start,'endDate'=>$end));
			$invoiceentries = $this->zt2016_retainersmodal->invoiceEntries($options = array('client'=>$client,'startDate'=>$start,'endDate'=>$end));
			// print_r($invoiceentries);
			// die;
			$this->session->set_flashdata('Reportdata',$reportData);
			$totalhours = round($invoice->totalhours,2);
			$newslides = round($invoice->sumnew,2);
			$editslides = round($invoice->sumedit,2);
			$dircethours = round($invoice->sumhours,2);
			$perhour = $clientdata->BasePrice;
		}

		
		
		
		
		

	

		
		$this->load->dbutil();
					
		
		$StartDate = date("d-M-Y", $timestamp);
		$EndDate = date("d-M-Y", $timestamp1);
				
		


				
		$delimiter = ",";
		$newline = "\n";
		$data ="";

		// Data for the left section
		$leftSection = '';
		$data .= '"Retainer Update ' . $client . '"' . ',,,,,' . '"date"' . $delimiter . '"Originator"' . $delimiter . '"File Name","New slides","Edited slides","Other (hours)"' . $newline;
		
		// print_r($invoiceentries);
		// die;
		$leftData = array();
		$leftData[] = array('Period',$StartDate,"to",$EndDate,"");
		$leftData[] = array('',"","","","");
		$leftData[] = array('Month Figures',date("F-Y", $timestamp1),"","","");
		$leftData[] = array('Billed hours',$totalhours,"","","");
		$leftData[] = array('Price per hour',$perhour,"","","");
		$leftData[] = array('Period Total',number_format($totalhours * $perhour,2),"","","");


		$leftData[] = array('',"","","","");
		$leftData[] = array('Updated Overall Retainer Hours',"","","","");
		$leftData[] = array('Available'.date("d M Y", $timestamp),number_format($clientdata->RetainerHours,2),"","","");
		$leftData[] = array('Used',$totalhours,"","","");
		$leftData[] = array('Remaining',number_format($clientdata->RetainerHours - $totalhours,2),"","","");

		$leftData[] = array('',"","","","");
		$leftData[] = array('Updated Overall Retainer Figures',"","","","");
		$leftData[] = array('Available'.date("d M Y", $timestamp),number_format($clientdata->RetainerHours * $perhour,2),"","","");
		$leftData[] = array('Used',number_format($totalhours * $perhour),"","","");
		$remainingfigure = ($clientdata->RetainerHours * $perhour) - ($totalhours * $perhour);
		$leftData[] = array('Remaining',number_format($remainingfigure,2),"","","");

		$newslideshour = number_format($newslides/5,2);
		$editslideshour = number_format($editslides/10,2);
		$totalhoursHwe = $newslideshour + $editslideshour + $dircethours;
		$leftData[] = array('',"","","","");
		$leftData[] = array('Month totals',"Sub totals","Billed hours","","");
		$leftData[] = array('New Slides',$newslides,number_format($newslideshour,2),"","");
		$leftData[] = array('Edited slides',$editslides,number_format($editslideshour,2),"","");
		$leftData[] = array('Direct hours',$dircethours,$dircethours,"","");
		$leftData[] = array('Total billed hours',"",$totalhoursHwe,"","");


		$leftCount = count($leftData);
		$invoiceCount = count($invoiceentries);

		$loopCount = $leftCount < $invoiceCount ? $invoiceCount : $leftCount;



		for ($i=0; $i < $loopCount; $i++) { 
			$leftDataHwe = $leftData[$i];
			if($leftDataHwe){
				foreach($leftDataHwe as $left){
					$data .= '"'.$left.'"'.$delimiter;
				}
			}else{
				$data.='"","","","","",';
			}
			
			$row = $invoiceentries[$i];
			if($row){
				$data .= '"' . $row->DateOut . '"' . $delimiter;
				$data .= '"' . $row->Originator . '"' . $delimiter;
				$data .= '"' . $row->FileName . '"' . $delimiter;
				$data .= '"' . $row->NewSlides . '"' . $delimiter;
				$data .= '"' . $row->EditedSlides . '"' . $delimiter;
				$data .= '"' . $row->Hours . '"' . $delimiter . $newline;
			}
			else{
				$data  .= $newline;
			}
			
		}

		$name = "ZOW_Report.csv";

		$this->load->helper('download');
		// "\xEF\xBB\xBF" sets download to UTF encoding
		force_download($name, "\xEF\xBB\xBF" . $data);


	}	
	


}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>