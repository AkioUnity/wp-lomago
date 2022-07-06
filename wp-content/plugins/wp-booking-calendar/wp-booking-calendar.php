<?php
/*
Plugin Name: Lux Eleven Booking calendar
Plugin URI: http://www.cbooking.de/
Description: Lux Eleven Booking Calendar Plugin
Version: 1.0
Author: Hawk Johns
Author URI: http://www.cbooking.de/
License: Not yet specified
*/

require_once 'lux_widget.php';

if(!class_exists('BookingCalendar')){
	class BookingCalendar{
		static public function initialize()
		{
			// hooks
			register_activation_hook(__FILE__, [self::class, 'activate']);
			register_deactivation_hook(__FILE__, [self::class, 'deactivate']);

			// actions
			add_action('widgets_init', [self::class, 'load_widget']);
			add_action('admin_init', [self::class, 'Hotel_Booking_settings']);
			add_action('admin_menu', [self::class, 'hotel_menu_function']);

			//shortcodes
			add_shortcode('lux-booking', [self::class, 'show_lux_widget']);
		} // END function initialize()

		//
		// Activate the plugin
		static public function activate()
		{
		}

		//
		// Deactivate the plugin
		public static function deactivate()
		{
		}

		public static function load_widget()
		{
			register_widget('lux_widget');
		}

		public static function show_lux_widget()
		{
			ob_start();
			the_widget('lux_widget');
			$contents = ob_get_clean();
			return $contents;
		}

		function hotel_menu_function()
		{
			add_theme_page('Lux Hotel', 'Lux Hotel', 'manage_options', 'hotel-boooking-option', [self::class, 'hotel_sub_menu']);
		}

		function hotel_sub_menu()
		{
			if(!current_user_can('manage_options')){
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}
			?>
			<div class="wrap">
				<h1>Lux Eleven Hotel Booking Options Page</h1>
				<form method="post" action="options.php">
					<?php
					// display settings field on theme-option page
					settings_fields("booking-option-group");
					// display all sections for theme-options page
					do_settings_sections("hotel-option-page");
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		function hotel_section()
		{
			echo '<p>Lux Eleven Hotel Option Section</p>';
		}

		function display_adults_callback()
		{
			//php code to take input from text field for twitter URL.
			$opt = get_option('adults_count');
			$content = <<<CONTENT
				<input type="text" name="adults_count" id="adults_count" value="{$opt}" style="width:200px"/>
CONTENT;
			echo $content;
		}

		function display_children_callback()
		{
			//php code to take input from text field for twitter URL.
			$opt = get_option('children_count');
			$content = <<<CONTENT
				<input type="text" name="children_count" id="children_count" value="{$opt}" style="width:200px"/>
CONTENT;
			echo $content;
		}

		function Hotel_Booking_settings()
		{
			add_option('first_field_option', 2);// add theme option to database
			add_settings_section('hotel_section_id', 'Hotel Options Section',
				[self::class, 'hotel_section'], 'hotel-option-page');

			add_settings_field('adults_id', 'Number of Adults', [self::class, 'display_adults_callback'], 'hotel-option-page', 'hotel_section_id');
			register_setting('booking-option-group', 'adults_count');

			add_settings_field('children_id', 'Number of Children', [self::class, 'display_children_callback'], 'hotel-option-page', 'hotel_section_id');
			register_setting('booking-option-group', 'children_count');
		}
	}
}

BookingCalendar::initialize();
