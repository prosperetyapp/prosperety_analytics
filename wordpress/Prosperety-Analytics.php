<?php
/*
Plugin Name: Prosperety-Analytics
Version: 1.0
Description: Prosperety Analytics Tool.
Author: Naresh Gupta
*/


/**
 * Function Prosperety Analytics Store User
 * When URL hit with username querystring, function will store username in local storage with name "pAnalyticsUserName"
 * And then whenever user click on "a", "li", "button" press, The function will call webhook to presperety.
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
    const pAnalyticsToURL = 'http://127.0.0.1:8000/analytics/webhook';
    const requestFrom = '". $currentURL ."';
    jQuery( document ).ready(function($) {
        $('a').click(function() {
            pAnalyticsWebHook('a', $(this).text());
        });
        $('button').click(function() {
            pAnalyticsWebHook('button', $(this).text());
        });
        $('li').click(function() {
            pAnalyticsWebHook('li', $(this).text());
        });
    });
    function pAnalyticsWebHook(elementType, elementText){
        const pAnalyticsUserName = localStorage.getItem('pAnalyticsUserName');
        if(pAnalyticsUserName) {
            var data = {
                elementType: elementType,
                elementText: elementText,
                pAnalyticsUserName: pAnalyticsUserName,
                requestFrom: requestFrom,
            };
            jQuery.post(pAnalyticsToURL, data, function(response) {
                // Handle the response from the server
                console.log(response);
            }); 
        }
        return true;
    }
    </script>";
    return $output;
}
/**
 * First argument of shortcode is use in wordpress admin side where want to add this shortcode for whole website
 * Second argument is call above function.
 */
add_shortcode( 'pAnalyticsInitialize', 'pAnalyticsStoreUser' );