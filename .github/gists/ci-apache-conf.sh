# Copied from https://gist.github.com/matthewjackowski/b772ab278efb0e6f30ad/raw/travisci-apache
<VirtualHost *:80>
  ServerName wptest.localhost
  DocumentRoot %TRAVIS_BUILD_DIR%

  <Directory "%TRAVIS_BUILD_DIR%">
    Options FollowSymLinks MultiViews ExecCGI
    AllowOverride All
    Order deny,allow
    Allow from all
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_URI} ^/usr/lib/cgi-bin/php5-fcgi(.*)
    RewriteRule . - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
  </Directory>
  <IfModule mod_fastcgi.c>
    AddHandler php5-fcgi .php
    Action php5-fcgi /php5-fcgi
    Alias /php5-fcgi /usr/lib/cgi-bin/php5-fcgi
    FastCgiExternalServer /usr/lib/cgi-bin/php5-fcgi -host 127.0.0.1:9000 -pass-header Authorization
  </IfModule>

</VirtualHost>
