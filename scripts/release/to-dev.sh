# -------------------------------------------------------------------
# Script to create a build artifact and deploy it to dev in pantheon.
# -------------------------------------------------------------------

echo "clear out contents of deploy folder"
mkdir deploy
rm -rf deploy/*

echo "clone both projects"
git clone ssh://codeserver.dev.0af75102-c798-434f-b073-f3ddbb82b1c2@codeserver.dev.0af75102-c798-434f-b073-f3ddbb82b1c2.drush.in:2222/~/repository.git deploy/from-pantheon
git clone git@code.osu.edu:umarketing/standard.git deploy/from-ucr-to-pantheon

echo "cd to new repo, swap gits and composer install"
cd deploy/from-ucr-to-pantheon
git checkout master
rm -rf .git
mv ../from-pantheon/.git .git
mv .gitignore-pantheon .gitignore
composer build-assets
cp web/sites/default/default.custom.settings.php web/sites/default/settings.php

echo "Die git folders! Die!"
( find web -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf
( find vendor -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf

echo "commit and push to release repo"
terminus connection:set womensplace-osu.dev git
git add -A
git commit -m "updates merged"
git push

echo "cleanup"
cd ../..
