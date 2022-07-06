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
			add_menu_page( 'Smiling Form Effects', 'Smiling Form Effects', 'manage_options', 'caption_hover', array($this,'render_menu_page'), 'dashicons-format-image' );
			add_submenu_page( 'caption_hover', 'Export/Import', 'Export/Import','manage_options', 'caption_hover_submenu', array($this,'render_submenu_page'));
		}

		function render_submenu_page(){
			?>
			<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Export Settings' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Export the Smiling Form Effects settings if you want to use it in to another site.You will get a json file which you can upload using import section`. This allows you to easily import the configuration into another site.' ); ?></p>
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
						<p><?php _e( 'Import the Smiling Form Effects settings by uploading a .json file. This file can be obtained by exporting the settings on another site using the form above.' ); ?></p>
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
				<h2 style="text-align: center;font-size: 30px;">Smiling Form Effects</h2>
				<p style="text-align: center;">setting for Smiling Form Effects</p>
                <span class="moreimages">
                    <button class="button-primary addcat"><?php _e( 'Add New Form', 'la-captionhover' ); ?></button>
                </span>
				<div id="faqs-container" class="accordian">
				<?php if (isset($saved_captions['posts'])) { ?>
				<?php foreach ($saved_captions['posts'] as $key => $data) { ?>
                    <h3><a href="#"><?php echo "Form" . $data['shortcode'];} ?>  </a>
                        <button class="button removecat"><span class="dashicons dashicons-dismiss"
                                                           title="Remove Category"></span></button>
                    </h3>
				   <div class="accordian content">
				<?php $cn=0;
                foreach ($data['allcapImages'] as $key => $data2) {
                    $cn++;
                    if ($cn==1){
                    ?>
                        <h4 style="background: darkred">Form Settings</h4>
                        <div class="moreimages">
                        <button class="button moreimg"><b title="Add New" class="dashicons dashicons-plus-alt"></b> <?php _e( 'Add New Field', 'la-captionhover' ); ?></button>
                        <button class="button-primary fullshortcode pull-right" id="<?php echo $data['shortcode']; ?>"><?php _e( 'Get Shortcode', 'la-captionhover' ); ?></button>
                        <table class="form-table">
                    <?php }else{ ?>
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
                        <?php } ?>
				        	<table class="form-table">
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
				   <?php } else { ?>
				    <h3><a href="#">Image Caption Hover</a><button class="button removecat"><span class="dashicons dashicons-dismiss" title="Delete Category"></span></button></h3>

				   <div class="accordian content">

				        <h3><a class=href="#">Image</a><button class="button removeitem"><span class="dashicons dashicons-dismiss" title="Delete Image"></span></button></h3>
				        <div>
				        	<table class="form-table">
				        		<tr>
				        			<td >
				        				<strong><?php _e( 'Field Name', 'la-captionhover' ); ?></strong>
				        			</td>
				        			<td >
				        				<input type="text" class="imgname widefat form-control" value="">
				        			</td>
				        		</tr>
				        	</table>
				        	<button class="addimage button"><?php _e( 'Upload Image', 'la-captionhover' ); ?></button>
				        	<span class="image">
				        	</span>
				        </div>
				   </div>
				<?php } ?>
				</div>
                <button class="btn btn-success save-meta"><?php _e( 'Save Changes', 'la-captionhover' ); ?></button></br>
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
				<?php foreach($saved_captions['posts'] as $key => $data): ?>
			<?php if ($atts['id']== $data['shortcode']): ?>
					<?php
					$cn=0;
					foreach($data['allcapImages'] as $key => $data2):
					    $cn++;
                        wp_enqueue_style( 'wdo-ihe-hover-css', plugins_url( 'css/image-hover.min.css',__FILE__ ));
                        wp_enqueue_script( 'wdo-hover-front-js', plugins_url( 'js/front.js', __FILE__ ), array('jquery'));
                        if ($cn==1){ ?>
                            <div class="ih-item circle effect1">
                                <div class='spinner'></div>
                                <div class="img"><img id="smile_img" style="height:100%;" src="<?php if ( $data2['cap_img'] != '' ) {
										echo $data2['cap_img'];
									} else {
										echo "http://www.gemologyproject.com/wiki/images/5/5f/Placeholder.jpg";
									}
									?>" alt="img">
                                </div>
                            </div>
                            <script type="text/javascript">
                                let smile_image=document.querySelector("#smile_img");
                                let image_url;
                            </script>
                            <?php }else{ ?>
<script type="text/javascript">
jQuery(document).ready(function() {
   document.querySelector('[name="<?=$data2['img_name']?>"]').onfocus=function (){
                image_url='<?=$data2['cap_img']?>';
                smile_image.src=image_url;
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