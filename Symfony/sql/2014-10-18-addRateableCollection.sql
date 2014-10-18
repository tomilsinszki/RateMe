INSERT INTO company (name, ratingPageBackgroundColor, ratingPageFontColor, ratingPageStarsSubtitleFontColor, ratingPageCancelSubratingFontColor, ratingEmailBackgroundColor, ratingEmailFontColor) VALUES ('Benyovszky Orvosi Központ', 'D0E0EB', '607848', '607848', '607848', 'EBF7F8', '607848');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'Benyovszky Orvosi Központ' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='Benyovszky Orvosi Központ';

DELETE FROM rateable_collection_owner WHERE user_id=(SELECT id FROM user WHERE username='marton.tamas');
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='marton.tamas';

DELETE FROM rateable_collection_owner WHERE user_id=(SELECT id FROM user WHERE username='ilsinszki.tamas');
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='ilsinszki.tamas';

DELETE FROM rateable_collection_owner WHERE user_id=(SELECT id FROM user WHERE username='nguyen.tuan');
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='nguyen.tuan';
