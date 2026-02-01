-- Migration: Add appointments and cases permission columns to sma_permissions
-- Run this if your code expects: appointments-index, appointments-add, appointments-edit, appointments-delete, cases-index, cases-add, cases-edit, cases-delete
-- Execute one ALTER per column if your MySQL version doesn't support multiple ADD COLUMN in one statement.

ALTER TABLE `sma_permissions`
  ADD COLUMN `appointments-index` tinyint(1) DEFAULT '0',
  ADD COLUMN `appointments-add` tinyint(1) DEFAULT '0',
  ADD COLUMN `appointments-edit` tinyint(1) DEFAULT '0',
  ADD COLUMN `appointments-delete` tinyint(1) DEFAULT '0',
  ADD COLUMN `cases-index` tinyint(1) DEFAULT '0',
  ADD COLUMN `cases-add` tinyint(1) DEFAULT '0',
  ADD COLUMN `cases-edit` tinyint(1) DEFAULT '0',
  ADD COLUMN `cases-delete` tinyint(1) DEFAULT '0';
