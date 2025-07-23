<!-- white ghost themem horizontal slider -->
<?php
session_start();
include '../db/dbconnect.php'; 
/* 60 random books, 30 per row */
$stmt  = $conn->prepare("SELECT novel_id, title FROM Novels ORDER BY RAND() LIMIT 60");
$stmt->execute();
$books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* split books into two shelves */
$topRow    = array_slice($books, 0, 30);
$bottomRow = array_slice($books, 30, 30);

/* colour palettes for spines */
$palettes = [
  ['#a0522d','#8b4513','#69340d'],
  ['#b36b00','#944f00','#6e3b00'],
  ['#7b5132','#593520','#3f2618'],
  ['#c16c3a','#a0522d','#7d3d13']
];

/* helper that prints one shelf row */
function buildRow(string $rowId, array $books, array $palettes): void {
    echo '<div class="shelf-track" id="'.$rowId.'">';
    foreach ($books as $b) {
        $pal = $palettes[array_rand($palettes)];
        $w   = rand(55,85);            // wider books
        $h   = rand(200,280);
        $fs  = rand(11,16);
        $sc  = rand(90,110)/100;

        echo '<a class="book" style="
                --c1:'.$pal[0].';--c2:'.$pal[1].';--c3:'.$pal[2].';
                width:'.$w.'px;height:'.$h.'px;font-size:'.$fs.'px;--scale:'.$sc.';
              "
              href="template/novel/novel_info.php?novel_id='.$b['novel_id'].'"
              target=\"_blank\"
              title=\"'.htmlspecialchars($b['title']).'\">'.
              htmlspecialchars($b['title']).'</a>';
    }
    echo '</div>';
}
?>
<section class="horizontal-bookshelf">
  <div class="content">
    <h2 style="color:#4c2b0f">Similar Reads</h2>

    <div class="shelf-wrapper"><?php buildRow('track1', $topRow, $palettes); ?></div>
    <div class="shelf-wrapper"><?php buildRow('track2', $bottomRow, $palettes); ?></div>
  </div>
</section>

<script>
/* duplicate each track once for a seamless loop */
['track1','track2'].forEach(id=>{
  const t = document.getElementById(id);
  t.innerHTML += t.innerHTML;
});
</script>


<style>
body{
    margin:0;
    background:#f4f1de;
    font-family:'Libre Baskerville',serif;
    overflow-x:hidden;
  }
  .content{padding:40px 0;text-align:center}
  
  /* ─── SHELF ZONE ──────────────────────────────────── */
  .shelf-wrapper{
    overflow:hidden;
    white-space:nowrap;
    margin-bottom:50px;              /* space between the two rows */
  }
  .shelf-track{
    display:inline-flex;
    align-items:flex-end;            /* books sit ON the plank */
    gap:6px;
    animation:scroll 240s linear infinite;
    position:relative;
    padding-bottom:26px;             /* plank height */
  }
  /* dark wood plank (wider & highlighted) */
  .shelf-track::before{
    content:"";
    position:absolute;
    left:-200%;bottom:0;
    width:400%;height:26px;
    background:linear-gradient(#60411d 0%, #4c2b0f 60%, #3a1f08 100%);
    border-radius:5px 5px 0 0;
    box-shadow:0 2px 4px rgba(0,0,0,.35) inset;
    animation:scroll 240s linear infinite;
  }
  
  /* endless loop */
  @keyframes scroll{
    from{transform:translateX(0)}
    to  {transform:translateX(-50%)}
  }
  
  /* ─── BOOK SPINES ─────────────────────────────────── */
  .book {
    --scale: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;               /* stack characters vertically */
    text-align: center;
    color: #000;
    text-decoration: none;
    font-weight: 600;
    letter-spacing: 0.5px;
    font-family: 'Libre Baskerville', serif;
    font-size: 11px;                      /* smaller font */
    line-height: 1.1;                     /* tighter line spacing */
    border-radius: 4px;
    box-shadow: 2px 2px 6px rgba(0,0,0,0.25);
    transition: transform 0.25s;
    z-index: 1;
    transform-origin: bottom;
    transform: scale(var(--scale));
    writing-mode: initial;                /* horizontal */
    text-orientation: initial;
    white-space: normal;                  /* allow wrapping */
    word-break: break-word;              /* wrap after 10ish chars */
    padding: 4px 2px;
    max-width: 70px;
  
  }
  
  .book:hover {
    transform: scale(calc(var(--scale) * 1.12));
  }
  
  
  @media(max-width:768px){.book{font-size:12px}}
  
  
  
  
  </style>