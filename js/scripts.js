function togglePage() {
    const pages = document.querySelectorAll('.book-page');
    pages.forEach(page => page.classList.toggle('hidden'));
}

// /////////////////////////Slideshow Functionality////////////////////////////////////////////////////////////////////////////////
let slideIndex = 0;
showSlides();

function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 3000); // Change slide every 3 seconds
}

//////////////////////////Readers html///////////////////////////////////////////////////////////////////
// Open a new diary entry
function openDiaryEntry() {
    alert("Open a new diary entry for reviewing!");
}

// Add a new book
function addNewBook() {
    alert("Add a new book to your library!");
}

// Placeholder function to update score and book count (you could fetch real data here)
function updateReadingScore() {
    const score = document.getElementById("reading-score");
    score.textContent = parseInt(score.textContent) + 5;
}

function updateLibraryBooks() {
    const totalBooks = document.getElementById("total-books");
    totalBooks.textContent = parseInt(totalBooks.textContent) + 1;
}


/////////////////////////////////////////////////////
function togglePage() {
    const pages = document.querySelectorAll('.book-page');
    pages.forEach(page => page.classList.toggle('hidden'));
}

 


const swiper = new Swiper('.featured-slider', {
    slidesPerView: 3,
    spaceBetween: 20,
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
    },
  });
