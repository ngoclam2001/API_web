<?php 
    function readCSV($file) {
        $list_city = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
            fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (!empty($data[0]) && !empty($data[1])) {
                    $list_city[$data[0]] = $data[1];
                }
            }
            fclose($handle);
        }
        return $list_city;
    }
?>