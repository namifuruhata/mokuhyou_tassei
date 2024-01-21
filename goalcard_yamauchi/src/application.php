<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>入力フォーム</title>
    <link rel="stylesheet" href="style.css">
    <script>
        document.getElementById('myForm').addEventListener('submit', function(event){
    event.preventDefault();

    // ここでフォームデータを取得・処理します
    var name = document.getElementById('name').value;
    var postalCode = document.getElementById('postalCode').value;
    var address = document.getElementById('address').value;
    var phoneNumber = document.getElementById('phoneNumber').value;
    var preferredDate = document.getElementById('preferredDate').value;

    console.log('名前:', name);
    console.log('郵便番号:', postalCode);
    console.log('住所:', address);
    console.log('電話番号:', phoneNumber);
    console.log('受取希望日時:', preferredDate);

    // ここに送信処理などを追加できます
});

    </script>
</head>
<body>
    <form id="myForm">
        <div>
            <label for="name">名前：</label>
            <input type="text" id="name" name="name">
        </div>
        <div>
            <label for="postalCode">郵便番号：</label>
            <input type="text" id="postalCode" name="postalCode">
        </div>
        <div>
            <label for="address">住所：</label>
            <input type="text" id="address" name="address">
        </div>
        <div>
            <label for="phoneNumber">電話番号：</label>
            <input type="text" id="phoneNumber" name="phoneNumber">
        </div>
        <div>
            <label for="preferredDate">受取希望日時：</label>
            <input type="datetime-local" id="preferredDate" name="preferredDate">
        </div>
        <div>
            <button type="submit">送信</button>
        </div>
    </form>

    <script src="script.js"></script>
</body>
</html>
