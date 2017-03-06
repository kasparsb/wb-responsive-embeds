<?php
/*
Plugin Name: Webit Responsive embeds
Description: Make responsive embeded iframes
Version: 1.0
Author: Kaspars Bulins
Author URI: http://webit.lv
*/

include_once(plugin_dir_path(__FILE__).'base.php');
include_once(plugin_dir_path(__FILE__).'facade.php');
include_once(plugin_dir_path(__FILE__).'plugin.php');


// Define static facade to theme object
class ResponsiveEmbeds extends ResponsiveEmbeds\Facade {}

// Init facade and create plugin object
ResponsiveEmbeds::init('ResponsiveEmbeds\Plugin');