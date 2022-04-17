# Wordpress Mutlisite (sous-domaine)

## PROJET :
- Installation d'un site Wordpress en Multisite (sous domaine).
- Les sites doivent étre accessibles depuis un nom de domaine externe.
- Le nom de domaine externe doit remplacer l'url du site dans le multisite.
- La navigation doit rester transparente pour le visiteur.

## WORDPRESS :

### Installation de Wordpress via composer 
- Création du dossier public_html/wordpress/multisite/
- Copy composer.json from https://github.com/steveldev/wordpress/blob/main/Install/
- Run composer install

#### Configuration du Multisite :
https://github.com/steveldev/wordpress/tree/main/Multisite
- Créer multisite 
  - Nom de domaine : mydomain.com , 
  - Document root  : public_html/wordpress/multisite/wp
  - Mode           : sous domaine
- Créer un site dans le multisite : 
  - Site url : site.mydomain.com


## ENVIRONNEMENT (mydomain.com) : 

### Configuration du domaine mydomain.com

#### Création d'un wildcard pour le domaine
  - Nom de domaine : *.mydomain.com 
  - Document root  : public_html/wordpress/multisite/wp

### Configuration des Certificats SSL
  CPANEL (o2witch) : https://faq.o2switch.fr/hebergement-mutualise/tutoriels-cpanel/lets-encrypt-certificat-ssl  
  
 #### Certificat SSL mydomain.com 
  - Domaine : mydomain.com 
  - Option  : http-01
 
#### Certificat SSL Wildcard
  - Domaine : *.mydomain.com 
  - Option  : dns-01

#### Certificat SLL Wildcard *.mydomain.com
  Par défaut, les sous domaine n'héritent pas du certificat SSL du wildcard  
  Configuration du certificat pour tous les sous domaines
  - Soit : générer un certificat SLL pour chaque sous domaine
  - Soit : installer le certifcat wildcard pour tous les sous domaines dans la configuration SSL/TLS


## ENVIRONNEMENT (external.com) : 
 
### Configuration du nom de domaine 
- Nom de domaine : external.com 
- DNS CNAME      : **IP Mutltisite Wordpress (mydomain.com)**
 
 
## WORDPRESS : 
### Modification du site dans le multisite
- Site URL : external.com


# Documentation
## Create network
https://wordpress.org/support/article/create-a-network/  
https://wordpress.org/support/article/before-you-create-a-network/

## Domain mapping 
https://wordpress.org/support/article/wordpress-multisite-domain-mapping/

## Wildcard 
https://wordpress.org/support/article/configuring-wildcard-subdomains/

## Switch Multisite Configuration :  
https://wordpress.org/support/article/multisite-network-administration/#switching-network-types
