# Creates git repositories with the latest version of all
# OSU modules in the ignored local site folder.
chmod 755 web/sites/default
cd web/sites/default
mkdir themes
cd themes

git clone git@code.osu.edu:umarketing/standard_patterns.git
cd standard_patterns
composer install
yarn install
gulp compile
gulp build

cd ../../../../../
ln -s web/sites/default/themes/standard_patterns theme

echo ""
echo ""
echo "#########################################################################"
echo "If you see no errors, you have just cloned down the standard_patterns theme"
echo "into 'web/sites/default/themes' where it will take precedence "
echo "over the version installed by composer in 'web/themes'. "
echo ""

echo "These version may be more current than that installed "
echo "by composer since the composer.lock file pins its versions at"
echo "particular commits. "
echo ""

echo "It also makes it so that you can run 'composer install' "
echo "without overwriting what you are working on. "
echo ""

echo "For convenience, a 'theme' symlink has been created at the "
echo "root of the project pointing to 'web/sites/default/themes/standard_patterns'."
echo ""

echo "The theme is built but not \"watched\""
echo "To watch the theme, run \"cd theme; yarn start\""
echo "#########################################################################"

