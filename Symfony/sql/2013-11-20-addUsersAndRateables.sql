INSERT INTO user (username, salt, password, is_active) VALUES ('arvaine.kovacs.aniko', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='arvaine.kovacs.aniko';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Árvainé Kovács Anikó' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='arvaine.kovacs.aniko';

INSERT INTO user (username, salt, password, is_active) VALUES ('nemeth.nikolett', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='nemeth.nikolett';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Németh Nikolett' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='nemeth.nikolett';

INSERT INTO user (username, salt, password, is_active) VALUES ('jordanics.magdolna', 'a279dd3f27019c2bc6a937275fb381e4', 'a08d5deb8e19ef7c15ac6a9e422d2cc495749ce0', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='jordanics.magdolna';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Jordanics Magdolna' AS name, 'Telefonos ügyfélszolgálatos' AS type_name, 1 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Somtel' AND u.username='jordanics.magdolna';

