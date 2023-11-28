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
              <h3 class="mb-4 pb-2 pb-md-0 mb-md-5"> Edit Profile  </h3>

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
if($this->session->flashdata('ErrorMessage')){		
  $page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
  $page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
  $page_content.='  <span class="sr-only">Error:</span>'."\n";
  $page_content.=$this->session->flashdata('ErrorMessage');
  $page_content.='</div>'."\n";
}
echo $page_content;
?>

            <form action="" method="POST" id="edit_user_form" enctype="multipart/form-data">

              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Email">First Name</label>
                      <input type="hidden"  name="user_id" value="<?php echo $user_id;?>" />
                      <input type="text" id="Email" name="fname" class="form-control form-control-lg" placeholder="Enter Your First Name" value="<?php echo $result->fname; ?>" />
                  </div>

                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label class="form-label" for="fname">Last Name</label>
                    <input type="text" id="lname" name="lname" class="form-control form-control-lg"  placeholder="Enter Your Last Name" value="<?php echo $result->lname; ?>" />
                  </div>
                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Email">Email ID</label>
                      <input type="email" id="Email" name="email" class="form-control form-control-lg" value="<?php echo $result->user_email; ?>" />
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Email">Password</label>
                      <input type="text" id="pass" name="pass" min="4" class="form-control form-control-lg" value="" placeholder="Enter New Password">
                  </div>

                </div>
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Email">Select User Type</label>
                      <select class="form-control" name="user_type" >
                        <option>Select type</option>
                       
                        <option value="1" <?php if($result->user_type == 1 ){echo 'selected="selected"';}?> >Normal</option>
                       
                      
                        <option value="2" <?php if($result->user_type == 2 ){echo 'selected="selected"';}?> >Manager</option>
                       

                        </select>
                  </div>

                </div>
                
                <!-- <div class="col-md-6 mb-4 ">

                  <div class="form-outline">
                      <label class="form-label" for="Email">Confirm Password</label>
                      <input type="text" id="con_pass"  class="form-control form-control-lg"  placeholder="Enter Confirm Password" />
                  </div>

                </div> -->
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                      <label class="form-label" for="Last">Last Login</label>
                      <input type="text" id="Last" class="form-control form-control-lg" value="<?php echo  $result->user_last_login;?>" readonly />
                  </div>

                </div>
                
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label label for="old_user_pic" class="form-label">Upload Profile</label>
                    <input class="form-control " id="add_user_pic" name="add_user_pic" type="file">
                  </div>
                </div>
                <div class="col-md-6 mb-4"  style="margin-top: 18px;">

                  <div class="form-outline">
                      <label class="form-label" for="visibility">Enable Visibility</label><br>
                      <input type="checkbox" id="visibility" name="visibility" value="1" <?php if($result->visibility == 1 ){echo 'checked';}?> >
                  </div>

                </div>
              </div>
              <?php
                if($result->dp !=''){

                  if($result->dp == ''){
                    $path = base_url().'web/assets/usersprofile/u_dafault.png';
                  }else{
                    $path = base_url().'web/assets/usersprofile/'.$result->dp;
            
                  }
                  ?>
                <div class="col-md-6 mb-4">
                  <div class="form-outline">
                    <label label for="old_user_pic" class="form-label">View Profile</label><br>
                    <input class="form-hidden " id="old_user_pic" name="old_user_pic" type="hidden" value="<?php echo  str_replace('.','_!r!_',$result->dp);?>">
                    <a href="<?php echo $path;?>" target="_blank"><img src="<?php echo $path;?>" style="max-width:150px;"></a>
                  </div>
                </div>
                  <?php
                }
                ?>
              <div class="mt-4 pt-2" style="margin-top: 38px;" >
                <input class="btn btn-primary btn-lg submit_hsd"  type="button" value="Submit" />
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