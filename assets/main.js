/*
 * Like Gate
 */
jQuery(document).ready(function($) {
   if ( (typeof like_gate_cfg == 'undefined') || (typeof like_gate == 'undefined') ) {
       return;
   }

   var post_id = like_gate_cfg.post_id;
   var like_status = jQuery.cookie('like_gate_lp_' + post_id);

   // let's reveal if the user has already liked the page...
   // unless the admin wants not to be revealed
   if (like_gate.skip_reveal == 0 && like_status > 0) {
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
console && console.log('done:' + window.wp_like_gate_setup_callbacks_init_done);
}

// credits: phpjs, Jonas Raoni Soares Silva (http://www.jsfromhell.com), Ates Goral (http://magnetiq.com), Onno Marsman, Rafał Kukawski (http://blog.kukawski.pl)
function like_gate_decrypt(str, pwd) {
    var buff = '';
    str = str || '';

    buff = (str + '').replace(/[a-z]/gi, function (s) {
       return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
    });

    // src http://phpjs.org/functions/urldecode/
    buff = decodeURIComponent((buff + '').replace(/%(?![\da-f]{2})/gi, function () {
        // PHP tolerates poorly formed escape sequences
        return '%25';
    }).replace(/\+/g, '%20'));

    return buff;
}

// handles like and unlike events
function like_gate_handle_event(pars) {
  pars = pars || {};

  var post_id = like_gate_cfg.post_id;
  var cookie_domain = like_gate_cfg.cookie_domain;

  if (pars.event == 'unlike') {
     jQuery('.like-gate-result').hide('slow').html('');
     jQuery.cookie('like_gate_lp_' + post_id, null, { path: '/' });

     if (like_gate.hide_comment) {
        jQuery('.like_gate_like_container iframe').show();
     }

     if (like_gate.hide_call_to_action) {
        jQuery('.like-gate-call-to-action').show();
     }

     return true;
  } else {
     // somebody has liked the content let's hide the comment box
     if (like_gate.hide_comment) {
        jQuery('.like_gate_like_container iframe').hide();
     }

     if (like_gate.hide_call_to_action) {
        jQuery('.like-gate-call-to-action').hide();
     }
  }

  // , domain: '$cookie_domain' // chrome doesn't like the domain !?! doesn't delete the cookie.
  jQuery.cookie('like_gate_lp_' + post_id, 1, { expires: 730, path: '/' });

  var decrypted_hidden = like_gate_decrypt(jQuery('.like-gate').html());
  jQuery('.like-gate-result').html(decrypted_hidden).show('slow');
}
