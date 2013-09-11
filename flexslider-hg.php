<?php
/**
 * Plugin Name: Responsive Slider for Developers
 * Plugin URI: http://halgatewood.com/flexslider-hg
 * Description: An admin interface that uses WooThemes Flexslider on the frontend. Designed for developers to easily add image rotators that their clients can easily maintain.
 * Version: 1.3
 * Author: Hal Gatewood
 * Author URI: http://halgatewood.com
 
 
 	Based off DevPress's Responsive Slider and using WooThemes Flexslider. 
 	
 	http://wordpress.org/extend/plugins/responsive-slider/
 	http://www.woothemes.com/flexslider/
 	
 	SIMPLE USAGE:
 	
	function set_flexslider_hg_rotators( $rotators = array() )
	{
	    $rotators['homepage'] 		= array( 'size' => 'large', 'heading_tag' => 'h1' );
	    $rotators['contactus'] 		= array( 'size' => 'thumbnail' );
	    $rotators['gallerypage'] 	= array( 'size' => 'medium', 'hide_slide_data' => true );
	    $rotators['amenities'] 		= array( 'size' => 'your-custom-size', 'limit' => 5 );
	    return $rotators;
	}
	add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');
 
*/


/* SETUP */
add_action( 'plugins_loaded', 'flexslider_hg_setup' );
define( 'FLEXSLIDER_HG_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


// GET ROTATORS:
// TO SETUP ADDITIONAL ROTATORS SEE DOCS AT http://halgatewood.com/flexslider-hg
function flexslider_hg_rotators()
{
	$rotators = array();
	$rotators['homepage'] 		= array( 'size' => 'large' );
	$rotators['attachments'] 	= array( 'size' => 'large', 'hide_slide_data' => true );
	return apply_filters( 'flexslider_hg_rotators', $rotators );
}


// SETUP ACTIONS
function flexslider_hg_setup()
{
	add_action( 'init', 'flexslider_hg_setup_init' );
	add_action( 'admin_head', 'flexslider_hg_admin_icon' );	
	add_action( 'wp_enqueue_scripts', 'flexslider_wp_enqueue' );	

	add_action( 'add_meta_boxes', 'flexslider_hg_create_slide_metaboxes' );
	add_action( 'save_post', 'flexslider_hg_save_meta', 1, 2 );
	
	add_filter( 'manage_edit-slides_columns', 'flexslider_hg_columns' );
	add_action( 'manage_slides_posts_custom_column', 'flexslider_hg_add_columns' );
	
	add_shortcode('flexslider', 'flexslider_hg_shortcode');
}


// INIT
function flexslider_hg_setup_init()
{
	// 'SLIDES' POST TYPE
	$labels = array( 'name' => __( 'Slides', 'flexslider_hg' ), 'singular_name' => __( 'Slide', 'flexslider_hg' ), 'all_items' => __( 'All Slides', 'flexslider_hg' ), 'add_new' => __( 'Add New Slide', 'flexslider_hg' ), 'add_new_item' => __( 'Add New Slide', 'flexslider_hg' ), 'edit_item' => __( 'Edit Slide', 'flexslider_hg' ), 'new_item' => __( 'New Slide', 'flexslider_hg' ),'view_item' => __( 'View Slide', 'flexslider_hg' ),'search_items' => __( 'Search Slides', 'flexslider_hg' ),'not_found' => __( 'No Slide found', 'flexslider_hg' ), 'not_found_in_trash' => __( 'No Slide found in Trash', 'flexslider_hg' ), 'parent_item_colon' => '' );
	
	$args = array(
		'labels'               => $labels,
		'public'               => true,
		'publicly_queryable'   => true,
		'_builtin'             => false,
		'show_ui'              => true, 
		'query_var'            => true,
		'rewrite'              => apply_filters( 'flexslider_hg_post_type_rewite', array( "slug" => "slides" )),
		'capability_type'      => 'post',
		'hierarchical'         => false,
		'menu_position'        => 26.6,
		'supports'             => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
		'taxonomies'           => array(),
		'has_archive'          => true,
		'show_in_nav_menus'    => false
	);
	register_post_type( 'slides', $args );
}


// FRONTEND: heading
function flexslider_hg_do_scripts()
{
	wp_enqueue_script( 'flexslider', FLEXSLIDER_HG_URI . 'js/jquery.flexslider-min.js', array( 'jquery' ) );
	wp_enqueue_style( 'flexslider', FLEXSLIDER_HG_URI . 'css/flexslider.css' );
}

function flexslider_wp_enqueue()
{
	global $post;
	
	if( function_exists('has_shortcode') )
	{
		if( has_shortcode( $post->post_content, 'flexslider')) 
		{ 
			flexslider_hg_do_scripts();
		}
	}
	else
	{
		flexslider_hg_do_scripts();
	}
}


// ADMIN: WIDGET ICONS
function flexslider_hg_admin_icon()
{
	$icon 		= FLEXSLIDER_HG_URI . "/images/menu-icon.png";
	$icon_32 	= FLEXSLIDER_HG_URI . "/images/icon-32.png";
	
	echo "
		<style> 
			#menu-posts-slides .wp-menu-image { background: url({$icon}) no-repeat 6px -17px !important; }
			#menu-posts-slides.wp-has-current-submenu .wp-menu-image { background-position:6px 6px!important; }
			.icon32-posts-slides { background: url({$icon_32}) no-repeat 0 0 !important; }
		</style>
	";	
}


// SHOW ROTATOR
function show_flexslider_rotator( $slug )
{
	// GET ALL ROTATORS
	$rotators = flexslider_hg_rotators();
	
	// SET IMAGE SIZE: size
	$image_size = isset($rotators[ $slug ]['size']) ? $rotators[ $slug ]['size'] : 'large';

	// HIDE SLIDE TEXT: hide_slide_data
	$hide_slide_data = isset($rotators[ $slug ]['hide_slide_data']) ? true : false;
	
	// HEADING HTML ELEMENT: heading_tag
	$header_type = isset($rotators[ $slug ]['heading_tag']) ? $rotators[ $slug ]['heading_tag'] : "h2";

	// ORDER BY PARAMS: orderby, order, limit
	$orderby = isset($rotators[ $slug ]['orderby']) ? $rotators[ $slug ]['orderby'] : "menu_order";
	$order = isset($rotators[ $slug ]['order']) ? $rotators[ $slug ]['order'] : "ASC";
	$limit = isset($rotators[ $slug ]['limit']) ? $rotators[ $slug ]['limit'] : "-1";


	// DEFAULT QUERY PARAMS
	$query_args = array( 'post_type' => 'slides', 'order' => $order, 'orderby' => $orderby, 'meta_key' => '_slider_id', 'meta_value' => $slug, 'posts_per_page' => $limit );

	
	// IF ATTACHMENTS WE NEED THE POST PARENT
	if( $slug == "attachments" )
	{
		$query_args['post_type'] = 'attachment';
		$query_args['post_parent'] = get_the_ID();
		$query_args['post_status'] = 'inherit';
		$query_args['post_mime_type'] = 'image';
		unset( $query_args['meta_value'] );
		unset( $query_args['meta_key'] );
	}
	
	$rtn = "";
	
	query_posts( apply_filters( 'flexslider_hg_query_post_args', $query_args) );
	if ( have_posts() ) 
	{
		$rtn .= '<div id="flexslider_hg_' . $slug . '_wrapper" class="flexslider-hg-wrapper">';
		$rtn .= '<div id="flexslider_hg_' . $slug . '" class="flexslider_hg_' . $slug . ' flexslider flexslider-hg">';
		$rtn .= '<ul class="slides">';
		
		while ( have_posts() )
		{
			the_post();
		
			$url = get_post_meta( get_the_ID(), "_slide_link_url", true );
			$a_tag_opening = '<a href="' . $url . '" title="' . the_title_attribute( array('echo' => false) ) . '" >';
			
			
			$rtn .= '<li>';
			$rtn .= '<div id="slide-' . get_the_ID() . '" class="slide">';
			
			if( $slug == "attachments" )
			{
				$rtn .= wp_get_attachment_image( get_the_ID(), $image_size );
			}
			else if ( has_post_thumbnail() )
			{
				if($url) { $rtn .= $a_tag_opening; }
				$rtn .= get_the_post_thumbnail( get_the_ID(), $image_size , array( 'class' => 'slide-thumbnail' ) );
				if($url) { $rtn .= '</a>'; }
			}
			
			if( !$hide_slide_data)
			{
				$rtn .= '<div class="slide-data">';
				
				$rtn .= '<' . $header_type . ' class="slide-title flexslider-hg-title">';
				
				if($url) { $rtn .= $a_tag_opening; }
				$rtn .= get_the_title();
				if($url) { $rtn .= '</a>'; }
				
				$rtn .= '</' . $header_type . '>';
				
				$rtn .= get_the_excerpt();
				$rtn .= '</div>';
			}
	
			$rtn .= '</div><!-- #slide-' . get_the_ID() . ' -->';
			$rtn .= '</li>';
		}

		$rtn .= '</ul>';
		$rtn .= '</div><!-- close: #flexslider_hg_' . $slug . ' -->';
		$rtn .= '</div><!-- close: #flexslider_hg_' . $slug . '_wrapper -->';
		
		
		// INIT THE ROTATOR
		$rtn .= '<script>';
		$rtn .= " jQuery('#flexslider_hg_{$slug}').flexslider( ";
			
		if(isset($rotators[ $slug ]['options']) AND $rotators[ $slug ]['options'] != "") 
		{ 
			$rtn .= $rotators[ $slug ]['options'];
		}
				
		$rtn .= " ); ";
		$rtn .= '</script>';		
	}
	
	wp_reset_query();
	
	return $rtn;
}


// ADMIN META BOX
function flexslider_hg_create_slide_metaboxes() 
{
    add_meta_box( 'flexslider_hg_metabox_1', __( 'Slide Settings', 'flexslider-hg' ), 'flexslider_hg_metabox_1', 'slides', 'normal', 'default' );
}
function flexslider_hg_metabox_1() 
{
	global $post;	
    $rotators = flexslider_hg_rotators();

	$slide_link_url 	= get_post_meta( $post->ID, '_slide_link_url', true );
	$slider_id		 	= get_post_meta( $post->ID, '_slider_id', true ); ?>
	
	<p>URL: <input type="text" style="width: 90%;" name="slide_link_url" value="<?php echo esc_attr( $slide_link_url ); ?>" /></p>
	<span class="description"><?php echo _e( 'The URL this slide should link to.', 'flexslider_hg' ); ?></span>

	<p>
		<?php if($rotators) { ?>
		<?php _e('Attach to:', 'flexslider_hg'); ?>
		<select name="slider_id">
			<?php foreach( $rotators as $rotator => $size) { ?>
				<option value="<?php echo $rotator ?>" <?php if($slider_id == $rotator) echo " SELECTED"; ?>><?php echo $rotator ?></option>
			<?php } ?>
		</select>
		<?php } else { ?>
			<div style="color: red;">
				<?php _e('No rotators have been setup. Contact your site developer.', 'flexslider_hg'); ?><br />
				See <a href="http://halgatewood.com/flexslider-hg" target="_blank">the documentation</a> to learn how to setup a rotator.
			</div>
		<?php } ?>
	</p>
	
	<?php 
}


// SAVE THE EXTRA GOODS FROM THE SLIDE
function flexslider_hg_save_meta( $post_id, $post )
{
	if ( isset( $_POST['slide_link_url'] ) ) 
	{
		update_post_meta( $post_id, '_slide_link_url', strip_tags( $_POST['slide_link_url'] ) );
	}
	if ( isset( $_POST['slider_id'] ) ) 
	{
		update_post_meta( $post_id, '_slider_id', strip_tags( $_POST['slider_id'] ) );
	}
}


// ADMIN COLUMNS
function flexslider_hg_columns( $columns ) 
{
	$columns = array(
		'cb'       => '<input type="checkbox" />',
		'image'    => __( 'Image', 'flexslider_hg' ),
		'title'    => __( 'Title', 'flexslider_hg' ),
		'ID'       => __( 'Slider ID', 'flexslider_hg' ),
		'order'    => __( 'Order', 'flexslider_hg' ),
		'link'     => __( 'Link', 'flexslider_hg' ),
		'date'     => __( 'Date', 'flexslider_hg' )
	);

	return $columns;
}

function flexslider_hg_add_columns( $column )
{
	global $post;
	$edit_link = get_edit_post_link( $post->ID );

	if ( $column == 'image' ) 	echo '<a href="' . $edit_link . '" title="' . $post->post_title . '">' . get_the_post_thumbnail( $post->ID, array( 60, 60 ), array( 'title' => trim( strip_tags(  $post->post_title ) ) ) ) . '</a>';
	if ( $column == 'order' ) 	echo '<a href="' . $edit_link . '">' . $post->menu_order . '</a>';
	if ( $column == 'ID' ) 		echo get_post_meta( $post->ID, "_slider_id", true );
	if ( $column == 'link' ) 	echo '<a href="' . get_post_meta( $post->ID, "_slide_link_url", true ) . '" target="_blank" >' . get_post_meta( $post->ID, "_slide_link_url", true ) . '</a>';		
}


// SHORTCODE
function flexslider_hg_shortcode($atts, $content = null)
{
	$slug = isset($atts['slug']) ? $atts['slug'] : "attachments";
	if(!$slug) { return apply_filters( 'flexslider_hg_empty_shortcode', "<p>Flexslider: Please include a 'slug' parameter. [flexslider slug=homepage]</p>" ); }
	return show_flexslider_rotator( $slug );
}


?>