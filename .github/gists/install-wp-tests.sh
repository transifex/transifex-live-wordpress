#!/usr/bin/env bash
## Copied from https://gist.githubusercontent.com/matthewjackowski/3b26061241545564ae8d/raw/install-wp-tests.sh
# This script installs wordpress for phpunit tests and rspec integration tests
##
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
DIR=$(dirname ${DIR})

if [ $# -lt 3 ]; then
echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}

WP_VERSION=${5-latest}

# Use this for installing wordpress siteurl
WP_TEST_URL=${WP_TEST_URL-http://localhost:80}

# Get port from url
WP_PORT=${WP_TEST_URL##*:}

WP_TESTS_DIR=${WP_TESTS_DIR-$DIR/tmp/wordpress-tests-lib/includes}
WP_CORE_DIR=${WP_CORE_DIR-$DIR/wordpress}

# Use these credentials for installing wordpress
# Default test/test
WP_TEST_USER=${WP_TEST_USER-test}
WP_TEST_USER_PASS=${WP_TEST_USER_PASS-test}

set -ex

download() {
  if [ `which curl` ]; then
    curl -s "$1" > "$2";
  elif [ `which wget` ]; then
    wget -nv -O "$2" "$1"
  fi
}

install_wp() {
  if [ -d $WP_CORE_DIR ]; then
    return;
  fi

  mkdir -p $WP_CORE_DIR

  if [ $WP_VERSION == 'latest' ]; then
    local ARCHIVE_NAME='latest'
  else
    local ARCHIVE_NAME="wordpress-$WP_VERSION"
  fi

  download https://wordpress.org/${ARCHIVE_NAME}.tar.gz  /tmp/wordpress.tar.gz
  tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C $WP_CORE_DIR

}

install_test_suite() {
  # portable in-place argument for both GNU sed and Mac OSX sed
  if [[ $(uname -s) == 'Darwin' ]]; then
    local ioption='-i .bak'
  else
    local ioption='-i'
  fi

  # set up testing suite if it doesn't yet exist
  if [ ! "$(ls -A $WP_TESTS_DIR)" ]; then
    # set up testing suite
    mkdir -p $WP_TESTS_DIR
    svn co --quiet http://develop.svn.wordpress.org/trunk/tests/phpunit/includes/ $WP_TESTS_DIR
  fi

  cd $WP_TESTS_DIR

  # Install barebone wp-tests-config.php which is faster for unit tests
  if [ ! -f wp-tests-config.php ]; then
    download https://develop.svn.wordpress.org/trunk/wp-tests-config-sample.php $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
    sed $ioption "s:dirname( __FILE__ ) . '/src/':'$WP_CORE_DIR':" $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
    sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
    sed $ioption "s/yourusernamehere/$DB_USER/" $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
    sed $ioption "s/yourpasswordhere/$DB_PASS/" $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
    sed $ioption "s|localhost|${DB_HOST}|" $(dirname ${WP_TESTS_DIR})/wp-tests-config.php
  fi

  cd $WP_CORE_DIR

  if [ ! -f wp-config.php ]; then
    mv wp-config-sample.php wp-config.php
    sed $ioption "s/database_name_here/$DB_NAME/" $WP_CORE_DIR/wp-config.php
    sed $ioption "s/username_here/$DB_USER/" $WP_CORE_DIR/wp-config.php
    sed $ioption "s/password_here/$DB_PASS/" $WP_CORE_DIR/wp-config.php
    sed $ioption "s|localhost|${DB_HOST}|" $WP_CORE_DIR/wp-config.php
    # Use different prefix for integration tests
    sed $ioption "s|^.*\$table_prefix.*$|\$table_prefix  = 'integ_';|" $WP_CORE_DIR/wp-config.php
  fi
  cd $DIR
}

install_db() {
  # parse DB_HOST for port or socket references
  local PARTS=(${DB_HOST//\:/ })
  local DB_HOSTNAME=${PARTS[0]};
  local DB_SOCK_OR_PORT=${PARTS[1]};
  local EXTRA=""

  if ! [ -z $DB_HOSTNAME ] ; then
    if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
      EXTRA="--host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
    elif ! [ -z $DB_SOCK_OR_PORT ] ; then
      EXTRA="--socket=$DB_SOCK_OR_PORT"
    elif ! [ -z $DB_HOSTNAME ] ; then
      EXTRA="--host=$DB_HOSTNAME --protocol=tcp"
    fi
  fi

  # create database
  mysqladmin create $DB_NAME --user="$DB_USER" --password="$DB_PASS" $EXTRA
}

link_this_project() {
  cd $DIR
  local FOLDER_PATH=$(dirname $DIR/transifex-live-wordpress/transifex-live-integration.php)
  local FOLDER_NAME=$(basename $FOLDER_PATH)
  case $WP_PROJECT_TYPE in
    'plugin' )
        ln -s $FOLDER_PATH $WP_CORE_DIR/wp-content/plugins/$FOLDER_NAME
        php wp-cli.phar plugin activate --all --path=$WP_CORE_DIR
        ;;
    'theme' )
        ln -s $FOLDER_PATH $WP_CORE_DIR/wp-content/themes/$FOLDER_NAME
        php wp-cli.phar theme activate $FOLDER_NAME --path=$WP_CORE_DIR
        ;;
  esac
}

# Install databases with wp-cli
install_real_wp() {
  cd $DIR
  download https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar wp-cli.phar
  echo "$(curl -fsSL https://gist.github.com/matthewjackowski/b20d525757261fb4bc78/raw/wp-cli.yml)" | sudo tee wp-cli.yml > /dev/null
  php wp-cli.phar core install  --url=$WP_TEST_URL --title='Test' --admin_user=$WP_TEST_USER --admin_password=$WP_TEST_USER_PASS --admin_email="$WP_TEST_USER@wordpress.dev" --path=$WP_CORE_DIR
  php wp-cli.phar shell --path="$(pwd)/wordpress" <<< "site_url();"
#  php wp-cli.phar rewrite structure "/%year%/%monthnum%/%day%/%postname%/" --path="$(pwd)/wordpress"
#  php wp-cli.phar rewrite flush --hard --path="$(pwd)/wordpress"
  git clone https://github.com/manovotny/wptest wptest
  php wp-cli.phar plugin install wordpress-importer --activate --path=$WP_CORE_DIR
  cd $WP_CORE_DIR
  curl -OL https://raw.githubusercontent.com/manovotny/wptest/master/wptest.xml
  php $DIR/wp-cli.phar  import wptest.xml --authors=create
  rm wptest.xml
  cd $DIR
}

install_rspec_requirements() {
  gem install bundler
  bundle install --gemfile=$DIR/spec/Gemfile
}

start_server() {
  mv $DIR/lib/router.php $WP_CORE_DIR/router.php
  cd $WP_CORE_DIR
  # Start it in background
  php -S 0.0.0.0:$WP_PORT router.php &
}

install_wp
install_test_suite
install_db
install_real_wp
link_this_project
