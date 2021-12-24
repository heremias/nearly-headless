mkdir -p /tmp/standard/snaps/default
cp -rf web/sites/default/files /tmp/standard/snaps/default
mysqldump -uroot standard > /tmp/standard/snaps/default/dump.sql
