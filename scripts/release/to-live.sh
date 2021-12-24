echo "Making a backup of code/db/files"
terminus backup:create womensplace-osu.live --element=all --keep-for=7

echo "Deploying code from test to live"
terminus env:deploy womensplace-osu.live

echo "Running any pending database updates"
terminus drush womensplace-osu.live -- updatedb --yes

echo "Reverting all features"
terminus drush womensplace-osu.live -- features-import-all --yes

echo "Clear cache"
terminus drush womensplace-osu.live -- cache-rebuild --yes

echo "Clearing environment cache"
terminus env:clear-cache womensplace-osu.live

echo "Clear cache again"
terminus drush womensplace-osu.live -- cache-rebuild --yes

echo "Clearing environment cache again"
terminus env:clear-cache womensplace-osu.live
