-- Add rateables with identifiers
INSERT INTO user (username, salt, password, is_active) VALUES ('babuszek.anita', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='babuszek.anita';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Babuszek Anita' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='babuszek.anita';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fazonosito%2FH63V&size=250x250', 'H63V', NOW(), NOW());
UPDATE rateable SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='H63V') WHERE name='Babuszek Anita';



INSERT INTO user (username, salt, password, is_active) VALUES ('hengerics.dora', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='hengerics.dora';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Hengerics Dóra' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='hengerics.dora';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fazonosito%2F39SL&size=250x250', '39SL', NOW(), NOW());
UPDATE rateable SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='39SL') WHERE name='Hengerics Dóra';



INSERT INTO user (username, salt, password, is_active) VALUES ('kovacs.hajnalka', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='kovacs.hajnalka';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Kovács Hajnalka' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='kovacs.hajnalka';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fazonosito%2F9R3B&size=250x250', '9R3B', NOW(), NOW());
UPDATE rateable SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='9R3B') WHERE name='Kovács Hajnalka';



INSERT INTO user (username, salt, password, is_active) VALUES ('voros.eszter', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='voros.eszter';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Vörös Eszter' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='voros.eszter';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fazonosito%2FT6F3&size=250x250', 'T6F3', NOW(), NOW());
UPDATE rateable SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='T6F3') WHERE name='Vörös Eszter';



-- Archive rateables
UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='mocsan.dea');
UPDATE user SET is_active=0 WHERE username='mocsan.dea';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='siposne.deak.timea');
UPDATE user SET is_active=0 WHERE username='siposne.deak.timea';

UPDATE rateable SET is_active=0 WHERE rateable_user_id=(SELECT id FROM user WHERE username='kotfas.judit');
UPDATE user SET is_active=0 WHERE username='kotfas.judit';

