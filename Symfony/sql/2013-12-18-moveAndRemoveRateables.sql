UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Mosonmmagyaróvár (TESCO)') WHERE name='Menyhárt János';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='szilvas.aniko');
UPDATE user SET is_active=0 WHERE username='szilvas.aniko';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='szatmari.dora');
UPDATE user SET is_active=0 WHERE username='szatmari.dora';

