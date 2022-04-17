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


## More 
Swith Multisite Configuration :  
https://wordpress.org/support/article/multisite-network-administration/#switching-network-types
