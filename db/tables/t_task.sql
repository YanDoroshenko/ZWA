CREATE TABLE t_task (
  id          INT,
  name        VARCHAR(55) NOT NULL,
  description VARCHAR(255),
  parent      INT,
  created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deadline    TIMESTAMP,
  priority    INT         NOT NULL DEFAULT 5,
  status      INT         NOT NULL,
  reporter    INT         NOT NULL,
  assignee    INT,
  PRIMARY KEY (id),
  FOREIGN KEY (parent) REFERENCES t_task (id),
  FOREIGN KEY (status) REFERENCES t_status (id),
  FOREIGN KEY (reporter) REFERENCES t_user (id),
  FOREIGN KEY (assignee) REFERENCES t_user (id),
  CHECK (NOT parent = id),
  CHECK (priority >= (SELECT t.priority
                      FROM t_task t
                      WHERE parent = t.id)),
  CHECK (priority > 0),
  CHECK (priority <= 10)
);