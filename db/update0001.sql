CREATE TABLE predicate (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    schema TEXT NOT NULL,
    field TEXT NOT NULL,
    operator TEXT NOT NULL,
    days INTEGER NOT NULL,
    users_and_groups TEXT NOT NULL,
    message TEXT NOT NULL
);
