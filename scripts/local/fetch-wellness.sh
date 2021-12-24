mkdir -p /tmp/standard/wellness-osu.live

echo "Rsyncing files from remote to local cache"
terminus rsync wellness-osu.live:files /tmp/standard/wellness-osu.live

echo "Rsyncing files from local cache to local site"
rsync -rtvu --delete /tmp/standard/wellness-osu.live/files/ web/sites/default/files/ > /dev/null

echo "Creating DB backup"
terminus backup:create wellness-osu.live

echo "Fetching DB backup"
rm -f /tmp/wellness.sql.gz
terminus backup:get wellness-osu.live --element=db --to=/tmp/wellness.sql.gz

echo "Restoring backup"
gunzip < /tmp/wellness.sql.gz  | mysql -uroot standard

echo "Cleaning up"
rm -rf wellness.sql.gz

