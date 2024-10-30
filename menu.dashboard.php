<?php
$opts = $webweb_wp_like_gate_obj->get_options();
?>

<div class="wrap webweb_wp_plugin">
    <div id="icon-options-general" class="icon32"></div>
	<h2>Like Gate: Dashboard</h2>

    <div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h3><span>Plugin Status</span></h3>
						<div class="inside">
							<?php echo empty($opts['status']) ? $webweb_wp_like_gate_obj->msg('Disabled') : $webweb_wp_like_gate_obj->msg('Enabled', 1);?>
							
							<?php
								if ( in_array( 'like-gate-pro/like-gate-pro.php', 
										apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
									echo "You're running Like Gate (lite) and Like Gate Pro at the same time. The Pro version will take over automatically.";
								}
							?>
                        </div> <!-- .inside -->

                    </div> <!-- .postbox -->

                    <div class="postbox">
						<h3><span>Support</span></h3>
						<div class="inside">
                            Support is handled on our site: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/like-gate/?utm_source=like-gate-pro&utm_medium=plugin-dashboard&utm_campaign=product" target="_blank"
                                                                           title="[new window]">http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/like-gate/</a>
						</div> <!-- .inside -->
                    </div> <!-- .postbox -->

                    <div class="postbox">
						<h3><span>Share</span></h3>
						<div class="inside">
							<div>
                                <?php
                                $app_link = $webweb_wp_like_gate_obj->get('plugin_home_page');
                                $app_title = $webweb_wp_like_gate_obj->get('app_title');
                                $app_descr = $webweb_wp_like_gate_obj->get('plugin_description');
                                ?>
                                <span>Tell your your friends about it!</span>
                                <p>
                                    <!-- AddThis Button BEGIN -->
                                    <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                    <a class="addthis_button_facebook" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_twitter" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_email" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_myspace" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_google" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_digg" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_delicious" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_favorites" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                                    <a class="addthis_button_compact"></a>
                                    </div>
                                    <!-- The JS code is in the footer -->
                                </p>

                                <script type="text/javascript">
                                var addthis_config = {"data_track_clickback":true};
                                var addthis_share = {
                                  templates: { twitter: 'Check out {{title}} Visit {{lurl}} (from @orbisius)' }
                                }
                                </script>
                                <!-- AddThis Button START part2 -->
                                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=lordspace"></script>
                                <!-- AddThis Button END part2 -->
                            </div>

                            <span>Facebook Share</span>
                            <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=163116837104802&amp;xfbml=1"></script><fb:like href="http://webweb.ca/site/products/like-gate/" send="true" width="450" show_faces="true" font="arial"></fb:like>

                            <?php if (0) : ?>
                            <div>Please use forum for support questions.</div>
                            <div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:comments href="http://webweb.ca/site/products/like-gate/" num_posts="5" width="500"></fb:comments>
                            <?php endif; ?>
						</div> <!-- .inside -->
					</div> <!-- .postbox -->
                    
                    <div class="postbox">
						<h3><span>Donate to the project</span></h3>
						<div class="inside">
                            <?php echo $webweb_wp_like_gate_obj->generate_donate_box(); ?>
						</div> <!-- .inside -->
                    </div> <!-- .postbox -->
                    
				</div> <!-- .meta-box-sortables .ui-sortable -->

			</div> <!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">
						<h3><span>Hire Us</span></h3>
						<div class="inside">
                            Hire us to create a plugin/web/mobile app for your business.
                            <br/><a href="http://orbisius.com/page/free-quote/?utm_source=like-gate-pro&utm_medium=plugin-settings&utm_campaign=product"
                               title="If you want a custom web/mobile app/plugin developed contact us. This opens in a new window/tab"
                                class="button-primary" target="_blank">Get a Free Quote</a>
						</div> <!-- .inside -->
                    </div> <!-- .postbox -->

                    <div class="postbox">
						<h3><span>Newsletter</span></h3>
						<div class="inside">
							<?php echo $webweb_wp_like_gate_obj->generate_newsletter_box(); ?>
						</div> <!-- .inside -->
                    </div> <!-- .postbox -->

                    <div class="postbox">
						<h3>
                                <!-- Twitter: code -->
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="http://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                                <!-- /Twitter: code -->

                                <!-- Twitter: Orbisius_Follow:js -->
                                    <a href="https://twitter.com/orbisius" class="twitter-follow-button"
                                       data-align="right" data-show-count="false">Follow @orbisius</a>
                                <!-- /Twitter: Orbisius_Follow:js -->

                                &nbsp;

                                <!-- Twitter: Tweet:js -->
                                <a href="https://twitter.com/share" class="twitter-share-button"
                                   data-lang="en" data-text="Checkout Like Gate #WordPress #plugin.Increase your site & fb page's likes"
                                   data-count="none" data-via="orbisius" data-related="orbisius"
                                   data-url="http://wordpress.org/plugins/like-gate/">Tweet</a>
                                <!-- /Twitter: Tweet:js -->

                                <br/>
                                <span>Support: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/like-gate/?utm_source=like-gate&utm_medium=plugin-settings&utm_campaign=product"
                               target="_blank" title="[new window]">Forums</a>
                                 |
                                <a href="http://docs.google.com/viewer?url=https%3A%2F%2Fdl.dropboxusercontent.com%2Fs%2Fwz83vm9841lz3o9%2FOrbisius_LikeGate_Documentation.pdf" target="_blank">Documentation</a>
                            </span>
                        </h3>
                    </div> <!-- .postbox -->
                    
					<div class="postbox"> <!-- quick-contact -->
						<?php
						$current_user = wp_get_current_user();
						$email = empty($current_user->user_email) ? '' : $current_user->user_email;
						$quick_form_action = is_ssl()
								? 'https://ssl.orbisius.com/apps/quick-contact/'
								: 'http://apps.orbisius.com/quick-contact/';

						if (!empty($_SERVER['DEV_ENV'])) {
							$quick_form_action = 'http://localhost/projects/quick-contact/';
						}
						?>
						<script>
							var like_gate_quick_contact = {
								validate_form : function () {
									try {
										var msg = jQuery('#like_gate_msg').val().trim();
										var email = jQuery('#like_gate_email').val().trim();
										
										email = email.replace(/\s+/, '');
										email = email.replace(/\.+/, '.');
										email = email.replace(/\@+/, '@');

										if ( msg == '' ) {
											alert('Enter your message.');
											jQuery('#like_gate_msg').focus().val(msg).css('border', '1px solid red');
											return false;
										} else {
											// all is good clear borders
											jQuery('#like_gate_msg').css('border', '');
										}

										if ( email == '' || email.indexOf('@') <= 2 || email.indexOf('.') == -1) {
											alert('Enter your email and make sure it is valid.');
											jQuery('#like_gate_email').focus().val(email).css('border', '1px solid red');
											return false;
										} else {
											// all is good clear borders
											jQuery('#like_gate_email').css('border', '');
										}

										return true;
									} catch(e) {};
								}
							};
						</script>
						<h3><span>Quick Question or Suggestion</span></h3>
						<div class="inside">
							<div>
								<form method="post" action="<?php echo $quick_form_action; ?>" target="_blank">
									<?php
										global $wp_version;
										$plugin_data = get_plugin_data(dirname(__FILE__) . '/like-gate.php');

										$hidden_data = array(
											'site_url' => site_url(),
											'wp_ver' => $wp_version,
											'first_name' => $current_user->first_name,
											'last_name' => $current_user->last_name,
											'product_name' => $plugin_data['Name'],
											'product_ver' => $plugin_data['Version'],
											'woocommerce_ver' => defined('WOOCOMMERCE_VERSION') ? WOOCOMMERCE_VERSION : 'n/a',
										);
										$hid_data = http_build_query($hidden_data);
										echo "<input type='hidden' name='data[sys_info]' value='$hid_data' />\n";
									?>
									<textarea class="widefat" id='like_gate_msg' name='data[msg]' required="required"></textarea>
									<br/>Your Email: <input type="text" class=""
										   id="like_gate_email" name='data[sender_email]' placeholder="Email" required="required"
										   value="<?php echo esc_attr($email); ?>"
										   />
									<br/><input type="submit" class="button-primary" value="<?php _e('Send Feedback') ?>"
												onclick="return like_gate_quick_contact.validate_form();" />
									<br/>
									What data will be sent
									<a href='javascript:void(0);'
										onclick='jQuery(".like_gate_data_to_be_sent").toggle();'>(show/hide)</a>
									<div class="hide app_hide like_gate_data_to_be_sent">
										<textarea class="widefat" rows="4" readonly="readonly" disabled="disabled"><?php
										foreach ($hidden_data as $key => $val) {
											if (is_array($val)) {
												$val = var_export($val, 1);
											}

											echo "$key: $val\n";
										}
										?></textarea>
									</div>
								</form>
							</div>
						</div> <!-- .inside -->

					</div> <!-- .postbox --> <!-- /quick-contact -->
				</div> <!-- .meta-box-sortables -->

			</div> <!-- #postbox-container-1 .postbox-container -->

		</div> <!-- #post-body .metabox-holder .columns-2 -->

		<?php
            //echo $webweb_wp_like_gate_obj->generate_newsletter_box();
        ?>
    </div>
</div>
