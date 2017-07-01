CREATE TABLE t_status (
  id          INT,
  title       VARCHAR(55) NOT NULL UNIQUE,
  description VARCHAR(255),
  icon_path   VARCHAR(255),
  final       BOOLEAN     NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id)
);