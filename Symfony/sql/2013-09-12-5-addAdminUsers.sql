SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `rateable_collection_owner` DROP INDEX `UNIQ_5305B2EA76ED395`;
ALTER TABLE `rateable_collection_owner` ADD INDEX `IDX_5305B2EA76ED395` (`user_id` ASC);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;



INSERT INTO user (username, salt, password, is_active) VALUES ('takacs.laszlo', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='takacs.laszlo';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='takacs.laszlo';

INSERT INTO user (username, salt, password, is_active) VALUES ('hushegyi.karoly', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='hushegyi.karoly';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='hushegyi.karoly';

INSERT INTO user (username, salt, password, is_active) VALUES ('sejben.zsolt', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='sejben.zsolt';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='sejben.zsolt';

INSERT INTO user (username, salt, password, is_active) VALUES ('sator.csaba', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='sator.csaba';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='sator.csaba';

INSERT INTO user (username, salt, password, is_active) VALUES ('horvath.csaba', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='horvath.csaba';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='horvath.csaba';

INSERT INTO user (username, salt, password, is_active) VALUES ('horvath.norbert', 'eeace1a7b2086d1ade3b0298aa0c8aee', '11e123888cdd597286932a3c90f7640d2ba33b1f', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='horvath.norbert';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='horvath.norbert';

INSERT INTO user (username, salt, password, is_active) VALUES ('schuler.zoltan', 'eeace1a7b2086d1ade3b0298aa0c8aee', '66a859f2cabced5fd4343b85c09eca69826308f2', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='schuler.zoltan';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='schuler.zoltan' AND rc.name='Győr (Teleki u.)';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='schuler.zoltan' AND rc.name='Kaposvár (Honvéd u.)';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='schuler.zoltan' AND rc.name='Mosonmmagyaróvár (TESCO)';

INSERT INTO user (username, salt, password, is_active) VALUES ('varga.zsuzsanna', 'eeace1a7b2086d1ade3b0298aa0c8aee', '66a859f2cabced5fd4343b85c09eca69826308f2', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='varga.zsuzsanna';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='varga.zsuzsanna' AND rc.name='Győr (Teleki u.)';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='varga.zsuzsanna' AND rc.name='Kaposvár (Honvéd u.)';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='varga.zsuzsanna' AND rc.name='Mosonmmagyaróvár (TESCO)';

INSERT INTO user (username, salt, password, is_active) VALUES ('horvath.andrea', 'eeace1a7b2086d1ade3b0298aa0c8aee', 'fdbe0a12e2079b36cf3b246d8f9d7d2b98f7dc37', 1);
INSERT INTO user_group (user_id, group_id) SELECT u.id AS user_id, r.id AS group_id FROM user u, role r WHERE r.role='ROLE_MANAGER' AND u.username='horvath.andrea';
INSERT INTO rateable_collection_owner (collection_id, user_id) SELECT rc.id AS collection_id, u.id AS user_id FROM user u, rateable_collection rc WHERE u.username='horvath.andrea' AND rc.name='Somtel';

