<?php
/*
Plugin Name: Lomago Image Changer Plugin
Description: Lomago Contact Form Image Changer Plugin
*/
/* Start Adding Functions Below this Line */
// Register and load the widget
function wph_load_widget() {
    register_widget( 'image_changer' );
}
add_action( 'widgets_init', 'wph_load_widget' );

// Creating the widget
class image_changer extends WP_Widget {
    function __construct() {
        parent::__construct(
// Base ID of your widget
            'image_changer',
// Widget name will appear in UI
            __('Lomago Form Widget', 'image_changer_domain'),

// Widget description
            array( 'description' => __( 'Lomago Form Widget', 'image_changer_domain' ), )
        );
    }
// Creating widget front-end

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output
        ?>

        <?php
        $image_url= get_option('image_url_0');
        ?>
        <script type="text/javascript">
            let image_url='<?=$image_url?>';
            console.log(image_url);
            let changer_image=document.querySelector("img[src='"+image_url+"']");
            document.querySelector('[name="<?=get_option('field_name_1')?>"]').onfocus=function (){
                changer_image.src="<?=get_option('image_url_1')?>";
                changer_image.srcset=changer_image.src
            }
            document.querySelector('[name="<?=get_option('field_name_2')?>"]').onfocus=function (){
                changer_image.src="<?=get_option('image_url_2')?>";
                changer_image.srcset=changer_image.src
            }
            document.querySelector('[name="<?=get_option('field_name_3')?>"]').onfocus=function (){
                changer_image.src="<?=get_option('image_url_3')?>";
                changer_image.srcset=changer_image.src
            }
            document.querySelector('input[type="submit"]').onfocus=function (){
                changer_image.src="<?=get_option('image_url_3')?>";
                changer_image.srcset=changer_image.src
            }
        </script>

        <?php
        echo $args['after_widget'];
    }

// Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'image_changer_domain' );
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

// Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class image_changer ends here

function show_image_changer() {
    ob_start();
    the_widget( 'image_changer' );
    $contents = ob_get_clean();
    return $contents;
}

add_shortcode('image_changer', 'show_image_changer');
//add new menu for theme-options page with page callback theme-options-page.

add_action('admin_menu', 'sub_menu_function');
function sub_menu_function()
{
    add_theme_page('Con', 'Image changer', 'manage_options', 'image-changer-option', 'sub_menu_panel');
}

function sub_menu_panel()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h1>Image Changer Options Page</h1>
        <form method="post" action="options.php">
            <?php
            // display settings field on theme-option page
            settings_fields("theme-options-grp");
            // display all sections for theme-options page
            do_settings_sections("option-page");
            submit_button();
            ?>
        </form>
    </div>
<?php
}

function theme_section_description()
{
    echo '';
}

function display_image_0_callback(){
    ?>
    <input type="text" name="image_url_0" id="image_url_0" value="<?php echo get_option('image_url_0'); ?>" style="width:400px" />
    <?php
}

function display_image_1_callback(){
	?>
    <input type="text" name="image_url_1" id="image_url_1" value="<?php echo get_option('image_url_1'); ?>" style="width:400px" />
	<?php
}

function display_image_2_callback(){
	?>
    <input type="text" name="image_url_2" id="image_url_2" value="<?php echo get_option('image_url_2'); ?>" style="width:400px" />
	<?php
}

function display_image_3_callback(){
	?>
    <input type="text" name="image_url_3" id="image_url_3" value="<?php echo get_option('image_url_3'); ?>" style="width:400px" />
	<?php
}

function display_image_4_callback(){
	?>
    <input type="text" name="image_url_4" id="image_url_4" value="<?php echo get_option('image_url_4'); ?>" style="width:400px" />
	<?php
}

function display_field_name1_callback(){
    ?>
    <input type="text" name="field_name_1" id="field_name_1" value="<?php echo get_option('field_name_1'); ?>"/>
    <?php
}

function display_field_name2_callback(){
    ?>
    <input type="text" name="field_name_2" id="field_name_2" value="<?php echo get_option('field_name_2'); ?>"/>
    <?php
}

function display_field_name3_callback(){
    ?>
    <input type="text" name="field_name_3" id="field_name_3" value="<?php echo get_option('field_name_3'); ?>"/>
    <?php
}

function image_changer_settings()
{
    add_option('first_field_option', 1);// add theme option to database
    add_settings_section('first_section_id', 'Image Changer Options Section',
        'theme_section_description', 'option-page');

    add_settings_field('image_url_0_id', 'the default image url', 'display_image_0_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'image_url_0');

    add_settings_field('field1_id', 'First Field name', 'display_field_name1_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'field_name_1');
    
    add_settings_field('image_url_1_id', 'the image url for 1st field', 'display_image_1_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'image_url_1');

    add_settings_field('field2_id', '2nd field name', 'display_field_name2_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'field_name_2');

    add_settings_field('image_url_2_id', 'the image url for 2nd field', 'display_image_2_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'image_url_2');

    add_settings_field('field3_id', '3rd field name', 'display_field_name3_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'field_name_3');

    add_settings_field('image_url_3_id', 'the image url for 3rd field', 'display_image_3_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'image_url_3');

    add_settings_field('image_url_4_id', 'the image url for submit button', 'display_image_4_callback', 'option-page', 'first_section_id');
    register_setting( 'theme-options-grp', 'image_url_4');

}

add_action('admin_init', 'image_changer_settings');
/* Stop Adding Functions Below this Line */
?>