INSERT INTO user (username, salt, password, is_active) VALUES ('baranyaine.herold.andrea', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='baranyaine.herold.andrea';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Baranyainé Herold Andrea' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Csorna' AND u.username='baranyaine.herold.andrea';

INSERT INTO user (username, salt, password, is_active) VALUES ('varga.andrea', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='varga.andrea';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Varga Andrea' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Csorna' AND u.username='varga.andrea';

INSERT INTO user (username, salt, password, is_active) VALUES ('bokor.zsuzsanna', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='bokor.zsuzsanna';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Bokor Zsuzsanna' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Győr Pláza' AND u.username='bokor.zsuzsanna';

INSERT INTO user (username, salt, password, is_active) VALUES ('ihasz.bea', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='ihasz.bea';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Ihász Bea' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Győr Pláza' AND u.username='ihasz.bea';

INSERT INTO user (username, salt, password, is_active) VALUES ('wachtler.agota', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='wachtler.agota';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Némethné Wachtler Ágota' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Győr Pláza' AND u.username='wachtler.agota';

INSERT INTO user (username, salt, password, is_active) VALUES ('szoke.veronika', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='szoke.veronika';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Szőke Veronika' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Győr Pláza' AND u.username='szoke.veronika';

INSERT INTO user (username, salt, password, is_active) VALUES ('varga.ibolya', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='varga.ibolya';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Varga Ibolya' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Győr Pláza' AND u.username='varga.ibolya';

INSERT INTO user (username, salt, password, is_active) VALUES ('becsi.nikolett', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='becsi.nikolett';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Bécsi Nikolett' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Kaposvár (Szántó u.)' AND u.username='becsi.nikolett';

INSERT INTO user (username, salt, password, is_active) VALUES ('vegh.zsuzsanna', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='vegh.zsuzsanna';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Végh Zsuzsanna' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Kaposvár (Szántó u.)' AND u.username='vegh.zsuzsanna';

INSERT INTO user (username, salt, password, is_active) VALUES ('fordos.andrea', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='fordos.andrea';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Fördős Andrea' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Kapuvár' AND u.username='fordos.andrea';

INSERT INTO user (username, salt, password, is_active) VALUES ('hende.judit', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='hende.judit';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Hende Judit' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Kapuvár' AND u.username='hende.judit';

INSERT INTO user (username, salt, password, is_active) VALUES ('freyne.anka.zsuzsanna', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='freyne.anka.zsuzsanna';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Freyné Anka Zsuzsanna' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Mosonmmagyaróvár' AND u.username='freyne.anka.zsuzsanna';

INSERT INTO user (username, salt, password, is_active) VALUES ('nemedi.rita', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='nemedi.rita';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Némedi Rita' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Mosonmmagyaróvár' AND u.username='nemedi.rita';

INSERT INTO user (username, salt, password, is_active) VALUES ('eizler.kitti', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='eizler.kitti';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Eizler Kitti' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Pécs-Harkány' AND u.username='eizler.kitti';

INSERT INTO user (username, salt, password, is_active) VALUES ('szenasy.zoltanne', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='szenasy.zoltanne';
INSERT INTO rateable (collection_id, rateable_user_id, name, type_name, is_reachable_via_telephone, is_active, created, updated) SELECT rc.id AS collection_id, u.id AS rateable_user_id, 'Szénásy Zoltánné' AS name, 'Ügyfélszolgálatos' AS type_name, 0 AS is_reachable_via_telephone, 1 AS is_active, NOW() AS created, NOW() AS updated FROM rateable_collection rc, user u WHERE rc.name='Pécs-Harkány' AND u.username='szenasy.zoltanne';

INSERT INTO user (username, salt, password, is_active) VALUES ('nyiri.agnes', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='nyiri.agnes';

INSERT INTO user (username, salt, password, is_active) VALUES ('petroviczne.szalai.veronika', '3e2df717f255ea5371498d8a592dbdd8', '8fe58ae9996d24863faf325b938f1a781d6881d9', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_CUSTOMERSERVICE' AND u.username='petroviczne.szalai.veronika';

