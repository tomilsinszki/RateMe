ALTER TABLE `user` ADD COLUMN `email_address` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_8D93D649B08E074E ON `user` (`email_address`);

