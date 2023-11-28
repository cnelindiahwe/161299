<?php

class Zt2016_create_report extends MY_Controller {

	
	public function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//helpers
		$this->load->helper(array( 'userpermissions','url','zt2016_invoice','form'));
		
		$zowuser=_superuseronly(); 

	


		$templateData['title'] = 'Create Report';
		$templateData['sidebar_content']='sidebar';
		
		$pageOutput = $this->zt2016_display_create_report();

		$templateData['main_content'] =$pageOutput; 

		$templateData['ZOWuser']=_getCurrentUser();

		$this->load->view('admin_temp/main_temp',$templateData);

	}


    // ################## Generates report content . ##################	
	
	function  zt2016_display_create_report()
	{
		$this->load->model('zt2016_retainersmodal', '', TRUE);

		$pageOutput="";	
	
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$pageOutput.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$pageOutput.=$this->session->flashdata('SuccessMessage');
			$pageOutput.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$pageOutput.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$pageOutput.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('ErrorMessage');
			$pageOutput.='</div>'."\n";
		}
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


		
		$pageOutput.='<div class="panel panel-info">'."\n";

		$pageOutput.='<div class="panel-heading">'."\n"; 

		$pageOutput.='<h3 class="panel-title">Retainer Update '.$client.'</h3>'."\n";
		
		$pageOutput.="</div><!--panel-heading-->\n";

		$pageOutput.='<div class="panel-body">'."\n";
		
		$pageOutput.='
					<div class="row p-3">
					<div class="col-sm-12 col-lg-4">
						<div class="bg-success text-center "><p>Period '.date("d-M-Y", $timestamp).' to '.date("d-M-Y", $timestamp1).'</p></div>
						<div class="">
						<ul class="row border p-3">
							<li class="col-6 bg-info">
							Month Figures
							</li>
							<li class="col-6 text-end bg-info">'.date("F-Y", $timestamp1).'</li>
							<li class="col-6">Billed hour</li>
							<li class="col-6 text-end">'.number_format($totalhours,2).'</li>
							<li class="col-6">Price pre hour</li>
							<li class="col-6 text-end">'.$clientdata->BasePrice.'</li>
							<li class="col-6">Period Total</li>
							<li class="col-6 text-end">'.number_format($totalhours * $perhour,2).'</li>
						</ul>
						</div><!--col-->
						<div class="">
							<ul class="row border p-3">
								<li class="bg-info">Updated Overall Retainer Hours</li>
								<li class="col-6">Available '.date("d M Y", $timestamp).'</li>
								<li class="col-6 text-end">'.number_format($clientdata->RetainerHours,2).'</li>
								<li class="col-6">Used</li>
								<li class="col-6 text-end">'.$totalhours.'</li>
								<li class="col-6">Remaining</li>
								<li class="col-6 text-end">'.number_format($clientdata->RetainerHours - $totalhours,2).'</li>
							</ul>
						</div><!--col-->
						<div class="">
							<ul class="row border p-3">
								<li class="bg-info">Updated Overall Retainer Figures</li>
								<li class="col-6">Available '.date("d M Y", $timestamp).'</li>
								<li class="col-6 text-end">'.number_format($clientdata->RetainerHours * $perhour,2).'</li>
								<li class="col-6">Used</li>
								<li class="col-6 text-end">'.number_format($totalhours * $perhour).'</li>
								<li class="col-6">Remaining</li>';
								$remainingfigure = ($clientdata->RetainerHours * $perhour) - ($totalhours * $perhour);
								$pageOutput.='
								<li class="col-6 text-end">'.$remainingfigure.'</li>
							</ul>
						</div><!--col-->
						<div class="">
							<ul class="row border p-3">
								<li class="col-4 bg-info">Month totals</li>
								<li class="col-4 text-end bg-info">Sub totals</li>
								<li class="col-4 text-end bg-info">Billed hours</li>
								<li class="col-4">New Slides</li>
								<li class="col-4 text-end">'.$newslides.'</li>
								<li class="col-4 text-end">'.number_format($newslides/5,2).'</li>
								<li class="col-4">Edited slides</li>
								<li class="col-4 text-end">'.$editslides.'</li>
								<li class="col-4 text-end">'.number_format($editslides/10,2).'</li>
								<li class="col-4">Direct hours</li>
								<li class="col-4 text-end">'.$dircethours.'</li>
								<li class="col-4 text-end">'.$dircethours.'</li>
							</ul>
						</div><!--col-->
					</div>
					
		';
		$pageOutput.='
		<div class="col-lg-8 col-sm-12">
		<div class="table-responsive">
			<table id="addTable" class="table table-hover table-white">
				<thead>
					<tr>
						<th style="width: 20px">#</th>
						<th class="col-sm-2">Date</th>
						<th class="col-md-6" style="width:100px;">Originator</th>
						<th>File Name</th>
						<th style="width:100px;">New Slides</th>
						<th style="width:100px;">Edit Slides</th>
						<th style="width:100px;">Hours</th>
				
					</tr>
				</thead>
				<tbody class="tbodyone">';
				$index = 1;
				foreach($invoiceentries as $row){
					$entriesDate = strtotime($row->DateOut);
					$entriesformattedDate = date("d-M-Y", $entriesDate);
					$pageOutput.=
					'<tr>
						<td class="row-number">'.$index.'</td>
						<td>
							'.$entriesformattedDate.'
						</td>
						<td>
							'.$row->Originator.'
						</td>
						<td>
							'.$row->FileName.'
						</td>
						<td>
							'.$row->NewSlides.'
						</td>
						<td>
							'.$row->EditedSlides.'
						</td>
						<td>
							'.$row->Hours.'
						</td>';
						
					$pageOutput.='</tr>';
					$index++;
				}
				$pageOutput.=
				'
				</tbody>
		';

		
 	
		$pageOutput.='</div><!--row -->';		
		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		//

		 
		//$pageOutput ="test"; 
		return $pageOutput;/**/

	}


}

/* End of file Zt2016_view_invoice */
/* Location: ./system/application/controllers/invoicing/Zt2016_view_invoice.php */
?>