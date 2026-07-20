-- Fix project_start_date where it is invalid (0000-00-00)
-- Sets start date to the date portion of created_at for matching rows.

UPDATE project_details
SET project_start_date = DATE(created_at)
WHERE (project_start_date = '0000-00-00'
       OR project_start_date IS NULL
       OR project_start_date = '')
  AND created_at IS NOT NULL
  AND created_at != '0000-00-00 00:00:00';
