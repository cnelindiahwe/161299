<?php
$this->load->view('./admin_temp/header');


?>
<section class=" gradient-custom" >
  <div class="container ">
    <div class="row justify-content-center align-items-center ">
      <div class=" col-sm-12 ">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">
            <div class="row">
            <div class="col-sm-8">
              <h3 class="mb-4 pb-2 pb-md-0 mb-md-5"> Add New User </h3>

              </div>
              <div class="col-sm-4">
              <a href="javascript:void(0);" class="btn btn-info float-right cancle_back_hsd" style="float: right;">Cancel</a>

              </div>
            </div>
            <?php 

if($this->session->flashdata('SuccessMessage')){		
  $page_content.='<div class="alert alert-success" role="alert" style="">'."\n";
  $page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
  //$page_content.='  <span class="sr-only">Error:</span>'."\n";
  $page_content.=$this->session->flashdata('SuccessMessage');
  $page_content.='</div>'."\n";
}

######### Display error message

if($this->session->flashdata('ErrorsMessage')){		
  $page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
  $page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
  $page_content.='  <span class="sr-only">Error:</span>'."\n";
  $page_content.=$this->session->flashdata('ErrorsMessage');
  $page_content.='</div>'."\n";
}
echo $page_content;
?>
            <form action="" method="POST" enctype="multipart/form-data">

              <div class="row">
              <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="fname">First Name</label>
                    <input type="text" id="fname" name="fname" class="form-control form-control-lg"  placeholder="Enter Your First Name" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="fname">Last Name</label>
                    <input type="text" id="lname" name="lname" class="form-control form-control-lg"  placeholder="Enter Your Last Name" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="Email">Email ID</label>
                    <input type="email" id="Email" name="email" class="form-control form-control-lg"  placeholder="Enter Email Address" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Last"> Password </label>
                      <input type="text" id="Last"  name="pass" min="4" class="form-control form-control-lg"   placeholder="Enter New Password"  />
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Email">Select User Type</label>
                      <select class="form-control" name="user_type" >
                        <option>Select type</option>
                       
                        <option value="1" >Normal</option>
                       
                      
                        <option value="2" >Manager</option>
                       

                        </select>
                  </div>

                </div>
                 <div class="col-md-6 mb-4"  style="margin-top: 18px;">

                  <div class="form-outline">
                      <label class="form-label" for="visibility">Enable Visibility</label><br>
                      <input type="checkbox" id="visibility" name="visibility" value="1"  >
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                  <label for="formFileSm" class="form-label">Upload Profile</label>
                  <input class="form-control " id="add_user_pic" name="add_user_pic" type="file">
                  </div>

                </div>
              </div>
              
              <div class="mt-4 pt-2" style="margin-top: 38px;">
                <input class="btn btn-primary btn-lg " type="submit" value="Submit" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php

$this->load->view('./admin_temp/footer');

?>