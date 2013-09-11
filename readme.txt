=== Responsive Slider for Developers ===
Contributors: halgatewood
Donate link: http://halgatewood.com/flexslider-hg/
Tags: slider, rotator, FlexSlider, slides, features, call to actions, slider shortcode, shortcode
Requires at least: 3
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Flexslider Rotators to your themes that are easy to maintain by your clients.

== Description ==

THIS IS INTENDED FOR DEVELOPERS, BASIC PHP KNOWLEDGE IS NEEDED

Flexslider for Developers creates a new 'slides' custom post type with it's own section in the WordPress admin sidebar. It uses the standard WordPress user interface so your clients will know how to use it instantly. 

For the rotator itself it uses FlexSlider by WooThemes. 

By default the plugin comes with a 'homepage' placeholder. You can add more or overwrite it by adding a function to the functions.php file found in your theme. It is designed this way to keep clients greasy fingers from changing the settings.

`function set_flexslider_hg_rotators( $rotators = array() )
{
	$rotators['homepage'] 		= array( 'size' => 'homepage-rotator', 'heading_tag' => 'h1' );
	$rotators['contactus']		= array( 'size' => 'thumbnail', 'orderby' => 'title', 'order' => 'DESC' );
	$rotators['gallerypage'] 	= array( 'size' => 'medium', 'hide_slide_data' => true );
	$rotators['amenities'] 		= array( 'size' => 'your-custom-size', 'limit' => 5 );	
	return $rotators;
}
add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');`
	
The size of the rotator is set from your WordPress Image Size settings. You can set those with the following function (also in your functions.php file).

`add_image_size( 'homepage-rotator', '550', '250', true );`


Setup the FlexSlider options: see options at http://www.woothemes.com/FlexSlider/

`$rotators['homepage'] = array( 'size' => 'homepage-rotator', 'options' => "{slideshowSpeed: 7000, direction: 'vertical'}" );`

To include the rotator in your theme include the 'slug' found from your function above ($rotators['homepage']) and add the following line to your template:

`if(function_exists('show_flexslider_rotator')) echo show_flexslider_rotator( 'homepage' );`

You can also use the new shortcode [flexslider slug=homepage] to include the rotator in certain posts, pages, widgets, etc.

== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create placeholders in your templates (show below)
1. Stylize to your themes design
1. You and your clients can then upload slides with images, H2 titles and post excerpts. Great for SEO


By default the plugin comes with a 'homepage' placeholder. You can add more or overwrite it by adding a function to the functions.php file found in your theme.

`function set_flexslider_hg_rotators( $rotators = array() )
{
	$rotators['homepage'] 		= array( 'size' => 'homepage-rotator', 'heading_tag' => 'h1' );
	$rotators['contactus']		= array( 'size' => 'thumbnail', 'orderby' => 'title', 'order' => 'DESC' );
	$rotators['gallerypage'] 	= array( 'size' => 'medium', 'hide_slide_data' => true );
	$rotators['amenities'] 		= array( 'size' => 'your-custom-size', 'limit' => 5 );	
	return $rotators;
}
add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');`
	
The size of the rotator is set from your WordPress Image Size settings. You can set those with the following function (also in your functions.php file).

`add_image_size( 'homepage-rotator', '550', '250', true );`


Setup the FlexSlider options: see options at http://www.woothemes.com/FlexSlider/

`$rotators['homepage'] = array( 'size' => 'homepage-rotator', 'options' => "{slideshowSpeed: 7000, direction: 'vertical'}" );`

To include the rotator in your theme include the 'slug' found from your function above ($rotators['homepage']) and add the following line to your template:

`if(function_exists('show_flexslider_rotator')) echo show_flexslider_rotator( 'homepage' );`

You can also use the new shortcode [flexslider slug=homepage] to include the rotator in certain posts, pages, widgets, etc.


== Screenshots ==

1. List view of the slides, custom columns used for quick viewing and editing
2. Standard WordPress UI is used including Featured Image support, Post Excerpt and Page Attributes
3. Included default FlexSlider template included, can be modified with CSS
4. PHP function to create your FlexSlider placeholders.

== Changelog =

= 1.3 =
* New default attachments rotator
* If no slug is specified we grab the attachments
* Only enqueue scripts and styles when shortcode is present, (WP 3.6+)
* New limit attribute, set the number of slides to display
* New filters for developers to hook into

= 1.2 =
* Internet Explorer issues fixed
* Code cleanup

= 1.1.4 =
* Added posts_per_page = -1 to grab all slides for a rotator

= 1.1.3 =
* Added orderby and order parameters that can be passed

= 1.1 =
* Code cleanup and improvement in Javascript, PHP and CSS
* Shortcode added [flexslider slug=homepage]
* Default 'homepage' rotator added (can be removed, overwritten, updated)
* Option to hide the text box on top of slides
* Ability to change the Heading H2 tag in the slide box, SEO bonus!

= 1.0 =
* Initial load of the plugin.

