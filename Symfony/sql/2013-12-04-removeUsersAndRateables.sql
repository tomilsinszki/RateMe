UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='bertha.viktoria');
UPDATE user SET is_active=0 WHERE username='bertha.viktoria';

