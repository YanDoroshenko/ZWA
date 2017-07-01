INSERT INTO t_task (id, name, description, priority, status, reporter, assignee)
VALUES (1, 'Task 1', 'Task 1', 1, 1, 1, 2);
INSERT INTO t_task (id, name, description, parent, priority, status, reporter, assignee)
VALUES (2, 'Task 2', 'Task 2', 1, 1, 2, 1, 2);
INSERT INTO t_task (id, name, description, status, reporter) VALUES (3, 'Task 3', 'Task 3', 1, 3);