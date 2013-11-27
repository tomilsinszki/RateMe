UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='acs-gergely.zita');
UPDATE user SET is_active=0 WHERE username='acs-gergely.zita';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='valint.sarolt');
UPDATE user SET is_active=0 WHERE username='valint.sarolt';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='jordanics.magdolna');
UPDATE user SET is_active=0 WHERE username='jordanics.magdolna';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='gozo.viktoria');
UPDATE user SET is_active=0 WHERE username='gozo.viktoria';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='jordanics.julianna');
UPDATE user SET is_active=0 WHERE username='jordanics.julianna';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='kocsis.zsuzsanna');
UPDATE user SET is_active=0 WHERE username='kocsis.zsuzsanna';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='molnar.edina');
UPDATE user SET is_active=0 WHERE username='molnar.edina';

