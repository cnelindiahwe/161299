<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "main";


####### tracking 
$route['x:any'] = "download/zt2016_download";
$route['tracking'] = "tracking/zt2016_tracking";
$route['tracking/view/:any'] = "tracking/tracking/view";

$route['addentry'] = "tracking/addentry";
$route['editentry/:num'] = "tracking/editentry";
$route['trashentry/:num'] = "tracking/trashentry";
$route['updateentry/:num'] = "tracking/updateentry";


####### tracking zt2016
$route['tracking/zt2016_tracking/monthview/:any'] = "tracking/zt2016_tracking";
$route['zt2016_edit_job'] = "tracking/zt2016_edit_job";
$route['zt2016_edit_job/:num'] = "tracking/zt2016_edit_job";
$route['zt2016_new_job'] = "tracking/zt2016_new_job";
$route['zt2016_update_job/:num'] = "tracking/zt2016_update_job";
$route['zt2016_create_job'] = "tracking/zt2016_create_job";

####### past jobs zt2016
$route['tracking/zt2016_past_jobs/:any'] = "tracking/zt2016_past_jobs";


$route['tracking/zt2016_job_trash/:num'] = "tracking/zt2016_job_trash";
$route['tracking/zt2016_job_delete/:any'] = "tracking/zt2016_job_delete";
$route['tracking/zt2016_job_restore/:any'] = "tracking/zt2016_job_restore";


####### groups  zt2016
$route['groups/'] = "groups/zt_2016_groups";
$route['groups/zt2016_group_info/:any'] = "groups/zt2016_group_info";
$route['groups/zt2016_group_edit/:any'] = "groups/zt2016_group_edit";

####### clients zt2016 trash
$route['groups/zt2016_group_delete/:any'] = "groups/zt2016_group_delete";
$route['groups/zt2016_group_restore/:any'] = "groups/zt2016_group_restore";


####### clients 
$route['clients'] = "clients/zt2016_clients";

$route['clients/updateclient/:num'] = "clients/updateclient";
$route['clients/editclient'] = "clients/editclient";
$route['clients/editclient/:any'] = "clients/editclient";
$route['clients/manageclientmaterials/:any'] = "clients/manageclientmaterials";
$route['clients/ajax_clientmaterialslist/:any'] = "clients/ajax_clientmaterialslist";
$route['clients/ajax_downloadmaterials/:any'] = "clients/ajax_downloadmaterials";
$route['clients/deleteclientfile/:any'] = "clients/deleteclientfile";

####### clients zt2016
$route['clients/zt2016_client_info/:any'] = "clients/zt2016_client_info";
$route['clients/zt2016_client_edit/:any'] = "clients/zt2016_client_edit";
$route['clients/zt2016_client_update/:num'] = "clients/zt2016_client_update";
$route['clients/zt2016_client_trash/:any'] = "clients/zt2016_client_trash";
$route['clients/zt2016_client_quotation/:any'] = "clients/zt2016_client_quotation";


####### clients zt2016 trash
$route['clients/zt2016_client_delete/:any'] = "clients/zt2016_client_delete";
$route['clients/zt2016_client_restore/:any'] = "clients/zt2016_client_restore";

####### clients zt2016 materials
$route['clients/zt2016_manageclientmaterials/:any'] = "clients/zt2016_manageclientmaterials";
$route['clients/zt2016_deleteclientmaterials/:any'] = "clients/zt2016_deleteclientmaterials";
$route['clients/zt2016_createclientmaterialsfolder/:any'] = "clients/zt2016_createclientmaterialsfolder";
$route['clients/zt2016_downloadclientmaterials/:any'] = "clients/zt2016_downloadclientmaterials";


####### contacts
$route['contacts'] = "contacts/contacts";
$route['contacts/editcontact/:num'] = "contacts/editcontact";
$route['contacts/updatecontact/:num'] = "contacts/updatecontact";
$route['contacts/viewclientcontacts/:num'] = "contacts/viewclientcontacts";
$route['contacts/trashcontact/:num'] = "contacts/trashcontact";
$route['contacts/zt2016/:any'] = "contacts/zt2016";

####### contacts zt2016
$route['contacts/zt2016_contact_info/:any'] = "contacts/zt2016_contact_info";
$route['contacts/zt2016_contact_edit/:any'] = "contacts/zt2016_contact_edit";
$route['contacts/zt2016_contact_update/:num'] = "contacts/zt2016_contact_update";
$route['contacts/zt2016_contact_new/:num'] = "contacts/zt2016_contact_new";
$route['contacts/zt2016_contact_trash/:num'] = "contacts/zt2016_contact_trash";
$route['contacts/zt2016_contact_delete/:any'] = "contacts/zt2016_contact_delete";
$route['contacts/zt2016_contact_restore/:any'] = "contacts/zt2016_contact_restore";


####### reports
$route['reports'] = "reports/reports";
$route['reports/clientreport/:any'] = "reports/clientreport";

####### reports zt2016
$route['reports/zt2016_annual_client_figures/:any'] = "reports/zt2016_annual_client_figures";
$route['reports/zt2016_annual_originator_figures/:any'] = "reports/zt2016_annual_originator_figures";

$route['reports/zt2016_monthly_clients_breakdown/:any'] = "reports/zt2016_monthly_clients_breakdown";
$route['reports/zt2016_monthly_originators_breakdown/:any'] = "reports/zt2016_monthly_originators_breakdown";


$route['reports/zt2016_annual_clients_breakdown/:any'] = "reports/zt2016_annual_clients_breakdown";
$route['reports/zt2016_annual_originators_breakdown/:any'] = "reports/zt2016_annual_originators_breakdown";


$route['reports/zt2016_monthly_momentum_report/:any'] = "reports/zt2016_monthly_momentum_report";




####### invoicing
$route['invoicing'] = "invoicing/invoicing";
$route['invoicing/clientinvoices/:any'] = "invoicing/clientinvoices";
$route['invoicing/newinvoice/:any'] = "invoicing/newinvoice";
$route['invoicing/viewinvoice/:any'] = "invoicing/viewinvoice";
$route['invoicing/csvinvoice/:any'] = "invoicing/csvinvoice";
$route['invoicing/invoiceogoneform/:any'] = "invoicing/invoiceogoneform";
$route['invoicing/csvpastinvoice/:any'] = "invoicing/csvpastinvoice";
$route['invoicing/csvpastinvoice/:any'] = "invoicing/csvpastinvoice";
$route['zowindia/zowindia_view_invoice/:any'] = "zowindia/zowindia_view_invoice";
$route['zowindia/zowindia_pdf_existing/:any'] = "zowindia/zowindia_pdf_existing";

####### invoicing zt2016
$route['invoicing/zt2016_view_invoice/:any'] = "invoicing/zt2016_view_invoice";
$route['invoicing/zt2016_csv_existing_invoice/:any'] = "invoicing/zt2016_csv_existing_invoice";
$route['invoicing/zt2016_pdf_existing_invoice/:any'] = "invoicing/zt2016_pdf_existing_invoice";
$route['invoicing/zt2016_client_invoices/:any'] = "invoicing/zt2016_client_invoices";
$route['invoicing/zt2016_new_client_invoice/:any'] = "invoicing/zt2016_new_client_invoice";
$route['invoicing/zt2016_invoice_ogone_form/:any'] = "invoicing/zt2016_invoice_ogone_form";
$route['invoicing/zt2016_invoice_mollie_form/:any'] = "invoicing/zt2016_invoice_mollie_form";
$route['invoicing/zt2016_finalize_mollie_url/:any'] = "invoicing/zt2016_finalize_mollie_url";
$route['invoicing/zt2016_existing_invoices/:any'] = "invoicing/zt2016_existing_invoices";
$route['invoicing/zt2016_invoicesendmail/:any'] = "invoicing/zt2016_invoicesendmail";

#### estimate
$route['estimate/zt2016_edit_estimate/:any'] = "estimate/zt2016_edit_estimate";
$route['estimate/zt2016_estimateView/:any'] = "estimate/zt2016_estimateView";
$route['estimate/zt2016_pdf_existing_estimate/:any'] = "estimate/zt2016_pdf_existing_estimate";

### employee
$route['employee/zt2016_employee_profile/:any'] = "employee/zt2016_employee_profile";
$route['employee/zt2016_edit_employee/:any'] = "employee/zt2016_edit_employee";

####### financials
$route['financials'] = "financials/financials";
$route['financials/fin_breakdown/:num'] = "financials/fin_breakdown";


####### trash
$route['trash'] = "trash/trash";
$route['deleteentry/:num'] = "trash/deleteentry";
$route['untrashentry/:num'] = "trash/untrashentry";
$route['trash/deleteclient/:num'] = "trash/deleteclient";
$route['trash/untrashclient/:num'] = "trash/untrashclient";
$route['trash/deletecontact/:num'] = "trash/deletecontact";
$route['trash/untrashcontact/:num'] = "trash/untrashcontact";

####### export
$route['export'] = "export/zt2016_export";
$route['dump'] = "dump/dump";

####### export 2016
$route['export/zt2016_exportdata/:any'] = "export/zt2016_exportdata";
$route['export/zt2016_downloaddbbackup/:any'] = "export/zt2016_downloaddbbackup";



####### limbo
$route['store_files/:any'] = "limbo/zt2016_limbo/store_files";
$route['limbo'] = "limbo/zt2016_limbo";
$route['user'] = "users/zt2016_users";
$route['user/edit/:any'] = "users/zt2016_users/edit_user";
$route['user/add'] = "users/zt2016_users/adduser";
$route['user/view/:num'] = "users/zt2016_users";
$route['user/delete/:any'] = "users/zt2016_users/delete_user";
// $route['download-file'] = "download/zt2016_download";

$route['limbo/limbodir/:any'] = "limbo/limbodir";
$route['limbo/deletelimbofile/:any'] = "limbo/deletelimbofile";
$route['limbo/deletelimbodir/:any'] = "limbo/deletelimbodir";
$route['limbo/downloadlimbofile/:any'] = "limbo/downloadlimbofile";
$route['limbo/uploadlimbofile/:any'] = "limbo/uploadlimbofile";

####### limbo 2016
$route['limbo/zt2016_limbodir/:any'] = "limbo/zt2016_limbodir";
$route['limbo/zt2016_deletelimbodir/:any'] = "limbo/zt2016_deletelimbodir";
$route['limbo/zt2016_deletelimbofile/:any'] = "limbo/zt2016_deletelimbofile";
$route['limbo/zt2016_downloadlimbofile/:any'] = "limbo/zt2016_downloadlimbofile";

$route['scaffolding_trigger'] = "";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */