-- DÉSACTIVER LES CLÉS ÉTRANGÈRES pour le nettoyage
SET FOREIGN_KEY_CHECKS = 0;

-- NETTOYAGE COMPLET (Supprime les tables si elles existent pour recréer proprement)
DROP TABLE IF EXISTS `history`;
DROP TABLE IF EXISTS `x_playlist_song`;
DROP TABLE IF EXISTS `playlist`;
DROP TABLE IF EXISTS `song`;
DROP TABLE IF EXISTS `album`;
DROP TABLE IF EXISTS `artist`;

-- --------------------------------------------------------
-- 1. Table structure for table `artist`
-- AJOUT: Colonne 'is_liked'
-- --------------------------------------------------------
CREATE TABLE `artist`
(
    `id`                int          NOT NULL AUTO_INCREMENT,
    `name`              varchar(255) NOT NULL,
    `biography`         mediumtext,
    `cover`             mediumtext,
    `monthly_listeners` int                   DEFAULT '0',
    `is_liked`          tinyint(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 2. Table structure for table `album`
-- AJOUT: Colonne 'is_liked'
-- --------------------------------------------------------
CREATE TABLE `album`
(
    `id`           int          NOT NULL AUTO_INCREMENT,
    `name`         varchar(255) NOT NULL,
    `artist_id`    int          NOT NULL,
    `cover`        mediumtext,
    `release_date` datetime              DEFAULT NULL,
    `is_liked`     tinyint(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`),
    KEY `artist_id` (`artist_id`),
    CONSTRAINT `album_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 3. Table structure for table `song`
-- (Déjà existant: is_liked)
-- --------------------------------------------------------
CREATE TABLE `song`
(
    `id`        int          NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `artist_id` int          NOT NULL,
    `album_id`  int          NOT NULL,
    `duration`  int          NOT NULL,
    `note`      double       NOT NULL DEFAULT '0',
    `is_liked`  tinyint(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`),
    KEY `album_id` (`album_id`),
    KEY `artist_id` (`artist_id`),
    CONSTRAINT `song_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`),
    CONSTRAINT `song_ibfk_2` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 4. Table structure for table `playlist`
-- --------------------------------------------------------
CREATE TABLE `playlist`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `name`        varchar(20)  NOT NULL,
    `description` varchar(200) NULL,
    `status`      varchar(20)  NOT NULL DEFAULT 'private',
    `duration`    int          NOT NULL DEFAULT 0,
    `nb_song`     int          NOT NULL DEFAULT 0,
    `nb_album`    int          NOT NULL DEFAULT 0,
    `nb_artist`   int          NOT NULL DEFAULT 0,
    `nb_playlist` int          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 5. Table structure for table `x_playlist_song`
-- --------------------------------------------------------
CREATE TABLE `x_playlist_song`
(
    `id`          int NOT NULL AUTO_INCREMENT,
    `song_id`     int NOT NULL,
    `playlist_id` int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`),
    KEY `song_id` (`song_id`),
    KEY `playlist_id` (`playlist_id`),
    CONSTRAINT `xps_fk_1` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE,
    CONSTRAINT `xps_fk2` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 6. NOUVELLE TABLE: `history`
-- Pour gérer l'historique d'écoute
-- --------------------------------------------------------
CREATE TABLE `history`
(
    `id`        int NOT NULL AUTO_INCREMENT,
    `song_id`   int NOT NULL,
    `played_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `song_id` (`song_id`),
    CONSTRAINT `history_fk_song` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- 1. ARTISTES (32 entrées)
-- --------------------------------------------------------
INSERT INTO artist (id, name, biography, cover, monthly_listeners)
VALUES (1, 'Stromae',
        'Auteur-compositeur et interprète belge. Célèbre pour son mélange unique de musique électronique et de chanson française, explorant des thèmes sociaux et personnels.',
        'https://i.scdn.co/image/ab6761610000e5ebcc129f1c9b5047760d66aae2', 15500000),
       (2, 'Angèle',
        'Chanteuse, musicienne et icône de la pop belge francophone, reconnue pour ses textes engagés et son style léger.',
        'https://i.scdn.co/image/ab6761610000e5eb2aefdc0f42eef75a60b341dd', 8200000),
       (3, 'Orelsan',
        'Rappeur, compositeur et réalisateur français. Maître des chroniques sociales et des punchlines acerbes.',
        'https://i.scdn.co/image/ab6761610000e5eb526b24da7730a40ec6dba3b9', 9800000),
       (4, 'Clara Luciani',
        'Chanteuse française au style inspiré du disco et de la chanson française des années 70. Voix grave et mélodies entraînantes.',
        'https://i.scdn.co/image/ab6761610000e5eb7a5ef741d8389396869345df', 3500000),
       (5, 'PNL',
        'Duo de rap français composé des frères Ademo et N.O.S. Connus pour leur indépendance et leur esthétique mélancolique.',
        'https://i.scdn.co/image/ab6761610000e5eb51767bb5e3e3add66699344b', 4100000),
       (6, 'Aya Nakamura',
        'Artiste française la plus écoutée au monde, reine de l\'Afropop avec des titres incontournables.',
        'https://i.scdn.co/image/ab6761610000e5eb1b1d1a60a2e95efc4eea8259', 6300000),
       (7, 'Juliette Armanet',
        'Pop élégante française, souvent comparée aux grandes chanteuses des années 70, avec un penchant pour le piano.',
        'https://i.scdn.co/image/ab6761610000e5eb3869679fca4ed2840ceecb41', 1200000),
       (8, 'Dua Lipa',
        'Artiste pop anglaise d\'origine albanaise, célèbre pour son style disco-pop et ses succès mondiaux.',
        'https://i.scdn.co/image/ab6761610000e5eb0c68f6c95232e716f0abee8d', 55000000),
       (9, 'The Weeknd',
        'Artiste canadien R&B/Pop. Maître des ambiances sombres et des mélodies entêtantes, détenteur de records d\'écoute.',
        'https://i.scdn.co/image/ab6761610000e5eb9e528993a2820267b97f6aae', 90000000),
       (10, 'Doja Cat',
        'Rappeuse, chanteuse et compositrice américaine. Appréciée pour sa polyvalence stylistique et sa présence en ligne.',
        'https://i.scdn.co/image/ab6761610000e5eb8a0644455ebfa7d3976f5101', 40000000),
       (11, 'Booba',
        'Rappeur français, figure historique et controversée du hip-hop français. Connu pour son style agressif et son influence durable.',
        'https://i.scdn.co/image/ab67616d00001e02501811bc5051303e435bbeed', 6800000),
       (12, 'Queen',
        'Groupe de rock britannique légendaire, mené par Freddie Mercury. Célébré pour son éclectisme et ses performances scéniques.',
        'https://i.scdn.co/image/af2b8e57f6d7b5d43a616bd1e27ba552cd8bfd42', 39000000),
       (13, 'Ed Sheeran',
        'Auteur-compositeur-interprète britannique. Un des artistes les plus populaires du 21ème siècle, connu pour ses ballades et chansons pop.',
        'https://i.scdn.co/image/ab6761610000e5ebd55c95ad400aed87da52daec', 80000000),
       (14, 'Taylor Swift',
        'Artiste pop/country américaine. Phénomène mondial, célèbre pour ses récits personnels et ses changements de genre musical.',
        'https://i.scdn.co/image/ab677762000056b803e3b179489530d2ded95f4e', 105000000),
       (15, 'Damso', 'Rappeur belge d\'origine congolaise. Apprécié pour ses textes sombres, poétiques et techniques.',
        'https://image-cdn-ak.spotifycdn.com/image/ab67706c0000da84b57ba06d443d8758694a90a5', 5100000),
       (16, 'Kendrick Lamar',
        'Rappeur américain, considéré comme l\'un des plus grands de sa génération. Connu pour ses albums conceptuels et sa narration complexe.',
        'https://i.scdn.co/image/ab6761610000e5eb39ba6dcd4355c03de0b50918', 35000000),
       (17, 'Lomepal',
        'Rappeur et chanteur français, connu pour ses textes introspectifs et mélancoliques sur fond d\'influences rock.',
        'https://i.scdn.co/image/ab6761610000e5eb08a91c70b5af4a856d9ea2d0', 3200000),
       (18, 'Billie Eilish',
        'Chanteuse et auteure-compositrice américaine. S\'est fait connaître par son style pop alternatif sombre et minimaliste.',
        'https://i.scdn.co/image/ab676161000051744a21b4760d2ecb7b0dcdc8da', 60000000),
       (19, 'AC/DC',
        'Groupe de hard rock australo-britannique, un des pionniers du genre. Célèbres pour leurs riffs énergiques et constants.',
        'https://i.scdn.co/image/ab6761610000e5ebc4c77549095c86acb4e77b37', 25000000),
       (20, 'Gims',
        'Chanteur, rappeur et compositeur congolais naturalisé français. Ancien membre de Sexion d\'Assaut, il a une carrière solo à succès dans la pop urbaine.',
        'https://i.scdn.co/image/ab6761610000e5eb77144f838397a467e807df65', 4500000),
       (21, 'Rammstein',
        'Groupe de metal industriel allemand. Réputé pour ses spectacles pyrotechniques et ses textes controversés en allemand.',
        'https://i.scdn.co/image/ab6761610000e5eb32845b1556f9dbdfe8ee6575', 8000000),
       (22, 'Céline Dion',
        'Chanteuse canadienne, l\'une des plus grandes voix de l\'histoire de la musique. Connue pour ses ballades puissantes et sa carrière internationale.',
        'https://i.scdn.co/image/ab6761610000e5ebc3b380448158e7b6e5863cde', 9000000),
       (23, 'Coldplay',
        'Groupe de rock alternatif britannique. Célèbre pour ses hymnes pop-rock et ses concerts spectaculaires.',
        'https://i.scdn.co/image/ab6761610000e5eb1ba8fc5f5c73e7e9313cc6eb', 70000000),
       (24, 'Ninho',
        'Rappeur français, connu pour sa productivité et sa place dominante dans le rap game français. Spécialisé dans le drill et le trap.',
        'https://i.scdn.co/image/ab676161000051747844053470fa0a138eec6662', 8900000),
       (25, 'Adele',
        'Chanteuse britannique, connue pour sa voix puissante et ses succès mondiaux de ballades soul et pop.',
        'https://i.scdn.co/image/ab6761610000e5eb68f6e5892075d7f22615bd17', 45000000),
       (26, 'Imagine Dragons',
        'Groupe de rock américain qui mélange rock alternatif et pop. Célèbre pour ses hymnes explosifs.',
        'https://i.scdn.co/image/ab6761610000e5ebab47d8dae2b24f5afe7f9d38', 62000000),
       (27, 'Louane', 'Chanteuse et actrice française, révélée par "The Voice". Pop française moderne et émotionnelle.',
        'https://i.scdn.co/image/ab6761610000e5eb4555b8b58c6fc18af085ea53', 2300000),
       (28, 'Mylène Farmer',
        'Chanteuse, auteur et productrice française. Icône de la pop francophone, connue pour son univers sombre et théâtral.',
        'https://i.scdn.co/image/ab6761610000e5eb4f8558dab91ff967bfa4532c', 1500000),
       (29, 'Julien Doré',
        'Auteur-compositeur-interprète français. Apprécié pour son style décalé et ses ballades pop douces.',
        'https://i.scdn.co/image/ab6761610000e5eba7475b5895e9a1d3800d60e4', 2800000),
       (30, 'Vianney',
        'Auteur-compositeur-interprète français. Chansons simples, optimistes et entraînantes à la guitare acoustique.',
        'https://i.scdn.co/image/ab6761610000e5ebe1034a8e3787d61805792b2e', 2500000),
       (31, 'Sexion d\'Assaut',
        'Collectif de rap français influent des années 2010. Connu pour leurs textes et leurs refrains accrocheurs.',
        'https://i.scdn.co/image/ab6761610000e5ebe5380469a579bd4fc105899f', 3800000),
       (32, 'Daft Punk',
        'Duo de musique électronique français, pionniers de la French Touch. Connus pour leurs casques de robots et leur style disco-funk futuriste.',
        'https://i.scdn.co/image/ab6761610000e5ebd3aa7cc0e419b6c459b08e8e', 18000000);

-- --------------------------------------------------------
-- 2. ALBUMS (74 entrées)
-- --------------------------------------------------------
INSERT INTO album (id, name, artist_id, cover, release_date)
VALUES
-- Stromae (ID 1)
(1, 'Racine Carrée', 1,
 'https://i.scdn.co/image/ab67616d0000b2738a6bb98baff4c9b15f9972fc',
 '2013-08-16 00:00:00'),
(2, 'Multitude', 1,
 'https://i.scdn.co/image/ab67616d0000b2736af3a93773dcb59a67ab2953',
 '2022-03-04 00:00:00'),
(3, 'Cheese', 1,
 'https://i.scdn.co/image/ab67616d0000b273ae373174b14e3eac81554402',
 '2010-06-14 00:00:00'),

-- Angèle (ID 2)
(4, 'Brol', 2,
 'https://i.scdn.co/image/ab67616d0000b2732433cb43f0f2f0f23b7c8b82',
 '2018-10-05 00:00:00'),
(5, 'Nonante-Cinq', 2,
 'https://i.scdn.co/image/ab67616d0000b27392dc99b0795e0b8471780217',
 '2021-12-03 00:00:00'),

-- Orelsan (ID 3)
(6, 'Civilisation', 3,
 'https://i.scdn.co/image/ab67616d0000b27358ba1ea637001f9a15e55a92',
 '2021-11-19 00:00:00'),
(7, 'La Fête est Finie', 3,
 'https://i.scdn.co/image/ab67616d0000b2737e41808b163893160ccf414b',
 '2017-10-20 00:00:00'),
(8, 'Le Chant des Sirènes', 3,
 'https://i.scdn.co/image/ab67616d0000b27308c348d65e72ef5b47f1942b',
 '2011-09-26 00:00:00'),

-- Clara Luciani (ID 4)
(9, 'Sainte-Victoire', 4,
 'https://i.scdn.co/image/ab67616d0000b27333eb338e04b52de841a25f35',
 '2018-04-06 00:00:00'),
(10, 'Cœur', 4,
 'https://i.scdn.co/image/ab67616d0000b273c94a5cc2c56d5c0e7e2f0bf6',
 '2021-06-11 00:00:00'),

-- PNL (ID 5)
(11, 'Deux frères', 5,
 'https://i.scdn.co/image/ab67616d0000b27357c87959e63d634cd5128e36',
 '2019-04-05 00:00:00'),
(12, 'Dans la légende', 5,
 'https://i.scdn.co/image/ab67616d0000b2738ebfb654b1d20182942bd131',
 '2016-09-16 00:00:00'),

-- Aya Nakamura (ID 6)
(13, 'Aya', 6,
 'https://i.scdn.co/image/ab67616d0000b273e18ece75007e1aa0504e664a',
 '2020-11-13 00:00:00'),
(14, 'Nakamura', 6,
 'https://i.scdn.co/image/ab67616d0000b273a5461b593da03ab9ef61c99b',
 '2018-11-02 00:00:00'),

-- Juliette Armanet (ID 7)
(15, 'Brûler le feu', 7,
 'https://i.scdn.co/image/ab67616d0000b273c4dc96ff4ce16154d4e92085',
 '2021-11-19 00:00:00'),
(16, 'Petite Amie', 7,
 'https://i.scdn.co/image/ab67616d00001e02051bfefe55d576e5e8c04c26',
 '2017-04-07 00:00:00'),

-- Dua Lipa (ID 8)
(17, 'Future Nostalgia', 8,
 'https://i.scdn.co/image/ab67616d0000b273aa32437d66a6cf1db130061a',
 '2020-03-27 00:00:00'),
(18, 'Dua Lipa', 8,
 'https://i.scdn.co/image/ab67616d0000b273a22a7b828934f83ed9901354',
 '2017-06-02 00:00:00'),

-- The Weeknd (ID 9)
(19, 'After Hours', 9,
 'https://i.scdn.co/image/ab67616d0000b2738863bc11d2aa12b54f5aeb36',
 '2020-03-20 00:00:00'),
(20, 'Dawn FM', 9,
 'https://i.scdn.co/image/ab67616d0000b2734ab2520c2c77a1d66b9ee21d',
 '2022-01-07 00:00:00'),
(21, 'Starboy', 9,
 'https://i.scdn.co/image/ab67616d0000b2734718e2b124f79258be7bc452',
 '2016-11-25 00:00:00'),

-- Doja Cat (ID 10)
(22, 'Planet Her', 10,
 'https://i.scdn.co/image/ab67616d00001e0214b7b2a920a41166d53ddd49',
 '2021-06-25 00:00:00'),
(23, 'Hot Pink', 10,
 'https://i.scdn.co/image/ab67616d0000b273ccb4d7ed40a41c0d16d1dcd7',
 '2019-11-07 00:00:00'),

-- Booba (ID 11)
(24, 'Trône', 11,
 'https://i.scdn.co/image/ab67616d0000b2737b56fb8f7a5b37d234d53238',
 '2017-12-15 00:00:00'),
(25, 'Ouest Side', 11,
 'https://i.scdn.co/image/ab67616d0000b2730629318fc3855cd75c5664b2',
 '2006-02-13 00:00:00'),
(26, 'Lunatic', 11,
 'https://i.scdn.co/image/ab67616d0000b273ce41c9ba4adee1aec4a2b102',
 '2010-11-22 00:00:00'),

-- Queen (ID 12)
(27, 'A Night at the Opera', 12,
 'https://i.scdn.co/image/ab67616d0000b2735a0356dd4c5822509208f525',
 '1975-11-21 00:00:00'),
(28, 'The Game', 12,
 'https://i.scdn.co/image/ab67616d0000b273b1d860ab1ba847e778b2796d',
 '1980-06-30 00:00:00'),

-- Ed Sheeran (ID 13)
(29, '÷ (Divide)', 13,
 'https://i.scdn.co/image/ab67616d0000b273ba5db46f4b838ef6027e6f96',
 '2017-03-03 00:00:00'),
(30, '= (Equals)', 13,
 'https://i.scdn.co/image/ab67616d0000b273ef24c3fdbf856340d55cfeb2',
 '2021-10-29 00:00:00'),
(31, 'x (Multiply)', 13,
 'https://i.scdn.co/image/ab67616d0000b27313b3e37318a0c247b550bccd',
 '2014-06-20 00:00:00'),

-- Taylor Swift (ID 14)
(32, '1989 (Taylor\'s Version)', 14,
 'https://i.scdn.co/image/ab67616d0000b273b7e976d2b35c767f9012cb72',
 '2023-10-27 00:00:00'),
(33, 'Midnights', 14,
 'https://i.scdn.co/image/ab67616d0000b273bb54dde68cd23e2a268ae0f5',
 '2022-10-21 00:00:00'),
(34, 'Folklore', 14,
 'https://i.scdn.co/image/ab67616d0000b273c288028c2592f400dd0b9233',
 '2020-07-24 00:00:00'),

-- Damso (ID 15)
(35, 'Lithopédion', 15,
 'https://i.scdn.co/image/ab67616d0000b273b3b8a63ffd573ce414ae0851',
 '2018-06-15 00:00:00'),
(36, 'QALF', 15,
 'https://i.scdn.co/image/ab67616d0000b2734327854be74ad162694d5055',
 '2020-09-18 00:00:00'),

-- Kendrick Lamar (ID 16)
(37, 'DAMN.', 16,
 'https://i.scdn.co/image/ab67616d0000b2738b52c6b9bc4e43d873869699',
 '2017-04-14 00:00:00'),
(38, 'good kid, m.A.A.d city', 16,
 'https://i.scdn.co/image/ab67616d0000b273d28d2ebdedb220e479743797',
 '2012-10-22 00:00:00'),

-- Lomepal (ID 17)
(39, 'Jeannine', 17,
 'https://i.scdn.co/image/ab67616d0000b27300c0bba714d6c46e47ab1d07',
 '2018-10-05 00:00:00'),
(40, 'Mauvais Ordre', 17,
 'https://i.scdn.co/image/ab67616d0000b273f3b4ab85fc00ae35007eb7d0',
 '2022-09-16 00:00:00'),

-- Billie Eilish (ID 18)
(41, 'Happier Than Ever', 18,
 'https://i.scdn.co/image/ab67616d0000b2732a038d3bf875d23e4aeaa84e',
 '2021-07-30 00:00:00'),
(42, 'When We All Fall Asleep, Where Do We Go?', 18,
 'https://i.scdn.co/image/ab67616d0000b27350a3147b4edd7701a876c6ce',
 '2019-03-29 00:00:00'),

-- AC/DC (ID 19)
(43, 'Back in Black', 19,
 'https://i.scdn.co/image/ab67616d0000b273ff191d7fbdb5a13eaf84132b',
 '1980-07-25 00:00:00'),
(44, 'Highway to Hell', 19,
 'https://i.scdn.co/image/ab67616d0000b27343058ea096fa35ac33c43587',
 '1979-07-27 00:00:00'),

-- Gims (ID 20)
(45, 'Ceinture Noire', 20,
 'https://i.scdn.co/image/ab67616d0000b27389c78d6cb25a395756d86322',
 '2018-03-23 00:00:00'),

-- Rammstein (ID 21)
(46, 'Mutter', 21,
 'https://i.scdn.co/image/ab67616d0000b273954c078e0610345173a5102a',
 '2001-04-02 00:00:00'),
(47, 'Sehnsucht', 21,
 'https://i.scdn.co/image/ab67616d0000b273a715d32590424cd667879ba3',
 '1997-08-22 00:00:00'),

-- Céline Dion (ID 22)
(48, 'Falling into You', 22,
 'https://i.scdn.co/image/ab67616d0000b273c6aebd89b2dcda3348649633',
 '1996-03-12 00:00:00'),

-- Coldplay (ID 23)
(49, 'A Head Full of Dreams', 23,
 'https://i.scdn.co/image/ab67616d0000b2738ff7c3580d429c8212b9a3b6',
 '2015-12-04 00:00:00'),
(50, 'Parachutes', 23,
 'https://i.scdn.co/image/ab67616d0000b2739164bafe9aaa168d93f4816a',
 '2000-07-10 00:00:00'),

-- Ninho (ID 24)
(51, 'Destin', 24,
 'https://i.scdn.co/image/ab67616d0000b27349f298c896b4e4237e2a614e',
 '2019-03-22 00:00:00'),
(52, 'Jefe', 24,
 'https://i.scdn.co/image/ab67616d0000b273df4862f641044c61c4abe602',
 '2021-12-03 00:00:00'),

-- Adele (ID 25)
(53, '21', 25,
 'https://i.scdn.co/image/ab67616d0000b2732118bf9b198b05a95ded6300',
 '2011-01-24 00:00:00'),
(54, '30', 25,
 'https://i.scdn.co/image/ab67616d0000b273c6b577e4c4a6d326354a89f7',
 '2021-11-19 00:00:00'),

-- Imagine Dragons (ID 26)
(55, 'Night Visions', 26,
 'https://i.scdn.co/image/ab67616d0000b273b2b2747c89d2157b0b29fb6a',
 '2012-09-04 00:00:00'),
(56, 'Evolve', 26,
 'https://i.scdn.co/image/ab67616d0000b2737956bd9a3d7a15e4c2e37cc6',
 '2017-06-23 00:00:00'),

-- Louane (ID 27)
(57, 'Chambre 12', 27,
 'https://i.scdn.co/image/ab67616d0000b27303a2736d9196a5ee0987e589',
 '2015-03-02 00:00:00'),
(58, 'Joie de vivre', 27,
 'https://i.scdn.co/image/ab67616d0000b273365db393c43ceb77089d97a0',
 '2020-10-23 00:00:00'),

-- Mylène Farmer (ID 28)
(59, 'L\'Autre...', 28,
 'https://i.scdn.co/image/ab67616d0000b273c01f5670830f5ea89856e36c',
 '1991-04-09 00:00:00'),
(60, 'Innamoramento', 28,
 'https://i.scdn.co/image/ab67616d0000b2739566d96218948d7821daca81',
 '1999-04-07 00:00:00'),

-- Julien Doré (ID 29)
(61, 'Love', 29,
 'https://i.scdn.co/image/ab67616d0000b2736288f1ff8237d90bb1eac6e9',
 '2013-10-25 00:00:00'),
(62, 'Aimée', 29,
 'https://i.scdn.co/image/ab67616d0000b27368beb201c141c8612bacb0dc',
 '2020-09-04 00:00:00'),

-- Vianney (ID 30)
(63, 'Vianney', 30,
 'https://i.scdn.co/image/ab67616d0000b2735cf98a03daf27a005483e021',
 '2016-11-25 00:00:00'),
(64, 'Idées Blanches', 30,
 'https://i.scdn.co/image/ab67616d00001e02755cc6ac3092e4e47360981e',
 '2014-10-20 00:00:00'),

-- Sexion d'Assaut (ID 31)
(65, 'L\'Apogée', 31,
 'https://i.scdn.co/image/ab67616d0000b27381e6951f23e0a64449c9e975',
 '2012-03-05 00:00:00'),
(66, 'L\'Écrasement de tête', 31,
 'https://i.scdn.co/image/ab67616d0000b273e21e585ccb242b6d769f926e',
 '2010-04-12 00:00:00'),

-- Daft Punk (ID 32)
(67, 'Random Access Memories', 32,
 'https://i.scdn.co/image/ab67616d0000b2739b9b36b0e22870b9f542d937',
 '2013-05-17 00:00:00'),
(68, 'Discovery', 32,
 'https://i.scdn.co/image/ab67616d0000b2732c25dad9f8fd54652f7ba5df',
 '2001-03-13 00:00:00');


-- --------------------------------------------------------
-- 3. CHANSONS (407 entrées - Les durées sont en secondes, les notes sont aléatoires pour la variation, is_liked est par défaut à 0)
-- --------------------------------------------------------

-- Album 1: Racine Carrée (Stromae) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Ta fête', 1, 1, 176, 4.3),
       ('Papaoutai', 1, 1, 232, 4.8),
       ('Bâtard', 1, 1, 208, 3.9),
       ('Ave Cesaria', 1, 1, 269, 4.1),
       ('Tous les mêmes', 1, 1, 209, 4.6),
       ('Formidable', 1, 1, 220, 4.7),
       ('Moules frites', 1, 1, 172, 3.8),
       ('Carmen', 1, 1, 203, 4.2),
       ('Humain à l\'eau', 1, 1, 238, 3.5),
       ('Quand c\'est ?', 1, 1, 200, 4.5),
       ('Sommeil', 1, 1, 169, 3.3),
       ('Merci', 1, 1, 234, 3.0),
       ('AVF (feat. Orelsan & Maître Gims)', 1, 1, 222, 4.0),
       ('Silence', 1, 1, 230, 3.7);

-- Album 2: Multitude (Stromae) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Invaincu', 1, 2, 205, 4.1),
       ('Sante', 1, 2, 205, 4.5),
       ('La solassitude', 1, 2, 225, 3.9),
       ('Fils de joie', 1, 2, 280, 4.3),
       ('Enfer', 1, 2, 190, 4.6),
       ('C\'est que du bonheur', 1, 2, 203, 3.7),
       ('Pas vraiment', 1, 2, 185, 3.5),
       ('Mon amour', 1, 2, 180, 4.0),
       ('Déclaration', 1, 2, 210, 3.3),
       ('Riez', 1, 2, 235, 4.2),
       ('Mauvaise journée', 1, 2, 200, 3.8),
       ('Bonne journée', 1, 2, 195, 4.4);

-- Album 3: Cheese (Stromae) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Bienvenue chez moi', 1, 3, 165, 3.5),
       ('Te quiero', 1, 3, 210, 4.1),
       ('Peace or Violence', 1, 3, 185, 4.0),
       ('Rail de musique', 1, 3, 230, 3.8),
       ('Alors on danse', 1, 3, 215, 4.7),
       ('Summertime', 1, 3, 200, 3.6),
       ('Dodo', 1, 3, 195, 3.4),
       ('Silence', 1, 3, 230, 3.7),
       ('Je cours', 1, 3, 190, 3.9),
       ('House\'llelujah', 1, 3, 225, 4.2),
       ('Cheese', 1, 3, 205, 3.3);

-- Album 4: Brol (Angèle) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('La Thune', 2, 4, 180, 4.1),
       ('Balance ton quoi', 2, 4, 189, 4.5),
       ('Jalousie', 2, 4, 195, 3.8),
       ('Tout oublier', 2, 4, 202, 4.9),
       ('Ta Reine', 2, 4, 215, 4.3),
       ('Victime des réseaux', 2, 4, 175, 4.2),
       ('Les matins', 2, 4, 230, 3.6),
       ('Numero 2', 2, 4, 190, 3.5),
       ('Je veux tes yeux', 2, 4, 195, 4.0),
       ('Your Fault', 2, 4, 205, 3.7),
       ('Flemme', 2, 4, 180, 3.9),
       ('Brol', 2, 4, 210, 4.4);

-- Album 5: Nonante-Cinq (Angèle) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Bruxelles je t\'aime', 2, 5, 203, 4.6),
       ('Libre', 2, 5, 200, 4.2),
       ('Profite', 2, 5, 195, 4.1),
       ('Solo', 2, 5, 215, 3.9),
       ('Tempête', 2, 5, 220, 4.3),
       ('Démons (feat. Damso)', 2, 5, 205, 4.7),
       ('Plus de t\'aime', 2, 5, 185, 3.8),
       ('Pensées Positives', 2, 5, 190, 3.6),
       ('Miroir', 2, 5, 230, 4.0),
       ('Taxi', 2, 5, 175, 3.5),
       ('Regarde-moi', 2, 5, 200, 4.4),
       ('On s\'habitue', 2, 5, 210, 3.7);

-- Album 6: Civilisation (Orelsan) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Shonen', 3, 6, 240, 4.5),
       ('L\'odeur de l\'essence', 3, 6, 305, 4.7),
       ('Jour meilleur', 3, 6, 230, 4.6),
       ('Ensemble', 3, 6, 200, 4.5),
       ('La Quête', 3, 6, 230, 4.8),
       ('Manifeste', 3, 6, 225, 4.4),
       ('Fête de famille', 3, 6, 250, 4.3),
       ('Casseurs Flowters Infinity', 3, 6, 210, 4.2),
       ('Du propre', 3, 6, 190, 4.1),
       ('Baise le monde', 3, 6, 205, 4.0),
       ('Rêves bizarres (feat. Damso)', 3, 6, 215, 4.9),
       ('Simple', 3, 6, 180, 3.9),
       ('À l\'aube', 3, 6, 220, 3.8),
       ('Civilisation', 3, 6, 235, 4.3),
       ('J\'essaie d\'arrêter', 3, 6, 245, 4.1);

-- Album 7: La Fête est Finie (Orelsan) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('San', 3, 7, 190, 4.1),
       ('La Fête est Finie', 3, 7, 210, 4.5),
       ('Tout va bien', 3, 7, 200, 4.3),
       ('Défaite de famille', 3, 7, 225, 4.7),
       ('Paradis', 3, 7, 235, 4.6),
       ('Notes pour trop tard (feat. Ibeyi)', 3, 7, 240, 4.4),
       ('Zone (feat. Nekfeu & Dizzee Rascal)', 3, 7, 250, 4.2),
       ('Christophe (feat. Maître Gims)', 3, 7, 215, 4.0),
       ('Quand est-ce que ça s\'arrête', 3, 7, 195, 3.9),
       ('Dans ma ville on traîne', 3, 7, 205, 3.8),
       ('La lumière', 3, 7, 230, 4.3),
       ('Bonne meuf', 3, 7, 180, 4.1),
       ('Si seule', 3, 7, 210, 3.7),
       ('Mauvaise idée', 3, 7, 200, 4.5);

-- Album 8: Le Chant des Sirènes (Orelsan) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('RaelSan', 3, 8, 200, 3.9),
       ('Le Chant des Sirènes', 3, 8, 220, 4.5),
       ('Plus rien ne m\'étonne', 3, 8, 215, 4.7),
       ('La terre est ronde', 3, 8, 230, 4.6),
       ('1990', 3, 8, 190, 4.3),
       ('Double peine', 3, 8, 205, 4.1),
       ('Finir mal', 3, 8, 240, 4.4),
       ('Entre bien et mal', 3, 8, 210, 4.2),
       ('Si seul', 3, 8, 200, 4.0),
       ('Code barre', 3, 8, 225, 3.8),
       ('Nord', 3, 8, 185, 3.7),
       ('Mauvaise idée', 3, 8, 200, 4.5),
       ('Question/Réponse', 3, 8, 215, 4.3),
       ('Suicide social', 3, 8, 250, 4.9);

-- Album 9: Sainte-Victoire (Clara Luciani) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('La Grenade', 4, 9, 210, 4.8),
       ('Les Fleurs', 4, 9, 195, 4.2),
       ('On ne s\'aimera plus jamais', 4, 9, 230, 4.5),
       ('Drôle d\'époque', 4, 9, 200, 3.9),
       ('Nue', 4, 9, 215, 4.1),
       ('La Baie', 4, 9, 185, 4.0),
       ('Monstre d\'amour', 4, 9, 225, 4.3),
       ('Eddy', 4, 9, 190, 3.7),
       ('Comme toi', 4, 9, 240, 4.4),
       ('Sainte-Victoire', 4, 9, 205, 4.6),
       ('De la tête aux pieds', 4, 9, 210, 3.5);

-- Album 10: Cœur (Clara Luciani) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Cœur', 4, 10, 190, 4.3),
       ('Le Reste', 4, 10, 205, 4.7),
       ('Amour toujours', 4, 10, 215, 4.5),
       ('Tout le monde (Sauf toi)', 4, 10, 230, 4.1),
       ('Respire encore', 4, 10, 200, 4.4),
       ('Sad & Slow', 4, 10, 195, 3.8),
       ('Au revoir', 4, 10, 220, 4.0),
       ('La Place', 4, 10, 185, 3.9),
       ('Bandit', 4, 10, 210, 4.2),
       ('J\'sais pas plaire', 4, 10, 235, 4.6),
       ('Dans mon lit', 4, 10, 200, 3.7);

-- Album 11: Deux frères (PNL) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Au DD', 5, 11, 330, 4.9),
       ('Deux frères', 5, 11, 240, 4.5),
       ('Blanka', 5, 11, 210, 4.3),
       ('Cali', 5, 11, 225, 4.1),
       ('91\'s', 5, 11, 200, 4.0),
       ('Chang', 5, 11, 215, 3.8),
       ('Ryuk', 5, 11, 230, 4.2),
       ('De la fenêtre au ter-ter', 5, 11, 195, 3.7),
       ('Kuta Ubud', 5, 11, 220, 4.4),
       ('Menace', 5, 11, 205, 3.9),
       ('Shenmue', 5, 11, 245, 4.6),
       ('Zoulou tchaing', 5, 11, 185, 3.6),
       ('Celsius', 5, 11, 200, 4.1),
       ('Frontières', 5, 11, 210, 4.3),
       ('Autre monde', 5, 11, 225, 4.5),
       ('Capuche', 5, 11, 200, 3.9);

-- Album 12: Dans la légende (PNL) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('DA', 5, 12, 230, 4.6),
       ('Naha', 5, 12, 205, 4.8),
       ('Dans la légende', 5, 12, 220, 4.5),
       ('T\'sais', 5, 12, 195, 4.3),
       ('Tu sais pas', 5, 12, 210, 4.1),
       ('Jusqu\'au dernier gramme', 5, 12, 240, 4.4),
       ('Luz de Luna', 5, 12, 235, 4.0),
       ('Kratos', 5, 12, 200, 3.9),
       ('À l\'ammoniaque', 5, 12, 215, 4.7),
       ('Onizuka', 5, 12, 185, 3.8),
       ('Bené', 5, 12, 225, 4.2),
       ('Mira', 5, 12, 200, 3.7),
       ('J\'suis QLF', 5, 12, 210, 4.5),
       ('Oulala', 5, 12, 230, 4.6),
       ('Cramés', 5, 12, 245, 4.1),
       ('Le monde ou rien', 5, 12, 195, 4.3);

-- Album 13: Aya (Aya Nakamura) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Jolie nana', 6, 13, 212, 4.6),
       ('Doudou', 6, 13, 203, 4.4),
       ('Plus jamais', 6, 13, 200, 4.2),
       ('Tchop', 6, 13, 215, 4.1),
       ('Fly', 6, 13, 230, 3.9),
       ('Mon chéri', 6, 13, 185, 4.0),
       ('Préféré', 6, 13, 225, 4.3),
       ('Cadeau', 6, 13, 190, 3.7),
       ('Hot', 6, 13, 205, 4.5),
       ('Nakamura', 6, 13, 210, 4.6),
       ('40%', 6, 13, 235, 4.1),
       ('Maman', 6, 13, 200, 3.8),
       ('Petit bébé', 6, 13, 215, 4.2),
       ('Pookie', 6, 13, 195, 4.7),
       ('Copines', 6, 13, 220, 4.8);

-- Album 14: Nakamura (Aya Nakamura) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Nakamura', 6, 14, 210, 4.6),
       ('Copines', 6, 14, 220, 4.8),
       ('Pookie', 6, 14, 195, 4.7),
       ('La dot', 6, 14, 205, 4.3),
       ('Sucette', 6, 14, 230, 4.1),
       ('Oula', 6, 14, 185, 4.0),
       ('Djadja', 6, 14, 190, 4.9),
       ('Gangster', 6, 14, 225, 4.2),
       ('Comme ci', 6, 14, 200, 3.9),
       ('Ça fait mal', 6, 14, 215, 4.4),
       ('Whine up', 6, 14, 180, 3.8),
       ('Mon bébé', 6, 14, 235, 4.5),
       ('Soldat', 6, 14, 200, 4.3);

-- Album 15: Brûler le feu (Juliette Armanet) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Le Dernier Jour du Disco', 7, 15, 220, 4.8),
       ('Brûler le feu', 7, 15, 205, 4.5),
       ('Tu me kiffes', 7, 15, 190, 4.3),
       ('Boum boum baby', 7, 15, 230, 4.1),
       ('J\'t\ai dans ma tête', 7, 15, 200, 4.0),
       ('Fuguer', 7, 15, 215, 4.4),
       ('Imaginer l\'amour', 7, 15, 225, 4.6),
       ('Quitter la ville', 7, 15, 185, 3.9),
       ('Vertigo', 7, 15, 200, 3.8),
       ('L\'amour en solitaire', 7, 15, 210, 4.2),
       ('La Flamme', 7, 15, 235, 4.7),
       ('Simple et Fun', 7, 15, 195, 3.7),
       ('Corail', 7, 15, 220, 4.5);

-- Album 16: Petite Amie (Juliette Armanet) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('L\'amour en solitaire', 7, 16, 210, 4.5),
       ('Petite Amie', 7, 16, 200, 4.7),
       ('Alexandre', 7, 16, 215, 4.3),
       ('Manque d\'amour', 7, 16, 230, 4.1),
       ('Starflam', 7, 16, 195, 4.0),
       ('La Nuit', 7, 16, 225, 4.4),
       ('À la folie', 7, 16, 185, 3.8),
       ('Un rien', 7, 16, 200, 3.7),
       ('Histoire d\'un Gars', 7, 16, 235, 4.2),
       ('Comment dire', 7, 16, 190, 3.9),
       ('Je te ferai la cour', 7, 16, 210, 4.6),
       ('L\'Indien', 7, 16, 205, 3.5);

-- Album 17: Future Nostalgia (Dua Lipa) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Future Nostalgia', 8, 17, 160, 4.4),
       ('Don\'t Start Now', 8, 17, 183, 4.9),
       ('Physical', 8, 17, 193, 4.6),
       ('Levitating', 8, 17, 203, 4.7),
       ('Hallucinate', 8, 17, 195, 4.3),
       ('Love Again', 8, 17, 258, 4.5),
       ('Break My Heart', 8, 17, 223, 4.2),
       ('Good in Bed', 8, 17, 218, 3.9),
       ('Pretty Please', 8, 17, 208, 4.0),
       ('Boys Will Be Boys', 8, 17, 160, 3.8),
       ('Cool', 8, 17, 195, 4.1);

-- Album 18: Dua Lipa (Dua Lipa) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Genesis', 8, 18, 190, 4.0),
       ('New Rules', 8, 18, 215, 4.8),
       ('IDGAF', 8, 18, 217, 4.5),
       ('Be the One', 8, 18, 202, 4.3),
       ('Blow Your Mind (Mwah)', 8, 18, 178, 4.1),
       ('Thinking \'Bout You', 8, 18, 165, 3.9),
       ('Lost in Your Light (feat. Miguel)', 8, 18, 203, 4.2),
       ('Hotter than Hell', 8, 18, 187, 4.4),
       ('Room for 2', 8, 18, 180, 3.7),
       ('No Goodbyes', 8, 18, 208, 3.8),
       ('Last Dance', 8, 18, 205, 4.0),
       ('Homesick', 8, 18, 230, 4.1);

-- Album 19: After Hours (The Weeknd) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Alone Again', 9, 19, 240, 4.1),
       ('Too Late', 9, 19, 239, 4.5),
       ('Hardest to Love', 9, 19, 211, 4.3),
       ('Scared to Live', 9, 19, 193, 4.7),
       ('Blinding Lights', 9, 19, 200, 5.0),
       ('In Your Eyes', 9, 19, 237, 4.5),
       ('Save Your Tears', 9, 19, 215, 4.8),
       ('Heartless', 9, 19, 198, 4.6),
       ('Faith', 9, 19, 220, 4.2),
       ('Repeat After Me (Interlude)', 9, 19, 195, 3.9),
       ('After Hours', 9, 19, 361, 4.4),
       ('Until I Bleed Out', 9, 19, 190, 4.0),
       ('Snowchild', 9, 19, 205, 4.1),
       ('Escape from LA', 9, 19, 210, 4.3);

-- Album 20: Dawn FM (The Weeknd) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Dawn FM', 9, 20, 105, 4.0),
       ('Gasoline', 9, 20, 212, 4.4),
       ('How Do I Make You Love Me?', 9, 20, 201, 4.1),
       ('Take My Breath', 9, 20, 359, 4.6),
       ('Sacrifice', 9, 20, 214, 4.8),
       ('A Tale By Tyler', 9, 20, 110, 3.9),
       ('Out of Time', 9, 20, 214, 4.3),
       ('Is There Someone Else?', 9, 20, 245, 4.5),
       ('Starry Eyes', 9, 20, 148, 3.8),
       ('Every Angel is Terrifying', 9, 20, 210, 4.2),
       ('Don\'t Break My Heart', 9, 20, 230, 4.0),
       ('I Heard You\'re Married', 9, 20, 233, 4.1),
       ('Less Than Zero', 9, 20, 234, 4.3),
       ('Phantom Regret by Jim', 9, 20, 180, 3.7),
       ('Best Friends', 9, 20, 215, 4.5),
       ('Here We Go... Again', 9, 20, 200, 4.4);

-- Album 21: Starboy (The Weeknd) - 18 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Starboy (feat. Daft Punk)', 9, 21, 239, 4.7),
       ('Party Monster', 9, 21, 252, 4.3),
       ('False Alarm', 9, 21, 200, 4.0),
       ('Reminder', 9, 21, 218, 4.5),
       ('Sidewalks (feat. Kendrick Lamar)', 9, 21, 235, 4.8),
       ('Six Feet Under', 9, 21, 237, 4.2),
       ('Love to Lay', 9, 21, 211, 4.1),
       ('A Lonely Night', 9, 21, 218, 4.0),
       ('Attention', 9, 21, 203, 3.9),
       ('Ordinary Life', 9, 21, 222, 4.3),
       ('Stargirl Interlude (feat. Lana Del Rey)', 9, 21, 142, 3.8),
       ('I Feel It Coming (feat. Daft Punk)', 9, 21, 269, 4.9),
       ('Secrets', 9, 21, 241, 4.4),
       ('Die for You', 9, 21, 260, 4.6),
       ('Rockin\'', 9, 21, 208, 4.0),
       ('All I Know (feat. Future)', 9, 21, 230, 4.1),
       ('Nothing Without You', 9, 21, 220, 3.9),
       ('True Colors', 9, 21, 215, 4.3);

-- Album 22: Planet Her (Doja Cat) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Woman', 10, 22, 172, 4.6),
       ('Kiss Me More (feat. SZA)', 10, 22, 208, 4.9),
       ('Need to Know', 10, 22, 210, 4.5),
       ('I Don\'t Do Drugs (feat. Ariana Grande)', 10, 22, 188, 4.3),
       ('Get Into It (Yuh)', 10, 22, 215, 4.1),
       ('Naked', 10, 22, 210, 4.0),
       ('Payday (feat. Young Thug)', 10, 22, 205, 4.2),
       ('Options (feat. J.I.D)', 10, 22, 230, 4.4),
       ('Ain\'t Shit', 10, 22, 220, 4.6),
       ('Imagine', 10, 22, 195, 3.8),
       ('Ride', 10, 22, 215, 4.0),
       ('You Right (feat. The Weeknd)', 10, 22, 218, 4.7),
       ('Love to Dream', 10, 22, 205, 3.9),
       ('Why the hell are you here?', 10, 22, 210, 4.1);

-- Album 23: Hot Pink (Doja Cat) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Cyber Sex', 10, 23, 166, 4.1),
       ('Say So', 10, 23, 238, 4.8),
       ('Rules', 10, 23, 220, 4.5),
       ('Bottom Bitch', 10, 23, 178, 4.3),
       ('Shine', 10, 23, 215, 4.0),
       ('Better Than Me', 10, 23, 205, 3.9),
       ('Juicy (with Tyga)', 10, 23, 200, 4.6),
       ('Talk Dirty', 10, 23, 195, 3.7),
       ('Wine Pon You (feat. Konshens)', 10, 23, 210, 4.2),
       ('Addiction', 10, 23, 230, 4.4),
       ('Streets', 10, 23, 225, 4.7),
       ('Like That (feat. Gucci Mane)', 10, 23, 200, 4.1);

-- Album 24: Trône (Booba) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('T.R.O.N.E', 11, 24, 215, 4.5),
       ('Friday', 11, 24, 185, 4.2),
       ('Petite fille', 11, 24, 230, 4.7),
       ('Désactivé', 11, 24, 200, 4.0),
       ('Kayna', 11, 24, 210, 4.3),
       ('À la folie', 11, 24, 225, 4.6),
       ('Éléphant', 11, 24, 195, 3.9),
       ('Magnifique', 11, 24, 240, 4.4),
       ('Salside', 11, 24, 205, 4.1),
       ('Ridin\'', 11, 24, 215, 4.2),
       ('Centurion', 11, 24, 200, 4.5),
       ('13 Block', 11, 24, 230, 4.3),
       ('DKR', 11, 24, 190, 4.8);

-- Album 25: Ouest Side (Booba) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Couleur ébène', 11, 25, 220, 4.7),
       ('Garde la pêche', 11, 25, 190, 4.5),
       ('Au bout des rêves', 11, 25, 230, 4.3),
       ('Mauvais œil', 11, 25, 205, 4.1),
       ('Temps mort', 11, 25, 215, 4.0),
       ('Killer', 11, 25, 240, 4.2),
       ('92i Veyron', 11, 25, 185, 3.9),
       ('Pitbull', 11, 25, 200, 4.4),
       ('Mon bébé', 11, 25, 235, 4.6),
       ('Lunatic', 11, 25, 210, 4.5),
       ('Le bitume avec une plume', 11, 25, 225, 4.3),
       ('Jusqu\'ici tout va bien', 11, 25, 200, 4.1),
       ('Tallac', 11, 25, 195, 4.0),
       ('Destinée', 11, 25, 215, 4.2);

-- Album 26: Lunatic (Booba) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Jour de paye', 11, 26, 205, 4.3),
       ('Ma couleur', 11, 26, 220, 4.5),
       ('Caracas', 11, 26, 190, 4.1),
       ('Killer', 11, 26, 215, 4.0),
       ('Lunatic', 11, 26, 230, 4.7),
       ('Mon bébé', 11, 26, 200, 4.2),
       ('92i Veyron', 11, 26, 185, 3.9),
       ('Pitbull', 11, 26, 205, 4.4),
       ('Boss', 11, 26, 235, 4.6),
       ('Saddam Hauts-de-Seine', 11, 26, 210, 4.5),
       ('Réel', 11, 26, 225, 4.3),
       ('Jusqu\'ici tout va bien', 11, 26, 200, 4.1),
       ('Tallac', 11, 26, 195, 4.0),
       ('Destinée', 11, 26, 215, 4.2),
       ('Fast Life', 11, 26, 230, 4.5);

-- Album 27: A Night at the Opera (Queen) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Death on Two Legs', 12, 27, 223, 4.5),
       ('Lazing on a Sunday Afternoon', 12, 27, 60, 4.1),
       ('I\'m in Love with My Car', 12, 27, 185, 4.0),
       ('You\'re My Best Friend', 12, 27, 170, 4.7),
       ('39', 12, 27, 208, 4.3),
       ('Sweet Lady', 12, 27, 240, 3.9),
       ('Seaside Rendezvous', 12, 27, 133, 4.2),
       ('The Prophet\'s Song', 12, 27, 488, 4.6),
       ('Love of My Life', 12, 27, 217, 4.9),
       ('Good Company', 12, 27, 203, 3.8),
       ('Bohemian Rhapsody', 12, 27, 355, 5.0),
       ('God Save the Queen', 12, 27, 72, 4.4);

-- Album 28: The Game (Queen) - 10 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Play the Game', 12, 28, 209, 4.4),
       ('Dragon Attack', 12, 28, 260, 4.2),
       ('Another One Bites the Dust', 12, 28, 217, 4.9),
       ('Need Your Loving Tonight', 12, 28, 149, 4.1),
       ('Crazy Little Thing Called Love', 12, 28, 168, 4.7),
       ('Rock It (Prime Jive)', 12, 28, 250, 4.0),
       ('Don\'t Try Suicide', 12, 28, 220, 3.8),
       ('Sail Away Sweet Sister', 12, 28, 230, 4.3),
       ('Coming Soon', 12, 28, 175, 3.9),
       ('Save Me', 12, 28, 227, 4.6);

-- Album 29: ÷ (Divide) (Ed Sheeran) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Eraser', 13, 29, 227, 4.2),
       ('Castle on the Hill', 13, 29, 261, 4.7),
       ('Dive', 13, 29, 238, 4.3),
       ('Shape of You', 13, 29, 233, 4.9),
       ('Perfect', 13, 29, 263, 5.0),
       ('Galway Girl', 13, 29, 170, 4.5),
       ('Happier', 13, 29, 207, 4.1),
       ('New Man', 13, 29, 189, 4.0),
       ('Hearts Don\'t Break Around Here', 13, 29, 248, 4.4),
       ('What Do I Know?', 13, 29, 237, 4.1),
       ('How Would You Feel (Paean)', 13, 29, 277, 4.6),
       ('Supermarket Flowers', 13, 29, 221, 4.8),
       ('Barcelona', 13, 29, 176, 4.3),
       ('Bibia Be Ye Ye', 13, 29, 178, 4.0),
       ('Nancy Mulligan', 13, 29, 170, 4.5),
       ('Save Myself', 13, 29, 247, 4.2);

-- Album 30: = (Equals) (Ed Sheeran) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Tides', 13, 30, 210, 4.1),
       ('Shivers', 13, 30, 207, 4.8),
       ('First Times', 13, 30, 210, 4.3),
       ('Bad Habits', 13, 30, 233, 4.9),
       ('Overpass Graffiti', 13, 30, 236, 4.5),
       ('The Joker and the Queen', 13, 30, 185, 4.6),
       ('Leave Your Life', 13, 30, 210, 4.2),
       ('Collide', 13, 30, 203, 4.1),
       ('2step', 13, 30, 200, 4.0),
       ('Stop the Rain', 13, 30, 208, 4.3),
       ('Love in Slow Motion', 13, 30, 225, 4.4),
       ('Visiting Hours', 13, 30, 206, 4.7),
       ('Sandman', 13, 30, 250, 4.0),
       ('Be Right Now', 13, 30, 200, 4.1);

-- Album 31: x (Multiply) (Ed Sheeran) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('One', 13, 31, 253, 4.1),
       ('I\'m a Mess', 13, 31, 264, 4.3),
       ('Sing', 13, 31, 247, 4.7),
       ('Don\'t', 13, 31, 239, 4.5),
       ('Nina', 13, 31, 233, 4.2),
       ('Photograph', 13, 31, 259, 4.9),
       ('Bloodstream', 13, 31, 300, 4.4),
       ('Tenerife Sea', 13, 31, 241, 4.6),
       ('Runaway', 13, 31, 213, 4.1),
       ('The Man', 13, 31, 237, 4.0),
       ('Thinking Out Loud', 13, 31, 281, 5.0),
       ('Afire Love', 13, 31, 314, 4.3),
       ('Take It Back / I\'m a Mess', 13, 31, 293, 4.2),
       ('Shirtsleeves', 13, 31, 210, 4.0),
       ('Even My Dad Does Sometimes', 13, 31, 200, 4.1),
       ('All of the Stars', 13, 31, 215, 4.5);

-- Album 32: 1989 (Taylor\'s Version) (Taylor Swift) - 21 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Welcome to New York (Taylor\'s Version)', 14, 32, 212, 4.3),
       ('Blank Space (Taylor\'s Version)', 14, 32, 231, 4.8),
       ('Style (Taylor\'s Version)', 14, 32, 220, 4.6),
       ('Out of the Woods (Taylor\'s Version)', 14, 32, 235, 4.4),
       ('Shake It Off (Taylor\'s Version)', 14, 32, 219, 4.7),
       ('Bad Blood (Taylor\'s Version)', 14, 32, 211, 4.2),
       ('Wildest Dreams (Taylor\'s Version)', 14, 32, 220, 4.5),
       ('How You Get the Girl (Taylor\'s Version)', 14, 32, 227, 4.1),
       ('This Love (Taylor\'s Version)', 14, 32, 230, 4.3),
       ('I Know Places (Taylor\'s Version)', 14, 32, 215, 4.6),
       ('Clean (Taylor\'s Version)', 14, 32, 260, 4.0),
       ('Wonderland (Taylor\'s Version)', 14, 32, 250, 4.4),
       ('New Romantics (Taylor\'s Version)', 14, 32, 230, 4.5),
       ('You Are in Love (Taylor\'s Version)', 14, 32, 240, 4.7),
       ('Slut! (Taylor\'s Version) (From The Vault)', 14, 32, 200, 4.3),
       ('Say Don\'t Go (Taylor\'s Version) (From The Vault)', 14, 32, 215, 4.6),
       ('Now That We Don\'t Talk (Taylor\'s Version) (From The Vault)', 14, 32, 205, 4.4),
       ('Suburban Legends (Taylor\'s Version) (From The Vault)', 14, 32, 220, 4.5),
       ('Is It Over Now? (Taylor\'s Version) (From The Vault)', 14, 32, 242, 4.7),
       ('Forever Winter (Taylor\'s Version) (From The Vault)', 14, 32, 210, 4.3),
       ('Sweeter Than Fiction (Taylor\'s Version) (From The Vault)', 14, 32, 220, 4.6);

-- Album 33: Midnights (Taylor Swift) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Lavender Haze', 14, 33, 202, 4.5),
       ('Maroon', 14, 33, 218, 4.3),
       ('Anti-Hero', 14, 33, 200, 4.8),
       ('Snow on the Beach (feat. Lana Del Rey)', 14, 33, 255, 4.6),
       ('You\'re on Your Own, Kid', 14, 33, 194, 4.4),
       ('Midnight Rain', 14, 33, 174, 4.2),
       ('Question...?', 14, 33, 223, 4.1),
       ('Vigilante Shit', 14, 33, 168, 4.0),
       ('Bejeweled', 14, 33, 194, 4.5),
       ('Labyrinth', 14, 33, 247, 4.3),
       ('Karma', 14, 33, 204, 4.7),
       ('Sweet Nothing', 14, 33, 188, 4.1),
       ('Mastermind', 14, 33, 191, 4.4);

-- Album 34: Folklore (Taylor Swift) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('The 1', 14, 34, 210, 4.4),
       ('Cardigan', 14, 34, 240, 4.7),
       ('The Last Great American Dynasty', 14, 34, 248, 4.5),
       ('Exile (feat. Bon Iver)', 14, 34, 285, 4.9),
       ('My Tears Ricochet', 14, 34, 255, 4.6),
       ('Mirrorball', 14, 34, 210, 4.3),
       ('Seven', 14, 34, 208, 4.1),
       ('August', 14, 34, 254, 4.5),
       ('This Is Me Trying', 14, 34, 216, 4.2),
       ('Illicit Affairs', 14, 34, 190, 4.0),
       ('Invisible String', 14, 34, 252, 4.4),
       ('Mad Woman', 14, 34, 247, 4.6),
       ('Epiphany', 14, 34, 280, 4.3),
       ('Betty', 14, 34, 290, 4.7),
       ('Peace', 14, 34, 239, 4.5),
       ('Hoax', 14, 34, 225, 4.2);

-- Album 35: Lithopédion (Damso) - 17 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Introduction', 15, 35, 120, 4.0),
       ('TieksVie', 15, 35, 200, 4.3),
       ('Ipséité', 15, 35, 215, 4.5),
       ('Fais-moi un virement', 15, 35, 190, 4.1),
       ('Mosaïque solitaire', 15, 35, 220, 4.6),
       ('Julien', 15, 35, 230, 4.4),
       ('God Save The Queen', 15, 35, 205, 4.2),
       ('Silence', 15, 35, 210, 4.0),
       ('60 Litres', 15, 35, 235, 4.3),
       ('Dix Lunes', 15, 35, 195, 4.5),
       ('Mon bébé d\'amour', 15, 35, 240, 4.7),
       ('Pour l\'argent', 15, 35, 200, 4.1),
       ('Mort', 15, 35, 215, 4.4),
       ('Smeagol', 15, 35, 185, 4.2),
       ('J\'avais juste la dalle', 15, 35, 220, 4.0),
       ('Tueurs', 15, 35, 230, 4.3),
       ('Fin', 15, 35, 150, 4.5);

-- Album 36: QALF (Damso) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('MEVTR', 15, 36, 210, 4.5),
       ('DEUX TOILES DE MER', 15, 36, 230, 4.7),
       ('SENTIMENTAL', 15, 36, 195, 4.3),
       ('THEVIE SHOOT', 15, 36, 200, 4.6),
       ('911', 15, 36, 220, 4.8),
       ('BXL ZOO', 15, 36, 215, 4.4),
       ('CŒUR EN MIETTES', 15, 36, 235, 4.2),
       ('POUR L\'ARGENT', 15, 36, 205, 4.1),
       ('FAIS-MOI UN VIREMENT', 15, 36, 190, 4.0),
       ('PUEBLA', 15, 36, 240, 4.5),
       ('VANTARD', 15, 36, 200, 4.3),
       ('ROSE MARTHE ET LES PROMESSE', 15, 36, 210, 4.7),
       ('D\'JACK', 15, 36, 225, 4.2),
       ('SIMPLE LIFE', 15, 36, 200, 4.1);

-- Album 37: DAMN. (Kendrick Lamar) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('BLOOD.', 16, 37, 110, 4.1),
       ('DNA.', 16, 37, 185, 4.8),
       ('YAH.', 16, 37, 160, 4.3),
       ('ELEMENT.', 16, 37, 200, 4.6),
       ('FEEL.', 16, 37, 214, 4.4),
       ('LOYALTY. (feat. Rihanna)', 16, 37, 227, 4.5),
       ('PRIDE.', 16, 37, 238, 4.2),
       ('HUMBLE.', 16, 37, 177, 4.9),
       ('LUST.', 16, 37, 301, 4.1),
       ('LOVE. (feat. Zacari)', 16, 37, 220, 4.3),
       ('XXX. (feat. U2)', 16, 37, 255, 4.6),
       ('FEAR.', 16, 37, 461, 4.7),
       ('GOD.', 16, 37, 238, 4.0),
       ('DUCKWORTH.', 16, 37, 260, 4.5);

-- Album 38: good kid, m.A.A.d city (Kendrick Lamar) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Sherane a.k.a Master Splinter\'s Daughter', 16, 38, 280, 4.4),
       ('Bitch, Don\'t Kill My Vibe', 16, 38, 310, 4.7),
       ('Backseat Freestyle', 16, 38, 212, 4.5),
       ('The Art of Peer Pressure', 16, 38, 300, 4.3),
       ('Money Trees (feat. Jay Rock)', 16, 38, 370, 4.8),
       ('Poetic Justice (feat. Drake)', 16, 38, 300, 4.6),
       ('Good Kid', 16, 38, 220, 4.2),
       ('M.A.A.D City (feat. MC Eiht)', 16, 38, 260, 4.9),
       ('Swimming Pools (Drank)', 16, 38, 313, 4.4),
       ('Sing About Me, I\'m Dying of Thirst', 16, 38, 721, 4.7),
       ('Real (feat. Anna Wise)', 16, 38, 331, 4.1),
       ('Compton (feat. Dr. Dre)', 16, 38, 260, 4.5);

-- Album 39: Jeannine (Lomepal) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Ne me dis pas non', 17, 39, 215, 4.3),
       ('Trop beau', 17, 39, 200, 4.8),
       ('Mélancolie Heureuse', 17, 39, 230, 4.5),
       ('1000 degrés (feat. Roméo Elvis)', 17, 39, 210, 4.6),
       ('Lucy', 17, 39, 195, 4.4),
       ('Plus de larmes', 17, 39, 225, 4.2),
       ('Le Monde', 17, 39, 205, 4.0),
       ('Parachute', 17, 39, 215, 4.1),
       ('Jeannine', 17, 39, 240, 4.3),
       ('Sans toi', 17, 39, 200, 4.5),
       ('X-men', 17, 39, 235, 4.7),
       ('Cinq doigts', 17, 39, 190, 4.0),
       ('Beaucoup trop', 17, 39, 220, 4.4),
       ('Plus faim', 17, 39, 205, 4.2),
       ('70', 17, 39, 215, 4.1);

-- Album 40: Mauvais Ordre (Lomepal) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('À l\'eau', 17, 40, 210, 4.4),
       ('Tee', 17, 40, 200, 4.6),
       ('Mauvais Ordre', 17, 40, 230, 4.2),
       ('Hashtag', 17, 40, 215, 4.0),
       ('Le Vrai Seul', 17, 40, 195, 4.3),
       ('50 (feat. Romeo Elvis)', 17, 40, 225, 4.5),
       ('Plus de larmes', 17, 40, 205, 4.1),
       ('Skit 1', 17, 40, 60, 3.5),
       ('Regarde-moi', 17, 40, 215, 4.4),
       ('À la mer', 17, 40, 230, 4.7),
       ('Mauvais Ordre II', 17, 40, 200, 4.3),
       ('Skit 2', 17, 40, 75, 3.6),
       ('Le Nord', 17, 40, 220, 4.5),
       ('Outro', 17, 40, 190, 4.0),
       ('J\'essaie d\'oublier', 17, 40, 215, 4.2);

-- Album 41: Happier Than Ever (Billie Eilish) - 16 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Getting Older', 18, 41, 244, 4.2),
       ('I Didn\'t Change My Number', 18, 41, 137, 4.0),
       ('Billie Bossa Nova', 18, 41, 207, 4.5),
       ('My Future', 18, 41, 208, 4.6),
       ('Oxytocin', 18, 41, 212, 4.3),
       ('Goldwing', 18, 41, 172, 4.1),
       ('Lost Cause', 18, 41, 212, 4.4),
       ('Halley\'s Comet', 18, 41, 220, 4.7),
       ('Not My Responsibility', 18, 41, 212, 4.0),
       ('OverHeated', 18, 41, 166, 4.3),
       ('Everybody Dies', 18, 41, 239, 4.5),
       ('Your Power', 18, 41, 245, 4.8),
       ('NDA', 18, 41, 172, 4.1),
       ('Therefore I Am', 18, 41, 175, 4.6),
       ('Happier Than Ever', 18, 41, 298, 5.0),
       ('Male Fantasy', 18, 41, 169, 4.4);

-- Album 42: When We All Fall Asleep, Where Do We Go? (Billie Eilish) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('!!!!!!', 18, 42, 13, 3.5),
       ('Bad Guy', 18, 42, 194, 4.9),
       ('Xanny', 18, 42, 243, 4.3),
       ('You Should See Me in a Crown', 18, 42, 180, 4.5),
       ('All the Good Girls Go to Hell', 18, 42, 167, 4.2),
       ('Wish You Were Gay', 18, 42, 222, 4.1),
       ('When the Party\'s Over', 18, 42, 196, 4.6),
       ('8', 18, 42, 170, 4.0),
       ('My Strange Addiction', 18, 42, 178, 4.4),
       ('Bury a Friend', 18, 42, 193, 4.7),
       ('Ilomilo', 18, 42, 148, 4.1),
       ('Listen Before I Go', 18, 42, 242, 4.5),
       ('I Love You', 18, 42, 298, 4.8),
       ('Goodbye', 18, 42, 200, 4.3);

-- Album 43: Back in Black (AC/DC) - 10 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Hells Bells', 19, 43, 310, 4.7),
       ('Shoot to Thrill', 19, 43, 317, 4.5),
       ('What Do You Do for Money Honey', 19, 43, 216, 4.2),
       ('Givin\' the Dog a Bone', 19, 43, 211, 4.0),
       ('Back in Black', 19, 43, 255, 5.0),
       ('You Shook Me All Night Long', 19, 43, 210, 4.8),
       ('Have a Drink on Me', 19, 43, 238, 4.3),
       ('Shake a Leg', 19, 43, 250, 4.1),
       ('Rock and Roll Ain\'t Noise Pollution', 19, 43, 256, 4.6),
       ('Let Me Put My Love into You', 19, 43, 240, 4.4);

-- Album 44: Highway to Hell (AC/DC) - 10 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Highway to Hell', 19, 44, 208, 4.9),
       ('Girls Got Rhythm', 19, 44, 203, 4.3),
       ('Walk All Over You', 19, 44, 310, 4.5),
       ('Touch Too Much', 19, 44, 268, 4.1),
       ('Beating Around the Bush', 19, 44, 237, 4.2),
       ('Shot Down in Flames', 19, 44, 203, 4.0),
       ('Get It Hot', 19, 44, 230, 4.4),
       ('If You Want Blood (You\'ve Got It)', 19, 44, 261, 4.7),
       ('Love Hungry Man', 19, 44, 257, 3.8),
       ('Night Prowler', 19, 44, 360, 4.6);

-- Album 45: Ceinture Noire (Gims) - 34 titres (Simplifié à 15)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('La Même (feat. Vianney)', 20, 45, 200, 4.7),
       ('Bella', 20, 45, 180, 4.5),
       ('Corazon (feat. Lil Wayne)', 20, 45, 210, 4.3),
       ('Loup Garou (feat. Sofiane)', 20, 45, 220, 4.1),
       ('Mi Gna', 20, 45, 190, 4.0),
       ('Où aller', 20, 45, 230, 4.2),
       ('Caméléon', 20, 45, 205, 4.4),
       ('T\'es partie', 20, 45, 215, 4.6),
       ('Je t\'ai donné', 20, 45, 225, 4.8),
       ('Le Pire', 20, 45, 200, 4.1),
       ('Feuille de Match', 20, 45, 235, 4.3),
       ('Pardon', 20, 45, 210, 4.5),
       ('Tu la gères', 20, 45, 195, 4.2),
       ('Changer', 20, 45, 220, 4.0),
       ('Roulez', 20, 45, 205, 4.4);

-- Album 46: Mutter (Rammstein) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Mein Herz brennt', 21, 46, 275, 4.7),
       ('Links 2 3 4', 21, 46, 216, 4.5),
       ('Sonne', 21, 46, 272, 4.9),
       ('Ich will', 21, 46, 217, 4.8),
       ('Mutter', 21, 46, 269, 4.6),
       ('Feuer frei!', 21, 46, 190, 4.4),
       ('Spieluhr', 21, 46, 246, 4.2),
       ('Zwitter', 21, 46, 271, 4.1),
       ('Rein raus', 21, 46, 230, 4.0),
       ('Adios', 21, 46, 218, 4.3),
       ('Nebel', 21, 46, 263, 4.5);

-- Album 47: Sehnsucht (Rammstein) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Sehnsucht', 21, 47, 246, 4.6),
       ('Engel', 21, 47, 264, 4.8),
       ('Tier', 21, 47, 230, 4.4),
       ('Bestrafe mich', 21, 47, 218, 4.2),
       ('Du hast', 21, 47, 234, 5.0),
       ('Bück dich', 21, 47, 235, 4.0),
       ('Spiel mit mir', 21, 47, 246, 4.3),
       ('Küss mich (Fellfrosch)', 21, 47, 210, 4.1),
       ('Eifersucht', 21, 47, 210, 3.9),
       ('Klavier', 21, 47, 270, 4.5),
       ('Rammstein', 21, 47, 264, 4.7);

-- Album 48: Falling into You (Céline Dion) - 16 titres (Simplifié à 10)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('It\'s All Coming Back to Me Now', 22, 48, 440, 4.8),
       ('Because You Loved Me', 22, 48, 273, 4.9),
       ('Falling into You', 22, 48, 258, 4.5),
       ('All by Myself', 22, 48, 312, 4.7),
       ('Dreamin\' of You', 22, 48, 250, 4.3),
       ('Sola otra vez', 22, 48, 290, 4.1),
       ('River Deep - Mountain High', 22, 48, 290, 4.4),
       ('Call the Man', 22, 48, 255, 4.2),
       ('Make You Happy', 22, 48, 258, 4.0),
       ('Declaration of Love', 22, 48, 250, 4.3);

-- Album 49: A Head Full of Dreams (Coldplay) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('A Head Full of Dreams', 23, 49, 250, 4.5),
       ('Birds', 23, 49, 226, 4.3),
       ('Hymn for the Weekend', 23, 49, 258, 4.8),
       ('Everglow', 23, 49, 282, 4.6),
       ('Adventure of a Lifetime', 23, 49, 263, 4.7),
       ('Fun (feat. Tove Lo)', 23, 49, 250, 4.1),
       ('Kaleidoscope', 23, 49, 90, 3.9),
       ('Army of One', 23, 49, 260, 4.4),
       ('Amazing Day', 23, 49, 280, 4.2),
       ('Colour Spectrum', 23, 49, 100, 4.0),
       ('Up&Up', 23, 49, 390, 4.5);

-- Album 50: Parachutes (Coldplay) - 10 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Don\'t Panic', 23, 50, 147, 4.3),
       ('Shiver', 23, 50, 303, 4.5),
       ('Spies', 23, 50, 318, 4.1),
       ('Sparks', 23, 50, 226, 4.4),
       ('Yellow', 23, 50, 269, 4.9),
       ('Trouble', 23, 50, 271, 4.7),
       ('Parachutes', 23, 50, 46, 3.8),
       ('High Speed', 23, 50, 263, 4.2),
       ('We Never Change', 23, 50, 249, 4.0),
       ('Everything\'s Not Lost', 23, 50, 330, 4.6);

-- Album 51: Destin (Ninho) - 18 titres (Simplifié à 10)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Goutte d\'eau', 24, 51, 195, 4.6),
       ('Maman ne le sait pas', 24, 51, 210, 4.8),
       ('Paris c\'est magique', 24, 51, 200, 4.4),
       ('La vie qu\'on mène', 24, 51, 220, 4.7),
       ('Jeune Lossa', 24, 51, 185, 4.2),
       ('Un peu spécial', 24, 51, 230, 4.5),
       ('Money', 24, 51, 205, 4.1),
       ('Destin', 24, 51, 215, 4.3),
       ('À contresens', 24, 51, 190, 4.0),
       ('Dis-moi que tu m\'aimes', 24, 51, 225, 4.6);

-- Album 52: Jefe (Ninho) - 15 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Jefe', 24, 52, 210, 4.7),
       ('VVS', 24, 52, 200, 4.5),
       ('Goutte d\'eau', 24, 52, 195, 4.6),
       ('Intro', 24, 52, 120, 4.0),
       ('Dima', 24, 52, 220, 4.3),
       ('OG', 24, 52, 205, 4.1),
       ('Nés sous la même étoile', 24, 52, 230, 4.5),
       ('No Life', 24, 52, 190, 4.2),
       ('Yaro', 24, 52, 215, 4.4),
       ('Outro', 24, 52, 180, 4.0),
       ('Chauves-souris', 24, 52, 200, 4.3),
       ('Jamais', 24, 52, 225, 4.6),
       ('Mode S', 24, 52, 210, 4.1),
       ('Sans permis', 24, 52, 195, 4.2),
       ('Vie d\'artiste', 24, 52, 230, 4.4);

-- Album 53: 21 (Adele) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Rolling in the Deep', 25, 53, 228, 4.9),
       ('Rumour Has It', 25, 53, 223, 4.5),
       ('Turning Tables', 25, 53, 250, 4.3),
       ('Don\'t You Remember', 25, 53, 243, 4.6),
       ('Set Fire to the Rain', 25, 53, 241, 4.8),
       ('He Won\'t Go', 25, 53, 270, 4.1),
       ('Take It All', 25, 53, 228, 4.4),
       ('I\'ll Be Waiting', 25, 53, 241, 4.2),
       ('One and Only', 25, 53, 300, 4.5),
       ('Lovesong', 25, 53, 316, 4.0),
       ('Someone Like You', 25, 53, 285, 5.0);

-- Album 54: 30 (Adele) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Strangers by Nature', 25, 54, 182, 4.3),
       ('Easy on Me', 25, 54, 224, 4.8),
       ('My Little Love', 25, 54, 390, 4.5),
       ('Cry Your Heart Out', 25, 54, 212, 4.2),
       ('Oh My God', 25, 54, 225, 4.6),
       ('Can I Get It', 25, 54, 200, 4.1),
       ('I Drink Wine', 25, 54, 345, 4.4),
       ('All Night Parking (with Erroll Garner)', 25, 54, 161, 4.0),
       ('Woman Like Me', 25, 54, 300, 4.3),
       ('Hold On', 25, 54, 381, 4.7),
       ('To Be Loved', 25, 54, 400, 4.9),
       ('Love Is a Game', 25, 54, 360, 4.5);

-- Album 55: Night Visions (Imagine Dragons) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Radioactive', 26, 55, 187, 4.9),
       ('Tiptoe', 26, 55, 202, 4.3),
       ('It\'s Time', 26, 55, 200, 4.6),
       ('Demons', 26, 55, 177, 4.8),
       ('On Top of the World', 26, 55, 189, 4.5),
       ('Amsterdam', 26, 55, 241, 4.2),
       ('Hear Me', 26, 55, 210, 4.1),
       ('Every Night', 26, 55, 207, 4.0),
       ('Bleeding Out', 26, 55, 222, 4.3),
       ('Underdog', 26, 55, 210, 4.4),
       ('Nothing Left to Say / Rocks', 26, 55, 420, 4.7);

-- Album 56: Evolve (Imagine Dragons) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('I Don\'t Know Why', 26, 56, 190, 4.3),
       ('Whatever It Takes', 26, 56, 201, 4.7),
       ('Believer', 26, 56, 204, 4.9),
       ('Walking the Wire', 26, 56, 230, 4.5),
       ('Rise Up', 26, 56, 210, 4.2),
       ('Make It Up to You', 26, 56, 222, 4.1),
       ('Yesterday', 26, 56, 200, 4.0),
       ('Mouth of the River', 26, 56, 230, 4.4),
       ('Thunder', 26, 56, 187, 4.8),
       ('Start Over', 26, 56, 187, 4.3),
       ('Dancing in the Dark', 26, 56, 200, 4.5);

-- Album 57: Chambre 12 (Louane) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Jour 1', 27, 57, 197, 4.6),
       ('Avenir', 27, 57, 235, 4.8),
       ('Chambre 12', 27, 57, 200, 4.4),
       ('Maman', 27, 57, 210, 4.5),
       ('Jeune', 27, 57, 190, 4.2),
       ('Tourne', 27, 57, 220, 4.1),
       ('Inscrite à l\'histoire', 27, 57, 205, 4.3),
       ('Notre amour nous sauvera', 27, 57, 215, 4.0),
       ('Du courage', 27, 57, 185, 4.5),
       ('Rester seule', 27, 57, 230, 4.3),
       ('Alien', 27, 57, 200, 4.1),
       ('Si t\'étais là', 27, 57, 225, 4.6),
       ('Je vole', 27, 57, 200, 4.7);

-- Album 58: Joie de vivre (Louane) - 19 titres (Simplifié à 10)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Donne-moi ton cœur', 27, 58, 190, 4.5),
       ('Tornade', 27, 58, 200, 4.3),
       ('Fais le bien', 27, 58, 210, 4.1),
       ('Derrière le brouillard (avec Grand Corps Malade)', 27, 58, 230, 4.8),
       ('Aimer à mort', 27, 58, 205, 4.4),
       ('Toute ma vie', 27, 58, 220, 4.2),
       ('J\'peux pas', 27, 58, 185, 4.0),
       ('Sans toi', 27, 58, 215, 4.5),
       ('Désolée', 27, 58, 195, 4.3),
       ('Joie de vivre', 27, 58, 230, 4.6);

-- Album 59: L\'Autre... (Mylène Farmer) - 10 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('L\'Autre', 28, 59, 285, 4.5),
       ('Désenchantée', 28, 59, 290, 4.9),
       ('Je t\'aime mélancolie', 28, 59, 260, 4.7),
       ('Regrets', 28, 59, 300, 4.3),
       ('Beyond My Control', 28, 59, 275, 4.4),
       ('Mylène s\'en fout', 28, 59, 240, 4.1),
       ('Psychopath', 28, 59, 255, 4.2),
       ('Pas de doute', 28, 59, 270, 4.0),
       ('Il n\'y a pas d\'ailleurs', 28, 59, 280, 4.6),
       ('Ainsi soit je...', 28, 59, 260, 4.5);

-- Album 60: Innamoramento (Mylène Farmer) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('L\'Amour naissant', 28, 60, 290, 4.4),
       ('L\'Âme-Stram-Gram', 28, 60, 260, 4.7),
       ('Innamoramento', 28, 60, 320, 4.5),
       ('Optimistique-moi', 28, 60, 270, 4.3),
       ('XXL', 28, 60, 285, 4.1),
       ('Souviens-toi du jour...', 28, 60, 275, 4.6),
       ('Moi Mon Idiot', 28, 60, 265, 4.0),
       ('Dessine-moi un mouton', 28, 60, 255, 4.2),
       ('Je te rends ton amour', 28, 60, 290, 4.5),
       ('Effets secondaires', 28, 60, 280, 4.3),
       ('Pas le temps de vivre', 28, 60, 270, 4.1),
       ('Plus grandir', 28, 60, 260, 4.4),
       ('Vénus', 28, 60, 280, 4.6);

-- Album 61: Love (Julien Doré) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Viborg', 29, 61, 220, 4.3),
       ('Paris-Seychelles', 29, 61, 235, 4.8),
       ('On attendra l\'hiver', 29, 61, 240, 4.5),
       ('Habemus Papam', 29, 61, 210, 4.1),
       ('Chou Wasabi', 29, 61, 225, 4.6),
       ('Londonienne', 29, 61, 200, 4.0),
       ('Lovely Loon', 29, 61, 230, 4.2),
       ('Winchester', 29, 61, 215, 4.4),
       ('Corbeau Blanc', 29, 61, 245, 4.7),
       ('Les Bords de Mer', 29, 61, 200, 4.3),
       ('Vingt Ans', 29, 61, 235, 4.1),
       ('Kiss Me Forever', 29, 61, 220, 4.5),
       ('Toutes les Femmes', 29, 61, 205, 4.0),
       ('Comme Un Homme', 29, 61, 230, 4.2);

-- Album 62: Aimée (Julien Doré) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('La Fièvre', 29, 62, 210, 4.6),
       ('Nous', 29, 62, 225, 4.8),
       ('Kiki', 29, 62, 200, 4.4),
       ('Waf', 29, 62, 230, 4.7),
       ('Barracuda', 29, 62, 215, 4.3),
       ('L\'île au Lendemain', 29, 62, 205, 4.1),
       ('Ami', 29, 62, 220, 4.5),
       ('Mon Amour De Toujours', 29, 62, 235, 4.2),
       ('Aimée', 29, 62, 240, 4.6),
       ('Woodstock', 29, 62, 200, 4.0),
       ('La Baie Des Rois', 29, 62, 225, 4.4);

-- Album 63: Vianney (Vianney) - 11 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Je m\'en vais', 30, 63, 195, 4.7),
       ('Dès le début', 30, 63, 200, 4.3),
       ('Aux débutants de la vie', 30, 63, 210, 4.5),
       ('Le Fils à papa', 30, 63, 225, 4.1),
       ('Sans le dire', 30, 63, 205, 4.2),
       ('Tombe la neige', 30, 63, 190, 4.0),
       ('J\'m\'en fous', 30, 63, 230, 4.4),
       ('Quand on est seul', 30, 63, 215, 4.6),
       ('L\'homme et l\'ombre', 30, 63, 240, 4.8),
       ('Est-ce que tu danses ?', 30, 63, 200, 4.3),
       ('Mes repères', 30, 63, 225, 4.5);

-- Album 64: Idées Blanches (Vianney) - 12 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Je te déteste', 30, 64, 210, 4.5),
       ('Pas là', 30, 64, 200, 4.7),
       ('Veronica', 30, 64, 220, 4.3),
       ('Idées blanches', 30, 64, 230, 4.1),
       ('Les instruments', 30, 64, 205, 4.2),
       ('Pour de vrai', 30, 64, 195, 4.0),
       ('On est bien comme ça', 30, 64, 235, 4.4),
       ('La chanson dormante', 30, 64, 215, 4.6),
       ('Notre Dame des Oiseaux', 30, 64, 240, 4.8),
       ('Ma petite chérie', 30, 64, 200, 4.3),
       ('Tu es mon amour', 30, 64, 225, 4.5),
       ('Chère maman', 30, 64, 210, 4.2);

-- Album 65: L\'Apogée (Sexion d\'Assaut) - 17 titres (Simplifié à 10)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Avant qu\'elle parte', 31, 65, 200, 4.7),
       ('Ma direction', 31, 65, 210, 4.5),
       ('Dis-moi que tu m\'aimes', 31, 65, 230, 4.3),
       ('Wati House', 31, 65, 190, 4.1),
       ('Problèmes d\'adultes', 31, 65, 225, 4.6),
       ('Rien de méchant', 31, 65, 205, 4.2),
       ('J\'ai pas les mots', 31, 65, 240, 4.4),
       ('Balader', 31, 65, 215, 4.0),
       ('Africain', 31, 65, 200, 4.5),
       ('L\'Apogée', 31, 65, 230, 4.7);

-- Album 66: L\'Écrasement de tête (Sexion d\'Assaut) - 18 titres (Simplifié à 10)
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Désolé', 31, 66, 220, 4.8),
       ('Mon Ghetto', 31, 66, 195, 4.5),
       ('Wati by Night', 31, 66, 230, 4.3),
       ('L\'Écrasement de tête', 31, 66, 210, 4.1),
       ('Changement d\'ambiance', 31, 66, 225, 4.6),
       ('Encore une fois', 31, 66, 200, 4.2),
       ('Ne t\'inquiète pas', 31, 66, 240, 4.4),
       ('Triste époque', 31, 66, 215, 4.0),
       ('Casquette à l\'envers', 31, 66, 200, 4.5),
       ('Retour au quartier', 31, 66, 230, 4.7);

-- Album 67: Random Access Memories (Daft Punk) - 13 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('Give Life Back to Music', 32, 67, 271, 4.6),
       ('The Game of Love', 32, 67, 282, 4.3),
       ('Giorgio by Moroder', 32, 67, 515, 4.8),
       ('Within', 32, 67, 248, 4.1),
       ('Instant Crush (feat. Julian Casablancas)', 32, 67, 337, 4.9),
       ('Lose Yourself to Dance (feat. Pharrell Williams)', 32, 67, 353, 4.5),
       ('Touch (feat. Paul Williams)', 32, 67, 292, 4.7),
       ('Get Lucky (feat. Pharrell Williams)', 32, 67, 307, 5.0),
       ('Beyond', 32, 67, 310, 4.3),
       ('Motherboard', 32, 67, 308, 4.2),
       ('Fragments of Time (feat. Todd Edwards)', 32, 67, 280, 4.5),
       ('Doin\' It Right (feat. Panda Bear)', 32, 67, 251, 4.4),
       ('Contact', 32, 67, 370, 4.6);

-- Album 68: Discovery (Daft Punk) - 14 titres
INSERT INTO song (name, artist_id, album_id, duration, note)
VALUES ('One More Time', 32, 68, 320, 4.9),
       ('Aerodynamic', 32, 68, 202, 4.5),
       ('Digital Love', 32, 68, 298, 4.7),
       ('Harder, Better, Faster, Stronger', 32, 68, 224, 4.8),
       ('Crescendolls', 32, 68, 211, 4.1),
       ('Something About Us', 32, 68, 230, 4.6),
       ('Veridis Quo', 32, 68, 345, 4.3),
       ('Short Circuit', 32, 68, 206, 4.0),
       ('Face to Face', 32, 68, 248, 4.4),
       ('Too Long', 32, 68, 608, 4.2),
       ('Voyager', 32, 68, 227, 4.1),
       ('High Life', 32, 68, 217, 4.3),
       ('Phoenix', 32, 68, 247, 4.5),
       ('Fresh', 32, 68, 260, 4.6);

-- RÉTABLIR LES CLÉS ÉTRANGÈRES
SET FOREIGN_KEY_CHECKS = 1;

-- FIN DU SCRIPT