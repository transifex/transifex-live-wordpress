#!/bin/bash
echo "$PLUGIN_VERSION"
rm -rf ./build
mkdir ./build
cd build
svn co -q https://plugins.svn.wordpress.org/transifex-live-integration
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
cp -rf ../../includes ./trunk/
cp -rf ../../javascript ./trunk/
cp -rf ../../languages ./trunk/
cp -rf ../../stylesheets ./trunk/

touch ./trunk/$PLUGIN_VERSION.txt

cp -r ./trunk ./tags/$PLUGIN_VERSION


rm -f ./assets/*
cp -rf ../../assets ./
svn add --force .
svn st | grep ^! | awk '{print " --force "$2}' | xargs svn rm
svn status --show-updates
svn commit -m "Version $PLUGIN_VERSION Released" --username $SVN_USERNAME --password $SVN_PASSWORD --no-auth-cache
