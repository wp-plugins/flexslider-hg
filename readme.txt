=== Responsive Slider for Developers ===
Contributors: halgatewood
Donate link: http://halgatewood.com/flexslider-hg/
Tags: slider, rotator, FlexSlider, slides, features, call to actions
Requires at least: 3
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add FlexSlider Rotator Placeholders to your themes which are easily updateable by your clients.

== Description ==

THIS PLUGIN IS INTENDED FOR DEVELOPERS, SOME BASIC PHP KNOWLEDGE IS NEEDED

FlexSlider for Developers creates a new 'slides' custom post type with it's own section in the WordPress admin sidebar. It uses the standard WordPress user interface so your clients will know how to use it instantly. 

For the rotator itself it using the great WooThemes FlexSlider. 

The easiest way to add a placeholder is to put this in your functions.php

`function set_flexslider_hg_rotators()
{
	$rotators = array();
	$rotators['homepage'] = array( 'size' => 'homepage-rotator' );
	return $rotators;
}
add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');`
	
The size of the rotator is set from your WordPress Image Size settings. You can set those with the following function (also in your functions.php file).

`add_image_size( 'homepage-rotator', '550', '250', true );`


Setup the FlexSlider options: see options at http://www.woothemes.com/FlexSlider/

`$rotators['homepage'] = array( 'size' => 'homepage-rotator', 'options' => "{slideshowSpeed: 7000, direction: 'vertical'}" );`

To include the rotator in your theme add the following line to your template:

`if(function_exists('show_flexslider_rotator')) echo show_flexslider_rotator( 'homepage' );`

== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create placeholders in your templates (show below)
1. Stylize to your themes design
1. You and your clients can then upload slides with images, H2 titles and post excerpts. Great for SEO


The easiest way to add a placeholder is to put this in your functions.php

`function set_flexslider_hg_rotators()
{
	$rotators = array();
	$rotators['homepage'] = array( 'size' => 'homepage-rotator' );
	return $rotators;
}
add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');`
	
The size of the rotator is set from your WordPress Image Size settings. You can set those with the following function (also in your functions.php file).

`add_image_size( 'homepage-rotator', '550', '250', true );`

To include the rotator in your theme add the following line to your template:

`if(function_exists('show_flexslider_rotator')) echo show_flexslider_rotator( 'homepage' );`


== Screenshots ==

1. List view of the slides, custom columns used for quick viewing and editing
2. Standard WordPress UI is used including Featured Image support, Post Excerpt and Page Attributes
3. Included default FlexSlider template included, can be modified with CSS
4. PHP function to create your FlexSlider placeholders.

== Changelog ==

= 1.0 =
* Initial load of the plugin.

