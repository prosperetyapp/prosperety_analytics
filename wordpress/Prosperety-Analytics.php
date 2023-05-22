<?php
/*
Plugin Name: Prosperety-Analytics
Version: 1.0
Description: Prosperety Analytics Tool.
Author: Naresh Gupta
Author URI: Your Website
*/


function pAnalyticsStoreUser() {
    $output = '';
    if (isset($_GET['username'])) {
        $output = "<script>
            localStorage.setItem('pAnalyticsUserName', '". $_GET['username']."');
            </script>";
    }
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $currentURL = $protocol . $host . $_SERVER['REQUEST_URI'];

    $output .= "
    <script>
    const requestFrom = '". $currentURL ."';
    jQuery( document ).ready(function($) {
        $('a').click(function() {
            analyticsSend('a', $(this).text());
        });
        $('button').click(function() {
            analyticsSend('button', $(this).text());
        });
        $('li').click(function() {
            analyticsSend('li', $(this).text());
        });
    });
    function analyticsSend(elementType, elementText){
        const pAnalyticsUserName = localStorage.getItem('pAnalyticsUserName');
        if(pAnalyticsUserName) {
            var img = new Image();
            img.src = 'http://localhost/pwamazon?elementType='+elementType+'&elementText=' + elementText + '&pAnalyticsUserName='+ pAnalyticsUserName + '&requestFrom=' + requestFrom;
            img.style.display = 'none';
            document.body.appendChild(img);
        }
        return true;
    }
    </script>";
    return $output;
}
add_shortcode( 'pAnalyticsInitialize', 'pAnalyticsStoreUser' );