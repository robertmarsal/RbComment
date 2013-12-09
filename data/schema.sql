CREATE TABLE IF NOT EXISTS `rb_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread` varchar(40) NOT NULL,
  `uri` varchar(250) NOT NULL,
  `author` varchar(150) NOT NULL,
  `contact` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `spam` tinyint(1) NOT NULL,
  `published_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARSET=utf8;