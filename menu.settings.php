<?php
$settings_key = $webweb_wp_like_gate_obj->get('plugin_settings_key');
$opts = $webweb_wp_like_gate_obj->get_options();
?>
<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h2>Like Gate</h2>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h3><span>Settings</span></h3>

                        <?php if (!empty($_REQUEST['settings-updated'])) : ?>
                        <div class="updated settings-error" id="setting-error-settings_updated">
                            <p><strong>Settings saved.</strong></p>
                        </div>
                        <?php endif; ?>

						<?php 
							if ( in_array( 'like-gate-pro/like-gate-pro.php', 
									apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
								echo "<div class='updated'><p>This is the Settings of Like Gate (lite). 
								It seems Like Gate Pro is installed and it will take over automatically.
								<br/>Please make the changes in Like Gate Pro (left).
								</p></div>";
							}
						?>
						
						<div class="inside">
							<form method="post" action="options.php">

                            <?php settings_fields($webweb_wp_like_gate_obj->get('plugin_dir_name')); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">Status (Required)</th>
                                    <td>
                                        <label for="radio1">
                                            <input type="radio" id="radio1" name="<?php echo $settings_key; ?>[status]"
                                                value="1" <?php echo empty($opts['status']) ? '' : 'checked="checked"'; ?> /> Enabled
                                        </label>
                                        <br/>
                                        <label for="radio2">
                                            <input type="radio" name="<?php echo $settings_key; ?>[status]"  id="radio2"
                                                value="0" <?php echo !empty($opts['status']) ? '' : 'checked="checked"'; ?> /> Disabled
                                        </label>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Facebook App ID (Required)</th>
                                    <td>
                                        <label for="app_id">
                                            <input type="text" id="app_id" name="<?php echo $settings_key; ?>[app_id]"
                                                value="<?php echo esc_attr($opts['app_id']);?>" />
                                            <span>
                                                &nbsp; <a href="javascript:void(0);" onclick="jQuery('.facebook_why_create_fb_app').toggle('slow');">Why? (show/hide)</a>
                                                &nbsp;
                                                | &nbsp; <a href="javascript:void(0);" onclick="jQuery('.facebook_create_fb_app').toggle('slow');">How? (show/hide) </a>

                                                <div class="facebook_why_create_fb_app app_hide">
                                                    Like Gate and Like Gate Pro plugins both, in order to work, they require a Facebook App ID.
                                                    The plugins used one of the app IDs internally that's why you didn't have to enter one.
                                                    With the latest changes with Facebook a
                                                    facebook app can run on only one or two domains max, therefore using our Facebook App no longer works because we could
                                                    set it up to use our domains. Now, we have to ask you to create a Facebook app for each of the sites that you intend to
                                                    use our Like Gate plugins. If things change we'll gladly remove the requirement of App Id.
                                                </div>

                                                <div class="facebook_create_fb_app app_hide">
                                                    To create an app go to:
                                                    <a href='https://developers.facebook.com/apps/' target="_blank">https://developers.facebook.com/apps/</a>
                                                    <br/>
                                                    <iframe width="560" height="315" src="http://www.youtube.com/embed/Bfumb7jXuwE" frameborder="0" allowfullscreen></iframe>

                                                    <br/>Video Link: <a href="http://www.youtube.com/watch?v=Bfumb7jXuwE&feature=youtu.be"
                                                                        target="_blank">http://www.youtube.com/watch?v=Bfumb7jXuwE</a>
                                                </div>
                                            </span>
                                        </label>
                                    </td>
                                </tr>
                            </table>

                            <p class="submit">
                                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                            </p>
                        </form>
						</div> <!-- .inside -->

					</div> <!-- .postbox -->

                    <div class="postbox">
						<h3><span>Premium Version</span></h3>
						<div class="inside">
                            The premium version has the following feature.

                            <ul>
                                <li>&nbsp; &nbsp; - Change the language of the Like button</li>
                                <li>&nbsp; &nbsp; - Add Call to Action before each Like button generated by this plugin</li>
                                <li>&nbsp; &nbsp; - Choose where the the like should go to (e.g. to blog post, your site, or your Facebook fan page) by entering the url parameter</li>
                                <li>&nbsp; &nbsp; - Hide Facebook comment box after like (sometimes, after people like your piece of content Facebook will show a comment box
                                    which can obstruct the view of the hidden content.</li>
                                <li>&nbsp; &nbsp; ... and more</li>
                            </ul>
                            
                            <a href="http://club.orbisius.com/products/wordpress-plugins/like-gate-pro/?utm_source=like-gate&utm_medium=plugin-settings&utm_campaign=product" target="_blank" title="[new window]" class="button-primary">Buy Like Gate Pro</a>
						</div> <!-- .inside -->
                    </div> <!-- .postbox -->

                    <div class="postbox">
						<h3><span>Join the Club Orbisius</span></h3>
						<div class="inside">
                            Get Like Gate Pro and many more premium plugins at a low monthly price
                            <br/><a href="http://club.orbisius.com/plans/?utm_source=like-gate&utm_medium=plugin-settings&utm_campaign=product"
                                target="_blank" title="[new window]" class="button-primary">See Pricing</a>
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
                            <br/><a href="http://orbisius.com/page/free-quote/?utm_source=like-gate&utm_medium=plugin-settings&utm_campaign=product"
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
									<br/>Email: <input type="text" class="" id="like_gate_email" name='data[sender_email]'
                                                       placeholder="Email" required="required" value="<?php echo esc_attr($email); ?>" />
									<input type="submit" class="button-primary" value="<?php _e('Send Feedback') ?>"
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

		<br class="clear">
	</div> <!-- #poststuff -->

</div> <!-- .wrap -->
