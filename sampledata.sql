use Variant;
show tables;

select * from Users;
select * from novels;
select * from writernovels;


-- sample novel Data to input in 

INSERT INTO Novels (title, author_id, author_name, publication_date, genre, cover_image_url, total_chapters, series_name, series_position, description, isbn, rating, language)
VALUES 
    ('The Lightning Thief', 1, 'Rick Riordan', '2005-06-28', 'Fantasy', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1684776677i/123675190.jpg', 22, 'Percy Jackson and the Olympians', 1, 'Lately, mythological monsters and the Olympian gods seem to be walking straight out of the 
    pages of Percy Jackson’s Greek mythology textbook and into his life. Zeus\'s master lightning bolt has been stolen, and Percy is the prime suspect. Percy and his friends have just ten days to find and return Zeus\'s stolen property and bring peace 
    to a warring Mount Olympus.', '9780786838653', 4.5, 'English');

INSERT INTO Novels (title, author_id, author_name, publication_date, genre, cover_image_url, total_chapters, series_name, series_position, description, isbn, rating, language) 
VALUES
('The Sea of Monsters', 1, 'Rick Riordan', '2006-04-01', 'Fantasy', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1530819367i/40727118.jpg', 20, 'Percy Jackson and the Olympians', 2, 
'After a year spent trying to prevent a catastropic war among the Greek gods, Percy Jackson finds his seventh-gra
school year unnervingly quiet. His biggest problem is dealing with his new friend, Tyson--a six-foot-three, mentally
challenged homeless kid who follows Percy everywhere, making it hard for Percy to have any "normal" friends.
But things don\'t stay quiet for long. Percy soon discovers there is trouble at Camp Half-Blood: The magical borders
which protect Half-Blood Hill have been poisoned by a mysterious enemy, and the only safe haven for demigods is on
the verge of being overrun by mythological monsters. To save the camp, Percy needs the help of his best friend,
Grover, who has been taken prisoner by the Cyclops Polyphemus on an island somewhere in the Sea of
Monsters--the dangerous waters Greek heroes have sailed for millenia--only today, the Sea of Monsters goes by a
new name...the Bermuda Triangle.
Now Percy and his friends--Grover, Annabeth, and Tyson--must retrieve the Golden Fleece from the Island of the
Cyclopes by the end of the summer or Camp Half-Blood will be destroyed. But first, Percy will learn a stunning n
secret about his family--one that makes him question whether being claimed as Poseidon\'s son is an honor or simply
a cruel joke.', '9781423103349', 4.4, 'English');


INSERT INTO Novels (title, author_id, author_name, publication_date, genre, cover_image_url, total_chapters, series_name, series_position, description, isbn, rating, language) 
VALUES
('A Game of Thrones', 1, 'George R.R. Martin', '1996-08-06', 'Fantasy', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1386450049i/19270939.jpg', 72, 
'A Song of Ice and Fire', 1, 'A Game of Thrones is the first book in George R. R. Martin’s epic fantasy 
series A Song of Ice and Fire. Set in the
fictional continents of Westeros and Essos, the story follows several noble families as they vie for control of the Ir
Throne. The book introduces key characters like Eddard Stark, the honorable Lord of Winterfell, and his family, who
are drawn into a political web of intrigue and betrayal. Meanwhile, across the Narrow Sea, Daenerys Targaryen, the
last surviving member of a fallen dynasty, begins her own quest for power.
As the battle for the throne intensifies, Martin weaves complex narratives filled with political maneuvering, fa
loyalty, and unexpected twists. The novel explores themes of power, honor, and revenge, as well as the moral
ambiguity of its characters. With brutal betrayals and shocking events, A Game of Thrones sets the stage for a larger,
world-shattering conflict, blending medieval fantasy with a gritty, unpredictable world where the line between he
and villain is often blurred.', '9780553103540', 4.7, 'English'),

('The Catcher in the Rye', 1, 'J.D. Salinger', '1951-07-16', 'Fiction', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1477789850i/32808176.jpg', 26, 'N/A', 1, 
'It is written from the perspective of Holden Caulfield, a rebellious teenager who suffer
from depression and finds social conventions oppressive. After being expelled, Holden decides to run away, a
spends three days roaming the streets of New York in search of meaning, a better understanding of himself, and
somewhere he belongs. The Catcher in the Rye was first published in 1951 and remains a firm favourite with tee
readers around the world. It is the best-known work by J. D. Salinger, a reclusive American writer who died in 2010.
', '9780316769488', 4.3, 'English'),

('Harry Potter and the Philosopher’s Stone', 1, 'J.K. Rowling', '1997-06-26', 'Fantasy', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1698959693i/168031401.jpg', 17, 'Harry Potter', 1, 
'Harry Potter has no idea how famous he is. That\'s because he\'s being raised by his miserable aunt and uncle who are
terrified Harry will learn that he\'s really a wizard, just as his parents were. But everything changes when Harry 
summoned to attend an infamous school for wizards, and he begins to discover some clues about his illustrious
birthright. From the surprising way he is greeted by a lovable giant, to the unique curriculum and colorful faculty at
his unusual school, Harry finds himself drawn deep inside a mystical world he never knew existed and closer to h
own noble destiny.
', '9780747532699', 4.8, 'English'),

('The Hobbit', 1, 'J.R.R. Tolkien', '1937-09-21', 'Fantasy', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1539933609i/42390926.jpg', 19, 'N/A', 1, 
'The Hobbit by J. R. R. Tolkien, an enchanting children’s novel about a
hobbit named Bilbo Baggins who is swept off on a most unexpected adventure one day by the wizard Gandalf.
Travelling with a company of 13 dwarves and facing trolls, stone giants, goblins, wolves, giant spiders, wood-elves and
even a dragon is a far cry from his peaceful life at home, but as Bilbo adjusts to the life of an adventurer, he comes to
realise just how much he is truly capable of – particularly when he stumbles upon a mysterious magic ring.', 
'9780261103344', 4.6, 'English');




-- Dummy data for reviews and comments 
INSERT INTO Reviews (novel_id, user_id, reviewer_name, review_text, rating, created_at, updated_at) VALUES
(1, 101, 'Aarav Mehta', 'A breathtaking story with deep characters and an engaging plot.', 5, '2025-02-20 10:30:00', '2025-02-20 10:30:00'),
(2, 102, 'Neha Kapoor', 'A slow start but picked up really well in the second half.', 4, '2025-02-21 12:15:00', '2025-02-21 12:15:00'),
(3, 103, 'Rajesh Iyer', 'Loved the suspense! Kept me hooked till the last page.', 5, '2025-02-22 15:45:00', '2025-02-22 15:45:00'),
(4, 104, 'Priya Sharma', 'The protagonist was a bit unlikable, but the world-building was fantastic.', 3, '2025-02-23 09:00:00', '2025-02-23 09:00:00'),
(5, 105, 'Vikram Joshi', 'A masterpiece! The writing style is so immersive.', 5, '2025-02-24 11:20:00', '2025-02-24 11:20:00'),
(6, 106, 'Sanya Verma', 'Interesting take on fantasy, but some parts were too predictable.', 4, '2025-02-25 18:00:00', '2025-02-25 18:00:00'),
(7, 107, 'Amit Rathore', 'The best novel I have read this year! Highly recommended.', 5, '2025-02-26 20:30:00', '2025-02-26 20:30:00');

INSERT INTO Comments (novel_id, chapter_id, user_id, commenter_name, comment_text, parent_id, created_at) VALUES
(1, NULL, 201, 'Rohit Singh', 'Absolutely agree! This novel deserves all the praise.', NULL, '2025-02-21 11:00:00'),
(2, NULL, 202, 'Ananya Patel', 'I felt the same! The pacing was slow at first but got better.', NULL, '2025-02-22 14:00:00'),
(3, 12, 203, 'Kiran Das', 'Chapter 12 had such an unexpected twist! Loved it.', NULL, '2025-02-23 17:45:00'),
(4, NULL, 204, 'Anonymous', 'I think the protagonist’s flaws made the story more realistic.', 2, '2025-02-24 09:30:00'),
(5, 3, 205, 'Meenal Gupta', 'Chapter 3’s descriptions were so vivid! Felt like I was there.', NULL, '2025-02-25 13:15:00'),
(6, NULL, 206, 'Suresh Kumar', 'Did anyone else think the ending was a bit rushed?', NULL, '2025-02-26 18:50:00'),
(7, 5, 207, 'Deepak Rana', 'The action in Chapter 5 was insane! One of the best scenes.', NULL, '2025-02-27 21:00:00');




UPDATE Novels
SET cover_image_url = 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1598823299i/42844155.jpg'
WHERE novel_id = 11;

-- Dummy data for library 
INSERT INTO Library (user_id, novel_id, read_status)
VALUES (4, 21, 'reading'),
(4, 22, 'reading');

-- Dummy data for chapters
INSERT INTO Chapters (novel_id, chapter_number, title, content) VALUES
(14, 1, 'The Reaping', 'The sun rises over District 12, casting a pale light on the run-down houses. Katniss Everdeen wakes up to the sound of her sister\'s cries...'),
(14, 2, 'The Train Ride', 'Katniss and Peeta board the train to the Capitol, leaving behind their families and everything they have ever known...'),
(14, 3, 'The Capitol', 'The train arrives in the Capitol, a place of unimaginable luxury and excess. Katniss is overwhelmed by the opulence...');

-- dummy data for comments- 

INSERT INTO Comments (novel_id, chapter_id, user_id, commenter_name, comment_text, parent_id) VALUES
(1, NULL, 1, 'Aarya', 'Loved the world-building in this novel!', NULL),
(1, 5, 4, 'Kajal Mane', 'This chapter had an unexpected twist!', NULL),
(2, NULL, 5, 'Orca', 'The character development was fantastic.', NULL),
(2, 3, 6, 'Pokemon', 'I didn’t see that coming! Great storytelling.', NULL),
(3, NULL, 7, 'Atharva Urkude', 'The pacing felt a bit slow, but still enjoyable.', NULL),
(1, NULL, 6, 'Pokemon', 'Can’t wait for the next update!', NULL),
(3, 7, 4, 'Kajal Mane', 'The protagonist is really growing on me.', NULL),
(4, 2, 5, 'Orca', 'This chapter left me on edge! What a cliffhanger.', NULL),
(2, NULL, 7, 'Atharva Urkude', 'Would love to see a sequel!', NULL),
(3, NULL, 1, 'Aarya', 'Did anyone else catch the hidden reference in this chapter?', NULL),
(1, NULL, 4, 'Kajal Mane', 'Replying to Aarya: Totally agree, the lore is deep.', 1),
(2, 3, 5, 'Orca', 'Replying to Pokemon: Right?! I was shocked!', 4);

-- Dummy chapters  

INSERT INTO Chapters (novel_id, chapter_number, title, content)
VALUES
    (1, 1, 'I Accidentally Vaporize My Pre-Algebra Teacher', 'Content of Chapter 1 for The Lightning Thief...'),
    (1, 2, 'Three Old Ladies Knit the Socks of Death', 'Content of Chapter 2 for The Lightning Thief...'),
    (4, 1, 'My Best Friend Shops for a Wedding Dress', 'Content of Chapter 1 for The Sea of Monsters...'),
    (9, 1, 'Prologue', 'Content of Chapter 1 for A Game of Thrones...'),
    (11, 1, 'The Boy Who Lived', 'Content of Chapter 1 for Harry Potter and the Philosopher’s Stone...'),
    (12, 1, 'An Unexpected Party', 'Content of Chapter 1 for The Hobbit...'),
    (13, 1, 'When He Was Nearly Thirteen', 'Content of Chapter 1 for To Kill a Mockingbird...'),
    (14, 1, 'The Tribute', 'Content of Chapter 1 for The Hunger Games...'),
    (15, 1, 'The Spark', 'Content of Chapter 1 for Catching Fire...'),
    (16, 1, 'The Ashes', 'Content of Chapter 1 for Mockingjay...'),
    (17, 1, 'The Lift', 'Content of Chapter 1 for The Maze Runner...'),
    (18, 1, 'The Scorch Begins', 'Content of Chapter 1 for The Scorch Trials...'),
    (19, 1, 'The Endgame', 'Content of Chapter 1 for The Death Cure...'),
    (21, 1, 'The Worst Birthday', 'Content of Chapter 1 for Harry Potter and the Chamber of Secrets...');

-- Add more chapters as needed.
