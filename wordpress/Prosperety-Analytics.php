<?php
/*
Plugin Name: Prosperety-Analytics
Version: 1.0
Description: Prosperety Analytics Tool.
Author: Naresh Gupta
*/

defined( 'ABSPATH' ) or die( 'Access Restricted!' );

class ProsperityAnalytics
{
    function __construct() {
         add_action('wp_footer', array( $this, 'pAnalyticsStoreUser' ));
    }

    function activate() {
        add_action('wp_footer', array( $this, 'pAnalyticsStoreUser' ));
        flush_rewrite_rules();
    }

    function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Function Prosperety Analytics Store User
     * When URL hit with username querystring, function will store username in local storage with name "pAnalyticsUserName"
     * And then whenever user click on "a", "li", "button" press, The function will call webhook to presperety.
     */
    function pAnalyticsStoreUser() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $currentURL = $protocol . $host . $_SERVER['REQUEST_URI'];
        // /**
        //  * If we use enqueue and add "https://code.jquery.com/jquery-3.6.0.min.js" script to the code, then it's loaded at the end of the page and we want to use the script here.
        //  * Because when the page is loaded we want to execute the jQuery function.
        //  */
        $output = '
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://prosperety.tritest.link/js/scripts/analytics.js"></script>
            <script>
                const requestFrom = "'. $currentURL .'";
                function setCookie(name, value, days) {
                    var expires = "";
                    if (days) {
                        var date = new Date();
                        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                        expires = "; expires=" + date.toUTCString();
                    }
                    document.cookie = name + "=" + (value || "") + expires + "; path=/";
                }
                function getProspUsername(name = "pAnalyticsUserName") {
                    var nameEQ = name + "=";
                    var ca = document.cookie.split(";");
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == " ") c = c.substring(1, c.length);
                        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
                    }
                    return null;
                }
            </script>';
        $output .= "<script>
        var pajQuery = jQuery.noConflict();
        window.prosp_data = window.prosp_data || [];
        function prosp_tag(key, val) {
            var obj = Object.create(null);
            obj[key] = val;
            prosp_data.push(obj);
        }
        prosp_tag('js_time', Date.now());
        prosp_tag('config', 'PROSP-9ul81zLpuFGn9nV');

        pajQuery( document ).ready(function($) {
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
            const pAnalyticsUserName = getProspUsername('pAnalyticsUserName');
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
        function callProsperetySession(type) {
            const pAnalyticsUserName = getProspUsername('pAnalyticsUserName');
            const payload = {
                brand: 'Nike',
                id: pAnalyticsUserName,
                action: 'action',
                typeOfActivity: type
            };
            logProsperetySession(payload);
        }
        document.onvisibilitychange = function() {
            if (document.visibilityState === 'hidden') {
                callProsperetySession('out');
            }
            if (document.visibilityState === 'visible') {
                callProsperetySession('in');
            }
        };
        </script>";
        if (isset($_GET['username'])) {
            $cookie_name = 'pAnalyticsUserName';
            $cookie_value = $_GET['username'];
            $days = 1;

            $output .= "<script>
                    setCookie('$cookie_name', '$cookie_value', '$days');
                    callProsperetySession('in');
                </script>";
        }
        echo $output;
        return;
    }
}

if ( class_exists( 'ProsperityAnalytics' ) ) {
    $prosperityAnalytics = new ProsperityAnalytics();
}

// activation
register_activation_hook( __FILE__, array( $prosperityAnalytics, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $prosperityAnalytics, 'deactivate' ) );

?>