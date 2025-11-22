USE au_lms;

INSERT INTO user (member_type, first_name, last_name, email, password)
VALUES ('Admin', 'System', 'Admin', 'admin@aulms.edu.au', MD5('Admin@123')),
       ('Member', 'Test', 'User', 'user@aulms.edu.au', MD5('User@123'));
