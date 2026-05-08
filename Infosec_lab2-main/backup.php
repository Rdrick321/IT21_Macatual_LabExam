<?php
$file = "backup_" . date("Y-m-d_H-i-s") . ".sql";

system("mysqldump -u root infosec_lab > backups/$file");

echo "Backup created";
?>