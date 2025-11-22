use au_lms;

INSERT INTO book (title, author, publisher, language, category, cover_image, isbn)
VALUES ('Great Expectations', 'Charles Dickens', 'Macmillan Collectors Library', 'English', 'Fiction', 'book_1.png',
        '9780141439563'),
       ('An Inconvenient Truth', 'Al Gore', 'Penguin Books', 'English', 'Non-Fiction', 'book_2.png', '9780743282939'),
       ('Oxford Dictionary', 'Oxford Press', 'Oxford Press', 'English', 'Reference', 'book_3.png', '9780198611868'),
       ('Anna Karenina', 'Leo Tolstoy', 'Star Publishing', 'Russian', 'Fiction', 'book_4.png', '9780143035008'),
       ('The Tale of Genji', 'Murasaki Shikibu', 'Kinokuniya', 'Japanese', 'Fiction', 'book_5.png', '9780679417385');

INSERT INTO book_status (book_id, status)
VALUES (1, 'Available'),
       (2, 'Available'),
       (3, 'Available'),
       (4, 'Available'),
       (5, 'Available');
