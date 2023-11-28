<html lang="en"><head>

        <meta charset="utf-8">
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Zowtrack Dashboard Panel" name="description">
        <meta content="Themesbrand" name="author">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

            	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>web/assets/img/favicon.png">
			
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/font-awesome.min.css">

	<!-- Lineawesome CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/line-awesome.min.css">

	<!-- Alertify CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/alertify/alertify.min.css">

	<!-- Lightbox CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/lightbox/glightbox.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/c3-chart/c3.min.css">

	<!-- Toatr CSS -->		
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins//toastr/toatr.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/select2.min.css">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/bootstrap-datetimepicker.min.css">

	<!-- Calendar CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/fullcalendar.min.css">

	<!-- Summernote CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/plugins/summernote/dist/summernote-bs4.css">

	<!-- Datatable CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/dataTables.bootstrap4.min.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="<?php echo base_url();?>web/assets/css/style.css">
</head>


    <!-- <body data-layout="horizontal"> -->
        <body class="account-page">
         <!-- Main Wrapper -->
        <div class="main-wrapper">
            <div class="account-content">
                <div class="container">
                
                    <!-- Account Logo -->
                    <div class="account-logo">
                        <a href="dashboard"><img src="<?php echo base_url();?>web/assets/img/logoh.png" alt="Zowtrak"></a>
                    </div>
                    <!-- /Account Logo -->
                    
                    <div class="account-box">
                        <div class="account-wrapper">
                            <h3 class="account-title">Login</h3>
                            <p class="account-subtitle">Access to our dashboard</p>
                            
                            <!-- Account Form -->
                            <form class="needs-validation custom-form mt-4 pt-2" novalidate="" action="<?php echo site_url('main/login');?>" method="POST">
                                <div class="form-group">
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
                                    <label for="useremail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="useremail" placeholder="Enter email" name="login_username" required="" value="">  
                                    <div class="invalid-feedback">
                                        Please Enter Email
                                    </div>     
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col">
                                            <label for="userpassword" class="form-label">Password</label>
                                        </div>
                                        <!-- <div class="col-auto">
                                            <a class="text-muted" href="auth-recoverpw">
                                                Forgot password?
                                            </a>
                                        </div> -->
                                    </div>
                                    <div class="position-relative">
                                      <input type="password" class="form-control" id="password" placeholder="Enter password" required="" name="login_password" value="">
                                        <div class="invalid-feedback">
                                            Please Enter Password
                                        </div>     
                                        <span class="fa fa-eye-slash" id="toggle-password"></span>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <input class="btn btn-primary account-btn" type="submit" name="login" value="Login"/>
                                </div>
                                <!-- <div class="account-footer">
                                    <p>Don't have an account yet? <a href="auth-register">Register</a></p>
                                </div> -->
                            </form>
                            <!-- /Account Form -->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Main Wrapper -->
        
        <!-- JAVASCRIPT -->
       		<!-- jQuery -->
               <script src="<?php echo base_url();?>web/assets/js/jquery-3.6.0.min.js"></script>
		
		<!-- Bootstrap Core JS -->
		<!-- Custom JS -->
		<script src="<?php echo base_url();?>web/assets/js/app.js"></script>
        </body></html>
