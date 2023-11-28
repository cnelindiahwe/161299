<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>ZOWTrak - <?php echo $pageName ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl."/"?>web/css/zowtrak.css" />

	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl."/"?>web/css/zowtraktrackingnew.css" />



	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl."/"?>web/css/ui-lightness/jquery-ui-1.8.2.custom.css" />

	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/"?>web/js/libs/jquery-1.6.2.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/"?>web/js/libs/jquery-ui-1.8.2.custom.min.js"></script>
	
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/plugins/jquery.metadata.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/plugins/jquery.tablesorter.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/plugins/jquery.validate.pack.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/zowtrakcommon.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/<?php echo $pageJavascript ?>.js"></script>
	<?php if ($pageType=="trackingnew" ) {?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/trackingnew_pastjobs.js"></script> 
	<?php }?>

	<?php if ($pageType=="trackingnew") {?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/libs/jquery-ui-1.8.16.customResize.min.js"></script>
	<?php }?>
	
	<script type="text/javascript">
		var BaseUrl="<?php echo base_url();?>";
	</script>

</head>

<body <?php echo "class=\"".$pageType."\"";?>>


	<?php if (isset($pageSidebar)) {?>
		<div id="pageSidebar">
			<?php echo $pageSidebar; ?>
		</div>
	<?php }?>
	
	<?php if (isset($pageOutput)) {?>
		<div id="pageOutput">
			<?php echo $pageOutput; ?>
		</div>
	<?php }?>
	
	<?php if (isset( $pageInput)) {?>
		<div id="pageInput">
			<?php echo $pageInput; ?>
		</div>
	<?php }?>


</body>
</html>
