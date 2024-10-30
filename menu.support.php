<div class="webweb_wp_plugin">
    <div class="wrap">
        <div class="wrap">

            <div id="icon-options-general" class="icon32"></div>
            <h2>Like Gate</h2>

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-2">

                    <!-- main content -->
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">

                            <div class="postbox">

                                <h3><span>Help/Usage</span></h3>
                                <div class="inside">
                                    <p>
                                        Click on <img src="<?php echo $webweb_wp_like_gate_obj->get('plugin_url'); ?>/images/icon.png" alt="" /> icon
                                        when editing a post/page. The content you enter between these opening and closing tags will be revealed when the user likes the post/page. <br/><br/>
                                        [like-gate] <br/>
                                        ...			<br/>
                                        [/like-gate] <br/>
                                    </p>
                                    <p>
                                        Please click <strong>Insert</strong> and the following tags will be inserted for you. <br /><br/>
                                        <strong>Option #1</strong> <br/>
                                        [like-gate] <br/>
                                        ...			<br/>
                                        [/like-gate] <br/>
                                    </p>

                                    <p>
                                        <strong>Option #2 </strong><br/>
                                        <a href="http://orbisius.com/go/pro-plugin?r=http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/&s=like-gate-pro"
                                           target="_blank">Like Gate Pro</a> allows you to include
                                        <br/>[like-gate url="yoursite.com"][/like-gate]
                                        <br/>which will send likes to a site/fan page of their choice not just a blog post.</p>
                                    <p>
                                        <strong>Option #3</strong> <br/>
                                        <a href="http://orbisius.com/go/pro-plugin?r=http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/&s=like-gate-pro"
                                           target="_blank">Like Gate Pro</a> also allows you to include other shortcodes within the like-gate tags.
                                        <br/>
                                        [like-gate] <br/>
                                        &nbsp; &nbsp; &nbsp; [another-short-code]<br/>
                                        &nbsp; &nbsp; &nbsp; .....<br/>
                                        &nbsp; &nbsp; &nbsp; [/another-short-code]<br/>
                                        [/like-gate] <br/>
                                    </p>

                                    <p>
                                        Sometimes after people like your piece of content Facebook will show a comment box which can obstruct the view of the hidden content.
                                        To hide the comment box insert "hide_comment="1".
                                        <br/>
                                        [like-gate hide_comment="1"] <br/>
                                        [/like-gate] <br/>
                                    </p>

                                </div> <!-- .inside -->

                            </div> <!-- .postbox -->

                            <div class="postbox">

                                <h3><span>Demo</span></h3>
                                <div class="inside">
                                    <p>
                                        <iframe width="640" height="480" src="http://www.youtube.com/embed/gFatlDwc8uU?hl=en&fs=1" frameborder="0" allowfullscreen></iframe>
                                    </p>

                                    Link: <a href="http://www.youtube.com/watch?v=gFatlDwc8uU&hd=1" target="_blank"
                                             title="[opens in a new and bigger tab/window]">http://www.youtube.com/watch?v=gFatlDwc8uU&hd=1</a>
                                </div> <!-- .inside -->

                            </div> <!-- .postbox -->

                        </div> <!-- .meta-box-sortables .ui-sortable -->

                    </div> <!-- post-body-content -->

                    <!-- sidebar -->
                    <div id="postbox-container-1" class="postbox-container">

                        <div class="meta-box-sortables">

                            <div class="postbox">
                                <h3><span>Support</span></h3>
                                <div class="inside">
                                    Support is handled on our site: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/like-gate-pro/?utm_source=like-gate-pro&utm_medium=plugin-settings&utm_campaign=product" target="_blank"
                                                                       title="[new window]">http://club.orbisius.com/support/</a>
                                    <br/>Please do NOT use the WordPress forums or other places to seek support.
                                </div> <!-- .inside -->
                            </div> <!-- .postbox -->

                            <div class="postbox">
                                <h3><span>Documentation</span></h3>
                                <div class="inside">
                                    <div>
                                        <a href="<?php echo $webweb_wp_like_gate_obj->get('plugin_url'); ?>doc/Orbisius_LikeGate_Documentation.pdf" target="_blank">
                                            Plugin Documentation
                                        </a> (PDF) (Right Click and then Save Link As)
                                    </div>
                                </div> <!-- .inside -->

                            </div> <!-- .postbox -->

                        </div> <!-- .meta-box-sortables -->

                    </div> <!-- #postbox-container-1 .postbox-container -->

                </div> <!-- #post-body .metabox-holder .columns-2 -->

                <br class="clear">
            </div> <!-- #poststuff -->

        </div> <!-- .wrap -->


        <?php //echo $webweb_wp_like_gate_obj->generate_donate_box(); ?>

        <?php
        $app_link = 'http://www.youtube.com/embed/gFatlDwc8uU?hl=en&fs=1';
        $app_title = $webweb_wp_like_gate_obj->get('app_title');
        $app_descr = $webweb_wp_like_gate_obj->get('plugin_description');
        ?>
        <p>Share this video:
            <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
            <a class="addthis_button_facebook" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_twitter" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_email" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_myspace" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_google" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_digg" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_delicious" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_favorites" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
            <a class="addthis_button_compact"></a>
        </div>
        <!-- The JS code is in the footer -->
        </p>

        <script type="text/javascript">
            var addthis_config = {"data_track_clickback": true};
            var addthis_share = {
                templates: {twitter: 'Check out {{title}} visit {{lurl}} (from @orbisius)'}
            }
        </script>
        <!-- AddThis Button START part2 -->
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=lordspace"></script>
        <!-- AddThis Button END part2 -->
        </p>
    </div>
</div>
