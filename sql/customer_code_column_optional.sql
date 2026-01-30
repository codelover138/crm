-- Optional: Add customer_code column to companies for friendly dashboard URLs.
-- After this, customers can access dashboard via: base_url/customers/ABC123
-- If not added, use numeric id: base_url/customers/5

ALTER TABLE companies ADD COLUMN customer_code VARCHAR(50) NULL UNIQUE AFTER id;
-- Optionally backfill from id: UPDATE companies SET customer_code = CONCAT('C', LPAD(id, 5, '0')) WHERE group_name = 'customer' AND (customer_code IS NULL OR customer_code = '');
