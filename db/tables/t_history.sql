CREATE TABLE t_history (
  id        INT AUTO_INCREMENT,
  task      INT,
  action    INT,
  actor     INT,
  timepoint TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (task) REFERENCES t_task (id),
  FOREIGN KEY (action) REFERENCES t_action (id),
  FOREIGN KEY (actor) REFERENCES t_user (id)
);