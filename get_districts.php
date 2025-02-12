<?php
    include 'read_csv.php';
    if($_POST['districts_id']){
        $districts_id = $_POST['districts_id'];
        $list_districts = readCSV("district_list/".$districts_id.".csv");
        echo '<option value="" selected disabled>Chọn Quận/Huyện</option>';
        foreach($list_districts as $code => $name){
            echo '<option value="'.$code.'">'.$name.'</option>';
        }
    }
?>