# Wordpress Multisite 

## Install Wordpress
https://github.com/steveldev/wordpress/tree/main/Install

## Create network 

### Allow multisite
File : wp-config.php  
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


### SubFolder
File : wp-config.php
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


File : .htaccess
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

### SubDomain
File : wp-config.php
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

File : .htaccess
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

## Create network
https://wordpress.org/support/article/create-a-network/  
https://wordpress.org/support/article/before-you-create-a-network/

## Domain mapping 
https://github.com/steveldev/wordpress/blob/main/Multisite/Domain_Mapping.md  
https://wordpress.org/support/article/wordpress-multisite-domain-mapping/

# Wildcard 
https://wordpress.org/support/article/configuring-wildcard-subdomains/

## Switch Multisite Configuration :  
https://wordpress.org/support/article/multisite-network-administration/#switching-network-types
