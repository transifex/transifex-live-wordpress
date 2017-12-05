#!/bin/bash
echo "$PLUGIN_VERSION"
rm -rf ./build
mkdir ./build
cd build
svn co https://plugins.svn.wordpress.org/transifex-live-integration
cd ./transifex-live-integration
rm -f ./trunk/*
# copy files
cp ../../index.php ./trunk
cp ../../LICENSE.txt ./trunk
cp ../../readme.txt ./trunk
cp ../../transifex-live-integration.php ./trunk
cp ../../transifex-live-integration-main.php ./trunk
cp ../../uninstall.php ./trunk
# copy dirs
rm -f ./trunk/includes/*
rm -f ./trunk/includes/admin/*
cp -rf ../../includes ./trunk/
rm -f ./trunk/javascript/*
cp -rf ../../javascript ./trunk/
rm -f ./trunk/languages/*
cp -rf ../../languages ./trunk/
rm -f ./trunk/stylesheets/*
cp -rf ../../stylesheets ./trunk/
touch ./trunk/$PLUGIN_VERSION.txt

mkdir ./tags/$PLUGIN_VERSION
cp ../../index.php ./tags/$PLUGIN_VERSION
cp ../../LICENSE.txt ./tags/$PLUGIN_VERSION
cp ../../readme.txt ./tags/$PLUGIN_VERSION
cp ../../transifex-live-integration.php ./tags/$PLUGIN_VERSION
cp ../../transifex-live-integration-main.php ./tags/$PLUGIN_VERSION
cp ../../uninstall.php ./tags/$PLUGIN_VERSION

cp -rf ../../includes ./tags/$PLUGIN_VERSION
cp -rf ../../javascript ./tags/$PLUGIN_VERSION
cp -rf ../../languages ./tags/$PLUGIN_VERSION
cp -rf ../../stylesheets ./tags/$PLUGIN_VERSION

rm -f ./assets/*
cp -rf ../../assets ./
sudo apt-get install tree
tree .
svn add --force .
svn st | grep ^! | awk '{print " --force "$2}' | xargs svn rm
svn status --show-updates
svn commit -m "Version $PLUGIN_VERSION Released" --username $SVN_USERNAME --password $SVN_PASSWORD --no-auth-cache
