CREATE TABLE users(
    user_id INT AUTO_INCREMENT,
    username varchar(200) UNIQUE,
    password varchar(200),
    location varchar(200),
    phone_number BIGINT(255),
    email varchar(200) UNIQUE,
    PRIMARY KEY(user_id)
);


-- webshop ------------------------------------------------------------------------------

CREATE OR REPLACE TABLE product(
    product_id int AUTO_INCREMENT,
    product_name varchar(200),
    product_description varchar(5000),
    product_image LONGBLOB,
    PRIMARY KEY (product_id),
    price int
);

CREATE OR REPLACE TABLE orders(
    user_id int,
    order_id int AUTO_INCREMENT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(order_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id)
);

CREATE OR REPLACE TABLE product_combination(
    order_id int,
    product_id int,
    FOREIGN KEY(order_id) REFERENCES orders(order_id),
    FOREIGN KEY(product_id) REFERENCES product(product_id)
);


-- Fórum ------------------------------------------------------------------------------

CREATE OR REPLACE TABLE forum_themes(
    theme_id int AUTO_INCREMENT,
    theme_name varchar(500) UNIQUE,
    user_id int,
    PRIMARY KEY(theme_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- DROP TABLE forum_message;

CREATE OR REPLACE TABLE forum_message(
    message_id BIGINT(255) AUTO_INCREMENT,
    message varchar(5000),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id int,
    theme_id int,
    PRIMARY KEY(message_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id),
    FOREIGN KEY (theme_id) REFERENCES forum_themes(theme_id)
);



-- DROP TABLE users;
-- DROP TABLE product_combination;
-- DROP TABLE orders;

-- SELECT username, order_id, order_date
-- FROM users
-- INNER JOIN orders ON users.user_id = orders.user_id;

-- INSERT INTO product_combination VALUES (3, 1), (3, 2), (4, 1), (5, 2);