select TimeZOneOut,HOUR(TimeIn), count('ID')
  from zowtrakentries
group by  TimeZOneOut,HOUR(TimeIn)