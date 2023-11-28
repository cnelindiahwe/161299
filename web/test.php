select * from `cars` where active='1'  && `price` BETWEEN 50000 AND 50000000 
&& `index` IN(
select carid from `car_model` where car_model.`modelid` IN(
125,128,133,146,148,156,165,190,197,290,308,327,335,382,479,480,481,482,515,516,545,575,595,598))
 && cityid = '3' ORDER by `index` desc limit 0,15



 