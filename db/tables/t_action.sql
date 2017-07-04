CREATE TABLE t_action (
  id            INT                AUTO_INCREMENT,
  task          INT,
  source_status INT,
  target_status INT,
  actor         INT,
  timepoint     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  description   VARCHAR(1000),
  PRIMARY KEY (id),
  FOREIGN KEY (task) REFERENCES t_task (id),
  FOREIGN KEY (source_status) REFERENCES t_status (id),
  FOREIGN KEY (target_status) REFERENCES t_status (id),
  FOREIGN KEY (actor) REFERENCES t_user (id),
  CHECK (source_status != t_action.target_status OR description IS NOT NULL)
);