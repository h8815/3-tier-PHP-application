-- TIER 3: DATABASE INITIALIZATION SCRIPT

CREATE TABLE IF NOT EXISTS student (
    ID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Age INT(3) NOT NULL
);

-- Optional: Add some initial data
INSERT INTO student (Name, Email, Age) VALUES ('Alice Smith', 'alice.s@example.com', 22);
INSERT INTO student (Name, Email, Age) VALUES ('Bob Johnson', 'bob.j@example.com', 25);