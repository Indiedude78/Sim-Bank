ALTER TABLE Accounts
    ADD COLUMN apy INT default null,
    ADD COLUMN total_years UNSIGNED DECIMAL(10, 2) default 0.00;