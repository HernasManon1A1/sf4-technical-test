# StadLine Technical Test

### Specs
* Symfony 4
* MySQL5.7
* Docker
* Guzzle

### Mise en route

* Cloner le projet
* Faire un composer:install
* Faire un **php bin/console d:d:c && php bin/console d:s:c**
* Executer la commande: **docker-compose up -d**
* Se rendre sur http://localhost:8000
* Enjoy

### Debug zone

J'ai une erreur de connexion au serveur MySQL:

Vérifier que les valeurs dans le .env sont les bonnes:

Il y a un exemple dans le .env.dist

Infos docker:

* Port: 8002
* User: root
* Password: root
* Database: test_sf4

Le template part aux fraises?

Pas de panique, il suffit de faire un **yarn run encore production**, et si jamais, un **php bin/console ca:c**

