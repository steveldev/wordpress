# Configuration d'un Wordpress Mutlisite en Sous-Domaine

## WORDPRESS :

### Installation de Wordpress via composer 

#### Configuration du Multisite :
https://wordpress.org/support/article/create-a-network/
- Créer multisite , domain : mydomain.com , document root : public_html/wordpress/multisite/wp
- Créer un site dans le multisite : url : site.mydomain.com


## ENVIRONNEMENT : CPANEL (o2witch) 

### Configuration du domaine mydomain.com

#### Création d'un wildcard pour le domain 
  *.mydomain.com > public_html/wordpress/multisite/wp

### Configuration des Certificats SSL
  https://faq.o2switch.fr/hebergement-mutualise/tutoriels-cpanel/lets-encrypt-certificat-ssl
 
#### Création Certificat SSL Wildcard
  *.mydomain.com *dns-01

#### Certificat SLL <site>.mydomain.com
  Configuration du certificat pour tous les sous domaines
  Par défaut, les sous domaine n'héritent pas du carticat SSL du wildcard
  - Soit : générer un certificat SLL pour chaque sous domaine
  - Soit : installer le certifcat wildcard pour tous les sous domaines dans la configuration SSL/TLS

### Congifuration d'un domaine pontant vers un site du multisite
- external.com >  mydomain.com
- Configuration du site dans le multisite
- Site URL : external.com

### Tests : 
  - Mutlisite : sites.reseau-net.fr
  - Site : tests.sites.reseau-net.fr => test.reseau-net.fr
  - External : tests.reseau-net.fr
