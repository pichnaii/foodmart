<?php 
    $product_cost = TRUE;
    $product_image = TRUE;


    function formatcurrency_khr($amount){
        return number_format($amount, 0, '.', ',') . ' ៛';
    }

    function formatcurrency_usd($amount){
        return number_format($amount, 2, '.', ',') . ' $';
    }
?>