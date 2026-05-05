-- Add receipt_path column to transactions table
ALTER TABLE transactions ADD COLUMN receipt_path VARCHAR(255) NULL AFTER created_by;
