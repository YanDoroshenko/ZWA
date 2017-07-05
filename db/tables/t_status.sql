CREATE TABLE t_status (
  id          INT                  AUTO_INCREMENT,
  title       VARCHAR(55) NOT NULL UNIQUE,
  description VARCHAR(255),
  icon_path   VARCHAR(255),
  system      BOOLEAN     NOT NULL DEFAULT FALSE,
  final       BOOLEAN     NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id)
);