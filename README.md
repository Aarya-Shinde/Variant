# Unified System For Author and Audience 

This project started as an idea to build more than just another book website. I wanted to create a space where readers could store what they read, reflect on it, and even create their own stories — all in one place. The goal was to design something that feels personal, almost like a digital reading room combined with a writing studio.

I built the platform using core web technologies: HTML, CSS, JavaScript, PHP, MySQL, and Python with Flask. Instead of relying on heavy frontend frameworks, I intentionally chose a traditional stack so I could understand and control each layer of the system. This also helped me structure the project in a way that clearly demonstrates how frontend, backend, and data processing components interact.

The first feature I implemented was user authentication. Since the platform revolves around personal libraries, diary entries, and writing drafts, it was important that each user’s data remains separate and secure. I created a login and registration system using PHP sessions and database storage, ensuring that all user-specific actions remain tied to their account.

Once authentication was working, I focused on the library system. I wanted users to be able to add books easily without manually entering details. To solve this, I built a scraping pipeline using Python Flask. Users can submit a book URL, and the scraper extracts information, processes it, and sends it to the PHP backend, which stores it in MySQL. The database structure separates Users, Novels, and Library entries, allowing multiple users to store the same book without duplicating data. This design keeps the database normalized and scalable.

After building the library, I moved on to improving the reading experience. Instead of a plain list of books, I designed the interface around a vintage, antique-inspired theme. I used warm parchment tones, leather-like browns, and subtle gold highlights to give the platform a literary atmosphere. Both light and dark modes follow this theme so the visual identity remains consistent throughout the site.

The next major feature I developed was the Reader’s Diary. I wanted users to interact with books in a more expressive way, not just mark them as read. The diary allows users to write reviews, save quotes, add decorative elements, and organize entries across multiple pages. I used JavaScript for interactive elements like dragging and positioning, CSS for styling and transitions, and PHP to store diary content in the database. Each entry is tied to a user and page number, allowing a multi-page journal experience.

Alongside the diary, I built a writer dashboard for users who want to create their own stories. This includes story management, chapter creation, and draft saving. The editor uses JavaScript for formatting tools and PHP for storing and updating content. I designed the dashboard to be simple but structured, so writers can focus on their content without distractions.

Throughout development, I treated the project as a full software lifecycle exercise. I planned modules, structured the database carefully, considered edge cases such as duplicate entries or invalid URLs, and ensured each component had a defined responsibility. The frontend handles user interaction, PHP manages application logic and persistence, MySQL stores structured data, and Python handles data extraction and processing. Keeping these responsibilities separate made the system easier to maintain and extend.

Security and reliability were also considered. Input validation, session handling, and structured queries were implemented to reduce common vulnerabilities. While this project is academic in nature, I aimed to design it with real-world usability and architecture in mind.

By the end of development, the project evolved into a hybrid platform combining a personal library, reflective diary, and writing workspace. Instead of separate features, these components form a connected ecosystem where users can discover stories, store them, reflect on them, and create their own.

This project demonstrates my ability to design and build a multi-module web application, integrate multiple technologies into a single system, structure relational databases properly, and maintain consistent UI design across pages. More importantly, it reflects my approach to development: building systems around user experience while keeping the architecture modular and scalable.


Feel free to explore, fork, and share your thoughts!
