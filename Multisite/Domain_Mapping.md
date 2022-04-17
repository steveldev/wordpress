# Configuration d'un Wordpress Mutlisite 

## PROJET :
- Installation d'un site Wordpress en Multisite (sous domaine).
- Les sites doivent étre accessibles depuis un nom de domaine externe.
- Le nom de domaine externe doit remplacer l'url du site dans le multisite.
- La navigation doit rester transparente pour le visiteur.

## WORDPRESS :

### Installation de Wordpress via composer 
https://github.com/steveldev/wordpress/blob/main/Install/
- Copy composer.json
- Run composer install

#### Configuration du Multisite :
https://github.com/steveldev/wordpress/tree/main/Multisite#readme
- Créer multisite 
  - Nom de domaine : mydomain.com , 
  - Document root  : public_html/wordpress/multisite/wp
  - Mode           : sous domaine
- Créer un site dans le multisite : 
  - Site url : site.mydomain.com


## ENVIRONNEMENT : CPANEL (o2witch) 

### Configuration du domaine mydomain.com

#### Création d'un wildcard pour le domaine
  - Nom de domaine : *.mydomain.com 
  - Document root  : public_html/wordpress/multisite/wp

### Configuration des Certificats SSL
  https://faq.o2switch.fr/hebergement-mutualise/tutoriels-cpanel/lets-encrypt-certificat-ssl
  
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

### Congifuration d'un domaine pontant vers un site du multisite
 
#### Configuration du nom de domaine 
- Nom de domaine : external.com 
- DNS CNAME  :   IP Mutltisite Wordpress
 
#### Configuration du site dans le multisite
- Site URL : external.com
