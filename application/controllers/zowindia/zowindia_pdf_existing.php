<?php
use Mpdf\Mpdf;
class Zowindia_pdf_existing extends MY_Controller {


	
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
		$invoicenumber= $this->uri->segment(3);

        $this->load->model('zowindiainvoice', '', TRUE);
        $invoicedata = $this->zowindiainvoice->getinvoice($options = array('invoiceNumber' => $invoicenumber));
		
		#get global setting data
		$this->load->model('globalSettingModal', '', TRUE);
		$globalData = $this->globalSettingModal->GetGlobalSetting();
		
		if(empty ($globalData)) {
			$Message='No Global data found for invoice '.$invoicenumber;						
			$this->session->set_flashdata('SuccessMessage',$Message);

			redirect('/invoicing/zt2016_view_invoice/'.$invoicenumber);
			// echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
			exit();
		 }
		 foreach ($globalData as $row){
			$fromAddress=  $row->zowIndiafromAddress;
			$toAddress=  $row->toAddress;
			$contactName = $row->contactName;
			$mobNumber = $row->zowIndiaMobile;
			$email = $row->email;
			$bankAccount = $row->zowIndiaBank;
			$taxinfo = $row->zowIndiafooter;
		 }
				
		
		$this->load->dbutil();

		$currency = $invoicedata->currency;
		if($currency == "USD"){
			$currencySymbol = '$';
		}else if($currency == 'EUR'){
			 $currencySymbol = '€';
		}
		else if($currency == 'INR'){
			 $currencySymbol = '₹';
		}
		
	    $VAThtml = '';
	    $invoiceDate = strtotime($invoicedata->date);
		$formattedDate = date("d-F-Y", $invoiceDate);
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
          .border_left{
            border-left: 1px solid rgb(193, 193, 193);
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
				<td style="vertical-align: middle;text-align: left;color: rgb(217, 217, 217);font-size: 28px;"><b/r><h1 class="text-uppercase">Invoice</h1></td>
				<td style="width: 40%;"></td>
				<td style="max-width: 40%;text-align: right;">
				<img src="https://zowtrak.com/web/assets/usersprofile/invoice-logo.jpg" class="" alt="logo" />
				</td>
			</tr>
			
		</table>';
		$htmltable .='<br><table class="table table-striped table-hover" style="width:100%;margin-left: 0%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: top;width:  30%;"><h4 style="max-width: 91%;">'.nl2br($fromAddress).'<br>Phone '.$mobNumber.'</h4></td>
				<td style="width: 5%; vertical-align: top;"></td>
				<td style="vertical-align: top;"></td>
				<td style="width: 20%;"></td>
				<td style="vertical-align: top;text-align: right;">
				<p><h4>Date:</h4></p>
				<p><p><span>'.$formattedDate.'</span></p></p>
				<br>
			<p style="margin-top: 35px !important;"></p>
			</td>

				</td>
			</tr>
		</table>';
        $stringWithBrTags = nl2br($toAddress);
		$htmltable .='<table class="table table-striped table-hover" style="width:100%;margin-bottom: 49px;margin-top: 4%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: bottom;width:  25%;">
					<div class="col-sm-3  m-b-20" >
                    <p>TO:</p><br>
					<h4 style="max-width: 90%;">'.$stringWithBrTags.'<br>
	
					
					</div>
				</td>
				<td style="width: 5%; vertical-align: bottom;"></td>
				<td style="vertical-align: bottom;width:40%;" >
					<div class="col-sm-4  m-b-20 ">
					<small style="max-width: 100%;"><span style="font-size:11px;font-weight: bold;">FOR:</span> <span style="font-size:11px;">'.$invoicedata->description.'</span></small>
					</div>
				</td>
				
				<td style="width: 30%; vertical-align: bottom;text-align: right;" colspan=2>
				<p><h4>Invoice Number:</h4></p>
				<p><p>'.$invoicenumber.'</p></p>
				</td>
			</tr>
		</table>';
		$htmltable .='<table class="table table-striped table-hover"  style="width:99%;margin-left: 5%;max-height: 500px;" >
		<thead style="background: rgb(196, 188, 150);color: #fff;">
			<tr class="table_color" >
				<th style="width:60%">DESCRIPTION</th>
				<th class="d-none d-sm-table-cell" style="width:10%">HOURS</th>
				<th style="width:10%">RATE</th>
				<th style="width:10%">AMOUNT</th>
			</tr>
			</thead>
		<tbody>';
		
                $row = $invoicedata;
				$htmltable .='<tr>
							<td style="width:12%"><small style="font-size: 11px;">'.$row->description.'</small></td>
							<td class="text-center"><small style="font-size: 11px;">'.$row->hour.'</small></td>
							<td class="d-none d-sm-table-cell text-center"><small style="font-size: 11px;">'.$currencySymbol.$row->rate.'</small></td>
							<td style="width:10%;text-align:center;"><small style="font-size: 11px;">'.$currencySymbol.$row->amount.'</small></td>
						</tr>
						';

                        for ($i=0; $i < 25; $i++) { 
                            $htmltable .='<tr>
							<td style="width:12%"><small style="font-size: 11px;"></small></td>
							<td><small style="font-size: 11px;"></small></td>
							<td class="d-none d-sm-table-cell"><small style="font-size: 11px;"></small></td>
							<td style="width:10%;text-align:center;"><small style="font-size: 11px;"></small></td>
						</tr>
						';
                        }
			
			
		$htmltable .='
					<tr>
						<th class="border_top" style="text-align:right;padding-top: 2px;padding-bottom:2px" colspan=3><strong>Total</strong></b></th>
						
						<td class="border_top" style="text-align:center;">'.$currencySymbol.$invoicedata->amount.'</td>
			
					</tr>
				
				</tbody>
				
			</table>';

            $htmltable.='<table class="table table-striped table-hover" style="width:99%;margin-left: 5%" >
            <tbody>'.$noteHTML.'
            <tr><td style="text-align:start;"></br></br><h4 class="text-center pe-5 pr-5"><br><br>'.nl2br($bankAccount).'</h4></td></tr>
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
		$filename= 'ZOW-Invoice-'.$invoicenumber;
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