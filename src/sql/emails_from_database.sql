DROP TABLE IF EXISTS `ublaboo_emails_templates`;
CREATE TABLE `ublaboo_emails_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` text COLLATE utf8_unicode_ci NOT NULL,
  `source` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
