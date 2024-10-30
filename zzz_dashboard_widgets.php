<?php

// let's check if you have like gate pro installed.
$like_gate_pro_installed = 0;

$like_gate_pro_dir = plugin_dir_path(__FILE__);
$like_gate_pro_dir = dirname($like_gate_pro_dir);
$like_gate_pro_dir .= '/like-gate-pro/';

if (is_dir($like_gate_pro_dir)) { // first check if directory exists
    $like_gate_pro_dir = 1;
} else { // check if the directory has some numbers because wp ads dirname-1
    $dirs = glob(dirname($like_gate_pro_dir). '/like-gate-pro*');
    $like_gate_pro_installed = empty($dirs) ? 0 : 1;
}

if (!$like_gate_pro_installed && !function_exists('orbisius_rss_dashboard_widgets')) {
    // Adds RSS feeds from Orbisius club
    if (!has_action('wp_dashboard_setup', 'orbisius_rss_dashboard_widgets')) {
        add_action('wp_dashboard_setup', 'orbisius_rss_dashboard_widgets');
    }

    function orbisius_rss_dashboard_widgets() {
         global $wp_meta_boxes;
         // remove unnecessary widgets
         // var_dump( $wp_meta_boxes['dashboard'] ); // use to get all the widget IDs
         unset(
              $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'],
              $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'],
              $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']
         );

         // add a custom dashboard widget
         wp_add_dashboard_widget( 'orbisius_custom_dashboard_widget', 'Latest Updates From Orbisius.com', 'orbisius_rss_dashboard_widgets_feed_output' ); //add new RSS feed output
    }

    function orbisius_rss_dashboard_widgets_feed_output() {
         echo '<div class="rss-widget">';

         wp_widget_rss_output(array(
            'url' => 'http://club.orbisius.com/feed',  //put your feed URL here
            'title' => 'Club.Orbisius.com RSS Feeds',
            'items' => 2, //how many posts to show
            'show_summary' => 1,
            'show_author' => 0,
            'show_date' => 1,
         ));

         wp_widget_rss_output(array(
            'url' => 'http://orbisius.com/feed',  //put your feed URL here
            'title' => 'Orbisius.com RSS Feeds',
            'items' => 2, //how many posts to show
            'show_summary' => 1,
            'show_author' => 0,
            'show_date' => 1,
         ));

         echo "</div>";

         // make sure that the RSS links from my site are opened in a new window.
         echo "<script>
                jQuery(document).ready(
                    function () {
                        jQuery('.rsswidget').attr('target', '_blank').attr('title', 'Learn more. Opens in a new [tab/window]');
                        jQuery('#orbisius_custom_dashboard_widget h3').css({
                            //'font-weight' : 'bold',
                            'background' : '#bbb',
                            //'color' : 'white'
                            'border-bottom' : '1px solid #777'
                        });

                        jQuery('#orbisius_custom_dashboard_widget').css({
                            'border' : '1px solid #777',
                            'padding' : 0
                        });
                    }
                );
              </script>";
    }
}