select TimeZOneOut,HOUR(TimeOut), sum(InvoiceTime)
  from zowtrakentries
  where Invoice!='NOT BILLED'
group by  TimeZOneOut,HOUR(TimeOut)