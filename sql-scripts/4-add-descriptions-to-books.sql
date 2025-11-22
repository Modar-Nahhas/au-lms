use au_lms;

ALTER TABLE book
    ADD COLUMN description TEXT NULL AFTER deleted_at;

UPDATE book
SET description = 'A classic coming-of-age novel following Pip, an orphan who navigates ambition, love, and social class in Victorian England after receiving mysterious “great expectations” from an unknown benefactor.'
WHERE id = 1;

UPDATE book
SET description = 'A non-fiction work that explains the science of climate change, its current and future impacts on the planet, and the urgent actions needed from governments and individuals to address global warming.'
WHERE id = 2;

UPDATE book
SET description = 'A comprehensive reference dictionary providing word definitions, spelling, pronunciation, and usage examples, widely used for academic, professional, and everyday English language support.'
WHERE id = 3;

UPDATE book
SET description = 'A Russian literary classic that intertwines the tragic love story of Anna Karenina with the life of landowner Levin, exploring themes of family, society, morality, and personal happiness in 19th-century Russia.'
WHERE id = 4;

UPDATE book
SET description = 'Often considered one of the first novels in world literature, this Japanese classic follows the romantic and political life of Prince Genji, offering insight into Heian-era court culture, aesthetics, and relationships.'
WHERE id = 5;
