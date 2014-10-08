ALTER TABLE company ADD COLUMN ratingEmailBackgroundColor VARCHAR(6) NOT NULL DEFAULT 'FFFFFF';
ALTER TABLE company ADD COLUMN ratingEmailFontColor VARCHAR(6) NOT NULL DEFAULT '000000';

UPDATE company SET ratingEmailBackgroundColor='FFFFFF';
UPDATE company SET ratingEmailFontColor='000000';

UPDATE company SET ratingEmailBackgroundColor='F2EAF3' WHERE name='Vidanet';
UPDATE company SET ratingEmailFontColor='791F7E' WHERE name='Vidanet';

