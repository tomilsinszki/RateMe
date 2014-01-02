ALTER TABLE rating ADD COLUMN rating_ip_address varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL AFTER email;

