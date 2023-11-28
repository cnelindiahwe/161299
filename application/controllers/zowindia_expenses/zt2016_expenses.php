<?php

class zt2016_expenses extends MY_Controller {

	
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
		


		$templateData['title'] = 'Expenses';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _display_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}

    function _display_page ($ZOWuser)
	{

        $this->load->model('zowindia_expenses_model', '', TRUE);
		$expensesData = $this->zowindia_expenses_model->GetExpensesEntrie();
        if($this->session->flashdata('expensesData')){
            $expensesData = $this->session->flashdata('expensesData');
        }

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
				
		########## panel head
		 //Payment 
        $page_content.= ' <script>
        var BaseUrl = "'.base_url().'"
        </script>';
		$page_content.='  <!-- Page Wrapper -->
       
        <div class="panel panel-info">
        <!-- Page Header -->
        <div class="panel-heading">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Expenses</h3>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="#" class="btn add-btn btn-info" data-bs-toggle="modal" data-bs-target="#add_expense"><i class="fa fa-plus"></i> Add Expense</a>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
            
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Search Filter -->
            <form method="post" action="'.site_url().'expenses/zt2016_expensesdata">
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                    <div class="form-group form-focus">
                        <input type="search" class="form-control floating" id="customSearch">
                        <label class="focus-label">Item Name</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                    <div class="form-group custom-select">
                        <select class="select floating category"  name="Category"> 
                            <option>Category (All)</option>
                            <option>Operational</option>
                            <option>Salary</option>
                            <option>Admin</option>
                            <option>Others</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12"> 
                    <div class="form-group custom-select">
                        <select class="select floating paidby" name="paidBy"> 
                            <option>Paid By (All)</option>
                            <option> Wise </option>
                            <option> Paypal </option>
                            <option> Bank transfer </option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                    <div class="form-group form-focus">
                        <div class="cal-icon">
                            <input class="form-control floating datetimepicker" type="text" name="startDate">
                        </div>
                        <label class="focus-label">From</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                    <div class="form-group form-focus">
                        <div class="cal-icon">
                            <input class="form-control floating datetimepicker" type="text" name="endDate">
                        </div>
                        <label class="focus-label">To</label>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">  
                     
                    <input type="submit" name="filter" value="Search" class="btn btn-success w-100" />
                </div>     
            </div>
            </form>
            <!-- /Search Filter -->';
            $page_content.='<div id="table_loading_message">Loading ... </div>';
        	$page_content.='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="expenses_table1">'."\n";
            $page_content.="<thead>"."\n";
            $page_content.="<tr><th data-sortable=\"true\">Item</th><th data-sortable=\"true\">Reference</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Category</th><th data-sortable=\"true\">Amount</th><th data-sortable=\"true\">Paid By</th><th data-sortable=\"true\">Status</th><th data-sortable=\"true\">Payment Amount</th><th data-sortable=\"true\">Payment Date</th><th data-sortable=\"true\">Remark</th><th class=\"text-end\" data-sortable=\"true\">Action</th></tr>"."\n";
            $page_content.="</thead>"."\n";
            $page_content.="<tfoot>"."\n";
            $page_content.="<tr><th data-sortable=\"true\">Item</th><th data-sortable=\"true\">Reference</th><th data-sortable=\"true\">Date</th><th data-sortable=\"true\">Category</th><th data-sortable=\"true\">Amount</th><th data-sortable=\"true\">Paid By</th><th data-sortable=\"true\">Status</th><th data-sortable=\"true\">Payment Amount</th><th data-sortable=\"true\">Payment Date</th><th data-sortable=\"true\">Remark</th><th class=\"text-end\" data-sortable=\"true\">Action</th></tr>"."\n";
            $page_content.="</tfoot>"."\n";		
            $page_content.="<tbody>"."\n";

            foreach($expensesData as $expenses){
                $page_content.='
            
                    <tr data-item-name="'.$expenses->item.'"
                    data-reference="'.$expenses->Reference.'"
                    data-date="'.$expenses->purchaseDate.'"
                    data-category="'.$expenses->Category.'"
                    data-amount="'.$expenses->amount.'"
                    data-paidBy="'.$expenses->paidBy.'"
                    data-status="'.$expenses->status.'"
                    data-paymentAmount="'.$expenses->paymentAmount.'"
                    data-remark="'.$expenses->Remark.'"
                    data-attch="'.$expenses->attch.'"
                    data-currency="'.$expenses->currency.'"
                    data-url="'.base_url().'pdfs/expenses/"
                    data-id="'.$expenses->id.'"
                    data-paymentdate="'.$expenses->paymentDate.'">
                        <td>
                            <strong>'.$expenses->item.'</strong>
                        </td>
                        <td>'.$expenses->Reference.'</td>
                        <td>'.$expenses->purchaseDate.'</td>
                        <td>'.$expenses->Category.'</td>
                        
                        
                        <td>'.$expenses->amount.'</td>
                        <td>'.$expenses->paidBy.'</td>';
                        if($expenses->status == 'Pending'){
                            $class = 'text-danger';
                        }
                        else if($expenses->status == 'Approved'){
                            $class='text-info';
                        }
                        else{
                            $class='text-success';
                        }
                        $page_content.='
                        <td class="">
                            <div class="dropdown action-label">
                                <a class="btn btn-white btn-sm btn-rounded dropdown-toggle text-dark" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-dot-circle-o '.$class.'"></i> '.$expenses->status.'
                                </a>
                                <div class="dropdown-menu dropdown-menu-right zowindiaexpenses">
                                    <a class="dropdown-item" data-status="Pending" href="#"><i class="fa fa-dot-circle-o text-danger"></i> Pending</a>
                                    <a class="dropdown-item" href="#" data-status="Approved"><i class="fa fa-dot-circle-o text-info"></i> Approved</a>
                                    <a class="dropdown-item" href="#" data-status="Paid"><i class="fa fa-dot-circle-o text-success"></i> Paid </a>
                                </div>
                            </div>
                        </td>
                        <td>'.$expenses->paymentAmount.'</td>
                        <td>'.$expenses->paymentDate.'</td>
                        <td>'.$expenses->Remark.'</td>
                        <td class="text-end">
                            <div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edit_expense"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_expense"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
            ';
                }
            $page_content.='</tbody></table>';
                            $page_content.='
        </div>
        <!-- /Page Content -->
        
        <!-- Add Expense Modal -->
        <div id="add_expense" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="vertical-align: unset;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Expense</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="'.site_url().'zowindia_expenses/zt2016_expensesdata" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Item Name</label>
                                        <input class="form-control" type="text" name="item" required="true">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reference</label>
                                        <input class="form-control" type="text" name="Reference" required="true">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Purchase Date</label>
                                        <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="purchaseDate" required="true"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="paymentDate"></div>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <select class="select" name="currency" required="true">
                                            <option> USD </option>
                                            <option> EUR </option>
                                            <option> INR </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category </label>
                                    <select class="select" name="Category" required="true">
                                        <option>Category</option>
                                        <option>Operational</option>
                                        <option>Salary</option>
                                        <option>Admin</option>
                                        <option>Others</option>
                                    </select>
                                </div>
                            </div>
                                
                               
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Paid By</label>
                                        <select class="select" name="paidBy" required="true">
                                            <option>Paid By</option>
                                            <option> Wise </option>
                                            <option> Paypal </option>
                                            <option> Bank transfer </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="select" name="status" required="true">
                                            <option>Pending</option>
                                            <option>Paid</option>
                                            <option>Approved</option>
                                        </select>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input placeholder="$50" class="form-control" type="text" name="amount" required="true">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Amount</label>
                                        <input placeholder="$50" class="form-control" type="text" name="paymentAmount" required="true" value="0">
                                    </div>
                                </div>
                                
                             
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Attachments</label>
                                        <input class="form-control" id="attach" type="file" name="attach" required="true">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Remark</label>
                                        <input type="text" class="form-control" name="Remark" />
                                    </div>
                                </div>
                            </div>

                            <div class="attach-files">
                                <ul id="image-list">
                                    <li>
                                        No image selected.
                                    </li>
                                </ul>
                            </div>
                            <div class="submit-section">
                                <input type="submit" class="btn btn-primary submit-btn" name="submit"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Expense Modal -->
        
        <!-- Edit Expense Modal -->
        <div id="edit_expense" class="modal custom-modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="vertical-align: unset;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Expense</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form action="'.site_url().'zowindia_expenses/zt2016_expensesdata" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="" name="id"/>
                    <input type="hidden" value="" name="attch"/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input class="form-control" type="text" name="item" required="true">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference</label>
                                <input class="form-control" type="text" name="Reference" required="true">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="purchaseDate" required="true"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text" name="paymentDate"></div>
                            </div>
                        </div>
                  
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Currency</label>
                                <select class="select" name="currency" required="true">
                                    <option> USD </option>
                                    <option> EUR </option>
                                    <option> INR </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category </label>
                                <select class="select" name="Category" required="true">
                                    <option value="Category">Category</option>
                                    <option value="Operational">Operational</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                   
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paid By</label>
                                <select class="select" name="paidBy" required="true">
                                    <option>Paid By</option>
                                    <option> Wise </option>
                                    <option> Paypal </option>
                                    <option> Bank transfer </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="select" name="status" required="true">
                                    <option>Pending</option>
                                    <option>Approved</option>
                                    <option>Paid</option>
                                </select>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount</label>
                                <input placeholder="50" class="form-control" type="text" name="amount" required="true">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Amount</label>
                                <input placeholder="50" class="form-control" type="text" name="paymentAmount" value=0>
                            </div>
                        </div>
                     
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Attachments</label>
                                <input class="form-control" type="file" name="attach">
                            </div>
                        </div>
                           
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Remark</label>
                                <input type="text" class="form-control" name="Remark" />
                            </div>
                        </div>
                    </div>
                    <div class="attach-files">
                        <ul>
                            <li><a href="'.base_url().'web/assets/img/placeholder.jpg" download>
                                <img name="attach" src="'.base_url().'web/assets/img/placeholder.jpg" alt="">
                                <a>
                                <a href="#" class="fa fa-close file-remove"></a>
                            </li>
                        </ul>
                    </div>
                    <div class="submit-section">
                        <input type="submit" class="btn btn-primary submit-btn" name="update"/>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Edit Expense Modal -->

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
                                <form action="'.site_url().'zowindia_expenses/zt2016_expensesdata" method="POST">
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
        <!-- Delete Expense Modal -->
        
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