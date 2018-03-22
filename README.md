# StadLine Technical Test

## Specs
* Symfony 4
* MySQL5.7
* Docker
* Guzzle

## Mise en route

* Cloner le projet
* Executer la commande: **docker-compose up -d**
* Faire un **docker exec -ti test-sf4-php-fpm composer install**
* Faire un **docker exec -ti test-sf4-php-fpm php bin/console doctrine:database:create**
* Faire un **docker exec -ti test-sf4-php-fpm php bin/console doctrine:schema:create**
* Se rendre sur http://localhost:8000
* Enjoy

## Debug zone

###J'ai une erreur de connexion au serveur MySQL
Vérifier que les valeurs dans le .env sont les bonnes:
Il y a un exemple dans le .env.dist

Infos docker:
* Port: 8002
* User: root
* Password: root
* Database: test_sf4

Pour connaitre l'adresse IP du container docker il faut:
* Faire un **docker inspect test-sf4-mysql** et récupérer l'IP dans Networks > IPAddress
  

###Le template part aux fraises?

Pas de panique, il suffit de faire un **yarn run encore production**, et si jamais, un **php bin/console ca:c**

## Commentaires

Je sais pas combien de temps j'ai passé sur le sujet mais j'ai sûrement dépassé les 4h (en ne comptant pas les pauses, le boulot et les imprévus etc,)

J'ai fais ça calmement et je voulais proposer quelque chose de propre (même s'il n'est pas 100% parfait, il y a du nettoyage à faire)

Le sujet est intéressant et je me suis permis quelques libertés comme l'ajout des informations de chaque utilisateurs.

Au niveau de mes choix techniques, notamment de recréer un système de connexion à la main était vraiment chronophage (je regrette le FOSUserBundle)

J'ai utilisé des snippets Bootstrap, j'aurais pu utiliser des form_themes (oops), mais avouez que c'est bô les particules !

J'ai eu un problème de certificat SSL local avec Guzzle, que j'ai bypass en mettant le verify à false au niveau des options (pourtant mes certificats étaient bien mis dans mon php.ini :( )

J'avais commit des fichiers propre à Phpstorm que j'ai supprimé par la suite (ou presque tout)

L'API Github etant limité dans le nombre de requêtes, j'ai dû mettre en place des données bouchons, et c'est pas très très propre (sorry)


Si jamais vous avez des questions : hernas.manon@gmail.com


PS: il y a un petit easter egg ;)
