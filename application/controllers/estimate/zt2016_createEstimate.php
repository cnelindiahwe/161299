<?php

class zt2016_createEstimate extends MY_Controller {

	
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
		


		$templateData['title'] = 'CreateEstimate';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _display_page ($ZOWuser)
	{

        $this->load->model('zt2016_expenses_model', '', TRUE);
		$expensesData = $this->zt2016_expenses_model->GetExpensesEntrie();

        #get client data
        $this->load->model('zt2016_clients_model', '', TRUE);
        $clientdata = $this->zt2016_clients_model->GetClient();
        // print_r($clientdata);
        // die;
		$page_content ='<div class="page_content">'."\n";

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

        $quotationNumber = $this->getquotationnumber();
       
				
		########## panel head
		 //Payment 
		$page_content.='          <!-- Page Wrapper -->
        <div class="panel panel-info">
        <!-- Page Header -->
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="page-title">Create Estimate <span class="quotationnum d-none">'.$quotationNumber.'</span></h3>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
            <!-- Page Content -->
            <div class="content container-fluid">
            
                
                
                <div class="row">
                    <div class="col-sm-12">
                        <form method="POST" action="'.base_url().'estimate/zt2016_estimateDataSave" id="estimate-form">
                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Client <span class="text-danger">*</span></label>
                                        <select class="select" id="clientSelect" style="width:100%" name="Client">';
                                        foreach($clientdata as $client){
                                            $page_content.='
                                            <option 
                                            data-country="'.$client->Country.'"
                                            data-address="'.$client->Address.'"
                                            data-priceperhour="'.$client->BasePrice.'"
                                            data-currency="'.$client->Currency.'"
                                            data-currency="'.$client->Currency.'"
                                            data-code="'.$client->ClientCode.'"
                                            data-email="'.$client->ClientContact.'"

                                            >'.$client->CompanyName.'</option>';
                                        }
                                            $page_content.='
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Project <span class="text-danger">*</span></label>
                                        <select class="select" name="Project">
                                            <option>Select Project</option>
                                            <option selected>Office Management</option>
                                            <option>Project Management</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control email" type="text" name="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Tax</label>
                                        <select class="select" name="tax">
                                            <option>Select Tax</option>
                                            <option>VAT</option>
                                            <option>GST</option>
                                            <option>No Tax</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Client Address</label>
                                        <textarea class="form-control" rows="3" id="client-address" name="ClientAddress"></textarea>
                                    </div>
                                </div>
                                 
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Estimate Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="Estimagedate">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Expiry Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="ExpiryDate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="table-responsive">
                                        <table id="addTable" class="table table-hover table-white" border="1">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20px">#</th>
                                                    <th class="col-sm-2">Date</th>
                                                    <th class="col-md-6" style="width:100px;">Originator</th>
                                                    <th>File Name</th>
                                                    <th style="width:100px;">New Slides</th>
                                                    <th style="width:100px;">Edit Slides</th>
                                                    <th style="width:100px;">Hours</th>
                                                    <th></th>
                                            
                                                </tr>
                                            </thead>
                                            <tbody class="tbodyone">
                                            <tr>
                                                <td class="row-number">1</td>
                                                <td>
                                                    <div class="cal-icon">
                                                        <input class="form-control datetimepicker" type="text" name="date[]" required>
                                                    </div>
                                                    
                                                </td>
                                                <td>
                                                    <select class="form-control originator" type="text" name="originator[]" required></select>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="filename[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control newslides text-right" type="text" name="newslides[]" value=0 required>
                                                </td>
                                                <td>
                                                    <input class="form-control editslides text-right" type="text" name="editslides[]" value=0 required>
                                                </td>
                                                <td>
                                                    <input class="form-control hours text-right" type="text" name="hour[]" value=0 required>
                                                </td>
                                               
                                                <td style="width:3%"><a href="javascript:void(0)" class="text-success font-18" title="Add" id="addfeild"><i class="fa fa-plus"></i></a></td>
                                            </tr>
                                            <tr>
                                                <td class="row-number">2</td>
                                                <td>
                                                    <div class="cal-icon">
                                                    <input class="form-control datetimepicker" type="text" name="date[]" required>
                                                    </div>
                                               
                                                </td>
                                                <td>
                                                    <select class="form-control originator" type="text" name="originator[]" required></select>
                                                </td>
                                                <td>
                                                    <input class="form-control" type="text" name="filename[]" required>
                                                </td>
                                                <td>
                                                    <input class="form-control newslides text-right" type="text" name="newslides[]" value=0 required>
                                                </td>
                                                <td>
                                                    <input class="form-control editslides text-right" type="text" name="editslides[]" value=0 required>
                                                </td>
                                                <td >
                                                    <input class="form-control hours text-right" type="text" name="hour[]" value=0 required>
                                                </td>
                                                
                                                <td style="width:3%"><a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a></td>
                                            </tr>
                                            </tbody>
                                            </table>
                                            <table id="" class="table table-hover table-white">
                                           
                                            <tbody>
                                            <tr>
                                          
                                            <td colspan=4>Subtotal (Slides)</td>
                                            <td style="text-align: right;width:100px;"><div class="newslidestotal">0</div><input type="hidden" name="SumNewSlides" value=0></td>
                                            <td style="text-align: right;width:100px;"><div class="editslidestotal">0</div><input type="hidden" name="SumEditedSlides" value=0></td>
                                            <td style="text-align: right;width:100px;"><div class="hourstotal">0</div><input type="hidden" name="SumHours" value=0></td>
                                            <td style="width:3%"></td>
                                            </tr>
                                            <tr>
                                          
                                            <td colspan=4>Subtotal (Hours)</td>
                                            <td style="text-align: right;"><div class="newslidehours">0</div></td>
                                            <td style="text-align: right;"><div class="editslideshours">0</div></td>
                                            <td style="text-align: right;"><div class="hourssumtotal">0</div></td>
                                            <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="rowcount" value=2 class="rowcount">
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-white">
                                            <tbody class="tbodytwo">
                                    
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-end">Total Hours</td>
                                                    <td style="text-align: right; padding-right: 30px;width: 230px"><input class="form-control text-end" readonly type="text" id="totalHours" name="TotalHour" value=0></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-end">Subtotal <span class="preHour">(€84 per hour)</span></td>
                                                    <td style="text-align: right; padding-right: 30px;width: 230px">
                                                        <input class="form-control text-end perhourvalue" value="0" readonly type="text" name="PerHourvalue">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-end">Tax</td>
                                                    <td style="text-align: right; padding-right: 30px;width: 230px">
                                                        <input class="form-control text-end tax" value="0" readonly type="text">
                                                        <input  class="form-control perhourinput" type="hidden" value=0 name="PerHour">
                                                        <input  class="form-control country" type="hidden" name="country">
                                                        <input  class="form-control quotationNumber" type="hidden" name="quotationNumber">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="text-end">
                                                        Discount 
                                                    </td>
                                                    <td style="text-align: right; padding-right: 30px;width: 230px">
                                                        <input class="form-control text-end estimate-discount" type="text" name="Discount" value=0>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" style="text-align: right; font-weight: bold">
                                                        Grand Total
                                                    </td>
                                                    <td style="text-align: right; padding-right: 30px; font-weight: bold; font-size: 16px;width: 230px">
                                                        <span class="currencySymbol">$</span><span class="grnadTotal">0.00</span>
                                                        <input type="hidden" name="grandTotal" class="grandTotalinput">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>                               
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Other Information</label>
                                                <textarea class="form-control" rows="4" name="otherInformation"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn m-r-10">Save & Send</button>
                                <button class="btn btn-primary submit-btn" type="submit" name="submit">Save</button>
                            </div>
                        </form>
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

function getquotationnumber(){
    $finished = false;
    $quotationCount = 100;
    
    while (!$finished)
    { 
        $quotationCount+=1;
        $formattedResult = str_pad($quotationCount, 4, '0', STR_PAD_LEFT);
        $Year = date("y");
        
        $quotationnumber = 'QTN-'.$Year.'-'.$formattedResult;

        $this->db->select('quotationNumber');
        $this->db->from('zowestimate');
        //$this->db->limit(1);
        $this->db->where("quotationNumber ='".$quotationnumber ."'"); 
        $query = $this->db->get();
        if ($query->num_rows()==0) { $finished = true;   }
    }
    $InvoiceNumberhwe=$quotationnumber;
    

    return $InvoiceNumberhwe;
}

}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>