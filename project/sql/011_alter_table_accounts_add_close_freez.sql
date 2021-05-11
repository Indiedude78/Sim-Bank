ALTER TABLE Accounts
    ADD COLUMN closed TINYINT(1) default 0,
    ADD COLUMN frozen TINYINT(1) default 0;