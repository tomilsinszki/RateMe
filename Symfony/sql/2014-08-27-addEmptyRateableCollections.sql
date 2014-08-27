INSERT INTO company (name)  VALUES ('TEST1');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST1' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST1';

INSERT INTO company (name)  VALUES ('TEST2');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST2' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST2';

INSERT INTO company (name)  VALUES ('TEST3');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST3' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST3';

INSERT INTO company (name)  VALUES ('TEST4');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST4' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST4';

INSERT INTO company (name)  VALUES ('TEST5');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST5' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST5';

INSERT INTO company (name)  VALUES ('TEST6');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST6' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST6';

INSERT INTO company (name)  VALUES ('TEST7');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST7' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST7';

INSERT INTO company (name)  VALUES ('TEST8');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST8' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST8';

INSERT INTO company (name)  VALUES ('TEST9');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST9' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST9';

INSERT INTO company (name)  VALUES ('TEST10');
INSERT INTO rateable_collection (company_id, question_order_id, name, created, updated) SELECT c.id AS company_id, 1 AS question_order_id, 'TEST10' AS name, NOW() AS created, NOW() AS updated FROM company c WHERE c.name='TEST10';

