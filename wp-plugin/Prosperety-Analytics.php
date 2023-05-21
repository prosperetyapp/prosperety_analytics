<?php
/*
Plugin Name: Prosperety-Analytics
Version: 1.0
Description: Prosperety Analytics Tool.
Author: Naresh Gupta
Author URI: Your Website
*/


function pAnalyticUserStore() {
    $output = '';
    if ( isset( $_GET['username'] ) ) {
        $output = '<script>
        localStorage.setItem("pausername", "'. $_GET['username'].'");
        </script>';
    }
    return $output;
}
add_shortcode( 'initialize', 'pAnalyticUserStore' );

function callUrlToProsperety() {
    $output = "
    <script>
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
        const pausername = localStorage.getItem('pausername');
        if(pausername) {
            var img = new Image();
            img.src = 'http://localhost/pwamazon?elementType='+elementType+'&elementText=' + elementText + '&pausername='+ pausername;
            img.style.display = 'none';
            document.body.appendChild(img);
        }
        return true;
    }
    </script>";
    return $output;
}
add_shortcode( 'call_prosperety_url', 'callUrlToProsperety' );