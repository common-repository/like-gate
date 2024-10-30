<?php
/*
  Plugin Name: Like Gate
  Plugin URI: http://club.orbisius.com/products/wordpress-plugins/like-gate/?utm_source=like-gate&utm_medium=plugin-readme&utm_campaign=product
  Description: Like Gate allows you to reveal some hidden/secret content when the user likes the article. Therefore increasing the likeness of your articles
  Tags: wordpress,wp,plugins,facebook,fb,like,likes,socia media,social media,viral,viral content
  Version: 1.1.6
  Author: Svetoslav Marinov (Slavi)
  Author URI: http://orbisius.com
  License: GPL v2
 */

/*
  Copyright 2011-2020 Svetoslav Marinov (slavi@slavi.biz)

  This program ais free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 2 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// we can be called from the test script
if (empty($_ENV['WEBWEB_WP_LIKE_GATE_TEST'])) {
    // Make sure we don't expose any info if called directly
    if (!function_exists('add_action')) {
        echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
        exit;
    }

	$webweb_wp_like_gate_obj = WebWeb_WP_LikeGate::get_instance();

    add_action('init', array($webweb_wp_like_gate_obj, 'init'));

    register_activation_hook(__FILE__, array($webweb_wp_like_gate_obj, 'on_activate'));
    register_deactivation_hook(__FILE__, array($webweb_wp_like_gate_obj, 'on_deactivate'));
    //register_uninstall_hook(__FILE__, array($webweb_wp_like_gate_obj, 'on_uninstall'));

    include_once(WEBWEB_WP_LIKE_GATE_BASE_DIR . '/zzz_dashboard_widgets.php');
}

class WebWeb_WP_LikeGate {
    private $log = 1;
    private static $instance = null; // singleton
    private $site_url = null; // filled in later
    private $plugin_url = null; // filled in later
    private $plugin_settings_key = null; // filled in later
    private $plugin_dir_name = null; // filled in later
    private $plugin_data_dir = null; // plugin data directory. for reports and data storing. filled in later
    private $plugin_name = 'Like Gate'; //
    private $plugin_id_str = 'like-gate'; //
    private $plugin_business_sandbox = false; // sandbox or live ???
    private $plugin_business_email_sandbox = 'seller_1264288169_biz@slavi.biz'; // used for paypal payments
    private $plugin_business_email = 'billing@orbisius.com'; // used for paypal payments
    private $plugin_business_ipn = 'http://orbisius.com/wp/hosted/payment/ipn.php'; // used for paypal IPN payments
    //private $plugin_business_status_url = 'http://localhost/wp/hosted/payment/status.php'; // used after paypal TXN to to avoid warning of non-ssl return urls
    private $plugin_business_status_url = 'https://ssl.orbisius.com/orbisius.com/wp/hosted/payment/status.php'; // used after paypal TXN to to avoid warning of non-ssl return urls
    private $plugin_support_email = 'help@orbisius.com'; //
    private $plugin_support_link = 'http://miniads.ca/widgets/contact/profile/like-gate?height=200&width=500&description=Please enter your enquiry below.'; //
    private $plugin_admin_url_prefix = null; // filled in later
    private $plugin_home_page = 'http://club.orbisius.com/products/wordpress-plugins/like-gate/';
    private $plugin_tinymce_name = 'like_gate'; // if you change it update the tinymce/editor_plugin.js and reminify the .min.js file.
    private $plugin_cron_hook = __CLASS__;
    private $plugin_cron_freq = 'daily';
    private $plugin_default_opts = array(
        'status' => 0,
        'app_id' => '',
    );

	private $app_title = 'Like Gate: increase the likeness of your articles!';
	private $plugin_description = 'Like Gate allows you to reveal some hidden/secret content when the user likes the article. Therefore increasing the likeness of your articles';

    // can't be instantiated; just using get_instance
    private function __construct() {

    }

    /**
     * handles the singleton
     */
    static public function get_instance() {
		if (is_null(self::$instance)) {
			$cls = __CLASS__;
			$inst = new $cls;

			$site_url = site_url();

			$inst->site_url = $site_url;
			$inst->plugin_dir_name = basename(dirname(__FILE__)); // e.g. wp-command-center; this can change e.g. a 123 can be appended if such folder exist
			$inst->plugin_data_dir = dirname(__FILE__) . '/data';
			$inst->plugin_url = $site_url . '/wp-content/plugins/' . $inst->plugin_dir_name . '/';
			$inst->plugin_settings_key = $inst->plugin_id_str . '_settings';
            $inst->plugin_support_link .= '&css_file=' . urlencode(get_bloginfo('stylesheet_url'));

			// not sure if this will work here
			// Use when develing to trigger cron sooner e.g. every 3 mins.
			//add_filter('cron_schedules', array($webweb_wp_like_gate_obj, 'define_cron_frequencies'));
			//add_filter('cron_schedules', array($inst, 'define_cron_frequencies'));
			//$inst->plugin_cron_freq = $inst->plugin_id_str . '3min';

            $inst->plugin_admin_url_prefix = $site_url . '/wp-admin/admin.php?page=' . $inst->plugin_dir_name;

			define('WEBWEB_WP_LIKE_GATE_BASE_DIR', dirname(__FILE__)); // e.g. // htdocs/wordpress/wp-content/plugins/wp-command-center
			define('WEBWEB_WP_LIKE_GATE_DIR_NAME', $inst->plugin_dir_name);

			if ($inst->log) {
				ini_set('log_errors', 1);
				ini_set('error_log', $inst->plugin_data_dir . '/error.log');
			}

			add_action('plugins_loaded', array($inst, 'init'), 100);

            self::$instance = $inst;
        }

		return self::$instance;
	}

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }

    /**
     * handles the init
     */
    function init() {
        $opts = $this->get_options();
        $pro_version_active = in_array( 'like-gate-pro/like-gate-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

        if (is_admin()) {
            // Administration menus
            add_action('admin_enqueue_scripts', array($this, 'load_assets'));

            if (!$pro_version_active) { // if we have the Pro running don't add the editor buttons.
                add_action('admin_init', array($this, 'add_buttons'));
                add_action('admin_notices', array($this, 'notices'));
            }
            
            add_action('admin_init', array($this, 'register_settings'));
            add_action('admin_menu', array($this, 'administration_menu'));
        } else if (!empty($opts['status']) // load only if enabled and Pro version not installed.
					&& !$pro_version_active ) {
            add_action('wp_head', array($this, 'add_meta_header'));

            // The short code is has a closing *tag* e.g. [tag]...[/tag] so normal tag partse won't work
            add_shortcode($this->plugin_id_str, array($this, 'parse_short_code'));

            //add_action('wp', array($this, 'setup_output_handler'), 9999, 0); // make sure it runs before the free like gate
            add_action( 'wp_enqueue_scripts', array($this, 'load_assets_public') );
            add_action( 'wp_footer', array($this, 'print_scripts'), 100 );
        }
    }

	/**
     * Outputs Facebook SDK stuff.
     */
    function print_scripts() {
        $app_id = '00000';
        $post_id = $this->get_current_post_id();
        $channel_url = $this->get('plugin_url') . 'channel.html';
        $cookie_domain = $_SERVER['HTTP_HOST'];
        $cookie_domain = preg_replace('#^(blog|www|store|secure|ssl|test)\.#si', '', $cookie_domain); // rm www.
        $cookie_domain = '.' . $cookie_domain; // leading dot

        $opts = $this->get_options();

        if (!empty($opts['app_id'])) {
            $app_id = $opts['app_id'];
        }

        // if it's not in the correct format we'll default to en_US. We even check the case.
        $lang = empty($opts['lang']) || !preg_match('#^[a-z][a-z]_[A-Z][A-Z]$#s', $opts['lang']) ? 'en_US' : $opts['lang'];
        $like_gate_fb_sdk = <<<JS_EOF

    <script>
    // LIKE_GATE_FB_SDK
    (function(d, s, id) { // Load the SDK asynchronously
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "//connect.facebook.net/$lang/all.js";
       fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));
     // /LIKE_GATE_FB_SDK
    </script>

JS_EOF;

    $fb_init = <<<JS_EOF
    <script>
    try {
        var like_gate_old_fb_async_init = window.fbAsyncInit;
        var like_gate_fb_async_init = function() {
            FB.init({
                //status: false, // user logged info not needed?
                appId      : '$app_id', // App ID from the App Dashboard
                channelUrl : '$channel_url', // Channel File for x-domain communication
                status     : true, // check the login status upon init?
                cookie     : true, // set sessions cookies to allow your server to access the session?
                xfbml      : true  // parse XFBML tags on this page?
            });

            wp_like_gate_setup_callbacks();
        };

        // Like Gate Pro FB INIT: https://developers.facebook.com/docs/reference/javascript/
        // do we have a previous fb async init function?
        window.fbAsyncInit = function() {
            if (typeof like_gate_old_fb_async_init === 'function') {
                like_gate_old_fb_async_init();
            }

            like_gate_fb_async_init();
         };

        // Like Gate Pro FB INIT END
    } catch (e) {
        console && console.log('done:' + e);
    }
    </script>
JS_EOF;

        $json = json_encode(array( 
            'app_id' => $app_id,
            'post_id' => $post_id,
            'product' => 'like_gate',
            'channel_url' => $channel_url,
            'cookie_domain' => $cookie_domain,
        ));

        $json_cfg = "<script type='text/javascript'>\n";
        $json_cfg .= "var like_gate_cfg = $json;\n";
        $json_cfg .= "</script>";

        $buff = '';
        $buff .= $json_cfg;
        $buff .= $like_gate_fb_sdk;
        $buff .= $fb_init;

        echo $buff;
    }

    /**
     * returns .min for live and empty string for dev
     * @return str
     */
    function get_assets_suffix() {
        $suffix = empty($_SERVER['DEV_ENV']) ? '.min' : '';

        return $suffix;
    }

    /**
     * 
     */
    function load_assets_public() {
        $suffix = $this->get_assets_suffix();
        
        wp_enqueue_script( 'jquery' );

        wp_register_script( 'like_gate_req_cookie', plugins_url("/assets/share/jquery.cookie{$suffix}.js", __FILE__), array('jquery', ),
            filemtime( plugin_dir_path( __FILE__ ) . "/assets/share/jquery.cookie{$suffix}.js" ), true);
        wp_enqueue_script( 'like_gate_req_cookie' );

        wp_register_script( 'like_gate', plugins_url("/assets/main{$suffix}.js", __FILE__), array('jquery', 'like_gate_req_cookie'),
            filemtime( plugin_dir_path( __FILE__ ) . "/assets/main{$suffix}.js" ), true);
        wp_enqueue_script( 'like_gate' );
    }

    function load_assets() {
		$suffix = $this->get_assets_suffix();
	
        wp_enqueue_script('jquery');

        wp_register_style($this->plugin_dir_name, $this->plugin_url . "css/main{$suffix}.css", false,
            filemtime( plugin_dir_path( __FILE__ ) . "/css/main{$suffix}.css" ) );

        wp_enqueue_style($this->plugin_dir_name);
    }
	
    /**
     * Setups the callback for the output handler
     *
     * @param void
     */
    function setup_output_handler() {
        if (is_feed() || is_admin()) {
            return;
        }

        ob_start(array($this, 'parse_output'));
    }

    /**
     * Parse the whole content (wp doesn't still allow that).
     * We need to add some attributes in the <html> tag even before the <head>...tag
     *
     * @param string $buffer
     * @return string
     */
    function parse_output($buffer) {
        $buffer = $this->check_for_missing_namespaces($buffer);
        $buffer = $this->parse_short_code_process_full_page($buffer);

        return $buffer;
    }

    /**
     * searches and replaces the short code
	 * [like-gate] ..... [/like-gate] everything in between is hidden from non-likers
     */
    function parse_short_code($attr = array(), $content = '') {
        $this->like_gate_shortcode_present = 1;
        $buffer = $this->encrypt_text($content, '', $attr);

        return $buffer;
    }

    /**
     * searches and replaces the short code
	 * [like-gate] ..... [/like-gate] everything in between is hidden from non-likers
     */
    function parse_short_code_process_full_page($buffer) {
        $like_gate_buffer = '';
        $app_id = '109282182482812'; // hardcoded my like gate app
        
        $buffer = stripslashes_deep($buffer);

        $post_id = $this->get_current_post_id();
        
        // https://developers.facebook.com/docs/reference/javascript/
        // Adding a Channel File greatly improves the performance of the JS SDK by addressing issues with cross-domain communication in certain browsers.
        $channel_url = $this->get('plugin_url') . 'channel.html';

        $opts = $this->get_options();

        if (!empty($opts['app_id'])) {
            $app_id = $opts['app_id'];
        }

        if ((stripos($buffer, '[like-gate]') !== false)
				&& (stripos($buffer, '[/like-gate]') !== false)) {

            // Plugin is inactive let's remove the tags
            if (empty($opts['status'])) {
                $buffer = preg_replace('#\[like-gate\](.*?)\[/like-gate\]#si', '', $buffer, $matches);

                return $buffer;
            }

            // encrypt the text enclosed into the like gate short code
            // we want the replacement to start from within the <body> because there could be header/meta fields that match our
            // short code.
			$buffer = preg_replace('#(<body[^>]*>.*?)\[like-gate\](.*?)\[/like-gate\]#sie', '$this->encrypt_text("\\2", "\\1")', $buffer);

            // is it added already?
            if (stripos($buffer, '<div id="fb-root"></div>') === false) {
                $like_gate_buffer .= '<div id="fb-root"></div>';
            }

            // is it added already?
            // 2013-02-20: we'll add
            /*if (stripos($buffer, 'connect.facebook.net/en_US/all.js') === false) {
                $like_gate_buffer .= '<script src="http://connect.facebook.net/en_US/all.js#appId=109282182482812&amp;xfbml=1"></script>';
            }*/

            if (stripos($buffer, 'LIKE_GATE_OUTPUT') === false) { // only once
                $cookie_domain = $_SERVER['HTTP_HOST'];
                $cookie_domain = preg_replace('#^(blog|www|store|secure|ssl|test)\.#si', '', $cookie_domain); // rm www.
                $cookie_domain = '.' . $cookie_domain; // leading dot
                $lang = empty($opts['lang']) || !preg_match('#^[a-z][a-z]_[A-Z][A-Z]$#s', $opts['lang']) ? 'en_US' : $opts['lang'];
                
                $like_gate_fb_sdk = <<<JS_EOF
    // LIKE_GATE_FB_SDK
    (function(d, s, id){ // Load the SDK asynchronously
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "//connect.facebook.net/$lang/all.js";
       fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));
     // /LIKE_GATE_FB_SDK
JS_EOF;

                $like_gate_buffer .= <<<JS_EOF
<script>
    // LIKE_GATE_OUTPUT
    var cookie_domain = '$cookie_domain';

    if (typeof jQuery.cookie == 'undefined') {
        /**
         * Cookie plugin
         *
         * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
         * Dual licensed under the MIT and GPL licenses:
         * http://www.opensource.org/licenses/mit-license.php
         * http://www.gnu.org/licenses/gpl.html
         *
         */

        /**
         * Create a cookie with the given name and value and other optional parameters.
         *
         * @example $.cookie('the_cookie', 'the_value');
         * @desc Set the value of a cookie.
         * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
         * @desc Create a cookie with all available options.
         * @example $.cookie('the_cookie', 'the_value');
         * @desc Create a session cookie.
         * @example $.cookie('the_cookie', null);
         * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
         *       used when the cookie was set.
         *
         * @param String name The name of the cookie.
         * @param String value The value of the cookie.
         * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
         * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
         *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
         *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
         *                             when the the browser exits.
         * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
         * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
         * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
         *                        require a secure protocol (like HTTPS).
         * @type undefined
         *
         * @name $.cookie
         * @cat Plugins/Cookie
         * @author Klaus Hartl/klaus.hartl@stilbuero.de
         */

        /**
         * Get the value of a cookie with the given name.
         *
         * @example $.cookie('the_cookie');
         * @desc Get the value of a cookie.
         *
         * @param String name The name of the cookie.
         * @return The value of the cookie.
         * @type String
         *
         * @name $.cookie
         * @cat Plugins/Cookie
         * @author Klaus Hartl/klaus.hartl@stilbuero.de
         */
        jQuery.cookie = function(name, value, options) {
            if (typeof value != 'undefined') { // name and value given, set cookie
                options = options || {};
                if (value === null) {
                    value = '';
                    options.expires = -1;
                }
                var expires = '';
                if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                    var date;
                    if (typeof options.expires == 'number') {
                        date = new Date();
                        date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                    } else {
                        date = options.expires;
                    }
                    expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
                }
                // CAUTION: Needed to parenthesize options.path and options.domain
                // in the following expressions, otherwise they evaluate to undefined
                // in the packed version for some reason...
                var path = options.path ? '; path=' + (options.path) : '';
                var domain = options.domain ? '; domain=' + (options.domain) : '';
                var secure = options.secure ? '; secure' : '';
                document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
            } else { // only name given, get cookie
                var cookieValue = null;
                if (document.cookie && document.cookie != '') {
                    var cookies = document.cookie.split(';');
                    for (var i = 0; i < cookies.length; i++) {
                        var cookie = jQuery.trim(cookies[i]);
                        // Does this cookie string begin with the name we want?
                        if (cookie.substring(0, name.length + 1) == (name + '=')) {
                            cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                            break;
                        }
                    }
                }
                return cookieValue;
            }
        };
     }

     // credits: phpjs, Jonas Raoni Soares Silva (http://www.jsfromhell.com), Ates Goral (http://magnetiq.com), Onno Marsman, Rafał Kukawski (http://blog.kukawski.pl)
     function like_gate_decrypt(str, pwd) {
        return (str + '').replace(/[a-z]/gi, function (s) {
            return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
        });
     }

     // handles like and unlike events
     function like_gate_handle_event(pars) {
       pars = pars || {};

       if (pars.event == 'unlike') {
            jQuery('.like-gate-result').hide('slow').html('');
            jQuery.cookie('like_gate_lp_{$post_id}', null, { path: '/' });

            return true;
       }

       // , domain: '$cookie_domain' // chrome doesn't like the domain !?! doesn't delete the cookie.
       jQuery.cookie('like_gate_lp_{$post_id}', 1, { expires: 730, path: '/' });

       var decrypted_hidden = like_gate_decrypt(jQuery('.like-gate').html());
       jQuery('.like-gate-result').html(decrypted_hidden).show('slow');
     }

     jQuery(document).ready(function($) {
        var like_status = jQuery.cookie('like_gate_lp_{$post_id}');

        // let's reveal if the user has already liked the page.
        if (like_status > 0) {
            like_gate_handle_event({event:'like'});
        }

        wp_like_gate_setup_callbacks();
     });

     // we can call the setup callback functions 2 so
     // we want to make sure it is called only once.
     // 1 is when FB async is called
     // 2 if the option 1 doesn't fire then run on doc ready
     // option 2 can happen if there are multiple plugins overriding fb async
     var wp_like_gate_setup_callbacks_init_done = 0;
     var wp_like_gate_setup_callbacks_init_attempts = 0;
     var wp_like_gate_setup_callbacks_init_timer_id = 0;

     function wp_like_gate_setup_callbacks() {
        if (window.wp_like_gate_setup_callbacks_init_done) {
            if (wp_like_gate_setup_callbacks_init_timer_id) {
                clearTimeout(wp_like_gate_setup_callbacks_init_timer_id);
            }
            return ;
        } else if (typeof FB == 'undefined') {
            if (wp_like_gate_setup_callbacks_init_attempts < 5) {
                wp_like_gate_setup_callbacks_init_timer_id = setTimeout(function () {
                    wp_like_gate_setup_callbacks();
                }, 2000);
            }
            window.wp_like_gate_setup_callbacks_init_attempts++;
            return ;
        }

        // handles like
		FB.Event.subscribe('edge.create', function(href, widget) {
           like_gate_handle_event({
                event: 'like',
                url : href,
                widget: widget
           });
		});

		 // handles unlike, hide the hidden content
		FB.Event.subscribe('edge.remove', function(href, widget) {
           like_gate_handle_event({event:'unlike'});
		});

        window.wp_like_gate_setup_callbacks_init_done = 1;
     }

	// %%LIKE_GATE_FB_INIT%%
        
    // %%LIKE_GATE_FB_SDK%%
</script>
JS_EOF;
            } // /LIKE_GATE_OUTPUT

            $fb_init = <<<JS_EOF
    // Like Gate FB INIT: https://developers.facebook.com/docs/reference/javascript/
        window.fbAsyncInit = function() {
                FB.init({
                    appId      : '$app_id', // App ID from the App Dashboard
                    channelUrl : '$channel_url', // Channel File for x-domain communication
                    status     : true, // check the login status upon init?
                    cookie     : true, // set sessions cookies to allow your server to access the session?
                    xfbml      : true  // parse XFBML tags on this page?
                });

                wp_like_gate_setup_callbacks();
            };
    // Like Gate FB INIT END
JS_EOF;
            // is the FB SDK already included ? if not then inject it.
            if ((stripos($buffer, 'facebook-jssdk') === false) && !preg_match('#[\'"]facebook-jssdk[\'"]#si', $buffer)) {
                $like_gate_buffer = preg_replace('#\/+\s*%%LIKE_GATE_FB_SDK%%#si', $like_gate_fb_sdk, $like_gate_buffer);
            }
            
            // fb jssdk hasn't been instantiated by another fb plugin
            if (stripos($buffer, 'window.fbAsyncInit') === false) {
                $like_gate_buffer = preg_replace('#\/+\s*%%LIKE_GATE_FB_INIT%%#si', $fb_init, $like_gate_buffer);
            } else {
                // inject a call to wp_like_gate_setup_callbacks() right after existing FB.init method.
                $existing_fb_init_regex = '#(window\.fbAsyncInit.*?FB\.init.*?\}\s*\)\s*;)#si';
                $buffer = preg_replace($existing_fb_init_regex, "\\1\n// like-gate-auto-injected\n" . 'wp_like_gate_setup_callbacks();' . "\n", $buffer);

                // replace the LIKE_GATE_FB_INIT with another comment to hint us that we've injected our code into other fb.init methods.
                $like_gate_buffer = preg_replace('#\/+\s*%%LIKE_GATE_FB_INIT%%#si', '// like-gate-fb-init injected into other init methods.', $like_gate_buffer);
            }

            // prepend the closing body tag
            $buffer = preg_replace('#</body>#si', $like_gate_buffer . "\n\\0", $buffer);

            //$like_gate_buffer
		} // like gate short codes

		return $buffer;
    }

    /**
     * Sometimes plugins do not reset variables so we may get incorrect values.
     */
    function get_current_post_id() {
        global $post;
        global $wp_query;

        $post_id = $post->ID;

        if (!empty($wp_query->post) && $wp_query->post->ID != $post->ID) {
            $post_id = $wp_query->post->ID;
        }

        return $post_id;
    }

    /**
     * encrypts the hidden text so it's hard to see
     */
    function encrypt_text($buffer, $buffer_preceeding_buffer = '', $attrib_list = '') {
        $style = 'style="display:none;"';

        $post_id = $this->get_current_post_id();
        $post_url = get_permalink($post_id); // the user will like this

        $like_box =<<<LIKE_BOX
<!-- like-gate-result : $attrib_list -->
    <div id='like-gate-result' class='like-gate-result' $style></div>
<!-- /like-gate-result -->

<div class='like_gate_like_container' post_url="$post_url" post_id='{$post_id}'>
        <fb:like href="$post_url" layout="standard" show-faces="false" width="450" action="like" colorscheme="light"></fb:like>
</div>
LIKE_BOX;

        $like_box .= "\n<script> var like_gate = { }; </script>\n"; // pro has options here
        $buffer = $like_box . "<!-- like-gate-secret id:{$post_id} -->\n"
            . "<div $style class='like-gate like-gate-secret' post_id='{$post_id}' post_url='$post_url'>"
            . $this->prepare_buffer($buffer) . "\n</div>\n<!-- /like-gate-secret -->\n"; // . var_export($post, 1);

        $buffer = $buffer_preceeding_buffer . $buffer;

		return $buffer;
    }

    /**
     * We'll encode the text first and then encrypt it because it turned out that just doing rot13
     * caused some chars e.g. & to be encrypted as html entity.
     *
     * @param string $buff
     * @return string
     */
    function prepare_buffer($buff) {
        $buff = urlencode($buff);
        $buff = str_rot13($buff);

        return $buff;
    }

    /**
     * Handles the plugin activation. Setup cron and set default configs
     */
    function install_cron() {
        $when = mktime(23, 30, 0, date('m'), date('d'), date('Y'));
        $when = time();

        wp_schedule_event($when, $this->plugin_cron_freq, $this->plugin_cron_hook);
    }

    /**
     * Handles the plugin activation. Setup cron and set default configs
     */
    function uninstall_cron() {
        wp_clear_scheduled_hook($this->plugin_cron_hook);
    }

    /**
     * checks if WP has installed the hook
     * @return bool
     */
    function is_cron_scheduled() {
        $status = wp_get_schedule($this->plugin_cron_hook);

        return $status !== false;
    }

    /**
     * Handles the plugin activation. Setup cron and set default configs
     * This code was left from another plugin of mine and should not be here.
     * let's clean old cron stuff if any.
     */
    function on_activate() {
        if ($this->is_cron_scheduled()) {
            $this->uninstall_cron();
        }
		
		/*$opts['status'] = 0;
        $this->set_options($opts);*/
    }

    /**
     * Handles the plugin deactivation. Remove cron and set default configs
     */
    function on_deactivate() {
        //$opts['status'] = 0;
        //$this->set_options($opts);
        $this->uninstall_cron();
    }

    /**
     * Handles the plugin uninstallation. remove cron and set default configs
     */
    function on_uninstall() {
        delete_option($this->plugin_settings_key);
        $this->uninstall_cron();
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function get($var) {
        if (isset($this->$var) /* && (strpos($var, 'plugin') !== false) */) {
            return $this->$var;
        }
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function generate_newsletter_box() {
        $file = WEBWEB_WP_LIKE_GATE_BASE_DIR . '/zzz_newsletter_box.html';

        $buffer = WebWeb_WP_LikeGateUtil::read($file);

        wp_get_current_user();
        global $current_user;
        $user_email = $current_user->user_email;

        $replace_vars = array(
            '%%PLUGIN_URL%%' => $this->get('plugin_url'),
            '%%USER_EMAIL%%' => $user_email,
        );

        $buffer = str_replace(array_keys($replace_vars), array_values($replace_vars), $buffer);

        return $buffer;
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function generate_donate_box() {
        $msg = '';
        $file = WEBWEB_WP_LIKE_GATE_BASE_DIR . '/zzz_donate_box.html';

        if (!empty($_REQUEST['error'])) {
            $msg = $this->message('There was a problem with the payment.');
        }

        if (!empty($_REQUEST['ok'])) {
            $msg = $this->message('Thank you so much!', 1);
        }

        $return_url = WebWeb_WP_LikeGateUtil::add_url_params($this->get('plugin_business_status_url'), array(
            'r' => $this->get('plugin_admin_url_prefix') . '/menu.dashboard.php&ok=1', // paypal de/escapes
            'status' => 1,
        ));

        $cancel_url = WebWeb_WP_LikeGateUtil::add_url_params($this->get('plugin_business_status_url'), array(
            'r' => $this->get('plugin_admin_url_prefix') . '/menu.dashboard.php&error=1', //
            'status' => 0,
        ));

        $replace_vars = array(
            '%%MSG%%' => $msg,
            '%%AMOUNT%%' => '10',
            '%%BUSINESS_EMAIL%%' => $this->plugin_business_email,
            '%%ITEM_NAME%%' => $this->plugin_name . ' Donation',
            '%%ITEM_NAME_REGULARLY%%' => $this->plugin_name . ' Donation (regularly)',
            '%%PLUGIN_URL%%' => $this->get('plugin_url'),
            '%%CUSTOM%%' => http_build_query(array('site_url' => $this->site_url, 'product_name' => $this->plugin_id_str)),
            '%%NOTIFY_URL%%' => $this->get('plugin_business_ipn'),
            '%%RETURN_URL%%' => $return_url,
            '%%CANCEL_URL%%' => $cancel_url,
        );

        // Let's switch the Sandbox settings.
        if ($this->plugin_business_sandbox) {
            $replace_vars['paypal.com'] = 'sandbox.paypal.com';
            $replace_vars['%%BUSINESS_EMAIL%%'] = $this->plugin_business_email_sandbox;
        }

        $buffer = WebWeb_WP_LikeGateUtil::read($file);
        $buffer = str_replace(array_keys($replace_vars), array_values($replace_vars), $buffer);

        return $buffer;
    }

    /**
     * gets current options and return the default ones if not exist
     * @param void
     * @return array
     */
    function get_options() {
        $opts = get_option($this->plugin_settings_key);
        $opts = empty($opts) ? array() : (array) $opts;

        // if we've introduced a new default key/value it'll show up.
        $opts = array_merge($this->plugin_default_opts, $opts);
        $opts['app_id'] = empty($opts['app_id']) ? '' : trim($opts['app_id']);
        $opts = array_map('trim', $opts);

        return $opts;
    }

    /**
     * Updates options but it merges them unless $override is set to 1
     * that way we could just update one variable of the settings.
     */
    function set_options($opts = array(), $override = 0) {
        if (!$override) {
            $old_opts = $this->get_options();
            $opts = array_merge($old_opts, $opts);
        }

        $opts = array_map('trim', $opts);
        update_option($this->plugin_settings_key, $opts);

        return $opts;
    }

    /**
     * This is what the plugin admins will see when they click on the main menu.
     * @var string
     */
    private $plugin_landing_tab = '/menu.dashboard.php';

    /**
     * Adds the settings in the admin menu
     */
    public function administration_menu() {
        // Settings > Like Gate
        //add_options_page(__($this->plugin_name, "WEBWEB_WP_PARTNER_WATCHER"), __($this->plugin_name, "WEBWEB_WP_PARTNER_WATCHER"), 'manage_options', __FILE__, array($this, 'options'));

        // Main page
        add_menu_page(__($this->plugin_name, $this->plugin_dir_name), __($this->plugin_name, $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.dashboard.php', null, $this->plugin_url . '/images/icon.png');

        // Sub Pages
        add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('Dashboard', $this->plugin_dir_name), __('Dashboard', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.dashboard.php');
        add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('Settings', $this->plugin_dir_name), __('Settings', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.settings.php');
        add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('FAQ', $this->plugin_dir_name), __('FAQ', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.faq.php');
        add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('Help', $this->plugin_dir_name), __('Help', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.support.php');
        //add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('Contact', $this->plugin_dir_name), __('Contact', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.contact.php');
        //add_submenu_page($this->plugin_dir_name . '/' . $this->plugin_landing_tab, __('About', $this->plugin_dir_name), __('About', $this->plugin_dir_name), 'manage_options', $this->plugin_dir_name . '/menu.about.php');

        // when plugins are show add a settings link near my plugin for a quick access to the settings page.
        add_filter('plugin_action_links', array($this, 'add_plugin_settings_link'), 10, 2);
    }

    /**
     * Outputs some options info. No save for now.
     */
    function options() {
		$webweb_wp_like_gate_obj = WebWeb_WP_LikeGate::get_instance();
        $opts = get_option('settings');

        include_once(WEBWEB_WP_LIKE_GATE_BASE_DIR . '/menu.settings.php');
    }

    /**
     *
     * @param type $input_arr
     * @return type
     */
    function validate_options($input_arr) {
        // we want to preserve other options e.g. from the Pro version.
        $opts = $this->get_options();
        $input_arr = array_merge($opts, $input_arr);

        $input_arr = stripslashes_deep($input_arr);
        $input_arr = array_map('trim', $input_arr);

        if ( empty($input_arr['app_id']) || !preg_match('#^\d+$#si', $input_arr['app_id']) ) {
            $input_arr['app_id'] = '';
        }
        return $input_arr;
    }

    /**
     * Sets the setting variables
     */
    function register_settings() { // whitelist options
        register_setting($this->plugin_dir_name, $this->plugin_settings_key, array($this, 'validate_options'));
    }

    // Add the ? settings link in Plugins page very good
    function add_plugin_settings_link($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
            $link_html = '<a href="http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/?utm_source=like-gate&utm_medium=plugin-action-links&utm_campaign=product"'
                    . ' target="_blank" title="[new window]">Get Like Gate Pro</a>';
            array_unshift($links, $link_html);

            $support_link = 'http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/like-gate/?utm_source=like-gate&utm_medium=plugin-action-links&settings&utm_campaign=product';
            $settings_link = "<a href='$support_link' target='_blank'>Support</a>";
            array_unshift($links, $settings_link);

            // old settings link
            $link = admin_url("admin.php?page=$this->plugin_dir_name/menu.dashboard.php");
            //$link_html = '<a href="options-general.php?page=' . dirname(plugin_basename(__FILE__)) . '/' . basename(__FILE__) . '">' . (__("Settings", "WEBWEB_WP_PARTNER_WATCHER")) . '</a>';
            $link_html = "<a href='$link'>Settings</a>";
            array_unshift($links, $link_html);
        }

        return $links;
    }

    function add_meta_header() {
        printf("\n" . '<meta name="generator" content="Powered by ' . $this->plugin_name . ' (' . $this->plugin_home_page . ') " />' . PHP_EOL);
    }

    // kept for future use if necessary

    /**
     * Adds buttons only for RichText mode
     * @return void
     */
    function add_buttons() {
        // Don't bother doing this stuff if the current user lacks permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // Add only in Rich Editor mode
        if (get_user_option('rich_editing') == 'true') {
            // add the button for wp2.5 in a new way
            add_filter("mce_external_plugins", array($this, "add_tinymce_plugin"), 5);
            add_filter('mce_buttons', array(&$this, 'register_button'), 5);

            add_action( 'wp_ajax_like_gate_ajax_render_popup_content', 'like_gate_ajax_render_popup_content');
            add_action( 'wp_ajax_like_gate_ajax_render_popup_content', 'like_gate_ajax_render_popup_content');
        }
    }

    // used to insert button in wordpress 2.5x editor
    function register_button($buttons) {
        array_push($buttons, "separator", $this->plugin_tinymce_name);

        return $buttons;
    }

    // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
    function add_tinymce_plugin($plugin_array) {
        $suffix = $this->get_assets_suffix();
        $plugin_array[$this->plugin_tinymce_name] = $this->plugin_url . "tinymce/editor_plugin$suffix.js";


        return $plugin_array;
    }

    /**
     * Checks if WP simpple shopping cart is installed.
     */
    function notices() {
        $opts = $this->get_options();

        if (empty($opts['status'])) {
            echo $this->message($this->plugin_name . " is currently disabled. Please, enable it from "
                    ."<a href='{$this->plugin_admin_url_prefix}/menu.settings.php'> {$this->plugin_name} &gt; Settings</a>");
        } elseif (empty($opts['app_id'])) {
            echo $this->message($this->plugin_name . " now requires facebook app ID. You can set it from "
                    . "<a href='{$this->plugin_admin_url_prefix}/menu.settings.php'> {$this->plugin_name} &gt; Settings</a>");
        }
    }

    /**
     * Outputs a message (adds some paragraphs)
     */
    function message($msg, $status = 0) {
        $id = $this->plugin_id_str;
        $cls = empty($status) ? 'app_error fade alternate' : 'app_success';

        $str = <<<MSG_EOF
<div id='$id-notice' class='app_message_box $cls'><p><strong>$msg</strong></p></div>
MSG_EOF;
        return $str;
    }

    /**
     * a simple status message, no formatting except color
     */
    function msg($msg, $status = 0) {
        $id = $this->plugin_id_str;
        $cls = empty($status) ? 'app_error' : 'app_success';

        $str = <<<MSG_EOF
<div id='$id-notice' class='$cls'><strong>$msg</strong></div>
MSG_EOF;
        return $str;
    }

    /**
     * a simple status message, no formatting except color, simpler than its brothers
     */
    function m($msg, $status = 0) {
        $cls = empty($status) ? 'app_error' : 'app_success';

        $str = <<<MSG_EOF
<span class='$cls'>$msg</span>
MSG_EOF;
        return $str;
    }

    /**
     * Adds missing namespaces because the like will not show up in IE 6,7,8 if they are not set. the header must contain some tags so IE allows tags starting with <fb:... >
     * @param string $matched_str
     * @return string
     */
    public function check_for_missing_namespaces($buffer) {
        // Adds missing namespaces because the like will not show up in IE 6,7,8 if they are not set
        $buffer = preg_replace('#<html([^>]*)>#sie', "WebWeb_WP_LikeGateUtil::add_missing_namespaces('\\1')", $buffer);

        return $buffer;
    }
}

class WebWeb_WP_LikeGateUtil {
    // options for read/write methods.
    const FILE_APPEND = 1;
    const UNSERIALIZE_DATA = 2;
    const SERIALIZE_DATA = 3;

    /**
     * Gets the content from the body, removes the comments, scripts
     * Credits: http://php.net/manual/en/function.strip-tags.phpm /  http://networking.ringofsaturn.com/Web/removetags.php
     * @param string $buffer
     * @string string $buffer
     */
    public static function html2text($buffer = '') {
        // we care only about the body so it must be beautiful.
        $buffer = preg_replace('#.*<body[^>]*>(.*?)</body>.*#si', '\\1', $buffer);
        $buffer = preg_replace('#<script[^>]*>.*?</script>#si', '', $buffer);
        $buffer = preg_replace('#<style[^>]*>.*?</style>#siU', '', $buffer);
//        $buffer = preg_replace('@<style[^>]*>.*?</style>@siU', '', $buffer); // Strip style tags properly
        $buffer = preg_replace('#<[a-zA-Z\/][^>]*>#si', ' ', $buffer); // Strip out HTML tags  OR '@<[\/\!]*?[^<>]*\>@si',
        $buffer = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $buffer); // Strip multi-line comments including CDATA
        $buffer = preg_replace('#[\t\ ]+#si', ' ', $buffer); // replace just one space
        $buffer = preg_replace('#[\n\r]+#si', "\n", $buffer); // replace just one space
        //$buffer = preg_replace('#(\s)+#si', '\\1', $buffer); // replace just one space
        $buffer = preg_replace('#^\s*|\s*$#si', '', $buffer);

        return $buffer;
    }

    /**
     * Gets the content from the body, removes the comments, scripts
     *
     * @param string $buffer
     * @param array $keywords
     * @return array - for now it returns hits; there could be some more complicated results in the future so it's better as an array
     */
    public static function match($buffer = '', $keywords = array()) {
        $status_arr['hits'] = 0;

        foreach ($keywords as $keyword) {
            $cnt = preg_match('#\b' . preg_quote($keyword) . '\b#si', $buffer);

            if ($cnt) {
                $status_arr['hits']++; // total hits
                $status_arr['matches'][$keyword] = array('keyword' => $keyword, 'hits' => $cnt,); // kwd hits
            }
        }

        return $status_arr;
    }

    /**
     * @desc write function using flock
     *
     * @param string $vars
     * @param string $buffer
     * @param int $append
     * @return bool
     */
    public static function write($file, $buffer = '', $option = null) {
        $buff = false;
        $tries = 0;
        $handle = '';

        $write_mod = 'wb';

        if ($option == self::SERIALIZE_DATA) {
            $buffer = serialize($buffer);
        } elseif ($option == self::FILE_APPEND) {
            $write_mod = 'ab';
        }

        if (($handle = @fopen($file, $write_mod))
                && flock($handle, LOCK_EX)) {
            // lock obtained
            if (fwrite($handle, $buffer) !== false) {
                @fclose($handle);
                return true;
            }
        }

        return false;
    }

    /**
     * @desc read function using flock
     *
     * @param string $vars
     * @param string $buffer
     * @param int $option whether to unserialize the data
     * @return mixed : string/data struct
     */
    public static function read($file, $option = null) {
        $buff = false;
        $read_mod = "rb";
        $tries = 0;
        $handle = false;

        if (($handle = @fopen($file, $read_mod))
                && (flock($handle, LOCK_EX))) { //  | LOCK_NB - let's block; we want everything saved
            $buff = @fread($handle, filesize($file));
            @fclose($handle);
        }

        if ($option == self::UNSERIALIZE_DATA) {
            $buff = unserialize($buff);
        }

        return $buff;
    }

    /**
     *
     * Appends a parameter to an url; uses '?' or '&'
     * It's the reverse of parse_str().
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function add_url_params($url, $params = array()) {
        $str = '';

        $params = (array) $params;

        if (empty($params)) {
            return $url;
        }

        $query_start = (strpos($url, '?') === false) ? '?' : '&';

        foreach ($params as $key => $value) {
            $str .= ( strlen($str) < 1) ? $query_start : '&';
            $str .= rawurlencode($key) . '=' . rawurlencode($value);
        }

        $str = $url . $str;

        return $str;
    }

    // generates HTML select
    public static function html_select($name = '', $options = array(), $sel = null, $attr = '') {
        $html = "\n" . '<select name="' . $name . '" ' . $attr . '>' . "\n";

        foreach ($options as $key => $label) {
            $selected = $sel == $key ? ' selected="selected"' : '';
            $html .= "\t<option value='$key' $selected>$label</option>\n";
        }

        $html .= '</select>';
        $html .= "\n";

        return $html;
    }

    // generates status msg
    public static function msg($msg = '', $status = 0) {
        $cls = empty($status) ? 'error' : 'success';
        $cls = $status == 2 ? 'notice' : $cls;

        $msg = "<p class='status_wrapper'><div class=\"status_msg $cls\">$msg</div></p>";

        return $msg;
    }

    /**
     * Adds missing namespaces because the like will not show up in IE 6,7,8 if they are not set
     * @param string $matched_str
     * @return string
     */
    public static function add_missing_namespaces($matched_str) {
        $og = 'xmlns:og="http://opengraphprotocol.org/schema/"';
        $fb = 'xmlns:fb="http://www.facebook.com/2008/fbml"';

        if (stripos($matched_str, 'xmlns:og') === false) {
            $matched_str .= ' ' . $og;
        }

        if (stripos($matched_str, 'xmlns:fb') === false) {
            $matched_str .= ' ' . $fb;
        }

        $matched_str = stripslashes_deep($matched_str);
        $matched_str = '<html' . $matched_str . '>';

        return $matched_str;
    }
}

class WebWeb_WP_LikeGateCrawler {

    private $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:6.0) Gecko/20100101 Firefox/6.0";
    private $error = null;
    private $buffer = null;

    function __construct() {
        ini_set('user_agent', $this->user_agent);
    }

    /**
     * Error(s) from the last request
     *
     * @return string
     */
    function getError() {
        return $this->error;
    }

    // checks if buffer is gzip encoded
    function is_gziped($buffer) {
        return (strcmp(substr($buffer, 0, 8), "\x1f\x8b\x08\x00\x00\x00\x00\x00") === 0) ? true : false;
    }

    /*
      henryk at ploetzli dot ch
      15-Feb-2002 04:28
      http://php.online.bg/manual/hu/function.gzencode.php
     */

    function gzdecode($string) {
        if (!function_exists('gzinflate')) {
            return false;
        }

        $string = substr($string, 10);
        return gzinflate($string);
    }

    /**
     * Fetches a url and saves the data into an instance variable. The returned status is whether the request was successful.
     *
     * @param string $url
     * @return bool
     */
    function fetch($url) {
        $ok = 0;
        $buffer = '';

        $url = trim($url);

        if (!preg_match("@^(?:ht|f)tps?://@si", $url)) {
            $url = "http://" . $url;
        }

        // try #1 cURL
        // http://fr.php.net/manual/en/function.fopen.php
        if (empty($ok)) {
            if (function_exists("curl_init") && extension_loaded('curl')) {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip'));
                curl_setopt($ch, CURLOPT_TIMEOUT, 45);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5); /* Max redirection to follow */
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                /* curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; // in the future pwd protected dirs
                  curl_setopt($ch, CURLOPT_USERPWD, "username:password"); */ //  http://php.net/manual/en/function.curl-setopt.php

                $string = curl_exec($ch);
                $curl_res = curl_error($ch);

                curl_close($ch);

                if (empty($curl_res) && strlen($string)) {
                    if ($this->is_gziped($string)) {
                        $string = $this->gzdecode($string);
                    }

                    $this->buffer = $string;

                    return 1;
                } else {
                    $this->error = $curl_res;
                    return 0;
                }
            }
        } // empty ok*/
        // try #2 file_get_contents
        if (empty($ok)) {
            $buffer = @file_get_contents($url);

            if (!empty($buffer)) {
                $this->buffer = $buffer;
                return 1;
            }
        }

        // try #3 fopen
        if (empty($ok) && preg_match("@1|on@si", ini_get("allow_url_fopen"))) {
            $fp = @fopen($url, "r");

            if (!empty($fp)) {
                $in = '';

                while (!feof($fp)) {
                    $in .= fgets($fp, 8192);
                }

                @fclose($fp);
                $buffer = $in;

                if (!empty($buffer)) {
                    $this->buffer = $buffer;
                    return 1;
                }
            }
        }

        return 0;
    }

    function get_content() {
        return $this->buffer;
    }
}

/**
 * Are we running WP 3.9 or higher?
 * We need this because some of the TinyMCE API has changed.
 */
function like_gate_39up() {
    global  $wp_version;
    $wp_3_9_plus = floatval($wp_version) >= 3.9;

    return $wp_3_9_plus ? 1 : 0;
}

/**
 * This is triggered by editor_plugin.min.js and WP proxies the ajax calls to this action.
 *
 * @return void
 */
function like_gate_ajax_render_popup_content() {
    // check for rights
    if (!is_user_logged_in()) {
        wp_die(__("You must be logged in order to use this plugin."));
    }

    $site_url = site_url();

    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Like Gate</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
        <script language="javascript" type="text/javascript" src="<?php echo $site_url; ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

        <script language="javascript" type="text/javascript">
            var app_like_gate = {
                is_new_wp : <?php echo like_gate_39up(); ?>,
                init : function () {
                    tinyMCEPopup.resizeToInnerSize();
                    //document.body.style.display = '';
                    
                    setTimeout(function () {
                        document.getElementById('like_gate_hidden_content').focus();
                    }, 250);
                },

                close : function () {
                    if (this.is_new_wp) {
                        top.tinymce.activeEditor.windowManager.close();
                    } else {
                        tinyMCEPopup.close();
                    }
                },

                insert_content : function () {
                    var content = '';
                    var sep = String.fromCharCode(13) + '<br/>';
                    var template = sep + '<p>[like-gate]' + sep + '%%DATA%%' + sep + '[/like-gate]</p><br/>';

                    var panel = document.getElementById('like_gate_panel');
                    var default_hidden_content = 'Please replace this text with the content (images, download links etc)';
                    var hidden_content = document.getElementById('like_gate_hidden_content').value;
                    hidden_content = hidden_content || default_hidden_content;

                    // who is active ?
                    if (panel.className.indexOf('current') != -1) {
                        content = template.replace('%%DATA%%', hidden_content);
                    }

                    if (this.is_new_wp) {
                        parent.tinyMCE.execCommand('mceInsertContent', false, content);
                    } else if (window.tinyMCE) {
                        window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, content);
                        //Peforms a clean up of the current editor HTML.
                        //tinyMCEPopup.editor.execCommand('mceCleanup');
                        //Repaints the editor. Sometimes the browser has graphic glitches.
                        tinyMCEPopup.editor.execCommand('mceRepaint');
                    }

                    this.close();
                 } // insert_content
            }; // app
        </script>
        <style>
            #like_gate_popup_container, #like_gate_popup_container textarea {
                font-size: 12px;
            }

            .like_gate_form .app_positive_button {
                background:#99CC66 !important;
            }

            .like_gate_form .app_negative_button {
                background:#F19C96 !important;
            }

            .like_gate_form .app_max_width {
                width: 100%;
            }

            .like_gate_form .app_text_field {
                border: 1px solid #888888;
                padding: 3px;
            }
        </style>
        <base target="_self" />
    </head>
    <body id="like_gate_popup_container" onload="app_like_gate.init();">
    <!--<body id="like_gate_popup_container" style="display: none;"
          onload="tinyMCEPopup.executeOnLoad('app_like_gate_init();');document.getElementById('like_gate_hidden_content').focus();">-->
        <form id="like_gate_form" class="like_gate_form" action="#">
            <div class="tabs">
                <ul>
                    <li id="like_gate_tab" class="current"><span><a href="javascript:mcTabs.displayTab('like_gate_tab','like_gate_panel');" onmousedown="return false;">
                        <?php _e("Like Gate", 'WWWPLIKEGATE'); ?></a></span></li>
                </ul>
            </div>

            <div class="panel_wrapper">
                <!-- panel -->
                <div id="like_gate_panel" class="panel current">
                    <p>Enter the hidden content in the box below and click <strong>Insert</strong>.
                        That's the content will be shown after a like.</p>
                        <textarea id="like_gate_hidden_content" name="like_gate_hidden_content"
                                          rows="6" cols="30" class='app_max_width app_text_field'></textarea>
                    <br />

                    <!--<p>
                        Please click <strong>Insert</strong> and the following tags will be inserted for you. <br /><br/>
                        <strong>Option #1</strong> <br/>
                        [like-gate] <br/>
                        ...			<br/>
                        [/like-gate] <br/>
                    </p>-->

                    <p>
                        Did you know that <a href="http://orbisius.com/go/pro-plugin?r=http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/&s=like-gate-pro"
                          target="_blank">Like Gate Pro</a> allows you to make the likes to go to a website/fan page directly 
                          and to include other shortcodes within the like-gate shortcodes.
                          <br/>[like-gate url="yoursite.com"][/like-gate]
                          <br/>which will send likes to a site/fan page of their choice not just a blog post.</p>
                    <br/>
                    For more info go to:
                        <a href="http://orbisius.com/go/pro-plugin?r=http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/&s=like-gate-pro"
                          target="_blank">Like Gate Pro</a>
                </div>
                <!-- end panel -->
            </div>

            <div class="mceActionPanel">
                <div style="float: left">
                    <input type="button" id="like_gate_insert" name="insert" value="<?php _e("Insert", 'like_gate'); ?>"
                           class='app_positive_button mceButton' onclick="app_like_gate.insert_content(); return false;" />
                </div>

                <div style="float: right">
                    <input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'like_gate'); ?>"
                           class='app_negative_button' onclick="app_like_gate.close();" />
                </div>
            </div>
        </form>
    </body>
</html>
<?php
?>
    <?php

    die(); // This is required to return a proper result
}