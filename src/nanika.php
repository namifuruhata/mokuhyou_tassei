<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ポイントカード</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script>
document.addEventListener('DOMContentLoaded', function() {
    var email = '<?php echo $email; ?>';
    if (email) {
        fetchUserCards(email);
    }
});

function fetchUserCards(email) {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=fetch_reward_cards&email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(rewardCards => {
        var userCardsList = document.getElementById('user-cards-list');
        userCardsList.innerHTML = '';
        rewardCards.forEach(function(card) {
            var cardItem = document.createElement('li');
            var cardLink = document.createElement('a');
            cardLink.href = 'javascript:void(0);';
            cardLink.className = 'check-card';
            cardLink.setAttribute('data-id', card.id);
            cardLink.textContent = card.card_name + ' (' + card.total_point + ' ポイント)';
            cardLink.addEventListener('click', function() {
                fetchCardDetails(card.id);
            });
            cardItem.appendChild(cardLink);
            userCardsList.appendChild(cardItem);
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function fetchCardDetails(cardId) {
     console.log("Fetching details for card ID:", cardId);
      var requestBody = 'action=get_reward_card_details&card_id=' + encodeURIComponent(cardId);
    console.log("Request body:", requestBody); // 追加するデバッグ行

    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_reward_card_details&card_id=' + encodeURIComponent(cardId)
    })
    .then(response => response.json())
       .then(cardDetails => {
        console.log("Received card details:", cardDetails);
        if (cardDetails.error) {
            console.error(cardDetails.error);
        } else {
            displayCardDetails(cardDetails);
            updateSlots(cardDetails); // cardDetailsを引数として渡す
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateSlots(cardDetails) {
    var pointCardContainer = document.querySelector('#point-card-container');
    if (pointCardContainer) {
        var slots = pointCardContainer.querySelectorAll('.point-slot');
        slots.forEach(function(slot) {
            slot.addEventListener('click', function() {
                var currentPoints = pointCardContainer.querySelectorAll('.stamped').length;
                var slotNumber = parseInt(this.getAttribute('data-point-number'), 10);
                if (!this.classList.contains('stamped') && slotNumber === currentPoints + 1) {
                    addPoint(cardDetails.id, 1); // cardDetails.idをaddPointに渡す
                }
            });
        });
    }
}


        function displayCardDetails(cardDetails) {
                // カードの基本情報を表示
    var detailContainer = document.getElementById('card-detail-container');
    detailContainer.innerHTML = `
       <h3>カード詳細</h3>
        <p>カード名: ${cardDetails.card_name}</p>
        <p>ポイント数: ${cardDetails.total_point}</p>
        <p>期限: ${cardDetails.goal_date}</p>
        <p>ルール: ${cardDetails.rule}</p>
        <p>ご褒美: ${cardDetails.reward}</p>
    `;
            var pointCardHtml = '<div class="point-card">';
            pointCardHtml += '<div class="card-name">' + cardDetails.card_name + 'カード</div>';
            pointCardHtml += '<div class="point-grid">';
            for (var i = 1; i <= 18; i++) {
                pointCardHtml += '<div class="point-slot ' + (i <= cardDetails.total_point ? 'stamped' : '') + '" data-point-number="' + i + '" data-card-id="' + cardDetails.id + '">' + i + '</div>';
            }
            pointCardHtml += '</div></div>';

            var pointCardContainer = document.querySelector('#point-card-container');
            pointCardContainer.innerHTML = pointCardHtml;

    var slots = pointCardContainer.querySelectorAll('.point-slot');
    slots.forEach(function(slot) {
        slot.addEventListener('click', function() {
            if (!this.classList.contains('stamped')) {
                this.classList.add('stamped'); // スタンプの見た目を即時更新
                addPoint(cardDetails.id, 1); // 1ポイント加算のリクエスト
            }
        });
    });
}

   // 18ポイントに達した場合、モーダルを表示
    if (cardDetails.total_point >= 18) {
        showModal();
    }


// モーダルポップアップを表示する関数
function showModal() {
    var modal = document.getElementById('modal');
    modal.style.display = 'block';
}

// モーダルポップアップを閉じる関数
function closeModal() {
    var modal = document.getElementById('modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// 画面外のクリックでモーダルを閉じる処理
window.onclick = function(event) {
    var modal = document.getElementById('modal');
    if (event.target == modal) {
        closeModal();
    }
}

function addPoint(cardId, pointsToAdd) {
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add_points&card_id=' + encodeURIComponent(cardId) + '&points=' + pointsToAdd
    })
    .then(response => response.json())
      .then(data => {
        if (data.message === 'Points added successfully') {
            var pointCardContainer = document.querySelector('#point-card-container');
            var currentPoints = pointCardContainer.querySelectorAll('.stamped').length;
            var nextSlot = pointCardContainer.querySelector('.point-slot[data-point-number="' + (currentPoints + 1) + '"]');
            if (nextSlot) {
                nextSlot.classList.add('stamped');
                nextSlot.innerHTML = '<img src="img/point.png">'; // スタンプ画像のパスに注意
            }
        } else {
            console.error('Error: Failed to add points');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// スロットクリック時の処理を更新
document.addEventListener('DOMContentLoaded', function() {
    var slots = document.querySelectorAll('.point-slot');
    slots.forEach(function(slot) {
        slot.addEventListener('click', function() {
            var currentPoints = pointCardContainer.querySelectorAll('.stamped').length;
            var slotNumber = parseInt(this.getAttribute('data-point-number'), 10);
            if (!this.classList.contains('stamped') && slotNumber === currentPoints + 1) {
                addPoint(cardDetails.id, 1); // ここでエラーが発生する可能性があります
            }
        });
    });
});



// スタンプの見た目を更新する関数
function updatePointSlotVisual(cardId, pointsToAdd) {
    var cardContainer = document.querySelector(`[data-card-id='${cardId}']`).parentNode;
    var slots = cardContainer.querySelectorAll('.point-slot:not(.stamped)');
    
    for (var i = 0; i < pointsToAdd && i < slots.length; i++) {
        slots[i].classList.add('stamped');
    }
}




        document.addEventListener('DOMContentLoaded', function() {
            var email = '<?php echo $email; ?>';
            fetchUserCards(email);
        });


         document.addEventListener('DOMContentLoaded', function() {
            var slots = document.querySelectorAll('.point-slot');
            slots.forEach(function(slot) {
                slot.addEventListener('click', function() {
                    this.classList.toggle('stamped');
                });
            });
        });
    </script>

</head>

<body>
      <header>
  <nav>
    <ul>
      <li><a href="card_registration.html">カード登録</a></li>
      <li><a href="exchange.php">交換</a></li>
      <li><a href="index.html">カード管理</a></li>
      <li><a href="#">お問い合わせ</a></li>
           <li><a href="top.html">サービス説明</a></li>
      <div class="button_group">
 </div>
  </nav>
</header>
    <!-- <div class="card">
        <h2>ポイントカード一覧</h2>
        <ul id="user-cards-list">

        </ul>
    </div>
    <div id="point-card-container">

    </div>


<div class="card-details">
    <div id="card-detail-container">

    </div>
</div>
 -->

  <div class="point-card-container">
        <div class="point-card-header">
            <h2>ポイントカード</h2>
        </div>
        <div class="point-grid">
            <!-- ポイントの表示 -->
            <?php for ($i = 1; $i <= 18; $i++): ?>
                <div class="point-slot">
                    <?php echo $i; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>

<!-- カード作成フォーム -->
<div class="card">
    <h2>ポイントカード登録</h2>
    <form action="card_registration.php" method="POST">
        <input type="hidden" name="action" value="create_card">
        カードの名前: <input type="text" name="card_name" required><br>
        目標期日: <input type="date" name="goal_date" required><br>
        ルール: <input type="text" name="rule" required><br>
        報酬: <input type="text" name="reward" required><br>
        <input type="submit" value="カードを作成">
    </form>
</div>



</body>
</html>
