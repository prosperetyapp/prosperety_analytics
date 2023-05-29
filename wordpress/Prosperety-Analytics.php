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
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script src='https://prosperety.tritest.link/js/scripts/analytics.js'></script>
    <script>
    window.prosp_data = window.prosp_data || [];
    function prosp_tag(key, val) {
        var obj = Object.create(null);
        obj[key] = val;
        prosp_data.push(obj);
    }
    prosp_tag('js_time', Date.now());
    prosp_tag('config', 'PROSP-9ul81zLpuFGn9nV');

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
    const pAnalyticsUserName = localStorage.getItem('pAnalyticsUserName');
    function pAnalyticsWebHook(elementType, elementText){
        if(pAnalyticsUserName) {
            logProsperety({
                        brand: 'nike',
                        referrer: requestFrom,
                        id: pAnalyticsUserName,
                        action: elementText
                    });
        }
        return true;
    }
    function callProsperetySession() {
        logProsperetySession({
            brand: 'Nike',
            id: pAnalyticsUserName,
            action: 'action'
        });
     }
     setInterval(callProsperetySession, 5000);
    </script>";
    return $output;
}
/**
 * First argument of shortcode is use in wordpress admin side where want to add this shortcode for whole website
 * Second argument is call above function.
 */
add_shortcode( 'pAnalyticsInitialize', 'pAnalyticsStoreUser' );