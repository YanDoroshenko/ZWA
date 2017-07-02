CREATE TABLE t_action (
  id            INT AUTO_INCREMENT,
  name          VARCHAR(55) NOT NULL UNIQUE,
  description   VARCHAR(255),
  source_status INT         NOT NULL,
  target_status INT         NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (source_status) REFERENCES t_status (id),
  FOREIGN KEY (target_status) REFERENCES t_status (id),
  UNIQUE (source_status, target_status)
);