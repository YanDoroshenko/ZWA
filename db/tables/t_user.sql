CREATE TABLE t_user (
  id            INT AUTO_INCREMENT,
  login         VARCHAR(55) UNIQUE NOT NULL,
  password_hash VARCHAR(255)       NOT NULL,
  name          VARCHAR(255),
  PRIMARY KEY (id)
);