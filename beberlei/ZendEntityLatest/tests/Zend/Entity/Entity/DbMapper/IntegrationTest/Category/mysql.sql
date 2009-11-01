CREATE TABLE ze_category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    parent INT DEFAULT NULL
);
