header('Content-type: application/force-download');
header('Content-Disposition: attachment; filename="dbbackup.sql.gz"');
passthru("mysqldump --user=xx --host=xx --password=xx dbname | gzip");