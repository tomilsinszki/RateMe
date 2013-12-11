INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Nyiri Ágnes' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Tata' AND u.username='nyiri.agnes';

INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Petroviczné Szalai Veronika' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Tata' AND u.username='petroviczne.szalai.veronika';

