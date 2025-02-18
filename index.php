<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include 'db.php';

global $core;
$db = new dbBasic();

$db->connect();
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
                <select name="options_city">
                    <option value="" selected disabled>Chọn Tỉnh/Thành phố</option>
                    <?php $list_city = $core->readCSV("list_all.csv");foreach($list_city as $code => $name){ ?>
                        <option value="<?=$code?>"><?=$name?></option>
                    <?php }; ?>
                </select>
                <select name="options_district">
                    <option value="" selected disabled>Chọn Quận/Huyện</option>
                </select>
                <input type="submit" style="cursor: pointer;" value="Tạo">
                <div id="existFolder" style="display: none; color: green; margin-top: 10px;">
                    
                </div>
                <div id="notExistFolder" style="display: none; color: red; margin-top: 10px;">
                    
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select[name="options_city"]').change(function() {
            var selectedCity = $(this).val();
            $.ajax({
                    type: 'POST',
                    url: 'get_districts.php',
                    data: { districts_id: selectedCity },
                    success: function(response) {
                        $('select[name="options_district"]').html(response);
                    }
                });
            });
            $('form').submit(function(event) {
                event.preventDefault();
                var selectedCity = $('select[name="options_city"]').val();
                var selectedDistrict = $('select[name="options_district"]').val();
                if(!selectedCity || !selectedDistrict){
                    alert('Vui lòng chọn Tỉnh/Thành phố và Quận/Huyện!');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: 'check_folder.php',
                    data: { city_id: selectedCity, 
                        district_id: selectedDistrict 
                    },
                    success: function(response) {
                        if(response == 0){
                            $('#existFolder').html('Thư mục tạo không thành công hoặc đã tồn tại!').show();
                            $('#notExistFolder').hide();
                        }else{
                            $('#notExistFolder').html('Thư mục được tạo thành công!').show();
                            $('#existFolder').hide();
                        }
                    },
                });
            });
        });
    </script>
</body>
</html>