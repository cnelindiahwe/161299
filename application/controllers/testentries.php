<?php

class Testentries extends MY_Controller {

	
	public function index()
	{
		
		$totaleur=0;
		$totalUSD=0;
		
		$this->load->helper(array('invoice','financials'));
	
	$billedinvoices = $this->db->get('zowtrakinvoices');
		foreach ($billedinvoices->result() as $invoice) {
			echo $invoice->InvoiceTotal. "    ";
			
			$invoicedata= InvoiceTotalsByNumber($invoice->InvoiceNumber,$invoice->Client);
						
 			if ($invoice->Currency=="EUR") {$totaleur=$totaleur+$invoice->InvoiceTotal;}
			else if ($invoice->Currency=="USD") {$totalUSD=$totalUSD+$invoice->InvoiceTotal;}
				
				
			$temptotal=str_replace(",","",$invoicedata['invoicetotal']);
			$newtotal= number_format(floatval($temptotal),2, '.', '');
			echo  $newtotal;
			
			if ($newtotal!=$invoice->InvoiceTotal) echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx".$invoice->InvoiceNumber;
			
			echo "<br/>";
			//$this->db->where('Invoice', $invoice->InvoiceNumber);
			//$this->db->limit(1);
			//$entry=$this->db->get('zowtrakentries');
			//echo $entry->row()->InvoicePrice."<br/>";
		}
	
		//echo "nothing going on";
	
		//add invoice price to entries
		/*$billedinvoices = $this->db->get('zowtrakinvoices');
		foreach ($billedinvoices->result() as $invoice) {
				$this->db->set('InvoicePrice',$invoice->PricePerHour);
				$this->db->where('Invoice', $invoice->InvoiceNumber);
				$this->db->where('Trash', '0');
				$this->db->update('zowtrakentries');
				echo $this->db->affected_rows()." rows changed<br/>";			
		}
		*/
		//calculate entry total
		//add entrybilledhours
		
		/*$this->db->where('Trash', '0');
		$clientlist=$this->db->get('zowtrakclients');
		foreach ($clientlist->result() as $currentclient) {
			$multiplier=$currentclient->PriceEdits;
			$this->db->where('Client',$currentclient->CompanyName);
			$this->db->where('Trash', '0');
			$entrieslist=$this->db->get('zowtrakentries');
			foreach ($entrieslist->result() as $currententry) {
				$total= $currententry->NewSlides/5;
				$total=$currententry->Hours+$total;
				$total=(($currententry->EditedSlides*$multiplier)/5)+$total;
				//echo $currententry->NewSlides." ".$currententry->EditedSlides." ".$currententry->Hours." ".$multiplier."=".$total."<br/>";
				$this->db->set('InvoiceTime',$total);
				$this->db->where('id', $currententry->id);
				$this->db->update('zowtrakentries');
			}
		*/
		//Calculate price per entry
		$this->db->where('Trash', '0');
		$this->db->where('Invoice !=', 'NOT BILLED');
		$finalentries = $this->db->get('zowtrakentries');
		foreach ($finalentries->result() as $finalentry) {
				$entrytotal=$finalentry->InvoiceTime*$finalentry->InvoicePrice;
				$this->db->set('InvoiceEntryTotal',$entrytotal);
				$this->db->where('id', $finalentry->id);
				$this->db->update('zowtrakentries');
		}
		/**/

echo "TOTAL ".$totaleur."   ".$totalUSD;
		}



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>