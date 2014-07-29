-- Rename rateableCollection 'Pécs-Harkány' to 'Pécs'
UPDATE rateable_collection SET name='Pécs' WHERE name='Pécs-Harkány';

-- Create rateableCollection 'Harkány'
INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FAHSV&size=250x250', 'AHSV', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, question_order_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 1 AS question_order_id, 'Harkány' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='AHSV';

-- Create rateableCollection 'Pest'
INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FBRGC&size=250x250', 'BRGC', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, question_order_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 1 AS question_order_id, 'Pest' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='BRGC';



-- Archive rateable 'Bécsi Nikolett'
UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='becsi.nikolett');
UPDATE user SET is_active=0 WHERE username='becsi.nikolett';

-- Archive rateable 'Végh Zsuzsanna'
UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='vegh.zsuzsanna');
UPDATE user SET is_active=0 WHERE username='vegh.zsuzsanna';

-- Move rateable 'Eizler Kitti' to rateableCollection 'Harkány'
UPDATE rateable SET collection_id=(SELECT id FROM rateable_collection WHERE name='Harkány') WHERE name='Eizler Kitti';

-- Create rateable 'Fehérvári Ildikó' and add to rateableCollection 'Pest'
INSERT INTO user (username, salt, password, is_active) VALUES ('fehervari.ildiko', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='fehervari.ildiko';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Fehérvári Ildikó' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Pest' AND u.username='fehervari.ildiko';

-- Create rateable 'Hegyes Tímea' and add to rateableCollection 'Pest'
INSERT INTO user (username, salt, password, is_active) VALUES ('hegyes.timea', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='hegyes.timea';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Hegyes Tímea' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Pest' AND u.username='hegyes.timea';

