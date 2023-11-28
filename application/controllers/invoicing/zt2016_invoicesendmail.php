<?php

class Zt2016_invoicesendmail extends MY_Controller
{

    public function index()
    {

        $this->output->set_header("Last-Modified: " . gmdate("D, j M Y H:i:s") . " GMT"); // Date in the past
        $this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");

        $this->load->helper(array('userpermissions', 'url', 'file'));
        $this->load->library('session'); //flashdata
        $this->load->helper(array('form', 'url', 'general', 'userpermissions', 'zt2016_clients', 'zt2016_timezone'));

        $zowuser = _superuseronly();

        #Determine whether there is flashdata related to invoice
        #(this means that it is a redirect zt2016_finalize_mollie_url)

        $invoiceinfo = new \stdClass();

        $invoiceinfo->InvoiceNumber = $this->uri->segment(3);

        if (empty($invoiceinfo->InvoiceNumber)) {
            die('No invoice number');
        }

        $this->load->model('zt2016_invoices_model', '', 'TRUE');
        $invoiceinfo = $this->zt2016_invoices_model->GetInvoice($options = array('Trash' => '0', 'InvoiceNumber' => $invoiceinfo->InvoiceNumber));

        $clientName = $invoiceinfo->Client;

        #get trakclients
        $this->load->model('trakclients', '', true);
        $clientInfo = $this->trakclients->GetEntry($options = array('CompanyName' => $invoiceinfo->Client));

        # retrieve active client contacts from db
        $this->load->model('zt2016_contacts_model', '', true);
        $ActiveClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $clientName, 'Active' => '1', 'sortBy' => 'FirstName', 'sortDirection' => 'Asc'));
        $contactInfoTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $clientName));

        $RawOriginators = explode(",", $invoiceinfo->Originators);
        $Originators = array_map('trim', $RawOriginators);
        sort($Originators);

        foreach ($Originators as $Originator) {

            $Originator = trim($Originator);
            foreach ($contactInfoTable as $Contact) {
                $ContactFullName = trim($Contact->FirstName . " " . $Contact->LastName);

                //echo "#".$Originator."#<br/>#".$ContactFullName."#";
                if ($Originator == $ContactFullName) {

                    $Contact->ContactFullName = $ContactFullName;
                    $OriginatorTable[] = $Contact;
                    break;
                }

            }

        }
        // print_r($OriginatorTable);
        // die;

        #get invoice entries
        $this->load->model('trakentries', '', true);
        $invoiceentries = $this->trakentries->GetEntry($options = array('Invoice' => $invoiceinfo->InvoiceNumber, 'sortBy' => 'DateOut', 'sortDirection' => 'asc'));

        #get global data
        $this->load->model('globalSettingModal', '', true);
        $globalData = $this->globalSettingModal->GetGlobalSetting();

        # determine if client is a Dutch company
        #if so, VAT needs to be added
        // if (strtolower($clientInfo->Country) == "the netherlands" || strtolower($clientInfo->Country) == "netherlands") {

        //     $temptotal = number_format($invoiceinfo->InvoiceTotal + ($invoiceinfo->InvoiceTotal * .21), 2, ".", "");
        //     $invoiceinfo->InvoiceTotal = $temptotal;

        // }
        if ($this->input->post('submit')) {
            $invoiceEmail = $this->_send_invoice_mail($invoiceinfo, $clientInfo, $invoiceentries, $globalData, $zowuser);
            $invoiceEmailQueryString = http_build_query($invoiceEmail);
            $this->session->set_flashdata('sendemail', $invoiceEmail);
            redirect('invoicing/zt2016_invoice_sendemail/', 'refresh');
        }
        $templateData['title'] = 'Email Setting';
        $templateData['ZOWuser'] = _getCurrentUser();
        $templateData['sidebar_content'] = 'sidebar';

        //  $this->_send_invoice_mail($invoiceinfo,$clientInfo);

        $templateData['main_content'] = $this->_display_page($invoiceinfo, $clientInfo, $globalData, $ActiveClientContacts, $OriginatorTable);

        $this->load->view('admin_temp/main_temp', $templateData);

    }

    public function _display_page($invoiceinfo, $clientInfo, $globalData, $ActiveClientContacts, $OriginatorTable)
    {
        $page_content = '<div class="page_content">' . "\n";

        ######### Display error message
        if ($this->session->flashdata('ErrorMessage')) {

            $page_content .= '<div class="alert alert-danger" role="alert" >' . "\n";
            $page_content .= '  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>' . "\n";
            $page_content .= '  <span class="sr-only">Error:</span>' . "\n";
            $page_content .= $this->session->flashdata('ErrorMessage');
            $page_content .= '</div>' . "\n";

        }

        ######### Display success message
        if ($this->session->flashdata('SuccessMessage')) {

            $page_content .= '<div class="alert alert-success" role="alert" style="margin-top:2em;>' . "\n";
            $page_content .= '  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>' . "\n";
            //$page_content.='  <span class="sr-only">Error:</span>'."\n";
            $page_content .= $this->session->flashdata('SuccessMessage');
            $page_content .= '</div>' . "\n";
        }

        if (!$invoiceinfo->MolliePaymentUrl) {
            $payment_url = $this->get_payment_link($invoiceinfo, $clientInfo);
            $invoiceinfo->MolliePaymentUrl = $payment_url['ZOWPaymentURL'];
        }

        // $message = "<h4>Dear ".$clientInfo->ClientContact.",</h4>\n\n";
        // $message .= "<p>Please find attached an invoice for recent support.</p>\n";
        // $message .= "<p style='margin-bottom:0px'>For your convenience, this invoice may be settled via the following URL:</p>\n";
        // $message .= "<a href='" . $invoiceinfo->MolliePaymentUrl ."'>" . $invoiceinfo->MolliePaymentUrl . "</a>\n\n";
        // $message .= "<p>Thank you for your business!</p>\n\n";
        // $message .= "<p>Cordially,</p>\n\n";
        // $message .= "<p>Jirka Blom - Art Director at Zebra on Wheels BV</p>\n\n";
        // $message .= "<span style='color: gray;'>This message and any attachment are for the intended recipient's use only. If you are not the intended recipient, you may not use, disclose, or reproduce this message, its attachment, or any part thereof, or take any action in reliance thereon. Emails are not secure and cannot be guaranteed to be error-free as they can be intercepted, amended, or contain viruses. Anyone who communicates with us by email is deemed to have accepted these risks. Zebra on Wheels BV denies any responsibility for any damage arising from the use of email.</span>";

        $message = str_replace("client_name", $clientInfo->ClientContact, $globalData[0]->StandardEmail);
        $message = str_replace("mollie_link", $invoiceinfo->MolliePaymentUrl, $message);

        $ReminderEmail = str_replace("client_name", $clientInfo->ClientContact, $globalData[0]->ReminderEmail);
        $ReminderEmail = str_replace("mollie_link", $invoiceinfo->MolliePaymentUrl, $ReminderEmail);

        $SecondReminderEmail = str_replace("client_name", $clientInfo->ClientContact, $globalData[0]->SecondReminderEmail);
        $SecondReminderEmail = str_replace("mollie_link", $invoiceinfo->MolliePaymentUrl, $SecondReminderEmail);

        $subject = str_replace("invoice_number", $invoiceinfo->InvoiceNumber, $globalData[0]->subject);
        $reminderSubject = str_replace("invoice_number", $invoiceinfo->InvoiceNumber, $globalData[0]->reminderSubject);
        $secondReminderSubject = str_replace("invoice_number", $invoiceinfo->InvoiceNumber, $globalData[0]->secondReminderSubject);
        ########## panel head
        //global setting form
        //$attributes='class="form-inline" id="invoice-status-form"';
        $attributes = 'id="client-information-form"';

        $formurl = site_url() . 'invoicing/zt2016_invoicesendmail/' . $invoiceinfo->InvoiceNumber;

        $page_content .= form_open($formurl, $attributes) . "\n";

        $page_content .= '<div id="client_info_panel" class="panel panel-default" >' . "\n";
        $page_content .= '<div class="panel-heading">' . "\n";
        $page_content .= ' <h4>';

        $page_content .= "Email Setting";

        $page_content .= ' </h4><div class="row p-3"><div class="col-sm-4 ">';

        $ndata = array('class' => 'submitButton btn btn-primary col-sm-8 mt-3 clinet_submit_button', 'value' => 'Send Mail', 'name' => 'submit');
        $billing_address = array();
        if ($clientInfo->BillingAddress) {
            $billing_address = explode(',', $clientInfo->BillingAddress);
        }

        // $options = array();
        // foreach($billing_address as $address){
        //     $options[$address] = $address;
        // }

        $ccmail_arr = explode(',', $globalData[0]->ccMailto);
        // $options1 = array();
        // foreach($ccmail_arr as $ccmail){
        //     $options1[$address] = $address;
        // }

        $page_content .= form_submit($ndata) . "\n";
        $page_content .= '</div></div></div>' . "\n";

        ########## panel body
        $page_content .= '<div class="panel-body"><div class="row" style="justify-content: center;">' . "\n";
        $page_content .= '<div class="col-md-6 row">';
        $page_content .= '<label class="invoice-label col-md-4">Send Invoice to:</label><input class="col-md-7" type="text" id="invoice-email" readonly name="BillingAddress" value="' . $clientInfo->BillingAddress . '"/>';
        $page_content .= '<div class="col-md-4"></div>';
        $page_content .= '<div class="col-md-7" style="padding:0px;">';
        $page_content .= '            <ul class="list-items invoiceUl">';
        if ($billing_address || $ActiveClientContacts) {
            foreach ($billing_address as $address) {
                $page_content .= '<li class="item checked invoiceto">
				<span class="item-text ">' . $address . '</span>
			</li>';
            }
            foreach ($ActiveClientContacts as $contacts) {
                $page_content .= '<li class="item invoiceto">
				<span class="item-text ">' . $contacts->Email1 . '</span>
			</li>';
            }
        } else {
            $page_content .= '<li class="item">
				<span class="item-text">Billing address not found please add billing address</span>
			</li>';
        }

        $page_content .= '</ul>';
        // $page_content.= form_multiselect('selected_options[]', $options, '', 'id="select_id" class="w-100"');
        $page_content .= '</div></div>';

        $page_content .= '<div class="col-md-6 row">';
        $page_content .= '<label class="invoice-label col-md-4">Send CC to:</label><input id="invoice-email-cc" class="col-md-7" type="text" name="fromAddress" value="' . $ccmail_arr[0] . '"/>';
        $page_content .= '<div class="col-md-4"></div>';
        $page_content .= '<div class="col-md-7" style="padding:0px;">';
        $page_content .= '            <ul class="list-items">';
        if ($ccmail_arr) {
            foreach ($ccmail_arr as $address) {
                $check = '';
                if ($address == $ccmail_arr[0]) {
                    $check = "checked";
                }
                $page_content .= '<li class="item ' . $check . ' ccto">
			<span class="item-text ">' . $address . '</span>
		</li>';
            }
            foreach ($ActiveClientContacts as $contacts) {
                $page_content .= '<li class="item ccto">
				<span class="item-text ">' . $contacts->Email1 . '</span>
			</li>';
            }
        }
        $page_content .= '</ul></div>';
        // $page_content.= form_multiselect('selected_options[]', $options, '', 'id="select_id1" class="col-md-7"');
        $page_content .= '</div>';

        $page_content .= '<div class="col-md-6 row">';
        $page_content .= '<div class="col-md-4"></div>';
        $page_content .= '<div class="col-md-7"><input class="mt-3" type="text" id="invoice-add"/><button type="button" id="invoice-add-btn">Add</button>';
        $page_content .= '</div></div>';
        $page_content .= '<div class="col-md-6"></div>';

        $page_content .= '<div class="col-md-6 row">';
        $page_content .= '<div class="col-md-4"><label>Template:</label></div>';
        $page_content .= '<div class="col-md-7"><div><input class="mt-3" id="Standard" name="template" type="checkbox" data-id="standard" data-subject="'.$subject.'" checked/> <label for="Standard"> Standard Template</label></div>';
        $page_content .= '<div><input class="mt-3" id="Reminder" name="template" type="checkbox" data-id="reminder" data-subject="'.$reminderSubject.'"/> <label for="Reminder"> Reminder Template</label></div><div><input class="mt-3" type="checkbox" id="SecondReminder" name="template" data-id="SecondReminder" data-subject="'.$secondReminderSubject.'"/> <label for="SecondReminder"> 2nd Reminder Template</label></div>';
        $page_content .= '</div></div>';
        $page_content .= '<div class="col-md-6"></div>';

        
        $page_content .= '<div class="col-md-6 row">';
        $page_content .= '<div class="col-md-4"><label>Email subject:</label></div>';
        $page_content .= '<div class="col-md-7"><input class="mt-3 w-100" type="text" id="subject" name="subject" value="'.$subject.'"/>';
        $page_content .= '</div></div>';
        $page_content .= '<div class="col-md-6"></div>';

        $page_content .= '<div class="row clinet p-1 mt-4">' . "\n";
        // $page_content.= '<label>Form Address</label><textarea name="fromAddress" required="true">invoices@zebraonwheels.com</textarea>';
        // $page_content.= '<label>Billing Address</label><textarea name="BillingAddress" required="true">'.$clientInfo->BillingAddress.'</textarea>';
        $page_content .= '<div class="col-md-2"><label>Body Content:</label></div>';
        $page_content .= '<div class="col-md-9">';
        $page_content .= '<div></div><textarea style="height:300px;" id="bodyContent" class="w-100" name="bodyContent" required="true">' . $message . '</textarea>';
        $page_content .= '</div>';
        $page_content .= form_hidden('standard', $message);
        $page_content .= form_hidden('reminder', $ReminderEmail);
        $page_content .= form_hidden('SecondReminder', $SecondReminderEmail);
        // $page_content .= form_hidden('subject', $subject);

        $page_content .= '</div></div></div>' . '<!-- // class="panel-body" -->' . "\n";
        $page_content .= form_close() . "\n";

        ##########  Notes row
        $page_content .= '	<div class="row ms-2 me-2" style="padding:1.5em 0;">' . "\n";

        ######### Client notes
        $page_content .= '		<div class="col-md-4">' . "\n";

        $attributes = 'id="client-billing-guidelines-form"';
        $formurl = site_url() . 'clients/zt2016_client_billing_info_update/';
        $page_content .= form_open($formurl, $attributes) . "\n";
        $page_content .= form_hidden('ID', $clientInfo->ID);
        $page_content .= form_hidden('InvoiceNumber', $invoiceinfo->InvoiceNumber);
        $page_content .= "			" . form_label("Client Billing Guidelines") . "\n";
        $page_content .= "			" . form_textarea('BillingGuidelines', $clientInfo->BillingGuidelines, 'id="ClientBillingGuidelines" class="form-control" style="min-width: 100%"') . "\n";
        $ndata = array('class' => 'Notes-Submit-Button btn btn-sm', 'value' => 'Update Client Guidelines');
        $page_content .= "<p>" . form_submit($ndata) . "</p>\n";
        $page_content .= form_close("\n");

        $page_content .= '		</div><!--col-->' . "\n";

        ######### Contact notes
        $page_content .= '		<div class="col-md-4">' . "\n";

        foreach ($OriginatorTable as $Originator) {

            $OriginatorFullName = $Originator->FirstName . ' ' . $Originator->LastName;

            if (count($OriginatorTable) < 6) {

                $attributes = 'class="contact-billing-guidelines-form"';
                $formurl = site_url() . 'contacts/zt2016_contact_billing_info_update/';
                $page_content .= form_open($formurl, $attributes) . "\n";
                $page_content .= form_hidden('ID', $Originator->ID);
                $page_content .= form_hidden('InvoiceNumber', $invoiceTotals->InvoiceNumber);
                $page_content .= "			" . form_label($OriginatorFullName . " Billing Guidelines") . "\n";
                $page_content .= "			" . form_textarea('ContactBillingGuidelines', $Originator->ContactBillingGuidelines, 'class="ClientBillingGuidelines form-control" style="min-width: 100%"') . "\n";
                $ndata = array('class' => 'Notes-Submit-Button btn btn-sm', 'value' => 'Update Contact Guidelines');
                $page_content .= "<p>" . form_submit($ndata) . "</p>\n";
                $page_content .= form_close("\n");

            } else {
                #if more than 5 originators, list names linked to contact info page
                $page_content .= '<a href="' . site_url() . 'contacts/zt2016_contact_info/' . $Originator->ID . '">' . $OriginatorFullName;
                if (!empty($Originator->ContactBillingGuidelines)) {
                    $page_content .= " **";
                }
                $page_content .= "</a><br/>\n";
            }
        }

        $page_content .= '		</div><!--col-->' . "\n";

        ##########  Invoice notes
        $page_content .= '		<div class="col-md-4">' . "\n";

        $attributes = 'id="invoice-billing-notes-form"';
        $formurl = site_url() . 'invoicing/zt2016_invoice_billing_notes_update/';
        $page_content .= form_open($formurl, $attributes) . "\n";
        $page_content .= form_hidden('InvoiceNumber', $invoiceinfo->InvoiceNumber);
        $page_content .= "			" . form_label("Invoice Billing Notes") . "\n";
        $page_content .= "			" . form_textarea('InvoiceBillingNotes', $invoiceinfo->BillingNotes, 'id="InvoiceBillingNotes" class="form-control" style="min-width: 100%"') . "\n";
        $ndata = array('class' => 'Notes-Submit-Button btn btn-sm', 'value' => 'Update Invoice Notes');
        $page_content .= "<p>" . form_submit($ndata) . "</p>\n";
        $page_content .= form_close("\n");

        $page_content .= '		</div><!--col-->' . "\n";

        $page_content .= '	</div><!--row--> ' . "\n";

        $page_content .= '</div><!-- // class="page_content" -->' . "\n";

        return $page_content;

    }

    // ################## Retrieves invoice data . ##################
    public function _send_invoice_mail($invoiceinfo, $clientInfo, $invoiceentries, $globalData)
    {

        if (empty($invoiceentries)) {
            $Message = 'No entries found for invoice ' . $invoicenumber;
            $this->session->set_flashdata('SuccessMessage', $Message);
            // $this->session->set_flashdata('ErrorMessage', 'Email sending failed.');
            redirect('invoicing/zt2016_invoicesendmail/' . $invoiceinfo->InvoiceNumber, 'refresh');

        }
        if (empty($globalData)) {
            $Message = 'No Global data found for invoice ' . $invoicenumber;
            $this->session->set_flashdata('SuccessMessage', $Message);

            redirect('/invoicing/zt2016_view_invoice/' . $invoicenumber);
            // echo "Export problem. Query without results is as follows:<br/>".$this->db->last_query();
            exit();
        }

        $file_upload = $this->_pdf_file_upload($invoiceinfo, $clientInfo, $invoiceentries, $globalData, $zowuser);
        $fromAddress = $this->input->post('fromAddress');
        $billingAddress = $this->input->post('BillingAddress');
        $bodyContent = $this->input->post('bodyContent');
        $subject = $this->input->post('subject');

        $recipient_email = $billingAddress;

        // Email subject
        // $subject = str_replace("invoice_number", $invoiceinfo->InvoiceNumber, $globalData[0]->subject);
        // $subject = 'ZOW Invoice '.$invoiceinfo->InvoiceNumber;

        // File path to the attachment
        $file_path = base_url() . 'pdfs/' . $file_upload; // Replace with the actual path to your attachment

        // Boundary for multipart email
        $boundary = md5(time());

        // Headers
        $headers = "From: invoices@zebraonwheels.com\r\n";
        $headers .= "Reply-To: invoices@zebraonwheels.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $headers .= "Cc: $fromAddress\r\n";

        // Email body
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=\"utf-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $message . "\r\n";

        $bodyContent = $this->input->post('bodyContent');
        $message .= $bodyContent . "\r\n";

        // Read and encode the attachment file
        $attachment = chunk_split(base64_encode(file_get_contents($file_path)));

        // Add attachment to email
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: application/pdf; name=\"invoice.pdf\"\r\n";
        $message .= "Content-Disposition: attachment; filename=\"" . $file_upload . "\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= $attachment . "\r\n";
        $message .= "--$boundary--\r\n";

        $invoiceEmail['status'] = 'Not Send';
        $invoiceEmail['invoice'] = $invoiceinfo->InvoiceNumber;
        $invoiceEmail['zowuser'] = $zowuser;
        $invoiceEmail['recipient'] = $fromAddress;
        $invoiceEmail['cc'] = $billingAddress;
        $invoiceEmail['pdf'] = $invoiceinfo->InvoiceNumber;

        // $invoiceDBinfo=$this->zt2016_invoices_model->AddEmailData($invoiceEmail);
        // Send the email using the mail() function
        if (mail($recipient_email, $subject, $message, $headers)) {
            $invoiceEmail['status'] = 'send';
            $this->session->set_flashdata('SuccessMessage', 'Email sent successfully.');

        } else {
            $this->session->set_flashdata('ErrorMessage', 'Email sending failed.');
        }
        return $invoiceEmail;
    }

    public function get_payment_link($Invoiceinfo, $clientInfo)
    {

        $invoiceinfo['InvoiceNumber'] = $Invoiceinfo->InvoiceNumber;

        $invoiceinfo['Currency'] = $Invoiceinfo->Currency;

        $invoiceinfo['InvoiceTotal'] = number_format($Invoiceinfo->InvoiceTotal, 2, ".", "");
        $invoiceinfo['VATCheck'] = 0;
        $discount = $Invoiceinfo->discount;
		$invoiceTotal = $Invoiceinfo->InvoiceTotal;
		$invoiceTotal = $invoiceTotal - $discount;
	

        if ($clientInfo->Country == "The Netherlands" || $clientInfo->Country == "Netherlands") {
            $vatrevenue = (float)str_replace(',', '', $invoiceTotal);
            $VAT=$vatrevenue *21/100;
			$VATFormatted=number_format($VAT, 2, '.', ',');
			$invoiceTotal = $invoiceTotal + $VATFormatted;
          
            $invoiceinfo['InvoiceTotal'] = number_format(round($invoiceTotal, 2), 2);
        
            $invoiceinfo['VATCheck'] = 1;

        }

        ##################### Main sequence

        $invoiceinfo['ZOWpaypageHTML'] = $this->_getmolliehtml($invoiceinfo);

        $invoiceinfo = $this->_upload_html_invoice($invoiceinfo);

        $this->session->set_flashdata('invoiceinfo', $invoiceinfo);
//         $this->session->set_flashdata('SuccessMessage', 'New payment URL created.');

        return $invoiceinfo;

        // redirect('invoicing/zt2016_invoice_mollie_form/'.$invoiceinfo['InvoiceNumber'], 'refresh');
    }

    ################## Generate HTML header ##################
    public function _getmolliehtml($invoiceinfo)
    {
        $molliehtml = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="cache-control" content="no-cache" />
            <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
            <meta http-equiv="pragma" content="no-cache" />

            <title>ZOW Invoice ' . $invoiceinfo['InvoiceNumber'] . '</title>
            <meta name="description" content="Zebra on Wheels - Creative Services for Corporate Managers">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

            <link href="https://fonts.googleapis.com/css2?family=Jockey+One&display=swap" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Spinnaker&display=swap" rel="stylesheet">

            <link rel="stylesheet" href="https://www.zebraonwheels.com/web/css/zow.css">

        </head>
        <body data-spy="scroll" data-target=".navbar" data-offset="50">

        <!-- nav bar -->

        <nav class="navbar navbar-expand-lg navbar-dark fixed-top black">
        <a class="navbar-brand" href="https://www.zebraonwheels.com/"><img src="https://www.zebraonwheels.com/web/img/ZOWlogo.svg" alt="Zebra on Wheels"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse flex-grow-1 text-right" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto flex-nowrap">
            <li class="nav-item">
                <a class="nav-link" href="https://www.zebraonwheels.com/#intro">Intro</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.zebraonwheels.com/#services">Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.zebraonwheels.com/#approach">Approach</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.zebraonwheels.com/#contact">Contact</a>
            </li>
            </ul>

        </div>
        </nav>
            <div class="container invoice">
                <h1 class="section-intro">Invoice<br />' . $invoiceinfo['InvoiceNumber'] . '<br />' . number_format($invoiceinfo['InvoiceTotal'], 2, ".", ",") . ' ' . $invoiceinfo['Currency'] . '</h1>

                <form action="https://www.zebraonwheels.com/createmollieurl" id="mcheckoutform" name="mcheckoutform" method="post" accept-charset="utf-8" class="mb-5">
                    <input type="hidden" id="InvoiceTotal" name="InvoiceTotal" value="' . $invoiceinfo['InvoiceTotal'] . '">
                    <input type="hidden" id="Currency" name="Currency" value="' . $invoiceinfo['Currency'] . '">
                    <input type="hidden" id="InvoiceNumber" name="InvoiceNumber" value="' . $invoiceinfo['InvoiceNumber'] . '">
                    <input type="submit" name="molliesubmit" value="Click here to pay" class="btn btn-sm btn-info">
                </form>

                <p>For security reasons, only the invoice number and due amount are shown on this page.</p>
                <p>Please ensure that they match your copy before proceeding with payment.</p>
                <p>Should you have any questions or comments, please contact <a href="mailto:invoices@zebraonwheels.com">invoices@zebraonwheels.com</a></p>
            </div>
        </body>
        </html>';
        return $molliehtml;
    }

    public function _upload_html_invoice($invoiceinfo)
    {

        $save_path = dirname(dirname(dirname(__dir__))) . "/zowtempa/etc/temp/";

        // ##### Create invoice file and save it in protected/temp
        if (!write_file($save_path . $invoiceinfo['InvoiceNumber'] . '.html', $invoiceinfo['ZOWpaypageHTML'])) {
            die("Unable to write the file");
        }
        // die ($save_path.$invoiceinfo['InvoiceNumber'].'.html');

        // ##### Upload invoice file to ZOW site

        $this->load->library('sftp');

        $config['hostname'] = $this->config->item('zowsftphostname');
        $config['username'] = $this->config->item('zowsftpusername');
        $config['password'] = $this->config->item('zowsftppassword');
        $config['debug'] = true;

        $sftp = new Net_SFTP($config['hostname']);
        if (!$sftp->login($config['username'], $config['password'])) {
            exit('Login Failed - cannot write Ogone url to ZOW site');
        }
        //$this->sftp->upload($_SERVER['NFSN_SITE_ROOT']  . 'protected/temp/'.$invoiceinfo['InvoiceNumber'].'.html', '/www/payments/'.$invoiceinfo['InvoiceNumber'].'.html', 'ascii');
        $filename = '/data/sites/web/zebraonwheelscom/www/paymentsm/' . $invoiceinfo['InvoiceNumber'] . '.html';

        $upload = $sftp->put($filename, $save_path . $invoiceinfo['InvoiceNumber'] . '.html', NET_SFTP_LOCAL_FILE);
        //print_r($upload);

        $sftp->disconnect();

        // ##### Delete invoice file in protected/temp

        //unlink($save_path.$invoiceinfo['InvoiceNumber'].'.html');

        $invoiceinfo['ZOWPaymentURL'] = "https://www.zebraonwheels.com/paymentsm/" . $invoiceinfo['InvoiceNumber'] . '.html';

        return $invoiceinfo;

    }

    public function _pdf_file_upload($invoicedata, $clientdata, $invoiceentries, $globalData)
    {
        $invoicenumber = $invoicedata->InvoiceNumber;
        foreach ($globalData as $row) {
            $fromAddress = $row->fromAddress;
            $contactName = $row->contactName;
            $mobNumber = $row->mobNumber;
            $email = $row->email;
            $bankAccount = $row->bankAccount;
            $taxinfo = $row->footer;
        }

        $this->load->dbutil();

        $StartDate = $invoicedata->StartDate;
        $EndDate = $invoicedata->EndDate;

        $StartDate = date('Y-m-d', strtotime('+1 day' . $StartDate));
        $EndDate = date('Y-m-d', strtotime($EndDate));

        $this->load->dbutil();

        $currency = $clientdata->Currency;
        if ($currency == "USD") {
            $currencySymbol = '$';
        } else if ($currency == 'EUR') {
            $currencySymbol = '€';
        }

        $VATregistration = "";
		$VATFormatted = number_format("0",2,".",",");
		$noteHTML ="";
		$discount = $invoicedata->discount;
		$invoiceTotal = $invoicedata->InvoiceTotal;
		$invoiceTotal = $invoiceTotal - $discount;
		$address = '';
		if($clientdata->Address){
			$address = "<br>".$clientdata->Address;
		}
		if($clientdata->Address2){
			$address.="<br>".$clientdata->Address2;
		}
		if($clientdata->Address3){
			$address.="<br>".$clientdata->Address3;
		}
		if($clientdata->Address4){
			$address.="<br>".$clientdata->Address4;
		}
		
		if($clientdata->VATOther){
		    $VATregistration = $clientdata->VATOther;
		}
		if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
			
			$vatrevenue = (float)str_replace(',', '', $invoiceTotal);
			$VAT=$vatrevenue *21/100;
			$VATFormatted=number_format($VAT, 2, '.', ',');
			$invoiceTotal = $invoiceTotal + $VATFormatted;
			
		}
		else{
			$noteHTML = '<tr><br>
			<td class="" style="text-align:center;padding-top: 20px;"><strong><i>Customer to account for any VAT arising on this supply in accordance with Article 196, Council Directive 2006/112/EC</i></strong></td>
		</tr>';
		}

		if($VATregistration){
			$VAThtml ='<br><span style="font-size:10px;font-weight: bold;">VAT Registration: </span>'.$VATregistration;
		}
		

		$delimiter = ",";
		$newline = "\n";
		$path = base_url() . 'web/assets/usersprofile/u_dafault.png';
		$data ="";
		$html='';
	    $invoiceDate = strtotime($invoicedata->EndDate);
		$formattedDate = date("d-F-Y", $invoiceDate);
		$htmltable='<style>
		body{
			justify-content: center;
			display: flex;
			font-size:11px;
		  }
		  ul{
			list-style: none !important;
		  }
		  li{
			line-height: 3px;
		  }
		  .border_top,th.border_top,td.border_top{
			border-top: 1px solid rgb(193, 193, 193);
		  }
		  .border_bottom,th.border_bottom,td.border_bottom{
			border-bottom: 1px solid rgb(193, 193, 193);
		  }
		  .border_top_bottom,th.border_top_bottom,td.border_top_bottom{
			border-top: 1px solid rgb(193, 193, 193);
			border-bottom: 1px solid rgb(193, 193, 193);
		  }
		  .table_color{
			background: rgb(196, 188, 150);color: #fff;
			color: #fff;
			border:inherit !important;
		  }
		  .table_color th,.table_color td{
			background: rgb(196, 188, 150);color: #fff;
			color: #fff;
			border:inherit !important;
		  }
		  .text-center{
			text-align:center;
		  }
		</style>
		<div style="width: 95%;">';

		$htmltable .='<table class="table table-striped table-hover" style="width:100%;">
			<tr style="text-align: center;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: middle;text-align: left;color: rgb(217, 217, 217);font-size: 28px;"><b/r><h1 class="text-uppercase">Invoice</h1></td>
				<td style="width: 40%;"></td>
				<td style="max-width: 40%;text-align: right;">
				<img src="https://zowtrak.com/web/assets/usersprofile/invoice-logo.jpg" class="" alt="logo" />
				</td>
			</tr>
			
		</table>';
		$htmltable .='<br><table class="table table-striped table-hover" style="width:100%;margin-left: 0%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: top;width:  25%;"><h4 style="max-width: 91%;">'.$fromAddress.'</h4></td>
				<td style="width: 5%; vertical-align: top;">To</td>
				<td style="vertical-align: top;"><h4 style="max-width: 91%;">'.$clientdata->CompanyName.$address.'<br>'.$clientdata->Country.'</h4><p style="max-width: 100%;">'.$VAThtml.'</p></td>
				<td style="width: 20%;"></td>
				<td style="vertical-align: top;text-align: right;">
				<p><h4>Date:</h4></p>
				<p><p><span>'.$formattedDate.'</span></p></p>
				<br>
			<p style="margin-top: 35px !important;"></p>
				<p><h4>PO:</h4></p>
				<p><p>'.$clientdata->PONumber.'</p></p>
			</td>

				</td>
			</tr>
		</table>';
		$htmltable .='<table class="table table-striped table-hover" style="width:100%;margin-bottom: 49px;margin-top: 4%;">
			<tr style="text-align: left;">
				<td style="width: 5%;"></td>
				<td style="vertical-align: bottom;width:  25%;">
					<div class="col-sm-3  m-b-20" >
					<h4 style="max-width: 90%;">Contact: '.$contactName.'<br>
					'.$mobNumber.'<br>
					<small style="font-size: 11px;">'.$email.'</small></h4>
					</div>
				</td>
				<td style="width: 5%; vertical-align: bottom;"></td>
				<td style="vertical-align: bottom;width:40%;" >
					<div class="col-sm-4  m-b-20 ">
					<small style="max-width: 100%;"><span style="font-size:11px;font-weight: bold;">Attention:</span> <span style="font-size:11px;">'.$clientdata->ClientContact.'</span></small>
					</div>
				</td>
				
				<td style="width: 30%; vertical-align: bottom;text-align: right;" colspan=2>
				<p><h4>Invoice Number:</h4></p>
				<p><p>'.$invoicenumber.'</p></p>
				</td>
			</tr>
		</table>';
		$htmltable .='<table class="table table-striped table-hover "  style="width:99%;margin-left: 5%;" >
		<thead style="background: rgb(196, 188, 150);color: #fff;">
			<tr class="table_color" >
				<th style="width:12%">Date</th>
				<th class="d-none d-sm-table-cell">Originator</th>
				<th>File Name</th>
				<th style="width:10%">New Slides</th>
				<th style="width:10%">Edit to existing slides</th>
				<th class="text-end">Hours</th>
			</tr>
			</thead>
		<tbody>';
		
		foreach ($invoiceentries as $row)
		
			{
				$entriesDate = strtotime($row->DateOut);
				$entriesformattedDate = date("d-M-Y", $entriesDate);
				$htmltable .='<tr>
							<td style="width:12%"><small style="font-size: 11px;">'.$entriesformattedDate.'</small></td>
							<td><small style="font-size: 11px;">'.$row->Originator.'</small></td>
							<td class="d-none d-sm-table-cell"><small style="font-size: 11px;">'.$row->FileName.'</small></td>
							<td style="width:10%;text-align:center;"><small style="font-size: 11px;">'.$row->NewSlides.'</small></td>
							<td style="width:10%; text-align:center;"><small style="font-size: 11px;">'.$row->EditedSlides.'</small></td>
							<td class="text-end" style="text-align:center;"><small style="font-size: 11px;">'.$row->Hours.'</small></td>
						</tr>
						';
			}
			
		$htmltable .='
					<tr>
						<th class="border_top" style="text-align:start;padding-top: 2px;padding-bottom:2px" colspan=3><strong>Subtotal (Slides)</strong></b></th>
						
						<td class="border_top" style="text-align:center;">'.$invoicedata->SumNewSlides.'</td>
						<td class="border_top" style="text-align:center;">'.$invoicedata->SumEditedSlides.'</td>
						<td class="text-end border_top" style="text-align:center;">'.round($invoicedata->SumHours,2).'</td>
					</tr>
					<tr>
					<th class="border_top" style="vertical-align: bottom; text-align:start" colspan=3><strong>Subtotal (Hours)</strong></th>
					<td class="border_top" style="text-align:center;">5 slides/<br>hour</td>
					<td class="border_top" style="text-align:center;">10 slides/<br>hour</td>
					<td class="text-end border_top" ></td>
				</tr>
				<tr style="padding-top:20px;">
					<th class="border_bottom" colspan=3></th>
					<td class="border_bottom text-center">'.round($invoicedata->SumNewSlides/5,2).'</td>
					<td class="border_bottom text-center">'.round($invoicedata->SumEditedSlides/(5/$invoicedata->PriceEdits),2).'</td>
					<td class="text-end border_bottom text-center">'.round($invoicedata->SumHours,2).'</td>
				</tr>
				
				</tbody>
				
			</table>';
			$discountHtml = '';
			$htmltable .='<table class="table table-striped table-hover" style="width:99%;margin-left: 2%;" >
							<tbody>	
							<tr>
							<td class="" style="text-align: right;padding-right: 78px;"><strong><h4>Total Hours</h4></strong></td>
							<td class="text-end " style="text-align: right;"><h4  style="">'.number_format($invoicedata->BilledHours,2).'</h4></td>
						</tr>';
			if($invoicedata->discount !=0){
				$discountHtml = '<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Discount</h4></strong></td>
				<td class="text-end " style="text-align: right;padding-top: 10px;"><h4  style="">-'.$currencySymbol.number_format($invoicedata->discount,2).'</h4></td>
			</tr>
            <tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Subtotal (after discount) </h4></strong></td>
				<td class="text-end " style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format($invoicedata->InvoiceTotal - $invoicedata->discount,2).'</h4></td>
			</tr>
				<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>VAT</h4></strong></td>
				<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format($VATFormatted,2).'</h4></td>
			</tr>
				';
			}
			else{
				$discountHtml = '<tr>
				<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>VAT</h4></strong></td>
				<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.$VATFormatted.'</h4></td>
			</tr>';
			}

				$htmltable.=	'<tr>
							<td class="" style="text-align: right;padding-right: 64px;padding-top: 10px;"><h4>Subtotal ('.$currencySymbol.$invoicedata->PricePerHour .' per hour)</h4></td>
							<td class="text-end " style="text-align: right; padding-top: 10px;"><h4 style="display: block ruby;">'.$currencySymbol.number_format($invoicedata->InvoiceTotal,2).'</h4></td>
						</tr>
						'.$discountHtml.'
						<tr>
							<td class="" style="text-align: right;padding-right: 78px;padding-top: 10px;"><strong><h4>Total</h4></strong></td>
							<td class="text-end border_bottom" style="text-align: right;padding-top: 10px;"><h4  style="">'.$currencySymbol.number_format(round($invoiceTotal,2),2).'</h4></td>
						</tr>
						</tbody>
						</table>';

					$htmltable.='<table class="table table-striped table-hover" style="width:100%;" >
					<tbody>'.$noteHTML.'
					<tr><td style="text-align:center;"></br></br><h4 class="text-center pe-5 pr-5"><br><br>'.$bankAccount.'</h4></td></tr>
						
							</tbody>
						</table>';
			
		//############### Table
		$htmltable .='</div>';

        ##footer
			$footerHtml = '<div style="position: fixed; bottom: 0px; width: 100%; text-align: center;">
            <h4>' . $taxinfo . '</h4>
            </div>';
        $htmltable .= $footerHtml;

        $this->load->library('Pdfcontroller');
        // $this->pdfcontroller->generate_pdf($htmltable,$invoicenumber);
        $pdf_path = $this->pdfcontroller->download_pdf($htmltable, $invoicenumber);
        return $pdf_path;
    }

}

/* End of file zt2016_client_invoices.php */
/* Location: ./system/application/controllers/invoicing/zt2016_client_invoices.php */
