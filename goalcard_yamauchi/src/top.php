<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>login</title>
     <link rel="stylesheet" href="style.css">
     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Firebase SDK の読み込み -->
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.1/firebase-auth.js"></script>
<script>// script.js
let startX;
let scrollLeft;
const slider = document.getElementById('slider-container');

slider.addEventListener('mousedown', (e) => {
    slider.classList.add('active');
    startX = e.pageX - slider.offsetLeft;
    scrollLeft = slider.scrollLeft;
});

slider.addEventListener('mouseleave', () => {
    slider.classList.remove('active');
});

slider.addEventListener('mouseup', () => {
    slider.classList.remove('active');
});

slider.addEventListener('mousemove', (e) => {
    if (!slider.classList.contains('active')) return;
    e.preventDefault();
    const x = e.pageX - slider.offsetLeft;
    const walk = (x - startX) * 3; // スクロール速度を調整
    slider.scrollLeft = scrollLeft - walk;
});

// スライドインデックスを追跡する変数を追加
let slideIndex = 1;
showSlides(slideIndex);

// 点をクリックして特定のスライドを表示
function currentSlide(n) {
  showSlides(slideIndex = n);
}

// スライドとページネーションの点を更新
function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("slide");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}

</script>
</head>
<body>

<div id="slider-container">
    <div class="slide" id="slide1">
      <div class="setsumei_group">
      <p class="emoji">💳</p>
      <p class="setsumei">あああ</p>
    </div>
    </div>
    <div class="slide" id="slide2">
       <div class="slide" id="slide1">
           <p class="emoji">📈</p>
      <p class="setsumei">あああ</p>
    </div>
    </div>
    <div class="slide" id="slide3">
       <div class="slide" id="slide1">
           <p class="emoji">📣</p>
      <p class="setsumei">応援</p>
    </div>
    </div>
    <!-- 他のスライドをここに追加 -->
</div>

<!-- 既存のスライダーコンテナの後に追加 -->
<div class="pagination">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
    <!-- 他のドットを必要な分だけ追加 -->
</div>


</body>
</html>
