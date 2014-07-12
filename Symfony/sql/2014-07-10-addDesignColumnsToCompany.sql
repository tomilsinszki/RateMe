ALTER TABLE company ADD COLUMN ratingPageBackgroundColor VARCHAR(6) NOT NULL DEFAULT 'E8DDCB';
ALTER TABLE company ADD COLUMN ratingPageFontColor VARCHAR(6) NOT NULL DEFAULT '033649';
ALTER TABLE company ADD COLUMN ratingPageStarsSubtitleFontColor VARCHAR(6) NOT NULL DEFAULT 'CDB380';
ALTER TABLE company ADD COLUMN ratingPageCancelSubratingFontColor VARCHAR(6) NOT NULL DEFAULT '031634';
ALTER TABLE company ADD COLUMN ratingPromotionPrizeName VARCHAR(511) DEFAULT NULL;
ALTER TABLE company ADD COLUMN ratingPromotionRulesURL VARCHAR(255) DEFAULT NULL;

UPDATE company SET ratingPageBackgroundColor='DBC4E0' WHERE name='Vidanet';
UPDATE company SET ratingPageFontColor='791F7E' WHERE name='Vidanet';
UPDATE company SET ratingPageStarsSubtitleFontColor='F9A350' WHERE name='Vidanet';
UPDATE company SET ratingPageCancelSubratingFontColor='F9A350' WHERE name='Vidanet';
UPDATE company SET ratingPromotionPrizeName='Bosch konyhai robotgépet' WHERE name='Vidanet';
UPDATE company SET ratingPromotionRulesURL='http://www.vidanet.hu/segitseg/dokumentumok/akcio-szabalyzatok/aktualis-akciok/a-vidanet-zrt-2014-03-evi-ugyfelvelemeny-gyujtesi-nyeremenyjatekanak-reszletes-szabalyzata.pdf' WHERE name='Vidanet';

UPDATE company SET ratingPageBackgroundColor='F7F0DE' WHERE name='Lipóti Pékség';
UPDATE company SET ratingPageFontColor='884E05' WHERE name='Lipóti Pékség';
UPDATE company SET ratingPageStarsSubtitleFontColor='F47F39' WHERE name='Lipóti Pékség';
UPDATE company SET ratingPageCancelSubratingFontColor='CA2718' WHERE name='Lipóti Pékség';
UPDATE company SET ratingPromotionPrizeName='egy kétfős, 2 napos hétvégét a lipóti Orchidea Hotelben' WHERE name='Lipóti Pékség';

