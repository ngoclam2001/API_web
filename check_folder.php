<?php
    require_once 'autoload.php';
    if($_POST['district_id'] && $_POST['city_id']){
        $desktopAPI = new DesktopAPI();
        $city_id = $_POST['city_id'];
        $districts_id = $_POST['district_id'];
        $check_folder = $desktopAPI->searchFolder($city_id,'information/');
        if($check_folder == true){
            $check_folder_child = $desktopAPI->searchFolder($districts_id,'information/'.$city_id.'/'.$districts_id);
            if($check_folder_child == true){
                echo 1; die();
            }else{
                $create_folder_c = $desktopAPI->createFolderInParent('information/',$city_id);
                if($create_folder_c == false){
                    echo 0;die();
                }else{
                    $create_folder = $desktopAPI->createFolderInParent('information/'.$city_id,$districts_id);
                    if($create_folder == false){
                        echo 0;die();
                    }else{
                        echo 1;die();
                    }
                    echo 1;die();
                }
            }
        }else{
            $create_folder_c = $desktopAPI->createFolderInParent('information/',$city_id);
            if($create_folder_c == false){
                echo 0;die();
            }else{
                echo 1;die();
            }
        }
    }
?>