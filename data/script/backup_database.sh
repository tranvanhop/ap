#!/usr/bin/env bash
now="$(date +'%d_%m_%Y_%H_%M_%S')"
filename="tranvanhop_$now".gz
backupFolder="/var/www/html/tranvanhop/data/database"
fullPathBackupFile="$backupFolder/$filename"
logfile="$backupFolder/"backup_log_"$(date +'%Y_%m')".txt
echo "mysqldump started at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
mysqldump --user=root --password=tranvanhop --default-character-set=utf8 tranvanhop | gzip > "$fullPathBackupFile"
echo "mysqldump finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
chown myuser "$fullPathBackupFile"
chown myuser "$logfile"
echo "file permission changed" >> "$logfile"
find "$backupFolder" -name db_backup_* -mtime +8 -exec rm {} \;
echo "old files deleted" >> "$logfile"
echo "operation finished at $(date +'%d-%m-%Y %H:%M:%S')" >> "$logfile"
echo "*****************" >> "$logfile"
exit 0