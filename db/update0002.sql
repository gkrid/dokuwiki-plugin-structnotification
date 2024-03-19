CREATE TABLE predicateTemp (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    schema TEXT NOT NULL,
    field TEXT NOT NULL,
    operator TEXT NOT NULL,
    value TEXT NOT NULL,
    users_and_groups TEXT NOT NULL,
    message TEXT NOT NULL
);

INSERT INTO predicateTemp(id,schema,field,operator,value,users_and_groups,message)
    SELECT id,schema,field,operator,days,users_and_groups,message FROM predicate;

DROP TABLE predicate;
ALTER TABLE predicateTemp RENAME TO predicate;
