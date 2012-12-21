<?php
/**
 * Plugin Name: Responsive Slider for Developers
 * Plugin URI: http://halgatewood.com/flexslider-hg
 * Description: An admin interface that uses WooThemes Flexslider on the frontend. Designed for developers to easily add image rotators that their clients can easily maintain.
 * Version: 1.0
 * Author: Hal Gatewood
 * Author URI: http://halgatewood.com
 
 
 	Based off DevPress's Responsive Slider and using WooThemes Flexslider. 
 	
 	http://wordpress.org/extend/plugins/flexslider_hg/
 	http://www.woothemes.com/flexslider/
 	
 	SIMPLE USAGE:
 	
	function set_flexslider_hg_rotators()
	{
		$rotators = array();
		$rotators['homepage'] = array( 'size' => 'homepage-rotator' );
		return $rotators;
	}
	add_filter('flexslider_hg_rotators', 'set_flexslider_hg_rotators');
 
*/


/* SETUP */
add_action( 'plugins_loaded', 'flexslider_hg_setup' );
define( 'FLEXSLIDER_HG_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

function flexslider_hg_setup()
{
	add_action( 'init', 'flexslider_hg_setup_init' );
	add_action( 'admin_head', 'flexslider_hg_admin_icon' );	

	add_action( 'add_meta_boxes', 'flexslider_hg_create_slide_metaboxes' );
	add_action( 'save_post', 'flexslider_hg_save_meta', 1, 2 );
	
	add_filter( 'manage_edit-slides_columns', 'flexslider_hg_columns' );
	add_action( 'manage_slides_posts_custom_column', 'flexslider_hg_add_columns' );
}

/* INIT */
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
		'rewrite'              => array( "slug" => "slides" ),
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


/* SHOW ROTATOR */
function show_flexslider_rotator( $slug )
{
	$rotators = apply_filters( 'flexslider_hg_rotators', array( ) );
	$image_size = isset($rotators[ $slug ]['size']) ? $rotators[ $slug ]['size'] : 'large';

	query_posts( array( 'post_type' => 'slides', 'order' => 'ASC', 'orderby' => 'menu_order', 'meta_key' => '_slider_id', 'meta_value' => $slug ) );
	
	if ( have_posts() ) :
	
		wp_enqueue_script( 'jquery', array(), false, false, false );
		wp_enqueue_script( 'flexslider', FLEXSLIDER_HG_URI . 'js/jquery.flexslider-min.js', array('jquery') );
		wp_enqueue_style( 'flexslider', FLEXSLIDER_HG_URI . 'css/flexslider.css' );
	
	?>
		<div id="flexslider_hg_<?php echo $slug ?>" class="flexslider_hg_<?php echo $slug ?> flexslider">
			<ul class="slides">
			<?php while ( have_posts() ) : the_post(); $url = get_post_meta( get_the_ID(), "_slide_link_url", true ); ?>
				<li>
					<div id="slide-<?php the_ID(); ?>" class="slide">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php if($url) { ?><a href="<?php echo $url ?>" title="<?php the_title_attribute(); ?>" ><?php } ?>
								<?php the_post_thumbnail( $image_size , array( 'class'	=> 'slide-thumbnail' ) ); ?>
							<?php if($url) { ?></a><?php } ?>
						<?php endif; ?>
						
						<div class="slide-data">
							<h2 class="slide-title"><?php if($url) { ?><a href="<?php echo $url; ?>" title="<?php the_title_attribute(); ?>" ><?php } ?><?php the_title(); ?><?php if($url) { ?></a><?php } ?></h2>
							<?php the_excerpt(); ?>
						</div>
					</div><!-- #slide-x -->
				</li>
			<?php endwhile; ?>
			</ul>			
		</div><!-- .flexslider -->

		<script>
		
			var flexslider_<?php echo $slug ?> = new Object();
			flexslider_<?php echo $slug ?>.slug = '<?php echo $slug ?>';
			<?php if(isset($rotators[ $slug ]['options']) AND $rotators[ $slug ]['options'] != "") { ?>flexslider_<?php echo $slug ?>.options = <?php echo $rotators[ $slug ]['options']; ?>;<?php } ?>

			if(!flexslider_hg_rotators) { var flexslider_hg_rotators = new Array(); }
			flexslider_hg_rotators.push(  flexslider_<?php echo $slug ?> );
		
		</script>
	
	<?php endif;
	wp_reset_query();	
}


/* ADMIN META BOX */
function flexslider_hg_create_slide_metaboxes() 
{
    add_meta_box( 'flexslider_hg_metabox_1', __( 'Slide Settings', 'flexslider-hg' ), 'flexslider_hg_metabox_1', 'slides', 'normal', 'default' );
}
function flexslider_hg_metabox_1() 
{
	global $post;	
	
	// GET ROTATORS: SEE DOCS AT http://halgatewood.com/flexslider-hg
    $rotators = apply_filters( 'flexslider_hg_rotators', array( ) );

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

/* SAVE THE EXTRA GOODS FROM THE SLIDE */
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

/* ADMIN COLUMNS */

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

?>