cp -rf /tmp/standard/snaps/default/files/* web/sites/default/files/
cat /tmp/standard/snaps/default/dump.sql | mysql -uroot standard
