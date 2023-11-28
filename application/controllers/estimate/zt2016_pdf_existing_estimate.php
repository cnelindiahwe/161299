<?php
use Mpdf\Mpdf;
class Zt2016_pdf_existing_estimate extends MY_Controller {


	
	function index()
	{
		
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		
		//$this->load->helper(array('form','url','invoice','reports','financials'));
		$this->load->helper(array('form','url','invoice','reports'));
		
		//Read form input values
        $estimateNumber=$this->uri->segment(3);

        $this->load->model('zt2016_estimateModal', '', TRUE);
		$estimateData = $this->zt2016_estimateModal->getestimate($options = array('quotationNumber'=>$estimateNumber));
		$estimateentries = $this->zt2016_estimateModal->getestimateentries($options = array('quotationNumber'=>$estimateNumber));


		
		
		#get client data
		$this->load->model('zt2016_clients_model', '', TRUE);
        $clientdata = $this->zt2016_clients_model->GetClient($options = array('CompanyName'=> $estimateData->Client));
		
		if(empty ( $clientdata)) {
			$Message='No Client data found for invoice '.$estimateNumber;						
			$this->session->set_flashdata('SuccessMessage',$Message);

			redirect('/estimate/zt2016_estimageView/'.$estimateNumber);
			// echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }
		

		#get global setting data
		$this->load->model('globalSettingModal', '', TRUE);
		$globalData = $this->globalSettingModal->GetGlobalSetting();
		
		if(empty ($globalData)) {
			$Message='No Global data found for invoice '.$estimateNumber;						
			$this->session->set_flashdata('SuccessMessage',$Message);

			redirect('/estimate/zt2016_estimageView/'.$estimateNumber);
			// echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }
		 foreach ($globalData as $row){
			$fromAddress=  $row->fromAddress;
			$contactName = $row->contactName;
			$mobNumber = $row->mobNumber;
			$email = $row->email;
			$bankAccount = $row->bankAccount;
			$taxinfo = $row->footer;
		 }
		 


		
		$this->load->dbutil();
					
		$StartDate=$estimateData->Estimagedate;
		$EndDate=$estimateData->ExpiryDate;
		
		$StartDate = date('Y-m-d', strtotime('+1 day'.$StartDate));
		$EndDate = date('Y-m-d', strtotime($EndDate));
				
		
		$this->load->dbutil();
		
	   	$currency = $clientdata->Currency;
	   	if($currency == "USD"){
	   	    $currencySymbol = '$';
	   	}else if($currency == 'EUR'){
	   	     $currencySymbol = '€';
	   	}

		$VATregistration = "";
		$VATFormatted = number_format("0",2,".",",");
		$noteHTML ="";
		$discount = $estimateData->discount;
		$estimateTotal = $estimateData->estimateTotal;
		$estimateTotal = $estimateTotal - $discount;
		$address = '';
		if($clientdata->Address){
			$address = "<br>".$clientdata->Address;
		}
		if($clientdata->Address2){
			$address.="<br>".$clientdata->Address2;
		}
		if($clientdata->Address3){
			$address.="<br>".$clientdata->Address3;
		}
		if($clientdata->Address4){
			$address.="<br>".$clientdata->Address4;
		}
		
		if($clientdata->VATOther){
		    $VATregistration = $clientdata->VATOther;
		}
		if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
			
			$vatrevenue = (float)str_replace(',', '', $estimateTotal);
			$VAT=$vatrevenue *21/100;
			$VATFormatted=number_format($VAT, 2, '.', ',');
			$estimateTotal = $estimateTotal + $VATFormatted;
			
		}
		else{
			$noteHTML = '<tr><br>
			<td class="" style="text-align:center;padding-top: 20px;"><strong><i>Customer to account for any VAT arising on this supply in accordance with Article 196, Council Directive 2006/112/EC</i></strong></td>
		</tr>';
		}

		if($VATregistration){
			$VAThtml ='<br><span style="font-size:10px;font-weight: bold;">VAT Registration: </span>'.$VATregistration;
		}
		

		$delimiter = ",";
		$newline = "\n";
		$path = base_url() . 'web/assets/usersprofile/u_dafault.png';
		$data ="";

        $estimateDate = strtotime(str_replace('/', '-', $estimateData->ExpiryDate));
		$formattedDate = date("d-F-Y", $estimateDate);
		$htmltable='<style>
		body{
			justify-content: center;
			display: flex;
			font-size:11px;
		  }
		  ul{
			list-style: none !important;
		  }
		  li{
			line-height: 3px;
		  }
		  .border_top,th.border_top,td.border_top{
			border-top: 1px solid rgb(193, 193, 193);
		  }
		  .border_bottom,th.border_bottom,td.border_bottom{
			border-bottom: 1px solid rgb(193, 193, 193);
		  }
		  .border_top_bottom,th.border_top_bottom,td.border_top_bottom{
			border-top: 1px solid rgb(193, 193, 193);
			border-bottom: 1px solid rgb(193, 193, 193);
		  }
		  .table_color{
			background: rgb(196, 188, 150);color: #fff;
			color: #fff;
			border:inherit !important;
		  }
		  .table_color th,.table_color td{
			background: rgb(196, 188, 150);color: #fff;
			color: #fff;
			border:inherit !important;
		  }
		  .text-center{
			text-align:center;
		  }
		</style>
		<div style="width: 95%;">';

		$htmltable .='<table class="table table-striped table-hover" style="width:100%;">
			<tr style="text-align: center;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: middle;text-align: left;color: rgb(217, 217, 217);font-size: 28px;"><b/r><h1 class="text-uppercase">QUOTATION</h1></td>
				<td style="width: 40%;"></td>
				<td style="max-width: 40%;text-align: right;">
				<img src="https://zowtrak.com/web/assets/usersprofile/invoice-logo.jpg" class="" alt="logo" />
				</td>
			</tr>
			
		</table>';
		$htmltable .='<br><table class="table table-striped table-hover" style="width:100%;margin-left: 0%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: top;width:  25%;"><h4 style="max-width: 91%;">'.$fromAddress.'</h4></td>
				<td style="width: 5%; vertical-align: top;">To</td>
				<td style="vertical-align: top;"><h4 style="max-width: 91%;">'.$clientdata->CompanyName.$address.'<br>'.$clientdata->Country.'</h4><p style="max-width: 100%;">'.$VAThtml.'</p></td>
				<td style="width: 20%;"></td>
				<td style="vertical-align: top;text-align: right;">
				<p><h4>Date:</h4></p>
				<p><p><span>'.$formattedDate.'</span></p></p>
				<br>
			</td>

				</td>
			</tr>
		</table>';
		$htmltable .='<table class="table table-striped table-hover" style="width:100%;margin-bottom: 49px;margin-top: 4%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: bottom;width:  25%;">
					<div class="col-sm-3  m-b-20" >
					<h4 style="max-width: 90%;">Contact: '.$contactName.'<br>
					'.$mobNumber.'<br>
					<small style="font-size: 11px;">'.$email.'</small></h4>
					</div>
				</td>
				<td style="width: 5%; vertical-align: bottom;"></td>
				<td style="vertical-align: bottom;width:40%;" >
					<div class="col-sm-4  m-b-20 ">
					<small style="max-width: 100%;"><span style="font-size:11px;font-weight: bold;">Attention:</span> <span style="font-size:11px;">'.$clientdata->ClientContact.'</span></small>
					</div>
				</td>
				
				<td style="width: 30%; vertical-align: bottom;text-align: right;" colspan=2>
				<p><h4>Quotation Number:</h4></p>
				<p><p>'.$estimateNumber.'</p></p>
				</td>
			</tr>
		</table>';
		$htmltable .='<table class="table table-striped table-hover "  style="width:99%;margin-left: 5%;" >
		<thead style="background: rgb(196, 188, 150);color: #fff;">
			<tr class="table_color" >
				<th style="width:12%">Date</th>
				<th class="d-none d-sm-table-cell">Originator</th>
				<th>File Name</th>
				<th style="width:10%">New Slides</th>
				<th style="width:10%">Edit to existing slides</th>
				<th class="text-end">Hours</th>
			</tr>
			</thead>
		<tbody>';
		
		foreach ($estimateentries as $row)
		
			{
				$entriesDate = strtotime(str_replace('/', '-', $row->date));
				$entriesformattedDate = date("d-M-Y", $entriesDate);
				$htmltable .='<tr>
							<td style="width:12%"><small style="font-size: 11px;">'.$entriesformattedDate.'</small></td>
							<td><small style="font-size: 11px;">'.$row->originator.'</small></td>
							<td class="d-none d-sm-table-cell"><small style="font-size: 11px;">'.$row->filename.'</small></td>
							<td style="width:10%;text-align:center;"><small style="font-size: 11px;">'.$row->newslides.'</small></td>
							<td style="width:10%; text-align:center;"><small style="font-size: 11px;">'.$row->editslides.'</small></td>
							<td class="text-end" style="text-align:center;"><small style="font-size: 11px;">'.$row->hour.'</small></td>
						</tr>
						';
			}
			
		$htmltable .='
					<tr>
						<th class="border_top" style="text-align:start;padding-top: 2px;padding-bottom:2px" colspan=3><strong>Subtotal (Slides)</strong></b></th>
						
						<td class="border_top" style="text-align:center;">'.$estimateData->SumNewSlides.'</td>
						<td class="border_top" style="text-align:center;">'.$estimateData->SumEditedSlides.'</td>
						<td class="text-end border_top" style="text-align:center;">'.round($estimateData->SumHours,2).'</td>
					</tr>
					<tr>
					<th class="border_top" style="vertical-align: bottom; text-align:start" colspan=3><strong>Subtotal (Hours)</strong></th>
					<td class="border_top" style="text-align:center;">5 slides/<br>hour</td>
					<td class="border_top" style="text-align:center;">10 slides/<br>hour</td>
					<td class="text-end border_top" ></td>
				</tr>
				<tr style="padding-top:20px;">
					<th class="border_bottom" colspan=3></th>
					<td class="border_bottom text-center">'.round($estimateData->SumNewSlides/5,2).'</td>
					<td class="border_bottom text-center">'.round($estimateData->SumEditedSlides/10,2).'</td>
					<td class="text-end border_bottom text-center">'.round($estimateData->SumHours,2).'</td>
				</tr>
				
				</tbody>
				
			</table>';
			$discountHtml = '';
			$htmltable .='<table class="table table-striped table-hover" style="width:99%;margin-left: 2%;" >
							<tbody>	
							<tr>
							<td class="" style="text-align: right;padding-right: 78px;"><strong><h4>Total Hours</h4></strong></td>
							<td class="text-end " style="text-align: right;"><h4  style="">'.number_format($estimateData->TotalHour,2).'</h4></td>
						</tr>';
			if($estimateData->Discount !=0){
				$discountHtml = '<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Discount</h4></strong></td>
				<td class="text-end " style="text-align: right;padding-top: 10px;"><h4  style="">-'.$currencySymbol.number_format($estimateData->Discount,2).'</h4></td>
			</tr>
				<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Subtotal (after discount) </h4></strong></td>
				<td class="text-end " style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format($estimateData->estimateTotal - $estimateData->Discount,2).'</h4></td>
			</tr>
				<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>VAT</h4></strong></td>
				<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format($VATFormatted,2).'</h4></td>
			</tr>
				';
			}
			else{
				$discountHtml = '<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>VAT</h4></strong></td>
				<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.$VATFormatted.'</h4></td>
			</tr>';
			}

				$htmltable.=	'<tr>
							<td class="" style="text-align: right;padding-right: 64px;padding-top: 10px;"><h4>Subtotal ('.$currencySymbol.$estimateData->PerHour .' per hour)</h4></td>
							<td class="text-end " style="text-align: right; padding-top: 10px;"><h4 style="display: block ruby;">'.$currencySymbol.number_format($estimateData->estimateTotal,2).'</h4></td>
						</tr>
						'.$discountHtml.'
						<tr>
							<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Total</h4></strong></td>
							<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format(round($estimateTotal,2),2).'</h4></td>
						</tr>
						</tbody>
						</table>';
						$htmltable.='<table class="table table-striped table-hover" style="width:100%;" >
						<tbody>'.$noteHTML.'
						<tr><td style="text-align:center;"></br></br><h4 class="text-center pe-5 pr-5"><br><br>'.$bankAccount.'</h4></td></tr>
						</tbody>
								
							</table>';
	
			
			//############### Table
			$htmltable .='</div>';
			##footer
			$footerHtml = '<div style="position: fixed; bottom: 0px; width: 100%; text-align: center;">
							<h4>' . $taxinfo . '</h4>
							</div>';
				$htmltable .= $footerHtml;
			// echo $htmltable;
			// die;
		
		$this->load->library('Pdfcontroller');
		$filename= 'ZOW-Quotation-'.$estimateNumber;
		$this->pdfcontroller->generate_pdf($htmltable,$filename);
		// $this->pdfcontroller->download_pdf($htmltable);
		
		// //echo $data;
		// $name = "ZOW_Invoice_".$invoicenumber.".csv";

		// $this->load->helper('download');
		// //"\xEF\xBB\xBF" sets downdload to utf encoding
		// //https://stackoverflow.com/questions/33592518/how-can-i-setting-utf-8-to-csv-file-in-php-codeigniter
		// force_download($name, "\xEF\xBB\xBF" . $data);

	}
	


	// ################## Get last invoice date ##################	
	function  _getDateLastInvoice($client)
	{

		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Invoice <>'NOT BILLED'");
		$query = $this->db->get();
		//echo $this->db->last_query();

		if ($query->row()->DateOut=="") {
			$this->db->select_min('DateOut');
			$this->db->from('zowtrakentries');
			$this->db->where("Client ='".$client."'"); 
			$query = $this->db->get();
			$StartDate = date('Y-m-d', strtotime('-1 day'.$query->row()->DateOut));
		}
		else
		{
		$StartDate =  $query->row()->DateOut;
		}
		return $StartDate;
	}


}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>