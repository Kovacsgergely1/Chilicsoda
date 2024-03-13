CREATE TABLE users(
    user_id INT AUTO_INCREMENT,
    username varchar(200) UNIQUE,
    password varchar(200),
    location varchar(200),
    phone_number BIGINT(255),
    email varchar(200) UNIQUE,
    full_name varchar(200),
    PRIMARY KEY(user_id)
);



-- webshop ------------------------------------------------------------------------------

CREATE TABLE brand(
    brand_id int AUTO_INCREMENT,
    brand_name varchar(200),
    PRIMARY KEY(brand_id)
);

CREATE OR REPLACE TABLE product(
    product_id int AUTO_INCREMENT,
    product_name varchar(200),
    product_description varchar(5000),
    price int,
    sale FLOAT,
    brand_id int,
    available BOOLEAN DEFAULT TRUE,
    in_stock int,
    spiciness int,

    CONSTRAINT chk_spiciness_range CHECK (spiciness >=0 AND spiciness <= 5),
    CONSTRAINT chk_sale_range CHECK (sale >= 0 AND sale <= 1),

    PRIMARY KEY (product_id),
    FOREIGN KEY(brand_id) REFERENCES brand(brand_id)
);

CREATE TABLE product_image(
    image_id int AUTO_INCREMENT,
    product_id int,
    image_data varchar(500),

    PRIMARY KEY(image_id),
    FOREIGN KEY(product_id) REFERENCES product(product_id)
);

-- ALTER TABLE product
-- ADD CONSTRAINT chk_spiciness_range CHECK (spiciness >=0 AND spiciness <= 5);

CREATE OR REPLACE TABLE orders(
    user_id int,
    order_id int AUTO_INCREMENT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    order_price int,

    PRIMARY KEY(order_id),
    FOREIGN KEY(user_id) REFERENCES users(user_id)
);

CREATE OR REPLACE TABLE product_combination(
    order_id int,
    product_id int,
    piece int,
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