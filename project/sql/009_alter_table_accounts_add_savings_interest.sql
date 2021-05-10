ALTER TABLE Accounts
    ADD COLUMN apy DECIMAL(12, 2) default null,
    ADD COLUMN total_years INT default null;