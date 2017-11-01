--
-- Structure de la table `brands`
--

CREATE TABLE IF NOT EXISTS `brands`
(
  `id`         int(11)             NOT NULL AUTO_INCREMENT,
  `marque`     varchar(50)         NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_uq` (`marque`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO brands (marque)
  SELECT distinct(marque) from product order by marque;