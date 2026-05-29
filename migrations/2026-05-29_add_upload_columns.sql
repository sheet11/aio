-- Migration: Add upload columns to tb_interstudent
-- Created: 2026-05-29
-- Purpose: Add columns for additional uploaded files used by the registration form

-- IMPORTANT: Backup your database before running this script.

-- Add columns (uses IF NOT EXISTS where supported)
ALTER TABLE `tb_interstudent`
  ADD COLUMN IF NOT EXISTS `transcript_file` VARCHAR(255) NULL AFTER `diploma_file`,
  ADD COLUMN IF NOT EXISTS `photo_file` VARCHAR(255) NULL AFTER `transcript_file`,
  ADD COLUMN IF NOT EXISTS `cv_file` VARCHAR(255) NULL AFTER `photo_file`,
  ADD COLUMN IF NOT EXISTS `letter_rec_file` VARCHAR(255) NULL AFTER `cv_file`,
  ADD COLUMN IF NOT EXISTS `statement_file` VARCHAR(255) NULL AFTER `letter_rec_file`;

-- Rollback (drop added columns)
-- To rollback, uncomment and run the following:
-- ALTER TABLE `tb_interstudent`
--   DROP COLUMN IF EXISTS `statement_file`,
--   DROP COLUMN IF EXISTS `letter_rec_file`,
--   DROP COLUMN IF EXISTS `cv_file`,
--   DROP COLUMN IF EXISTS `photo_file`,
--   DROP COLUMN IF EXISTS `transcript_file`;

-- End of migration
