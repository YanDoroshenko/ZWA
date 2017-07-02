INSERT INTO t_task (name, description, priority, status, reporter, assignee)
VALUES ('Task 1', 'Task 1', 1, 1, 1, 2);
INSERT INTO t_task (name, description, parent, priority, status, reporter, assignee)
VALUES ('Task 2', 'Task 2', 1, 1, 2, 1, 2);
INSERT INTO t_task (name, description, status, reporter) VALUES ('Task 3', 'Task 3', 1, 3);