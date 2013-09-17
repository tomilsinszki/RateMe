INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FS001&size=250x250', 'S001', NOW(), NOW());
UPDATE rateable_collection SET identifier_id=(SELECT id FROM identifier WHERE alphanumeric_value='S001') WHERE name='Somtel';
DELETE FROM identifier WHERE alphanumeric_value='1111';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FK001&size=250x250', 'K001', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Győr (Teleki u.)' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='K001';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FK002&size=250x250', 'K002', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Kaposvár (Honvéd u.)' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='K002';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FK003&size=250x250', 'K003', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Mosonmmagyaróvár (TESCO)' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='K003';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV001&size=250x250', 'V001', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Csorna' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V001';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV002&size=250x250', 'V002', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Győr Pláza' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V002';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV003&size=250x250', 'V003', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Kaposvár (Szántó u.)' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V003';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV004&size=250x250', 'V004', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Kapuvár' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V004';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV005&size=250x250', 'V005', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Mosonmmagyaróvár' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V005';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV006&size=250x250', 'V006', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Pécs-Harkány' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V006';

INSERT INTO identifier (qr_code_url, alphanumeric_value, created, updated) VALUE ('http://api.qrserver.com/v1/create-qr-code/?data=http%3A%2F%2Frate.me.uk%2Fhely%2Fazonosito%2FV007&size=250x250', 'V007', NOW(), NOW());
INSERT INTO rateable_collection (identifier_id, company_id, name, created, updated) SELECT i.id AS identifier_id, c.id AS company_id, 'Tata' AS name, NOW() AS created, NOW() AS updated FROM identifier i, company c WHERE c.name='Vidanet' AND i.alphanumeric_value='V007';

