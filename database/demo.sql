-- OPTIONAL. Local evaluation only. Clearly marked demonstration enquiries.
-- Do not import this file into a live customer database.
USE uc_properties;
INSERT INTO requests (reference,type,estate_id,name,phone,email,message,consent_at,is_demo)
VALUES ('DEMO-ENQUIRY-001','enquiry',1,'Demo visitor','00000000000','demo@example.invalid','DEMO: Explore the staff enquiry workflow. Not a real customer.',NOW(),1);
-- Remove the demonstration record with:
-- DELETE FROM requests WHERE is_demo=1;
