<?php
include 'db.php';
global $core;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 8px 20px;
        }
        h2{
            max-width: 500px;
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <div class="main">
        <h2 class="title_top">Tạo thư mục</h2>
        <div class="container">
            <form action="" method="post">
                <select name="options">
                    <option value="" selected disabled>Chọn Tỉnh/Thành phố</option>
                    <?php $list_city = $core->readCSV("list_all.csv");foreach($list_city as $code => $name){ ?>
                        <option value="<?=$code?>"><?=$name?></option>
                    <?php }; ?>
                </select>
                <input type="submit" value="Tạo">
            </form>
        </div>
    </div>
</body>
</html>