SET
@new_id = 0;
UPDATE users
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE users AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;

SET
@new_id = 0;
UPDATE products
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE products AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;

SET
@new_id = 0;
UPDATE product_log
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE product_log AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;

SET
@new_id = 0;
UPDATE categories
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE categories AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;

SET
@new_id = 0;
UPDATE history
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE history AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;

SET
@new_id = 0;
UPDATE rating
SET id = (@new_id := @new_id + 1);
SELECT @new_id := COALESCE(MAX(id), 0) + 1
FROM users;
SET
@query = CONCAT('ALTER TABLE rating AUTO_INCREMENT = ', @new_id);
PREPARE statement FROM @query;
EXECUTE statement;
