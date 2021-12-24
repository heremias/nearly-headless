# -------------------------------------------------------------------
# Script to create a build artifact and deploy it to a separate standard-built repo.
# -------------------------------------------------------------------

# Fail on error
set -e

echo "clear out contents of deploy folder"
mkdir -p deploy
rm -rf deploy/*

echo "clone both projects"
git clone git@code.osu.edu:umarketing/standard-built.git deploy/build
git clone git@code.osu.edu:umarketing/standard.git deploy/source

echo "cd to new repo, swap gits and composer install"
cd deploy/source
git checkout master
rm -rf .git
mv ../build/.git .git
mv .gitignore-pantheon .gitignore

/usr/local/bin/composer install
/usr/local/bin/composer build-assets
cp web/sites/default/default.custom.settings.php web/sites/default/settings.php

echo "remove git subfolders / submodules which pantheon does not like"
( find web -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf
( find vendor -type d -name ".git" && find . -name ".gitmodules" ) | xargs rm -rf

echo "commit and push to release repo"
git add -A
git commit -m "updates merged"
git push

echo "cleanup"
cd ../..
