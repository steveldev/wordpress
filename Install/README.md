# Installation

## Manual installation
https://fr.wordpress.org/

## Install with composer
See https://wpackagist.org/

```
mkdir app
cd app
# copy composer.json from https://github.com/steveldev/wordpress/blob/main/Install/composer.json
composer install
```
- Add Symilinks
ll


## Install with SSH
```
mkdir wordpress
cd wordpress
wget https://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
rm latest.tar.gz
mv wordpress wp-dev
```
