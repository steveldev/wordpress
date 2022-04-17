#Wordpress Multisite 

## Install Wordpress
https://github.com/steveldev/wordpress/tree/main/Install

## Create network 
https://fr.wordpress.org/support/article/create-a-network/

### Allow multisite
In wp-config.php  
- Add define('WP_ALLOW_MULTISITE', true);  

### Create Network
Refresh admin dashboard page ./wp-admin/
- Menu Outils > Création du réseau
- Edit wp-config.php
- Edit .htaccess
- Logout 

### Create site
- login as super admin
- Create new site


## Configurations 
This var set subdonaim or subfolder : define( 'SUBDOMAIN_INSTALL', true );

### SubDomain
**File : wp-config.php
```
/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );

define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', true );
define( 'DOMAIN_CURRENT_SITE', 'mydomain.fr' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
```

**File : .htaccess
```
# BEGIN WordPress
RewriteEngine On
RewriteBase /
RewriteRule ^index.php$ - [L]
 
# add a trailing slash to /wp-admin
RewriteRule ^wp-admin$ wp-admin/ [R=301,L]
 
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^(wp-(content|admin|includes).*) $1 [L]
RewriteRule ^(.*\.php)$ wp/$1 [L]
RewriteRule . index.php [L]
# END WordPress
```

### SubFolder
**File : wp-config.php
define( 'SUBDOMAIN_INSTALL', false );

```
/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );

define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'mydomain.fr' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
```


**File : .htaccess
```
# BEGIN WordPress
RewriteEngine On
RewriteBase /
RewriteRule ^index.php$ - [L]
 
# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]
 
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
# END WordPress
```





## More 
Swith Multisite Configuration :  
https://wordpress.org/support/article/multisite-network-administration/#switching-network-types
