<?php
$CI = get_instance();
$user_id =  $this->session->userdata('user_id');

// You may need to load the model if it hasn't been pre-loaded
$CI->load->model('zt2016_users_model');
$id_data =  $CI->zt2016_users_model->getsuer_visibility($user_id);

?>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <meta name="description" content="Smarthr - Codeigniter Admin Template">
  <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
  <meta name="author" content="Dreamguys - Codeigniter Admin Template">
  <meta http-equiv="refresh" content="300">
  <meta name="robots" content="noindex, nofollow">
  <title>Dashboard | <?php echo $title;  ?></title>

  <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(); ?>web/assets/img/favicon.png">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/bootstrap.min.css">

  <!-- Fontawesome CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/font-awesome.min.css">

  <!-- Lineawesome CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/line-awesome.min.css">

  <!-- Alertify CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/plugins/alertify/alertify.min.css">

  <!-- Lightbox CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/plugins/lightbox/glightbox.min.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/plugins/c3-chart/c3.min.css">

  <!-- Toatr CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/plugins//toastr/toatr.css">

  <!-- Select2 CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/select2.min.css">

  <!-- Datetimepicker CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/bootstrap-datetimepicker.min.css">

  <!-- Calendar CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/fullcalendar.min.css">

  <!-- Summernote CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/plugins/summernote/dist/summernote-bs4.css">

  <!-- Datatable CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/dataTables.bootstrap4.min.css">

  <!-- Main CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>web/assets/css/style.css">

  <?php
  if (substr($title, 0, 16) == "New Invoice for ") { ?>
    <link href="<?php echo base_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>web/zt2016/css/invoices/new_client_invoice.css" rel="stylesheet">

  <?php }
  if ($title != 'Users') {


  ?>
    <!-- Bootstrap core CSS -->
    <link href=" <?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Bootstrap theme -->
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Bootstrap dialog -->
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/css/bootstrap-dialog.css" rel="stylesheet">

    <!-- Fontawesome -->
    <link href="<?php echo site_url(); ?>web/zt2016/plugins/fontawesome-free-5.0.6/web-fonts-with-css/css/fontawesome-all.css" rel="stylesheet">

    <!-- ZOWtrak2016 theme -->
    <link href="<?php echo site_url(); ?>web/zt2016/css/zt2016_general.css" rel="stylesheet">

    <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo site_url(); ?>web/zt2016/css/zt2016_datatables.css" rel="stylesheet">
    <style>
      table.dataTable>thead .sorting_desc::before,
      table.dataTable>thead .sorting_asc::before,
      table.dataTable>thead .sorting::before {
        display: none;
      }

      table.dataTable>thead .sorting_asc::before {
        position: absolute;
        top: 8px;
      }

      .header .page-title-box h3 {
        font-family: "CircularStd", sans-serif;
        color: rgb(255, 255, 255);
        font-size: 20px;
        font-weight: 600;
        margin: 1px 0 0 -5px;
      }

      .table-striped>tbody>tr:nth-child(2n+1)>td {
        background-color: rgb(255, 255, 255);
      }

      #ongoing_jobs_table>thead>tr>th:nth-child(-n+3) {
        padding-right: 17px;
      }

      table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child::before {
        top: 6px;
      }
    </style>
  <?php

  } else if ($title != 'Tracking') {


  ?>
    <style>

    </style>
  <?php
  } else {
  ?>
    <style>
      div.dataTables_wrapper div.dataTables_info {
        padding-top: -0.15em;
        text-align: left !important;
      }

      #DataTables_Table_0_wrapper>.row:nth-child(1) {
        display: none !important;
      }

      .header .page-title-box h3 {
        color: rgb(255, 255, 255);
        font-size: 20px;
        font-weight: 600;
        margin: 0px 0 0 -5px;
      }

      .table td a {
        color: rgb(33, 37, 41);
        font-weight: 300 !important;
        font-size: 13px !important;
        font-family: "CircularStd", sans-serif;
      }

      #DataTables_Table_0_filter {
        display: none !important;
      }
    </style>
  <?php
  }
  if ($sub_title != 'report_page_hsd') {
  ?>
    <style>
      .tooltip>.tooltip-inner {
        background-color: #475AA3;
        color: #FFFFFF;
        padding: 8px 14px;
        font-size: 14px;
      }

      .tooltip.top>.tooltip-arrow {
        border-top: 5px solid #475AA3;
      }

      .tooltip {
        position: relative !important;
        /* background: red; */
        height: 200px;
        width: 200px;
        /* border: 1px solid #aaa; */
        margin: inherit !important;
        ;
        display: flex;
        justify-content: center !important;
      }

      .tooltip>.tooltip-arrow {
        position: relative !important;
        bottom: 10% !important;
        margin: inherit !important;
        top: 94% !important;
      }

      .tooltip>.tooltip-inner {
        position: absolute !important;
        bottom: 8% !important;
        display: flex !important;
        width: 100%;
        justify-content: center;

      }
    </style>
  <?php
  }
  ?>

  <style>
    .trash_job_page_table_td>a {
      margin: 0 3px;
      padding: 2px 8px;
      font-size: 12px !important;
    }

    .form-control {
      padding: 2px 15px !important;
    }

    #JobDuplicateSubmit {
      line-height: 0px;
    }

    .btn-primary_hwe {
      background: rgb(67, 149, 255);
      color: rgb(255, 255, 255);
    }

    .btn-primary_hwe:hover {
      background: rgb(71, 140, 230);

      color: #fff;
    }

    .btn-default_hwe {
      background-color: rgb(74, 110, 251);
      border: 1px solid rgb(189, 189, 189);
      color: rgb(255, 255, 255);
    }

    .btn-default_hwe:hover {
      background-color: rgb(80, 105, 206);

      color: rgb(255, 255, 255);
    }

    table>tbody>tr>td {
      vertical-align: middle !important;
    }

    table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child::before {
      top: 12px;
    }

    #JobDuplicateSubmit {
      font-size: 13px !important;
    }

    .mini-sidebar .badge {
      display: block !important;
    }

    .header .user-img img {
      width: 32px;
      border-radius: 50%;
      height: 32px;
      object-fit: cover !important;
    }

    span.graytext {
      color: rgb(153, 153, 153);
    }

    #invoices_table_length,
    #new-invoice-entries_paginate,
    #annualdatatable_paginate {
      text-align: right;
      padding-top: 17px;
    }

    #invoices_table_paginate,
    #new-invoice-entries_info,
    #annualdatatable_info {
      text-align: left;
      padding-top: 17px;
    }

    .well {
      min-height: 169px;

    }

    #client-information .table td {
      white-space: normal;
    }

    .btn-xs {
      padding: 2px 15px;
      font-size: 16px;
    }

    .sidebar-inner {
      font-family: "CircularStd", sans-serif;
    }

    .sidebar-inner {
      overflow-y: scroll;
      overflow-x: hidden;

    }

    .sidebar-inner {
      -ms-overflow-style: none;
      /* IE and Edge */
      scrollbar-width: thin;
      /* Firefox */
      scrollbar-color: rgb(113, 122, 127) rgb(52, 68, 76);
    }

    .sidebar-inner::-webkit-scrollbar {
      width: 7px;
      /* height: 16px; */
    }

    .sidebar-inner::-webkit-scrollbar-thumb {
      background-color: gray;
      /* rgb(113, 122, 127) rgb(52, 68, 76); */
      -webkit-box-shadow: inset 1px 1px 0 gray, inset 0 -1px 0 gray;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    /* #client_info_panel .panel-heading .btn {
      margin-right: .5em;
      margin-top: -26px;

    } */

    .form-control {
      -webkit-appearance: menulist;
    }

    .inactive-contact td {
      color: rgb(255, 0, 0) !important;
      text-decoration: line-through;
    }

    div.dataTables_wrapper div.dataTables_paginate {

      text-align: center;
    }

    .slimScrollDiv {
      height: 100% !important;
    }

    .slimScrollDiv>.sidebar-inner {
      height: 100% !important;

    }

    .mini-sidebar .header-left .logo img {
      max-height: 50px;
    }

    .header .page-title-box {
      margin-top: 6px;
    }

    .mini-sidebar .header .header-left .logo {
      margin-top: 12px !important;
    }

    .nav>li>a:hover,
    .nav>li>a:focus {
      text-decoration: none;
      background-color: unset;
    }

    table .sorting {
      font-size: .85em !important;
    }

    table.dataTable>thead .sorting_asc::after {
      bottom: 9px;
    }

    .fa,
    .fas {
      font-family: normal normal normal 14px/1 FontAwesome;
      font-weight: 900;
    }

    #PastJobsControlForm .panel-heading {
      padding: 0px 15px;

    }

    #PastJobsControlForm .col-sm-4 {
      display: flex;
    }

    #PastJobsControlForm .form-control {
      padding: 6px !important;
      display: inline !important;
      width: auto !important;
      margin: 5px 1em 0 .5em;
    }

    /* .table td {
    white-space: normal !important;
} */
    .table td a {
      color: rgb(33, 37, 41);
      font-weight: 300 !important;
      font-size: 13px !important;
      font-family: "CircularStd", sans-serif;
    }

    #ongoing_jobs_table td:nth-child(6)>a,
    #ongoing_jobs_table td:nth-child(8)>a,
    #ongoing_jobs_table td:nth-child(9)>a,
    #ongoing_jobs_table td:nth-child(10)>a {
      font-weight: 300 !important;
    }

    #trashed_jobs_table th {
      text-transform: capitalize !important;
      font-size: 15px !important;
    }

    #trashed_jobs_table td {
      color: rgb(33, 37, 41);
      font-weight: 500 !important;
      font-size: 13px !important;
      font-family: "CircularStd", sans-serif;
    }

    #DataTables_Table_0 td a {
      color: rgb(51, 51, 51);
      font-weight: 400 !important;
      font-size: 15px !important;
    }

    .sidebar a:hover {
      text-decoration: none;
    }

    div.dataTables_wrapper div.dataTables_filter {
      text-align: left;
    }

    div.dataTables_wrapper div.dataTables_info {

      padding-top: -0.15em;
      text-align: right;
    }

    /* #client_info_panel .panel-heading .btn {
  margin-right: .5em;
  margin-top: -23px;
} */

    .table td a.btn {
      color: #fff;
      font-weight: 300 !important;
    }

    .btn-default {
      /* background-image: linear-gradient(to bottom,rgb(255, 245, 245) 0,rgb(255, 253, 253) 100%); */
      background-image: unset;

      background-color: rgb(227, 227, 227);
      color: #000 !important;

    }

    #contact_dropdown_selector {
      width: 152px !important;
    }

    .edit_track_hsd .form-group {
      margin-left: -170px;
    }

    .tracking_page_button_order_desk {
      display: block;
    }

    .tracking_page_button_order_mob {
      display: none;
    }

    .fade {
      opacity: 1;
    }

    .bootbox.modal {
      background: rgba(0, 0, 0, 0.57);
    }

    .contacts-well {
      height: 119px !important;
    }

    .logo_div {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-repeat: no-repeat !important;
      background-position: center !important;
      background-position-x: center;
      background-size: cover !important;
      background-position-x: 4px !important;
      margin-top: 1px;
      margin-left: -43px;
      transition: 0.3s;
    }

    .mini-sidebar .logo_div {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-repeat: no-repeat !important;
      background-position: center !important;
      background-position-x: center;
      background-size: cover !important;
      background-position-x: 4px !important;
      margin-top: -4px;
      margin-left: -18px;
      transition: 0.3s;
    }

    .edit_job_client_btn {
      display: flex;
    }

    #past-jobs-panel .panel-heading {
      padding: 7px 1px 0px 6px;
    }

    .modal-dialog {
      width: 100%;
    }

    button.close {
      float: right;
    }

    /* 
limbo page */

    .file-content .file-search .input-group-prepend,
    .file-wrap .file-sidebar .file-search .input-group .input-group-prepend {
      left: inherit;
      right: 2% !important;
    }

    .file-cont-wrap {
      width: 100%;
    }

    #Annual_Client_Figures #client_info_panel .panel-heading .btn,
    #Edit_Contact_Information_for_Ashley_Nunn #client_info_panel .panel-heading .btn {
      margin-right: .5em;
      margin-top: inherit;
    }

    #client-totals-div svg {
      max-width: 100% !important;

    }

    .inactive-contact td a {
      color: red;
    }

    #NewSlides,
    #EditedSlides,
    #Hours,
    #NewSlides_1,
    #EditedSlides_1,
    #Hours_1,
    #NewSlides_2,
    #EditedSlides_2,
    #Hours_2,
    #NewSlides_3,
    #EditedSlides_3,
    #Hours_3 {
      padding: 5px !important;
    }

    .col-sm-12>form.form-inline {
      padding-left: unset !important;
    }

    form>.form-group>.input-group>.input-group-addon {
      padding: 0 15px !important;
    }

    /* midia  */
    @media (max-width: 1255px) {

      /* … */
      .edit_track_hsd .form-group {
        margin-left: -115px;
      }
    }

    @media (max-width: 1240px) {

      /* … */
      .edit_track_hsd .form-group {
        margin-left: -54px;
      }
    }

    @media (max-width: 991px) {

      /* … */
      .mini-sidebar .header-left .logo img {
        max-height: 70px;
        max-width: 70px;
      }

      .mini-sidebar .header .header-left .logo {
        margin-top: -2px !important;
      }

      .header .mobile_btn {
        top: 19px;
      }

      .header .user-img {

        margin-top: 16px;
      }

      .mini-sidebar .header-left .logo img {
        max-height: 100%;
        max-width: 100%;
        width: 200px;
        height: 63px;
      }

      #client_dropdown_selector,
      #contact_dropdown_form {
        width: 181px !important;
      }

      #contact_dropdown_selector {
        width: 124px !important;
      }

      .edit_track_hsd .form-group {
        margin-left: -132px;
      }

      .mini-sidebar .logo_div {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-position-x: center;
        background-position-x: center;
        background-size: cover !important;
        background-position-x: 4px !important;
        margin-top: 0px;
        margin-left: -22px;
      }
    }

    @media (max-width: 831px) {

      /* … */
      #top-panel .pull-right {
        width: 100%;
        margin: 5px 10px;
      }

      #JobDuplicateSubmit {
        left: 14px !important;
        width: 96% !important;
      }

      .edit_track_hsd .form-group {
        margin-left: -88px;
      }

      .tracking_page_button_order_desk {
        display: none;
      }

      .tracking_page_button_order_mob {
        display: block;
      }
    }

    @media (max-width: 767px) {
      #JobDuplicateSubmit {
        width: 94% !important;
      }

      .edit_track_hsd .form-group {
        margin-left: 0px;
      }

      #client_dropdown_selector,
      #contact_dropdown_form {
        width: 100% !important;
      }

      #contact_dropdown_selector {
        width: 100% !important;
      }

      .edit_job_client_btn {
        display: block;
      }

      .clinet_submit_button,
      .contact_submit_button {
        width: 100%;
      }

      .clinet.p-5 {
        padding: initial !important;
      }

      #NewSlides,
      #EditedSlides,
      #Hours,
      #NewSlides_1,
      #EditedSlides_1,
      #Hours_1,
      #NewSlides_2,
      #EditedSlides_2,
      #Hours_2,
      #NewSlides_3,
      #EditedSlides_3,
      #Hours_3 {
        padding: 15px !important;
      }

      .time_job_edit_hsd,
      .col-sm-4.EditedSlides_hwe\' {
        padding: 0 15px !important
      }

      .col-sm-4.Hours_hwe\' {
        padding: 0 15px !important
      }

      .col-sm-4.NewSlides_hwe\' {
        padding: 0 15px !important;
      }
    }

    @media (max-width: 575px) {

      /* … */
      .new_job_hsd {
        width: 20% !important;
        float: left !important;
      }

      #contact_dropdown_selector {
        width: inherit;
      }

      .contacts-well {
        height: 69px !important;
      }

      .clinet_submit_button,
      .contact_submit_button {
        width: initial;
      }

      #date_dropdown_form {
        display: inherit !important;
      }

      #client_info_panel .panel-heading .btn {
        margin-right: .5em;
        margin-top: 0px;
      }

      #client-totals-div svg {
        max-width: 100% !important;

      }

      .button_group_hwe {
        display: grid;
      }

      .button_group_hwe #newjobsubmit,
      .button_group_hwe #pastjobsform input,
      .button_group_hwe #Duplicate_contact_submit {
        width: 100%;
      }

      .button_group_hwe a.btn {
        margin-bottom: 9px;
      }

    }

    @media (max-width: 410px) {

      /* … */
      .new_job_hsd {
        width: 28% !important;
        float: left !important;
      }

      #PastJobsControlForm .form-control {
        width: 100% !important;
      }

      #JobDuplicateSubmit {
        width: 91% !important;
      }
    }

    a:hover {
      text-decoration: none;
    }

    .header .user-img .status {
      margin: 0 1px -9px 0;
    }

    @media (max-width: 575px) {
      .header .user-menu {
        display: block !important;
      }

      .header .user-menu>li.nav-item.dropdown.flag-nav {
        display: none;
      }
    }
  </style>
  <script>
   var baseUrl = '<?php echo base_url(); ?>';
  </script>
</head>

<!-- DEBUG-VIEW START 4 APPPATH\Views\partials\body.php -->

<body class="mini-sidebar" id="<?php echo str_replace(' ', '_', $title); ?>">
  <!-- DEBUG-VIEW ENDED 4 APPPATH\Views\partials\body.php -->


  <!-- ============================================================== -->
  <!-- Start right Content here -->
  <!-- ============================================================== -->
  <div class="main-wrapper">
    <!-- DEBUG-VIEW START 7 APPPATH\Views\partials\menu.php -->
    <!-- DEBUG-VIEW START 5 APPPATH\Views\partials\topbar.php -->
    <!-- Header -->
    <div class="header" style="background:linear-gradient(to right, rgb(255, 155, 68) 0%, rgb(249, 99, 1) 100%) !important">

      <!-- Logo -->
      <div class="header-left">
        <a href="<?php echo base_url(); ?>" class="logo">
          <div class="logo_div" style="background:url('<?php echo base_url(); ?>web/assets/img/ZOWlogo.svg')">

          </div>
          <!-- <img src="<?php echo base_url(); ?>web/assets/img/logosvg.png" alt="" style="width: 190px;height: 63px;"> -->
        </a>
      </div>
      <!-- /Logo -->

      <a id="toggle_btn" href="javascript:void(0);">
        <span class="bar-icon" style="margin-top:121%">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </a>

      <!-- Header Title -->
      <div class="page-title-box">
        <h3>ZOWtrak 2023</h3>

      </div>
      <!-- /Header Title -->

      <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>

      <!-- Header Menu -->
      <ul class="nav user-menu">

        <!-- Search -->
        <li class="nav-item d-none">
          <div class="top-nav-search">
            <a href="javascript:void(0);" class="responsive-search">
              <i class="fa fa-search"></i>
            </a>
            <form action="search">
              <input class="form-control" type="text" placeholder="Search here">
              <button class="btn" type="submit"><i class="fa fa-search"></i></button>
            </form>
          </div>
        </li>
        <!-- /Search -->

        <!-- Flag -->
        <li class="nav-item dropdown  flag-nav" has-arrow>
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">
            <img src="<?php echo base_url(); ?>web/assets/img/flags/us.png" alt="" height="20"> <span>English</span>
          </a>

        </li>
        <!-- /Flag -->


        <!-- /Message Notifications -->
        <?php
        $CI = &get_instance();
        $CI->load->model('zt2016_users_model');
        $getentries = $CI->zt2016_users_model->get_user_profile($this->session->userdata('user_id'));
        if ($getentries->dp == '') {
          $path = base_url() . 'web/assets/usersprofile/u_dafault.png';
        } else {
          $path = base_url() . 'web/assets/usersprofile/' . $getentries->dp;
        }
        ?>
        <li class="nav-item dropdown has-arrow main-drop">
          <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" title="<?php echo $ZOWuser; ?>">
            <span class="user-img"><img src="<?php echo $path; ?>" alt="">
              <span class="status online"></span></span>
          </a>
          <div class="dropdown-menu">
            <!-- <a class="dropdown-item" href="profile">My Profile</a> -->
            <!-- <a class="dropdown-item" href="settings">Settings</a> -->
            <a class="dropdown-item" href="#"><?php echo $ZOWuser; ?></a>
            <hr style="margin: 4px 0px;">
            <a class="dropdown-item" href="<?php echo base_url(); ?>main/logout">Logout</a>
          </div>
        </li>
      </ul>
      <!-- /Header Menu -->
    </div>
    <!-- DEBUG-VIEW ENDED 5 APPPATH\Views\partials\topbar.php -->

    <!-- DEBUG-VIEW START 6 APPPATH\Views\partials\sidebar.php -->

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: 100%;  height: 100% !important;">
        <div class="sidebar-inner slimscroll" style=" width: 100%; height: 100% !important;">
          <div id="sidebar-menu" class="sidebar-menu">
            <ul>
              <li class="menu-title">
                <span>Main</span>
              </li>
              <!-- <li class="">
                                <a href="#"><i class="la la-dashboard"></i> <span> Dashboard</span></a>
                               
                            </li> -->
              <li class="">
                <a href="<?php echo base_url(); ?>tracking/zt2016_tracking"><i class="la la-users"></i> <span>Tracking</span></a>
              </li>
              <li class="">
                <a href="<?php echo base_url(); ?>contacts/zt2016_contacts_search"><i class="la la-search"></i> <span>Search</span></a>
              </li>
              <li class="">
                <a href="<?php echo base_url(); ?>reports/zt2016_monthly_originators_breakdown"><i class="las la-chart-bar"></i> <span>Reports</span></a>
              </li>
              <li class="">
                <a href="<?php echo base_url(); ?>limbo"><i class="la la-cube"></i> <span>Limbo</span></a>
              </li>
              <?php if ($id_data->user_type == 2) { ?>
                <li class="submenu">
                  <a href="<?php echo base_url(); ?>limbo#"><i class="la la-briefcase"></i> <span> Manager</span> <span class="menu-arrow"></span></a>
                  <ul style="display: none;">
                    <li><a class="" href="<?php echo base_url(); ?>groups/zt2016_groups">Groups</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>clients/zt2016_clients">Clients</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>clients/zt2016_client_new">New Client</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>contacts/zt2016_contacts">Contacts</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>contacts/zt2016_contact_new">New Contacts</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>financials">Financials</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>export">Export</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>trash/zt2016_trash">Trash</a></li>
                  </ul>
                </li>

                <li class="">
                  <a href="<?php echo base_url(); ?>user"><i class="la la-user"></i> <span>User</span></a>
                </li>
                <li class="submenu">
                  <a href="javascript:void(0);"><i class="la la-files-o"></i> <span> Accounts</span> <span class="menu-arrow"></span></a>
                  <ul style="display: none;">
                    <li>
                      <a class="" href="javascript:void(0);">ZOW Amsterdam <span class="menu-arrow"></span></a>
                      <ul style="display: none;">
                        <li><a class="" href="javascript:void(0);">Invoice Outgoing<span class="menu-arrow"></span></a>
                          <ul style="display: none;">
                            <li><a class="" href="<?php echo base_url(); ?>invoicing/zt2016_new_invoices">Create New Invoice</a></li>
                            <li><a class="" href="<?php echo base_url(); ?>invoicing/zt2016_pending_invoices">Pending Invoice</a></li>
                            <li><a class="" href="<?php echo base_url(); ?>invoicing/zt2016_existing_invoices/fast">All Existing Invoice (fast)</a></li>
                            <li><a class="" href="<?php echo base_url(); ?>invoicing/zt2016_existing_invoices">All Existing Invoice (Complete)</a></li>
                          </ul>
                      </li>
                        <li><a class="" href="javascript:void(0);">Payment<span class="menu-arrow"></span></a>
                          <ul style="display: none;">
                            <!-- <li><a class="" href="<?php echo base_url(); ?>payment/zt2016_addpayment">Add payment</a></li> -->
                            <li><a class="" href="<?php echo base_url(); ?>payment/zt2016_payment">Paymnet History</a></li>
                          </ul>
                      </li>
                        <!-- <li><a class="" href="<?php echo base_url(); ?>payment/zt2016_payment">Payments</a></li> -->
                        <li><a class="" href="<?php echo base_url(); ?>expenses/zt2016_expenses">Expenses</a></li>
                        <li><a class="" href="<?php echo base_url(); ?>estimate/zt2016_estimate">Estimates/Quotation</a></li>
                        <li><a class="" href="<?php echo base_url(); ?>retainers/zt2016_retainers">Retainers</a></li>
                      </ul>
                    </li>
                    <li>
                      <a class="" href="javascript:void(0);">ZOW India <span class="menu-arrow"></span></a>
                      <ul style="display: none;">
                        <li><a class="" href="<?php echo base_url(); ?>zowindia/zowindia_invoice">Invoices</a></li>
                        <!-- <li><a class="" href="<?php echo base_url(); ?>">Payments</a></li> -->
                        <li><a class="" href="<?php echo base_url(); ?>zowindia_expenses/zt2016_expenses">Expenses</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <li class="submenu">
                <a href="<?php echo base_url(); ?>employees#"><i class="la la-users"></i> <span>Employees</span> <span class="menu-arrow"></span></a>
                  <ul style="display: none;">
                    <li><a class="" href="<?php echo base_url(); ?>employee/zt2016_employee_list">All Employee</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>holiday/zt2016_holiday">Holiday</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>employee/zt2016_leave_employee">Leaves</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>settings/globalemailsetting">Shift Schedule</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>settings/globalemailsetting">Timesheet</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>employee/zt2016_attendance_employee">Attendance</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>settings/globalemailsetting">Payroll</a></li>
                  </ul>
                </li>
                <li class="submenu">
                <a href="<?php echo base_url(); ?>limbo#"><i class="la la-wrench"></i> <span>Setting</span> <span class="menu-arrow"></span></a>
                  <ul style="display: none;">
                    <li><a class="" href="<?php echo base_url(); ?>settings/globalsetting">Invoice Setting</a></li>
                    <li><a class="" href="<?php echo base_url(); ?>settings/globalemailsetting">Invoice Email Setting</a></li>
                  </ul>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="slimScrollBar" style="background: rgb(204, 204, 204); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 242.257px;"></div>
        <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
      </div>
    </div>
    <!-- /Sidebar -->
    <!-- DEBUG-VIEW ENDED 6 APPPATH\Views\partials\sidebar.php -->

    <!-- DEBUG-VIEW ENDED 7 APPPATH\Views\partials\menu.php -->

    <!-- Page Wrapper -->
    <div class="page-wrapper" style="min-height: 344px;">

      <!-- Page Content -->
      <div class="content container-fluid">
        <!-- Page Header -->
        <!-- <div class="page-header">
                   <div class="row">
                       <div class="col-sm-12">
                           <h3 class="page-title">Welcome Admin!</h3>
                           <ul class="breadcrumb">
                               <li class="breadcrumb-item active">Dashboard</li>
                           </ul>
                       </div>
                   </div>
               </div> -->
        <!-- /Page Header -->