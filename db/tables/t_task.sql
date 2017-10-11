CREATE TABLE t_task (
  id          INT                  AUTO_INCREMENT,
  name        VARCHAR(55) NOT NULL,
  description VARCHAR(255),
  parent      INT,
  created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deadline    TIMESTAMP   NULL,
  priority    INT         NOT NULL DEFAULT 5,
  status      INT         NOT NULL,
  reporter    INT         NOT NULL,
  assignee    INT,
  PRIMARY KEY (id),
  FOREIGN KEY (status) REFERENCES t_status (id),
  FOREIGN KEY (reporter) REFERENCES t_user (id),
  FOREIGN KEY (assignee) REFERENCES t_user (id),
  CHECK (NOT parent = id),
  CHECK (priority > 0),
  CHECK (priority <= 10)
);
ALTER TABLE t_task
  ADD FOREIGN KEY (parent) REFERENCES t_task (id);
ALTER TABLE t_task
  ADD CONSTRAINT CHECK (parent IS NULL OR priority >= (SELECT t.priority
                                                       FROM t_task t
                                                       WHERE parent = t.id));