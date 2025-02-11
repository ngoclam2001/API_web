<?php 
    class Information extends dbBasic{
        protected $pkey;
        protected $tbl;
        
        function __construct(){
            $this->pkey = 'area_id';
            $this->tbl = 'ads_area';
        }
        
    }
?>
