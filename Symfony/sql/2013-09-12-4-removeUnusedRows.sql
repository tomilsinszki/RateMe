DELETE FROM rateable_collection_owner;

DELETE FROM user_group WHERE user_id=(SELECT id FROM user WHERE username='somtel.manager');
DELETE FROM user WHERE username='somtel.manager';

DELETE FROM user_group WHERE user_id=(SELECT id FROM user WHERE username='kspartner.manager');
DELETE FROM user WHERE username='kspartner.manager';

