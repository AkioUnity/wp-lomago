<?php
/**
 * Created by PhpStorm.
 * User: Home
 * Date: 1/16/2019
 * Time: 14:28
 */

class lux_widget extends WP_Widget{
	function __construct()
	{
		parent::__construct(
			// Base ID of your widget
			'lux_widget',

			// Widget name will appear in UI
			__('Lux Booking Widget', 'lux_widget_domain'),

			// Widget description
			array('description' => __('Lux Hotel Booking Widget', 'lux_widget_domain'),)
		);
	}

	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if(!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		$plugin_url = WP_PLUGIN_URL . "/wp-booking-calendar";
		$jquery_url = get_option('siteurl') . "/wp-includes/js/jquery";

		$widget_content = <<<WIDGET_CONTENT
			
			<link rel='stylesheet' href='{$plugin_url}/css/lux-booking-calendar.css' type='text/css' media='all'>
			<link rel='stylesheet' href='{$plugin_url}/css/jquery-datepicker.css' type='text/css' media='all'>
			<script type="text/javascript" src="{$jquery_url}/jquery.js" ></script>
			<script type="text/javascript" src="{$jquery_url}/ui/datepicker.min.js" ></script>
			
			<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#arrival_date').datepicker({showOn: 'both', dateFormat: 'dd.mm.yy', firstDay: 1, buttonImage: "{$plugin_url}/img/calendar.png"});
				jQuery('#departure_date').datepicker({showOn: 'both', dateFormat: 'dd.mm.yy', firstDay: 1, buttonImage: "{$plugin_url}/img/calendar.png"});
			});
			
			function checkAvailability(){
				// TODO: check URL validation.
				document.location = 'https://www.cbooking.de/v4/booking.aspx?id=luxeleven&module=public&ratetype=bar&lang=de&arrival=' + jQuery('#arrival_date').val() + '&departure=' + jQuery('#departure_date').val() + '&adults=2&rooms=1&children=0&roomtype=VR_2&showlowestrate=1';
			}
			</script>
			
			<div class="booking-calendar">
				<div class="calendar-wrapper">
					<div class="sub-calendar">
						<div class="calendar-border">
							<input type="text" id="arrival_date" placeholder="arrival date" />
						</div>
					</div>
					<div class="sub-calendar">
						<div class="calendar-border">
							<input type="text" id="departure_date" placeholder="departure date" />
						</div>
					</div>
				</div>
				<button id="bttn_check" onclick="checkAvailability()">LOOK &amp; BOOK</button>
			</div>

WIDGET_CONTENT;

		echo $widget_content;

		echo $args['after_widget'];
	}

	// Widget Backend
	public function form($instance)
	{
		if(isset($instance['title'])){
			$title = $instance['title'];
		}
		else{
			$title = __('New title', 'lux_widget_domain');
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
				   name="<?php echo $this->get_field_name('title'); ?>" type="text"
				   value="<?php echo esc_attr($title); ?>"/>
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		return $instance;
	}
}