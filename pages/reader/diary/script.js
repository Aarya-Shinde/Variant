
// Enhanced Reader's Diary Script with animations, resizable & deletable stickers/quotes
(() => {
  let page = 1;
  const entryArea = document.getElementById('entryArea');
  const skinDropdown = document.getElementById('skinDropdown');
  const fontDropdown = document.getElementById('fontDropdown');
  const pageNoDisplay = document.getElementById('pageNo');

  // Animate diary opening
  document.getElementById('diary').classList.add('open-animation');

  // Initialize sticker drag
  document.querySelectorAll('.sticker').forEach(sticker => {
    sticker.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', e.target.src);
    });
  });

  // Allow dropping
  entryArea.addEventListener('dragover', e => e.preventDefault());

  // Handle drop
  entryArea.addEventListener('drop', e => {
    e.preventDefault();
    const src = e.dataTransfer.getData('text/plain');
    const img = document.createElement('img');
    img.src = src;
    img.classList.add('placed-sticker');
    img.style.position = 'absolute';
    img.style.left = `${e.offsetX}px`;
    img.style.top = `${e.offsetY}px`;
    img.style.width = '40px';
    img.style.transition = 'transform 0.3s ease';
    entryArea.appendChild(img);
    makeResizableDeletable(img);

    requestAnimationFrame(() => {
      img.style.transform = 'scale(1.2)';
      setTimeout(() => img.style.transform = 'scale(1)', 300);
    });
  });

  // Save entry
  function saveEntry() {
    const content = entryArea.innerHTML;
    const font_style = fontDropdown.value;
    const skin_style = skinDropdown.value;

    const stickers = [];
    entryArea.querySelectorAll('.placed-sticker').forEach(img => {
      stickers.push({
        src: img.src,
        x: img.style.left,
        y: img.style.top
      });
    });

    const quotes = [];
    entryArea.querySelectorAll('.draggable-quote').forEach(q => {
      quotes.push({
        text: q.innerText,
        x: q.style.left,
        y: q.style.top
      });
    });

    const formData = new FormData();
    formData.append('page', page);
    formData.append('content', content);
    formData.append('stickers', JSON.stringify(stickers));
    formData.append('font', font_style);
    formData.append('skin', skin_style);
    formData.append('quotes', JSON.stringify(quotes));

    fetch('save_entry.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(msg => alert(msg))
      .catch(() => alert('Save failed!'));
  }

  // Load entry
  function loadEntry() {
    fetch(`load_entry.php?page=${page}`)
      .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(data => {
        entryArea.innerHTML = data.content || '';
        entryArea.style.fontFamily = data.font_style || 'Georgia';
        fontDropdown.value = data.font_style || 'Georgia';
        skinDropdown.value = data.skin_style || 'vintage';
        document.getElementById('diary').className = `diary-book skin-${skinDropdown.value}`;
        pageNoDisplay.innerText = `Page ${page}`;

        // Remove old stickers
        entryArea.querySelectorAll('.placed-sticker').forEach(el => el.remove());
        const stickers = JSON.parse(data.stickers || '[]');
        stickers.forEach(sticker => {
          const img = document.createElement('img');
          img.src = sticker.src;
          img.classList.add('placed-sticker');
          img.style.position = 'absolute';
          img.style.left = sticker.x;
          img.style.top = sticker.y;
          img.style.width = '40px';
          entryArea.appendChild(img);
          makeResizableDeletable(img);
        });

        entryArea.querySelectorAll('.draggable-quote').forEach(el => el.remove());
        const quotes = JSON.parse(data.quotes || '[]');
        quotes.forEach(quote => {
          const div = document.createElement('div');
          div.className = 'draggable-quote';
          div.contentEditable = true;
          div.innerText = quote.text;
          div.style.position = 'absolute';
          div.style.left = quote.x;
          div.style.top = quote.y;
          entryArea.appendChild(div);
          makeDraggable(div);
        });
      })
      .catch(err => {
        console.error('Load error:', err);
        alert('Load failed!');
      });
  }

  // Draggable
  function makeDraggable(el) {
    el.style.position = 'absolute';
    el.onmousedown = function (e) {
      const offsetX = e.clientX - el.offsetLeft;
      const offsetY = e.clientY - el.offsetTop;

      function moveAt(e) {
        el.style.left = `${e.clientX - offsetX}px`;
        el.style.top = `${e.clientY - offsetY}px`;
      }

      document.addEventListener('mousemove', moveAt);
      document.addEventListener('mouseup', () => {
        document.removeEventListener('mousemove', moveAt);
      }, { once: true });
    };
  }

  // Make resizable and deletable
  function makeResizableDeletable(el) {
    makeDraggable(el);
    el.style.resize = 'both';
    el.style.overflow = 'auto';
    el.title = 'Double click to delete';
    el.ondblclick = () => el.remove();
  }

  // Page navigation
  window.nextPage = function () {
    page++;
    loadEntry();
  };

  window.prevPage = function () {
    if (page > 1) page--;
    loadEntry();
  };

  // Theme + Font switchers
  skinDropdown.addEventListener('change', () => {
    document.getElementById('diary').className = `diary-book skin-${skinDropdown.value}`;
  });

  fontDropdown.addEventListener('change', () => {
    entryArea.style.fontFamily = fontDropdown.value;
  });

  // Initial load
  loadEntry();
  window.saveEntry = saveEntry;
})();



function createSection(className, placeholder) {
  const div = document.createElement('div');
  div.contentEditable = 'true';
  div.className = className;
  div.innerText = placeholder;
  div.style.border = '1px dotted #aaa';
  div.style.margin = '10px 0';
  div.style.padding = '8px';
  div.style.backgroundColor = '#fff8e7';
  entryArea.appendChild(div);
}

window.addReview = () => createSection('review-section', 'Write your review...');
window.addQuote = () => {
  const quote = document.createElement('div');
  quote.className = 'draggable-quote';
  quote.contentEditable = true;
  quote.innerText = 'Drag me! Add your favorite quote...';
  quote.style.left = '100px';
  quote.style.top = '100px';
  entryArea.appendChild(quote);
  makeDraggable(quote);
};

window.addBookLink = () => {
  const link = document.createElement('a');
  link.href = '#';
  link.target = '_blank';
  link.innerText = 'Book Link';
  link.className = 'book-link';
  link.style.display = 'block';
  link.style.margin = '10px 0';
  entryArea.appendChild(link);
};

document.querySelectorAll('.sticker').forEach(img => {
  img.addEventListener('click', () => {
    const sticker = document.createElement('img');
    sticker.src = img.src;
    sticker.className = 'placed-sticker';
    sticker.style.left = '100px';
    sticker.style.top = '100px';
    entryArea.appendChild(sticker);
    makeDraggable(sticker);
  });
});

// Make quotes draggable 
function makeDraggable(el) {
  el.style.position = 'absolute';
  let offsetX = 0, offsetY = 0;

  el.onmousedown = function (e) {
    e.preventDefault();
    offsetX = e.clientX - el.offsetLeft;
    offsetY = e.clientY - el.offsetTop;

    document.onmousemove = function (e) {
      el.style.left = (e.clientX - offsetX) + 'px';
      el.style.top = (e.clientY - offsetY) + 'px';
    };

    document.onmouseup = function () {
      document.onmousemove = null;
      document.onmouseup = null;
      saveEntry(); // auto-save position on drop
    };
  };
}


const viewBtn = document.getElementById('view-thumbnails');
const modal = document.getElementById('thumbnail-modal');
const thumbContainer = document.getElementById('thumbnail-container');

viewBtn.onclick = () => {
  thumbContainer.innerHTML = '';
  for (let i = 1; i <= 5; i++) {
    const thumb = document.createElement('div');
    thumb.className = 'thumbnail-page';
    thumb.innerText = `Page ${i}`;
    thumb.onclick = () => {
      page = i;
      loadEntry();
      modal.classList.add('hidden');
    };
    thumbContainer.appendChild(thumb);
  }
  modal.classList.remove('hidden');
};

function closeThumbnails() {
  modal.classList.add('hidden');
}

//  flip sound for diary page turning
const flipSound = document.getElementById('flip-sound');

function nextPage() {
  page++;
  pageNoDisplay.innerText = `Page ${page}`;
  flipSound.play();
  diary.classList.add('page-flip');
  setTimeout(() => {
    diary.classList.remove('page-flip');
    loadEntry();
  }, 600);
}

function prevPage() {
  if (page > 1) {
    page--;
    pageNoDisplay.innerText = `Page ${page}`;
    flipSound.play();
    diary.classList.add('page-flip');
    setTimeout(() => {
      diary.classList.remove('page-flip');
      loadEntry();
    }, 600);
  }
}
