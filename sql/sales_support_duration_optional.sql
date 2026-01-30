-- Optional: Add support_duration (days) to sales for license/support tracking on customer dashboard.
-- If the column already exists, skip this.

ALTER TABLE sales ADD COLUMN support_duration INT(11) NULL DEFAULT NULL COMMENT 'Support duration in days from sale date' AFTER assign_marketing_officers;
