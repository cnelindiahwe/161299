<?php

class zt2016_estimate extends MY_Controller {

	
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

        $this->load->model('zt2016_estimateModal', '', TRUE);
		$estimateData = $this->zt2016_estimateModal->getestimate();

        if(isset($_POST['search'])){
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];
            $estimateData = $this->zt2016_estimateModal->filterestimateentries($options = array('startDate'=>$startDate,'endDate'=>$endDate));
        }

        // print_r($estimateData);
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
				
		########## panel head
		 //Payment 
		$page_content.='      <!-- Page Wrapper -->
        <div class="panel panel-info">
        <!-- Page Header -->
                <div class="panel-heading">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Estimates</h3>
                            
                        </div>
                        <div class="col-auto float-end ms-auto">
                            <a href="zt2016_createEstimate" class="btn add-btn"><i class="fa fa-plus"></i> Create Estimate</a>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
        
            <!-- Page Content -->
            <div class="content container-fluid">
            
                
                
                <!-- Search Filter -->
                <form method="POST">
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3">  
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" name="startDate" type="text">
                            </div>
                            <label class="focus-label">From</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">  
                        <div class="form-group form-focus">
                            <div class="cal-icon">
                                <input class="form-control floating datetimepicker" name="endDate" type="text">
                            </div>
                            <label class="focus-label">To</label>
                        </div>
                    </div>
                   
                    <div class="col-sm-6 col-md-3">  
                        <button type="submit" class="btn btn-success w-100" name="search"> Search </button>  
                    </div>     
                </div>
                </form>
                <!-- /Search Filter -->';
                $page_content.='
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Estimate Number</th>
                                        <th>Client</th>
                                        <th>Estimate Date</th>
                                        <th>Expiry Date</th>
                                        <th>Amount</th>
                        
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                foreach($estimateData as $estimate){
                                
                                $page_content.='
                                    <tr>
                                        <td><a href="zt2016_estimateView/'.$estimate->quotationNumber.'">'.$estimate->quotationNumber.'</a></td>
                                        <td>'.$estimate->Client.'</td>
                                        <td>'.$estimate->Estimagedate.'</td>
                                        <td>'.$estimate->ExpiryDate.'</td>
                                        <td>'.$estimate->estimateTotal.'</td>
                                        
                                        <td class="text-end">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="'.base_url().'estimate/zt2016_edit_estimate/'.$estimate->quotationNumber.'"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_estimate"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>';
                                }

                                    $page_content.='
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Content -->
            
            <!-- Delete Estimate Modal -->
            <div class="modal custom-modal fade" id="delete_estimate" role="dialog">
                <div class="modal-dialog modal-dialog-centered" style="vertical-align: unset;">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Estimate</h3>
                                <p>Are you sure want to delete?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
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
            <!-- /Delete Estimate Modal -->
        
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