<?php

class zt2016_retainers extends MY_Controller {

	public function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));

		$zowuser=_superuseronly(); 

		$this->load->model('zt2016_invoices_model','','TRUE');

	
	
	
		$templateData['title'] = 'Retainers';	

        $this->load->model('zt2016_retainersmodal', '', TRUE);
        #get client data
        $this->load->model('zt2016_clients_model', '', TRUE);
        $clientdata = $this->zt2016_clients_model->GetClient($options = array('Group'=>'retainers'));
		
        $templateData['Fastflag']='';

		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_page($templateData,$clientdata); 
		$templateData['ZOWuser']=_getCurrentUser();
        
		
		$this->load->view('admin_temp/main_temp',$templateData); 

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contact lists content
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_contact_lists_content
	 * Gather content
	 */
	function _display_page($templateData,$clientdata) {
		

		$page_content ='<div class="page_content">';

		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}		
		
		$page_content .=		$this->_get_existing_invoices_table($templateData,$clientdata)."\n";
        $page_content.='<!-- Edit Expense Modal -->
        <div id="edit_retainers" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="vertical-align: unset;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Retainers</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form  action="'.base_url().'retainers/zt2016_retainersdata" method="POST">
                    <input type="hidden" value="" name="id"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client Name</label>
                                <input class="form-control" type="text" name="client" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Retainers Hours</label>
                                <input class="form-control" type="text" name="retainerhours" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="startDate"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="endDate"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Note </label>
                                <textarea class="form-control" name="note"></textarea>
                            </div>
                        </div>
                   
                        
                    </div>
                    
                
                    <div class="submit-section">
                        <input type="submit" class="btn btn-primary submit-btn" name="submit"/>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Edit Expense Modal -->';

        $page_content.='<!-- create Report Modal -->
        <div id="report_retainers" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="vertical-align: unset;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Report</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form  action="'.base_url().'retainers/zt2016_retainersdata" method="POST">
                    <input class="form-control" type="hidden" name="client">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="startDate"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="endDate"></div>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <input type="submit" class="btn btn-primary submit-btn" name="report" value="Create Report"/>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /create report Modal -->';
        $page_content.='
        <!-- Delete Expense Modal -->
        <div class="modal custom-modal fade" id="delete_expense" role="dialog">
            <div class="modal-dialog modal-dialog-centered" style="vertical-align: unset;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-header">
                            <h3>Delete Expense</h3>
                            <p>Are you sure want to delete?</p>
                        </div>
                        <div class="modal-btn delete-action">
                            <div class="row text-center">
                                <div class="col-6">
                                <form action="'.site_url().'expenses/zt2016_expensesdata" method="POST">
                                <input type="hidden" value="" name="id"/>
                                    <input type="submit" name="delete" class="btn btn-primary continue-btn" value="Delete">
                                    </form>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Expense Modal -->';
		$page_content .='</div>'."\n";
		return 	$page_content;		
		}





	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get existing invoice table
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_existing_invoices_table
	 * build existing (past) invoices table
	 */
    function _get_existing_invoices_table($templateData,$clientdata) {
				
		
        $InvoiceTableRaw =$this->zt2016_invoices_model->GetInvoice($options = array('Trash'=>'0','sortBy'=>'BilledDate','sortDirection'=>'DESC'));
        $page_content='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="retainers-table">'."\n";
        $page_content.="<thead>"."\n";
        $page_content.="<tr><th data-sortable=\"true\">Client name</th><th data-sortable=\"true\">Retainers Hours</th><th data-sortable=\"true\">Price</th><th data-sortable=\"true\">Start Date</th><th data-sortable=\"true\">End Date</th><th data-sortable=\"true\">Hours Utilized</th><th data-sortable=\"true\">Available Hours</th><th data-sortable=\"true\">Note</th><th class=\"text-right\" data-sortable=\"true\">Action</th></tr>"."\n";
        $page_content.="</thead>"."\n";
        // $page_content.="<tfoot>"."\n";
        // $page_content.="<tr><th data-sortable=\"true\">Client name</th><th data-sortable=\"true\">Retainers Hours</th><th data-sortable=\"true\">Price</th><th data-sortable=\"true\">Start Date</th><th data-sortable=\"true\">End Date</th><th data-sortable=\"true\">Hours Utilized</th><th data-sortable=\"true\">Available Hours</th><th data-sortable=\"true\">Note</th><th data-sortable=\"true\">Action</th></tr>"."\n";
        // $page_content.="</tfoot>"."\n";		
        $page_content.="<tbody>"."\n";
        
    
        
        if ($templateData['Fastflag']==''){
        
            foreach ($clientdata as $client){
               
                $price = number_format($client->BasePrice * $client->RetainerHours,2);
                $data = $this->zt2016_retainersmodal->getretainersdate($options = array('client'=>$client->CompanyName));
                
                $id = '';
                $startDate = '';
                $endDate = '';
                $note = '';
                $totalinvoice = '';
                $remininghours = '';
                
                if($data){
                    $id = $data->id;
                    $startDate = $data->startDate;
                    $endDate = $data->endDate;
                    $note = $data->note;
                }
                if($startDate != '' && $endDate != ''){
                    $timestamp = strtotime(str_replace('/', '-', $startDate));
                    $start = date("Y-m-d", $timestamp);
                    $timestamp1 = strtotime(str_replace('/', '-', $endDate));
                    $end = date("Y-m-d", $timestamp1);
    
                    $invoice = $this->zt2016_retainersmodal->getinvoicehours($options = array('client'=>$client->CompanyName,'startDate'=>$start,'endDate'=>$end));
                    if($invoice){
                        $RetainerHours = $client->RetainerHours;
                        $totalinvoice = round($invoice,2);
                        $remininghours = $RetainerHours - $totalinvoice;
                    }
                }
                //$page_content.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
    
                $page_content.= '  <tr 
                                        data-client="'.$client->CompanyName.'"
                                        data-retainerHours="'.$client->RetainerHours.'" 
                                        data-id="'.$id.'"
                                        data-startdate="'.$startDate.'"
                                        data-enddate="'.$endDate.'"
                                        data-note="'.$note.'"
                                    >';
                $page_content.= '<td>    <strong>'.$client->CompanyName.'</strong></td>'."\n";
    
              
                $page_content.= '<td>'.$client->RetainerHours.'</td>'."\n";
                $page_content.= '<td>'.$price.'</td>'."\n";
    
        
                $page_content.= '<td> 
                                        '.$startDate.'  
                                        </td>';
                $page_content.= '<td>
                                          '.$endDate.'  
                                        </td>'."\n";
                
    
                $page_content.= '<td class="text-center">'.$totalinvoice.'</td><td class="text-center">
                                           '.$remininghours.'
                                        </td>
                                        <td>'.$note.'</td>
                                        <td class="text-right"><div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_retainers"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_expense"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#report_retainers"><i class="fa fa-book m-r-5"></i> Create Report</a>
                                        </div>
                                    </div></td>
                                        </tr>'."\n";
            }
        }
        
        $page_content.="</tbody>"."\n";
        $page_content.="</table>"."\n";
        $page_content.= ' <script>
        var BaseUrl = "'.base_url().'"
        </script>';
    
        $page_header='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
        $page_header.='<h3 class="panel-title">Retainers</h3>';
        $page_header.="</div><!--panel-heading-->\n";
        $page_header.='<div class="panel-body">'."\n";
        $page_header.='<div id="table_loading_message">Loading ... </div>'."\n";
        $page_header.='<div class="date-filter"></div>';
        $page_header.='<div class="filter-row"></div>';
    
        $page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";
    
        
        return 	$page_content;
    }


	


}

/* End of file zt2016_trash.php */
/* Location: ./system/application/controllers/trash/zt2016_trash.php */
?>