-- auto-generated definition
CREATE TABLE user_order
(
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    VARCHAR(50)                         NOT NULL,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  order_json LONGTEXT                            NOT NULL
);