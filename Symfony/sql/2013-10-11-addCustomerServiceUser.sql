INSERT INTO user (username, salt, password, is_active) VALUES ('madar.zsanett', '8d4ede245edbeef76bb885e2f5340b75', '2a0026d10e9f190a05166d043b0d2663a421526c', 1);

INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT 2, u.id, 'Madár Zsanett', 'Ügyfélszolgálatos', 0, 1, NOW(), NOW() FROM user u WHERE u.username='madar.zsanett';

INSERT INTO user_group (user_id, group_id) SELECT u.id, r.id FROM user u, role r WHERE u.username='madar.zsanett' AND r.role='ROLE_CUSTOMERSERVICE';

