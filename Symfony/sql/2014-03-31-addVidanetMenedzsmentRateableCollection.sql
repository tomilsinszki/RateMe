-- http://redmine.rate.me.uk/issues/117

-- Add rateable collection
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'Vidanet Menedzsment' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='Vidanet';

-- Add user and rateable
INSERT INTO user (username, salt, password, is_active) VALUES ('vidanet.menedzsment', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='vidanet.menedzsment';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Vidanet Menedzsment' AS name, 'Dolgozó' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Vidanet Menedzsment' AND u.username='vidanet.menedzsment';

-- Add identifier
INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fazonosito%2FVIDA&size=250x250', 'VIDA', NOW(), NOW());
UPDATE rateable SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='VIDA') WHERE name='Vidanet Menedzsment';

