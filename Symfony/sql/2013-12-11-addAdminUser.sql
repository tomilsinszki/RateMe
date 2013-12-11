INSERT INTO user (username, salt, password, is_active) VALUES ('marton.tamas', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='marton.tamas';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='marton.tamas';

