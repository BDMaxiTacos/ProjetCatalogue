#Installation du projet

##Url du répertoire de base du projet

[GitLab](https://gitlab.com/TristanJ21/projectproductssymfony)

Ce projet a été créé pour ma formation sur Symfony

##Vérifier que ces extensions php sont installées

Avec `php -m` 
- calendar 
- Core 
- ctype 
- date 
- dom 
- exif 
- FFI 
- fileinfo 
- filter 
- ftp
- gd 
- gettext 
- hash 
- iconv 
- intl 
- json 
- libxml 
- mbstring 
- mysqli 
- mysqlnd 
- openssl 
- pcntl 
- pcre
- PDO 
- pdo_mysql 
- Phar 
- posix 
- readline 
- Reflection 
- session 
- shmop 
- SimpleXML 
- sockets 
- sodium 
- SPL 
- standard 
- sysvmsg 
- sysvsem 
- sysvshm 
- tokenizer 
- xml 
- xmlreader 
- xmlwriter 
- xsl 
- Zend OPcache 
- zip 
- zlib

##Copier le fichier .env dans un fichier .env.local

Remplir ces champs du fichier copié .env.local :

- SENDINDBLUE_MAIL 
- SENDINBLUE_KEY 
- DB_USR 
- DB_PWD

Si des identifiants sendinblue sont nécessaires, il faudra m'envoyer un mail sur 
[tristan.jacquemard@etu.univ-lyon1.fr](mailto:tristan.jacquemard@etu.univ-lyon1.fr)

##Installer les dépendences de projets avec Composer

Avec `composer install`

##Mettre en place la base de données

Avec `php bin/console doctrine:database:create`

##Envoyer les migrations (structurer la bdd)

Avec `php bin/console doctrine:migrations:migrate --all-or-nothing ALL`

##Vous pouvez maintenant démarrer le serveur

Avec `symfony serve` ou `symfony server:start`

##Le site est maintenant actif

Vous pouvez vous inscrire depuis le menu en haut de page, il est aussi possible de se connecter en admin grâce à ces identifiants: 
username: "admin"
password: "admin"
