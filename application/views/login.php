<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Zebra on Wheels</title>
	<?php //Sets base path;?>
	<base href="<?php echo $this->config->item('base_url') ?>web/" />
	<link rel="icon" href="favicon.ico" />
		<script type="text/javascript" src="<?php echo site_url(); ?>web/js/libs/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="<?php echo site_url(); ?>web/js/detect_timezone.js"></script>
		<script type="text/javascript" src="<?php echo site_url(); ?>web/js/login.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>web/css/zowtrak2012pages.css" />
	<style type="text/css">
	html {
		font-family: Arial,  Helvetica,sans-serif;
		background-color:#222;
		color:#ddd;
		width:100%;
		text-align:center;
		}

	</style>

</head>
<body class="welcome">
		<?php if(!$this->session->userdata('logged_in')) {
			echo '<div id="loginbox">'."\n";
				echo '<form action="' . site_url('main/login') . '" method="post">'."\n";
					echo '<fieldset>'."\n";
					echo '<label for="login_username">who:</label>'."\n";
					echo '<input type="text" id="login_username" name="login_username" value="" class="text" />'."\n";
					echo '<br/>'."\n";
					echo '<label for="login_password">which:</label>';
					echo '<input type="password" id="login_password" name="login_password" value="" class="text" />'."\n";
					echo '<br/>'."\n";
					echo '<input type="submit"  id="login" name="login" value="Ready" />'."\n";
					echo '</fieldset>'."\n";
				echo '</form>'."\n";
			echo '</div>';
			
			
			echo '<a href="' . site_url('/main/login/') . '">Click here to logout.</a>';
		} else {
			echo '<div id="logut">';
				echo '<a href="' . site_url('/example/logout/') . '">Click here to logout.</a>';
			echo '</div>';
			
		}
		?>
</body>

</html> 