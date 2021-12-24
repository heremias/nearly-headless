mkdir -p /tmp/standard/trustees-osu.live

echo "Rsyncing files from remote to local cache"
terminus rsync trustees-osu.live:files /tmp/standard/trustees-osu.live

echo "Rsyncing files from local cache to local site"
rsync -rtvu --delete /tmp/standard/trustees-osu.live/files/ web/sites/default/files/ > /dev/null

echo "Creating DB backup"
terminus backup:create trustees-osu.live

echo "Fetching DB backup"
rm -f /tmp/trustees.sql.gz
terminus backup:get trustees-osu.live --element=db --to=/tmp/trustees.sql.gz

echo "Restoring backup"
gunzip < /tmp/trustees.sql.gz  | mysql -uroot standard

echo "Cleaning up"
rm -rf trustees.sql.gz

