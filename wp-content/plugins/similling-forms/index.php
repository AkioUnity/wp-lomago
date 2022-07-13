<?php 

	/*
	Plugin Name: Smiling Forms
	Description: Add Smiling Image for CF7
	Plugin URI: https://www.lomago.io/
	Author: Akio Monk
	Author URI: https://www.lomago.io/
	Version: 1.1
	License: GPL2
	Text Domain: smiling-forms
	*/
	
	/*
	
	    Copyright (C) 2022  Akio  developer@lomago.de
	
	    This program is free software; you can redistribute it and/or modify
	    it under the terms of the GNU General Public License, version 2, as
	    published by the Free Software Foundation.
	
	    This program is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.
	
	    You should have received a copy of the GNU General Public License
	    along with this program; if not, write to the Free Software
	    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

	add_action( 'activated_plugin', 'wdo_free_ihe_activation_redirect' );

	function wdo_free_ihe_activation_redirect( $plugin ) {
	    if( $plugin == plugin_basename( __FILE__ ) ) {
	        exit( wp_redirect( admin_url( 'admin.php?page=caption_hover' ) ) );
	    }
	}

	include_once ('plugin.class.php');
	if (class_exists('LA_Caption_Hover')) {
		$object = new LA_Caption_Hover;
	}
	
 ?>