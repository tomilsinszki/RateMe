INSERT INTO user (username, salt, password, is_active) VALUES ('varga.andrea.lusy', 'a279dd3f27019c2bc6a937275fb381e4', '194c1e6939d1da6ed1ed6ddbac4df8c3e614e232', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='varga.andrea.lusy';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Varga Andrea Lusy' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Kaposvár (Honvéd u.)' AND u.username='varga.andrea.lusy';

