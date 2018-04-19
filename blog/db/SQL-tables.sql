/*--------------------------------------
------Création de la base donnée------
--------------------------------------*/

CREATE DATABASE IF NOT EXISTS blog CHARACTER SET 'utf8';
USE blog;

/*-----------------------------------------------------
------Création des tables et de leurs attributs------
-----------------------------------------------------*/

CREATE TABLE Person (
                idPerson INT AUTO_INCREMENT NOT NULL,
                personPseudo VARCHAR(100) NOT NULL,
                personMail VARCHAR(100) NOT NULL,
                personPass VARCHAR(100) NOT NULL,
                personRole VARCHAR(100) DEFAULT 'registred' NOT NULL,
                PRIMARY KEY (idPerson)
)
ENGINE=INNODB;

CREATE TABLE Post (
                idPost INT AUTO_INCREMENT NOT NULL,
                postTitle VARCHAR(100) NOT NULL,
                postChapo VARCHAR(100),
                postContent VARCHAR(100),
                postTag VARCHAR(100),
                postCreation DATETIME NOT NULL,
                postUpdate DATETIME NOT NULL,
                postStatus BOOLEAN NOT NULL,
                idPerson INT NOT NULL,
                PRIMARY KEY (idPost)
)
ENGINE=INNODB;

CREATE TABLE Comment (
                idComment INT AUTO_INCREMENT NOT NULL,
                commentContent VARCHAR(100) NOT NULL,
                commentCreation DATETIME NOT NULL,
                commentStatus BOOLEAN NOT NULL,
                idPost INT NOT NULL,
                idPerson INT NOT NULL,
                PRIMARY KEY (idComment)
)
ENGINE=INNODB;

/*-------------------------------------------
---Création des clés étrangères et index----
-----------------------------------------*/

ALTER TABLE Person ADD UNIQUE(personMail);

ALTER TABLE Post ADD CONSTRAINT person_post_fk
FOREIGN KEY (idPerson)
REFERENCES person (idPerson)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE Comment ADD CONSTRAINT person_comment_fk
FOREIGN KEY (idPerson)
REFERENCES person (idPerson)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE Comment ADD CONSTRAINT post_comment_fk
FOREIGN KEY (idPost)
REFERENCES Post (idPost)
ON DELETE NO ACTION
ON UPDATE NO ACTION;