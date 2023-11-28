<h1>ZOWTrak</h1>
<p>
<a href="<?php echo site_url()?>tracking" id="topbut_tracking">Tracking</a>
<a href="<?php echo site_url()?>clients" id="topbut_clients">Clients</a>
<a href="<?php echo site_url()?>contacts" id="topbut_contacts">Contacts</a>
<a href="<?php echo site_url()?>reports" id="topbut_billing">Reports</a>
<a href="<?php echo site_url()?>invoicing" id="topbut_invoicing">Billing</a>
<a href="<?php echo site_url()?>export" id="topbut_export">Export</a>
<a href="<?php echo site_url()?>trash" id="topbut_trash">Trash</a>
<a href="<?php echo site_url()?>main/logout" id="topbut_logout">Logout</a>
<span class="topmenuinfo">
<?php if (isset($userTimeData)) { 
	echo $userTimeData; 
	 }
	 else {
	 	 echo "&nbsp;"; 
	 } ?>
	</span>
</p>

