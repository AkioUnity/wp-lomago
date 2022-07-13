<?php 
	/**
	* Plugin Main Class
	*/
	class LA_Caption_Hover {
		
		function __construct()  
		{
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_options_page_scripts' ) );
			add_action( "admin_menu", array($this,'caption_hover_admin_options'));
			add_action('wp_ajax_la_save_caption_options', array($this, 'save_caption_options'));
			add_shortcode( 'smiling-form', array($this,'render_caption_hovers') );
			add_action( 'admin_init', array($this, 'wdo_process_export_settings') );
			add_action( 'admin_init', array($this, 'wdo_process_import_settings') );
		}

		// Admin Options Page 
		function admin_options_page_scripts($slug){
			if( $slug=='toplevel_page_caption_hover' || $slug=='image-hover-effects_page_caption_hover_pro_settings' ){
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wdo-admin-js', plugins_url( 'admin/admin.js', __FILE__ ), array('jquery', 'jquery-ui-accordion','wp-color-picker'));
				wp_enqueue_style( 'wdo-style-css', plugins_url( 'admin/style.css', __FILE__ ));
				wp_enqueue_style( 'wdo-ui-css', plugins_url( 'admin/jquery-ui.min.css', __FILE__ ));
				wp_localize_script( 'wdo-admin-js', 'laAjax', array( 'url' => admin_url( 'admin-ajax.php')));
			}
		}

		function caption_hover_admin_options(){
			add_menu_page( 'Smiling Effects For CF7', 'Smiling Effects For CF7', 'manage_options', 'caption_hover', array($this,'render_menu_page'), 'dashicons-format-image' );
			add_submenu_page( 'caption_hover', 'Export/Import', 'Export/Import','manage_options', 'caption_hover_submenu', array($this,'render_submenu_page'));
		}

		function render_submenu_page(){
			?>
			<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Export Settings' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Export the Smiling Effects For CF7 settings if you want to use it in to another site.You will get a json file which you can upload using import section`. This allows you to easily import the configuration into another site.' ); ?></p>
						<form method="post">
							<p><input type="hidden" name="wdo_action" value="export_settings" /></p>
							<p>
								<?php wp_nonce_field( 'wdo_export_nonce', 'wdo_export_nonce' ); ?>
								<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
							</p>
						</form>
					</div><!-- .inside -->
				</div><!-- .postbox -->

				<div class="postbox">
					<h3><span><?php _e( 'Import Settings' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Import the Smiling Effects For CF7 settings by uploading a .json file. This file can be obtained by exporting the settings on another site using the form above.' ); ?></p>
						<form method="post" enctype="multipart/form-data">
							<p>
								<input type="file" name="import_file"/>
							</p>
							<p>
								<input type="hidden" name="wdo_action" value="import_settings" />
								<?php wp_nonce_field( 'wdo_import_nonce', 'wdo_import_nonce' ); ?>
								<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
							</p>
						</form>
					</div><!-- .inside -->
				</div><!-- .postbox -->
			</div><!-- .metabox-holder -->
		<?php }

		function save_caption_options(){
			if (isset($_REQUEST)) {
				update_option( 'la_caption_hover', $_REQUEST);
			}
		}

		function render_menu_page(){
			$saved_captions = get_option( 'la_caption_hover' );
			?>
			<div class="wrapper" id="caption">
				<div class="se-saved-con"></div>
				<div class="overlay-message">
				    <p>Changes Saved..!</p>
				</div>
				<div style="margin: 20px auto;font-size: larger;">
                    <a class="button button-primary" href="https://lomago.net/smiling-plugin-4-c7" target="_blank">Documentation</a>
                    <a class="button button-primary" href="<?php echo plugin_dir_url( __FILE__ ); ?>documentation/" target="_blank">Effect List</a>
                </div>
                <h2 style="text-align: center;font-size: 25px;">Smiling Effects For CF7</h2>
                <span class="moreimages">
                    <button class="button-primary addcat"><?php _e( 'Add New Form', 'la-captionhover' ); ?></button>
                    <button class="btn btn-success save-meta pull-right"><?php _e( 'Save Changes', 'la-captionhover' ); ?></button>
                </span>
				<div id="faqs-container" class="accordian">
				<?php if (!isset($saved_captions['posts'])) {
                    $saved_captions['posts']=array(array('shortcode'=>1,'allcapImages'=>array(array('img_name'=>'default'),array('img_name'=>'Field1'))));
				    }?>
				<?php
				foreach ($saved_captions['posts'] as $key0 => $data) {
				    ?>
                    <h3><a href="#"><?php echo "Form" . $data['shortcode']; ?>  </a>
                        <button class="button removecat"><span class="dashicons dashicons-dismiss"
                                                           title="Remove Category"></span></button>
                    </h3>
				   <div class="accordian content">
                    <?php foreach ($data['allcapImages'] as $key => $data2) {
                        if ($key==0){  ?>
                            <h4 style="background: #444444">Form Settings</h4>
                            <div class="moreimages">
                                <button class="button moreimg"><b title="Add New" class="dashicons dashicons-plus-alt"></b> <?php _e( 'Add New Field', 'la-captionhover' ); ?></button>
                                <button class="button-primary fullshortcode pull-right" id="<?php echo $data['shortcode']; ?>"><?php _e( 'Get Shortcode', 'la-captionhover' ); ?></button>
                                <table class="form-table">
								<tr>
									<td>
										<strong><?php _e( 'Animation Direction', 'la-captionhover' ); ?></strong>
									</td>
									<td>
										<select class="directionopt form-control widefat">
										  <option <?php if ( $data['cap_direction'] == 'left_to_right' ) echo 'selected="selected"'; ?> value="left_to_right"><?php _e( 'Left To Right', 'la-captionhover' ); ?></option>
										  <option <?php if ( $data['cap_direction'] == 'right_to_left' ) echo 'selected="selected"'; ?> value="right_to_left"><?php _e( 'Right To Left', 'la-captionhover' ); ?></option>
										  <option <?php if ( $data['cap_direction'] == 'top_to_bottom' ) echo 'selected="selected"'; ?> value="top_to_bottom"><?php _e( 'Top To Bottom', 'la-captionhover' ); ?></option>
										  <option <?php if ( $data['cap_direction'] == 'bottom_to_top' ) echo 'selected="selected"'; ?> value="bottom_to_top"><?php _e( 'Bottom To Top', 'la-captionhover' ); ?></option>
										</select>
									</td>
									<td>
										<p class="description"><?php _e( 'Select direction in which animation occur.', 'la-captionhover' ); ?></p>
									</td>
								</tr>
								<tr>
									<td>
										<strong><?php _e( 'Select Change Effect', 'la-captionhover' ); ?></strong>
									</td>
									<td>
										<select class="effectopt form-control widefat">
										<option <?php if ( $data['cap_effect'] == 'simple' ) echo 'selected="selected"'; ?> value="simple">Simple</option>
										  <option <?php if ( $data['cap_effect'] == 'effect1' ) echo 'selected="selected"'; ?> value="effect1">Effect1</option>
										  <option <?php if ( $data['cap_effect'] == 'effect2' ) echo 'selected="selected"'; ?> value="effect2">Effect2</option>
										  <option <?php if ( $data['cap_effect'] == 'effect3' ) echo 'selected="selected"'; ?> value="effect3">Effect3</option>
										  <option <?php if ( $data['cap_effect'] == 'effect4' ) echo 'selected="selected"'; ?> value="effect4">Effect4</option>
										  <option <?php if ( $data['cap_effect'] == 'effect5' ) echo 'selected="selected"'; ?> value="effect5">Effect5</option>
										  <option <?php if ( $data['cap_effect'] == 'effect6' ) echo 'selected="selected"'; ?> value="effect6">Effect6</option>
										  <option <?php if ( $data['cap_effect'] == 'effect7' ) echo 'selected="selected"'; ?> value="effect7">Effect7</option>
										  <option <?php if ( $data['cap_effect'] == 'effect8' ) echo 'selected="selected"'; ?> value="effect8">Effect8</option>
										</select>
									</td>
									<td>
										<p class="description"><?php _e( 'Select animation.', 'la-captionhover' ); ?></p>
									</td>
								</tr>
								<tr>
									<td>
										<strong>Effect Speed</strong>
									</td>
									<td>
										<input type="text" class="speed form-control" value="<?php echo ( isset($data['speed']) && $data['speed'] != '' ) ? stripcslashes($data['speed']) : 100; ?>">
									</td>
									<td>
										<p class="description">ms</p>
									</td>
								</tr>
                    <?php }
                        else{ ?>
                            <h3><a href="#"><?php if ( $data2['img_name'] !== '' ) {
                                    echo stripcslashes( $data2['img_name'] );
                                } else {
                                    echo "Field";
                                }?>
                                </a>
                                <button class="button removeitem"><span class="dashicons dashicons-dismiss"
                                                                    title="Remove Image"></span></button>
                            </h3>
                            <div>
                                <table class="form-table">
                        <?php } ?>
				        		<tr>
				        			<td >
				        				<strong><?php _e( 'Field Name', 'la-captionhover' ); ?></strong>
				        			</td>
				        			<td >
				        				<input type="text" class="imgname widefat form-control" value="<?php echo ( isset($data2['img_name']) && $data2['img_name'] != '' ) ? stripcslashes($data2['img_name']) : ''; ?>">
				        			</td>
                                    <td>
                                        <button class="addimage button"><?php _e( 'Upload Image', 'la-captionhover' ); ?></button>
                                        <span class="image">
				        		<?php if (isset($data2['cap_img']) &&  $data2['cap_img']!='') {
							        echo '<span>
				        						<img src="'.$data2['cap_img'].'">
					        					<span class="dashicons dashicons-dismiss">
					        					</span>
				        					</span>'; } ?>

				        	            </span>
                                    </td>
				        		</tr>
				        	</table>
				            </div>
				        <?php } ?>
				   </div>
				   <?php } ?>
				</div>

                <span id="la-loader" class="pull-right"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/ajax-loader.gif"></span>
                <span id="la-saved"><strong><?php _e( 'Changes Saved!', 'la-captionhover' ); ?></strong></span>
			</div>
			<p class="clearfix"></p>
			
		<?php
		}

function render_caption_hovers($atts){
	$saved_captions = get_option( 'la_caption_hover' );

	if (isset($saved_captions['posts'])) {
		ob_start(); ?>
		<div class="image-hover-page-container animatedParent">
				<?php foreach($saved_captions['posts'] as $key0 => $data): ?>
			<?php if ($atts['id']== $data['shortcode']): ?>
                <?php foreach($data['allcapImages'] as $key => $data2):
                        wp_enqueue_style( 'wdo-ihe-hover-css', plugins_url( 'css/image-hover.min.css',__FILE__ ));
                        wp_enqueue_script( 'wdo-hover-front-js', plugins_url( 'js/front.js', __FILE__ ), array('jquery'));
                        if ($key==0){ ?>
                            <div class="ih-item square <?php echo $data['cap_effect']; ?> <?php if($data['cap_effect']=='effect8') {
			                    	echo "scale_up";
			                    }elseif($data['cap_effect']=='effect1' && $data['cap_direction']=='left_to_right'){
			                    		echo "left_and_right";
			                    }else{

			                    	echo $data['cap_direction'];
			                    }
			                     ?>">
			                     <div class="taphover" >
                                    <div class="img"><img id="smile_img" style="height:100%;" src="<?php if ( $data2['cap_img'] != '' ) {
                                            echo $data2['cap_img'];
                                        } else {
                                            echo "http://www.gemologyproject.com/wiki/images/5/5f/Placeholder.jpg";
                                        }
                                        ?>" alt="img">
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                let smile_image=document.querySelector("#smile_img");
                                let effect=smile_image.parentNode.parentNode;
                            </script>
                            <?php }else{ ?>
            <script type="text/javascript">
            jQuery(document).ready(function() {
               document.querySelector('[name="<?=$data2['img_name']?>"]').onfocus=function (){
                            effect.className="a_hover";
                            setTimeout(function() {
                              effect.className="taphover";
                              smile_image.src='<?=$data2['cap_img']?>';
                            }, <?=$data['speed']?>);
                        }
                        });
            </script>
					        <?php }
                    endforeach; ?>
					<?php endif ?>
				<?php endforeach; ?>
		</div>
		<?php				
	}
	return ob_get_clean();
}

		/**
		 * Process a settings export that generates a .json file of the shop settings
		 */
		function wdo_process_export_settings() {
			if( empty( $_POST['wdo_action'] ) || 'export_settings' != $_POST['wdo_action'] )
				return;
			if( ! wp_verify_nonce( $_POST['wdo_export_nonce'], 'wdo_export_nonce' ) )
				return;
			if( ! current_user_can( 'manage_options' ) )
				return;
			$settings = get_option( 'la_caption_hover' );
			ignore_user_abort( true );
			nocache_headers();
			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=image-hover-effect-export-' . date( 'm-d-Y' ) . '.json' );
			header( "Expires: 0" );
			echo json_encode( $settings );
			exit;
		}

		/**
		 * Process a settings import from a json file
		 */
		function wdo_process_import_settings() {
			if( empty( $_POST['wdo_action'] ) || 'import_settings' != $_POST['wdo_action'] )
				return;
			if( ! wp_verify_nonce( $_POST['wdo_import_nonce'], 'wdo_import_nonce' ) )
				return;
			if( ! current_user_can( 'manage_options' ) )
				return;
			$extension = end( explode( '.', $_FILES['import_file']['name'] ) );
			if( $extension != 'json' ) {
				wp_die( __( 'Please upload a valid .json file' ) );
			}
			$import_file = $_FILES['import_file']['tmp_name'];
			if( empty( $import_file ) ) {
				wp_die( __( 'Please upload a file to import' ) );
			}
			// Retrieve the settings from the file and convert the json object to an array.
			$settings = (array) json_decode( file_get_contents( $import_file ),true );
			update_option( 'la_caption_hover', $settings );
			wp_safe_redirect( admin_url( 'options-general.php?page=caption_hover' ) ); exit;
		}
	} 

 ?>