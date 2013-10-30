UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Győr (Teleki u.)') WHERE name='Madár Zsanett';
UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Győr (Teleki u.)') WHERE name='Szilvás Anikó';

UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Kaposvár (Honvéd u.)') WHERE name='Kovács Gyula';
UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Kaposvár (Honvéd u.)') WHERE name='Árbogászt Fanni';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='bekesi.alexandra');
UPDATE user SET is_active=0 WHERE username='bekesi.alexandra';

DELETE FROM rateable_collection_owner WHERE collection_id=(SELECT id FROM rateable_collection WHERE name='KS Partner');
DELETE FROM rateable_collection WHERE name='KS Partner';
DELETE FROM identifier WHERE alphanumeric_value='2222';

