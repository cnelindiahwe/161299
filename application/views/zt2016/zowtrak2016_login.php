<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Login</title>

	<link rel="icon" href="favicon.ico" />

    <!-- Bootstrap core CSS -->
    <link href=" <?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	  
    <!-- Bootstrap theme -->
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

</head>
<body class="welcome" style="background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#cbccc8)) fixed;">
		<?php if(!$this->session->userdata('logged_in')) {
			echo '<div id="loginbox">'."\n";
				
				### login form
				echo '<form  style="margin:0 auto; width:17em; padding-top:15%; text-align:center;"  method="post" action="' . site_url('main/login') . '">'."\n";
	
					# user
					echo '<div class="form-group">'."\n";
					echo '<label for="login_username">Who</label>'."\n";
					//echo '<input type="text" class="form-control" id="login_username">'."\n";
					echo '<input type="text" id="login_username" name="login_username" value="" class="text form-control" />'."\n";				
					echo '</div>'."\n";

					# password	
					echo '<div class="form-group">'."\n";
					echo '<label for="login_password">Which</label>'."\n";
					echo '<input type="password" id="login_password" name="login_password" value="" class="text form-control" />'."\n";
					echo '</div>'."\n";	

					echo '<button type="submit" class="btn btn-primary">Sign in</button>'."\n";

			echo '</form>'."\n";
			echo '</div>';
		} else {
			echo '<div id="logut">';
				echo '<a href="' . site_url('/example/logout/') . '">Click here to logout.</a>';
			echo '</div>';
			
		}
		?>
</body>

</html> 