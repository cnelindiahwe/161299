
<?php
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>ZOWTrak - <?php echo $pageName ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php //if ($pageType=="tracking" || strpos($pageType,"tracking")) {?>
		<meta http-equiv="refresh" content="300" />
	<?php //}?>
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/reset.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/zowtrak.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/zowtrak2012pages.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/zowtrak2012general.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/zowtrak2012ui.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/zowtrak2012pages.css" />
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/ui-lightness/jquery-ui-1.8.2.custom.css" />
	<?php //if ($pageType=="clientreport" || $pageType=="reports" || $pageType=="fin_totals" || $pageType=="fin_trends" ) {?>
	<?php if ($pageType=="clientreport" || $pageType=="reports"  ) {?>
	<link rel='stylesheet' type='text/css' media='all' href="<?php echo $baseurl?>web/css/visualize.css" />
	<?php }?>

	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl?>web/js/libs/jquery-1.7.1.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/plugins/jquery.metadata.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/plugins/jquery.tablesorter.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/plugins/jquery.validate.pack.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/zowtrakcommon.js"></script>
	
	<?php $staffpages =array("trackingnew","trash","reports","clientreport","staffreport","historicaldata","limbo");
	 if (in_array($pageType, $staffpages))  {?>
		<?php if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") { ?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl?>web/js/managermenu.js"></script>
		<?php }?>
</script> 

	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/libs/jquery-ui-1.8.16.customResize.min.js"></script>
	<?php }?>
	<?php //if ($pageType=="clientreport" || $pageType=="reports" || $pageType=="fin_totals" || $pageType=="fin_trends") {?>
	<?php if ($pageType=="clientreport" || $pageType=="reports" ) {?>

	<!--<script type="text/javascript" src="http://filamentgroup.github.com/EnhanceJS/enhance.js"></script>-->		
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/libs/reports.visualize.jQuery.js"></script>
	<?php }?>
	<?php if ($pageType=="fin_trends" || $pageType=="fin_totals" || $pageType=="fin_breakdown" || $pageType=="staffreport" || $pageType=="historicaldata") {?>
	<script type="text/javascript" src="<?php echo $baseurl ?>web/js/libs/d3.v3.min.js"></script>		
	<?php }?>	
	
	<?php if ($pageType=="trackingnew") {?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl."/" ?>web/js/libs/jquery-ui-1.8.16.custom.all.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/plugins/jquery.timePicker.min.js">	</script>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/tracking_traveller.js"></script>	
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/tracking_ongoingjobs.js"></script>	
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/tracking_pastjobs.js"></script>
	<?php } else {?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $baseurl ?>web/js/<?php echo $pageJavascript ?>.js"></script>
	<?php }?>
	
	<script type="text/javascript">
		var BaseUrl="<?php echo base_url();?>";
		<?php if ($ZOWuser=="miguel" ||	$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") { ?>
			var SuperUserFlag=1;
		<?php } else {?>
			var SuperUserFlag=0;
		<?php }?>
	</script>

</head>

<body <?php echo "class=\"".$pageType."\"";?>>



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

<?php //echo  phpversion(); ?>

</body>
</html>
