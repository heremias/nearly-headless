echo "Deploying code from dev to test and syncing content from live"
terminus env:deploy womensplace-osu.test --sync-content

echo "Running any pending database updates"
#terminus drush womensplace-osu.test -- updatedb --yes

echo "Reverting all features"
#terminus drush womensplace-osu.test -- features-import-all --yes

echo "Clear cache"
#terminus drush womensplace-osu.test -- cache-rebuild --yes

echo "Clearing environment cache"
#terminus env:clear-cache womensplace-osu.test

echo "Clear cache again"
#terminus drush womensplace-osu.test -- cache-rebuild --yes

echo "Clearing environment cache again"
#terminus env:clear-cache womensplace-osu.test
