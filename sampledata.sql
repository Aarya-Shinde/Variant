use Variant;

select * from Users;
select * from novels;

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


UPDATE Novels
SET cover_image_url = 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1598823299i/42844155.jpg'
WHERE novel_id = 11;


-- ('To Kill a Mockingbird', 6, 'Harper Lee', '1960-07-11', 'Fiction', 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1553383690i/2657.jpg', 31, 'N/A', 1, 'Scout Finch recounts her childhood in the racially divided Deep South, focusing on her father, Atticus Finch, defending an innocent Black man accused of a crime.', '9780061120084', 4.8, 'English'),
-- ('1984', 7, 'George Orwell', '1949-06-08', 'Dystopian', 'https://m.media-amazon.com/images/I/71kxa1-0wfL._AC_UY218_.jpg', 24, 'N/A', 1, 'In a dystopian future, Winston Smith secretly rebels against the totalitarian regime of Big Brother, grappling with surveillance, propaganda, and the loss of individuality.', '9780451524935', 4.7, 'English'),
-- ('The Great Gatsby', 8, 'F. Scott Fitzgerald', '1925-04-10', 'Fiction', 'https://m.media-amazon.com/images/I/81nLrrpE2zL._AC_UY218_.jpg', 9, 'N/A', 1, 'Narrated by Nick Carraway, this novel explores the mysterious life of Jay Gatsby and his unyielding love for Daisy Buchanan during the Roaring Twenties.', '9780743273565', 4.4, 'English'),
-- ('Pride and Prejudice', 9, 'Jane Austen', '1813-01-28', 'Romance', 'https://m.media-amazon.com/images/I/81lHkchD6QL._AC_UY218_.jpg', 61, 'N/A', 1, 'Elizabeth Bennet faces the challenges of love, prejudice, and social expectations in her evolving relationship with the enigmatic Mr. Darcy in Regency-era England.', '9780486284736', 4.3, 'English');
