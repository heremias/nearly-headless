mkdir -p /tmp/standard/womensplace-osu.live

echo "Rsyncing files from remote to local cache"
terminus rsync womensplace-osu.live:files /tmp/standard/womensplace-osu.live

echo "Rsyncing files from local cache to local site"
rsync -rtvu --delete /tmp/standard/womensplace-osu.live/files/ web/sites/default/files/ > /dev/null

echo "Creating DB backup"
terminus backup:create womensplace-osu.live

echo "Fetching DB backup"
rm -f /tmp/womensplace.sql.gz
terminus backup:get womensplace-osu.live --element=db --to=/tmp/womensplace.sql.gz

echo "Restoring backup"
gunzip < /tmp/womensplace.sql.gz  | mysql -uroot standard

echo "Cleaning up"
rm -rf womensplace.sql.gz

