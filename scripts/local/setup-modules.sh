# Creates git repositories with the latest version of all
# OSU modules in the ignored local site folder.
chmod 755 web/sites/default
cd web/sites/default
mkdir modules
cd modules

git clone git@code.osu.edu:umarketing/analytics.git
git clone git@code.osu.edu:umarketing/core.git
git clone git@code.osu.edu:umarketing/formats.git
git clone git@code.osu.edu:umarketing/media.git

cd ../../../../
ln -s web/sites/default/modules modules

echo ""
echo ""
echo "#########################################################################"
echo "If you see no errors, you have just cloned down the standard"
echo "osu modules (analytics, content, core, formats, media, and standard)"
echo "into 'web/sites/default/modules' where they will take precedence "
echo "over those installed by composer in 'web/modules'. "
echo ""

echo "These versions will often be more current than those installed "
echo "by composer since the composer.lock file pins its versions at"
echo "particular commits. "
echo ""

echo "It also makes it so that you can run 'composer install' "
echo "without overwriting what you are working on. "
echo ""

echo "Note that while this project works with the composer pinned versions,"
echo "it may not work with the latest versions of all reusable modules."
echo "Either way, after updating modules, you might want to run udpates"
echo "or even reinstall."

echo "For convenience, a 'modules' symlink has been created at the "
echo "root of the project pointing to 'web/sites/default/modules'."
echo "#########################################################################"

