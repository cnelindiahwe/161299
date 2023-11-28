<?php

class zt2016_estimateView extends MY_Controller {

	
	function index()
	{
        
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		
        $estimateNumber=$this->uri->segment(3);

        $this->load->model('zt2016_estimateModal', '', TRUE);
		$estimateData = $this->zt2016_estimateModal->getestimate($options = array('quotationNumber'=>$estimateNumber));
		$estimateentries = $this->zt2016_estimateModal->getestimateentries($options = array('quotationNumber'=>$estimateNumber));

		$templateData['title'] = 'Estimage View';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($templateData['ZOWuser'],$estimateData,$estimateentries); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _display_page ($ZOWuser,$estimateData,$estimateentries)
	{
        $this->load->model('zt2016_clients_model', '', TRUE);
        $clientdata = $this->zt2016_clients_model->GetClient($options = array('CompanyName'=> $estimateData->Client));

        #get global setting data
		$this->load->model('globalSettingModal', '', TRUE);
		$globalData = $this->globalSettingModal->GetGlobalSetting();

        foreach ($globalData as $row){
			$fromAddress=  $row->fromAddress;
			$contactName = $row->contactName;
			$mobNumber = $row->mobNumber;
			$email = $row->email;
			$bankAccount = $row->bankAccount;
			$taxinfo = $row->footer;
		 }

		$page_content ='<div class="page_content">'."\n";

        $currency = $clientdata->Currency;
        if($currency == "USD"){
            $currencySymbol = '$';
        }else if($currency == 'EUR'){
             $currencySymbol = '€';
        }

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" >'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";

		}

        ######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}
				
		########## panel head
		 //Payment 
		$page_content.='         <!-- Page Wrapper -->
        <div class="panel panel-info">
         <!-- Page Header -->
                <div class="panel-heading">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Estimate</h3>
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-white">CSV</button>
                                <a href="'.site_url().'estimate/zt2016_pdf_existing_estimate/'.$estimateData->quotationNumber.'" class="btn btn-sm btn-primary pull-right" style="height: fit-content;"  target="_blank">PDF</a>
                            
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
            <!-- Page Content -->
            <div class="content container-fluid">
            
                
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6 m-b-20">
                                       
                                        <div>'.$fromAddress.'</div>
                                    </div>
                                    <div class="col-sm-6 m-b-20">
                                        <div class="invoice-details">
                                            <h3 class="text-uppercase">'.$estimateData->quotationNumber.'</h3>
                                            <ul class="list-unstyled">
                                                <li>Create Date: <span>'.$estimateData->Estimagedate.'</span></li>
                                                <li>Expiry date: <span>'.$estimateData->ExpiryDate.'</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12 m-b-20">
                                        <h5>Estimate to:</h5>
                                        
                                        <textarea rows="4" cols="50" disabled>'.$estimateData->ClientAddress.'</textarea>
                                        
                                    </div>
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ITEM</th>
                                        <th class="d-none d-sm-table-cell">DESCRIPTION</th>
                                            <th>Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    $index = 1;
                                    foreach($estimateentries as $entries){
                                    $page_content.='
                                        <tr>
                                            <td>'.$index.'</td>
                                            <td>'.$entries->date.'</td>
                                        <td class="">'.$entries->originator.'</td>
                                            <td>'.$entries->filename.'</td>
                                            <td>'.$entries->newslides.'</td>
                                            <td>'.$entries->editslides.'</td>
                                            <td>'.$entries->hour.'</td>
                                        </tr>
                                        ';
                                        $index++;
                                    }
                                    $VATFormatted = 0;
                                    $estimateTotal = $estimateData->estimateTotal;
                                    if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
			
                                        $vatrevenue = (float)str_replace(',', '', $estimateTotal);
                                        $VAT=$vatrevenue *21/100;
                                        $VATFormatted=number_format($VAT, 2, '.', ',');
                                        $estimateTotal = $estimateTotal + $VATFormatted;
                                        
                                    }
                                    $grandtotal = $estimateTotal - $estimateData->Discount;
                                        $page_content.='
                                    </tbody>
                                </table>
                                <div>
                                    <div class="row invoice-payment">
                                        <div class="col-sm-7">
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="m-b-20">
                                                <div class="table-responsive no-border">
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <th>Subtotal:</th>
                                                                <td class="text-end">'.$currencySymbol.$estimateData->estimateTotal.'</td>
                                                            </tr>
                                                            <tr>
                                                                <th>VAT:</th>
                                                                <td class="text-end">'.$currencySymbol.$VATFormatted.'</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total:</th>
                                                                <td class="text-end text-primary"><h5>'.$currencySymbol.$grandtotal.'</h5></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invoice-info">
                                        <h5>Other information</h5>
                                        <textarea class="text-muted w-100">'.$estimateData->otherInformation.'</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
            
        </div>
        <!-- /Page Wrapper -->


</div>
<!-- end main wrapper-->';


		return $page_content;
		
	}	

	/*<td>
                                        <h2 class="table-avatar">
                                            <a href="profile" class="avatar avatar-xs"><img src="<?php echo base_url();?>/assets/img/profiles/avatar-04.jpg" alt=""></a>
                                            <a href="profile">Loren Gatlin</a>
                                        </h2>
                                    </td>*/


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>