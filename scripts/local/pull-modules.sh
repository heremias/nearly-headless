# Composer installs specific versions of our OSU modules
# but not necessarily the latest version of master.
# These commands will pull the latest version of modules.

echo "Updating web/sites/default/modules/analytics"
git -C web/sites/default/modules/analytics pull

echo "Updating web/sites/default/modules/core"
git -C web/sites/default/modules/core pull

echo "Updating web/sites/default/modules/formats"
git -C web/sites/default/modules/formats pull

echo "Updating web/sites/default/modules/media"
git -C web/sites/default/modules/media pull

echo "Updating web/sites/default/modules/standard"
git -C web/sites/default/modules/standard pull

echo ""
echo ""
echo "#########################################################################"
echo "If you see no errors, you have just pulled down the standard"
echo "osu modules (analytics, content, core, formats, media, and standard)"
echo "into 'web/sites/default/modules' where they will take precedence "
echo "over those installed by composer in 'web/modules'. "
echo ""

echo "Note that while this project works with the composer pinned versions,"
echo "it may not work with the latest versions of all reusable modules."
echo "Either way, after updating modules, you might want to run udpates"
echo "or even reinstall."
echo ""

echo "If you see a bunch of errors, maybe you need to run this first."
echo "./scripts/local/setup-modules.sh"
echo "#########################################################################"
