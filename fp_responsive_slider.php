<?php
/*
Plugin Name: FP Responsive Slider
Plugin URI: http://flourishpixel.com/
Description: This plugin will display image as slideshow with several effects. You can manage the options from FP Resposive Slider's Settings page or from widget settings. Also you can use Shortcode for pages and template code for your template and also you can use widget for your sidebar position.
Author: Moshiur Rahman Mehedi
Version: 1.0.0
Author URI: http://www.flourishpixel.com/
*/

/* Register a Custom Post Type (FP Responsive Slider) */
wp_enqueue_script('jquery');
wp_enqueue_script('fprslider_script', plugins_url('/js/responsiveslides.min.js',__FILE__) );
wp_enqueue_style('fprslider_css', plugins_url('/css/responsiveslides.css',__FILE__) );

// This theme uses a custom image size for featured images, displayed on "standard" posts.
//add_theme_support( 'post-thumbnails' );
//set_post_thumbnail_size( 900, 600 ); // Unlimited height, soft crop

add_action('init', 'fprslider_init');

function fprslider_init() {
	$labels = array(
		'name' => _x('FP Responsive Slider', 'post type general name'),
		'singular_name' => _x('RSlide', 'post type singular name'),
		'add_new' => _x('Add New', 'fprslider'),
		'add_new_item' => __('Add New Slide'),
		'edit_item' => __('Edit Slide'),
		'new_item' => __('New Slide'),
		'view_item' => __('View Slide'),
		'search_items' => __('Search Slides'),
		'not_found' => __('No Slides found yet.'),
		'not_found_in_trash' => __('No Slides found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'FP RSlider'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('thumbnail','title', 'editor')
	); 
	register_post_type('fprslider', $args);
}

/* Update Slide Messages */
add_filter('post_updated_messages', 'fprslider_updated_messages');
function fprslider_updated_messages($messages) {
	global $post, $post_ID;
	$messages['fprslider'] = array(
		0 => '',
		1 => sprintf(__('Slide updated.'), esc_url(get_permalink($post_ID))),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Slide updated.'),
		5 => isset($_GET['revision']) ? sprintf(__('Slide restored to revision from %s'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
		6 => sprintf(__('Slide published.'), esc_url(get_permalink($post_ID))),
		7 => __('Slide saved.'),
		8 => sprintf(__('Slide submitted.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
		9 => sprintf(__('Slide scheduled for: <strong>%1$s</strong>. '), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
		10 => sprintf(__('Slide draft updated.'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
	);
	return $messages;
}

/* Update Slide Help */
add_action('contextual_help', 'fprslider_help_text', 10, 3);
function fprslider_help_text($contextual_help, $screen_id, $screen) {
	if ('fprslider' == $screen->id) {
		$contextual_help =
		'<p>' . __('Things to remember when adding a Slide:') . '</p>' .
		'<ul>' .
		'<li>' . __('Give the slide a title. The title will be used as the slide\'s caption/title.') . '</li>' .
		'<li>' . __('Attach a Featured Image for the slide.') . '</li>' .
		'<li>' . __('Enter text into the Visual or HTML area. The text will appear within each slide during transitions.') . '</li>' .
		'</ul>';
	}
	elseif ('edit-fprslider' == $screen->id) {
		$contextual_help = '<p>' . __('A list of all slide appears below. To edit a slide, click on the slide\'s title.') . '</p>';
	}
	return $contextual_help;
}

// Styling for the Slide post type icon
add_action( 'admin_head', 'fprslider_icons' );
function fprslider_icons() {
	$path=plugins_url( '', __FILE__ );
    ?>
<style type="text/css" media="screen">
#menu-posts-fprslider .wp-menu-image {
 background: url(<?php echo $path;?>/images/fprslider.png) no-repeat 6px 6px !important;
}
#menu-posts-fprslider:hover .wp-menu-image, #menu-posts-fprslider.wp-has-current-submenu .wp-menu-image {
	opacity:0.6 !important;
}
#icon-edit.icon32-posts-fprslider {
background: url(<?php echo $path;?>/images/fprslider-icon.png) no-repeat 0px 2px;
}
p.fp_label input.custom {
	width:24%;
}
p.fp_label label {
	font-size:11px;
}
</style>
<?php 
}

// Widget Backend 
class FprsliderWidget extends WP_Widget
{
  function FprsliderWidget()
  {
    $widget_ops = array('classname' => 'FprsliderWidget', 'description' => 'Displays slider with effects' );
    $this->WP_Widget('FprsliderWidget', 'FP Responsive Slider', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '','auto' => 'true', 'speed' =>'500', 'timeout' =>'4000', 'pager'=>'false', 'nav' =>'false', 'random'=>'false','pause'=>'true', 'maxwidth'=>'800', 'imgWidth'=>'900','imgHeight'=>'600', 'limit'=>'5') );
    $title = $instance['title'];
	$auto = $instance['auto'];
	$speed = $instance['speed'];
	$timeout = $instance['timeout'];
	$pager = $instance['pager'];
	$nav = $instance['nav'];
	$random = $instance['random'];
	$pause = $instance['pause'];
	$maxwidth = $instance['maxwidth'];
	$imgWidth = $instance['imgWidth'];
	$imgHeight = $instance['imgHeight'];
	$limit = $instance['limit'];

?>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('title'); ?>">Title:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('auto'); ?>">Play:
    <select name="<?php echo $this->get_field_name('auto'); ?>" id="<?php echo $this->get_field_id('auto'); ?>">
      <option value="true" <?php if(attribute_escape($auto) == 'true'){echo 'selected';}?>>True</option>
      <option value="false" <?php if(attribute_escape($auto) == 'false'){echo 'selected';}?>>False</option>
    </select>
  </label>
  <label for="<?php echo $this->get_field_id('random'); ?>">Random:
    <select name="<?php echo $this->get_field_name('random'); ?>" id="<?php echo $this->get_field_id('random'); ?>">
      <option value="true" <?php if(attribute_escape($random) == 'true'){echo 'selected';}?>>True</option>
      <option value="false" <?php if(attribute_escape($random) == 'false'){echo 'selected';}?>>False</option>
    </select>
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('speed'); ?>">Speed:
    <input class="custom" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo attribute_escape($speed); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('maxwidth'); ?>">Maxwidth:
    <input class="custom" id="<?php echo $this->get_field_id('maxwidth'); ?>" name="<?php echo $this->get_field_name('maxwidth'); ?>" type="text" value="<?php echo attribute_escape($maxwidth); ?>" />
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('timeout'); ?>">Timeout:
    <input class="custom" id="<?php echo $this->get_field_id('timeout'); ?>" name="<?php echo $this->get_field_name('timeout'); ?>" type="text" value="<?php echo attribute_escape($timeout); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('pager'); ?>">Pager:
    <select name="<?php echo $this->get_field_name('pager'); ?>" id="<?php echo $this->get_field_id('pager'); ?>">
      <option value="true" <?php if(attribute_escape($pager) == 'true'){echo 'selected';}?>>true</option>
      <option value="false" <?php if(attribute_escape($pager) == 'false'){echo 'selected';}?>>false</option>
    </select>
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('nav'); ?>">Control:
    <select name="<?php echo $this->get_field_name('nav'); ?>" id="<?php echo $this->get_field_id('nav'); ?>">
      <option value="true" <?php if(attribute_escape($nav) == 'true'){echo 'selected';}?>>true</option>
      <option value="false" <?php if(attribute_escape($nav) == 'false'){echo 'selected';}?>>false</option>
    </select>
  </label>
  <label for="<?php echo $this->get_field_id('pause'); ?>">Hover:
    <select name="<?php echo $this->get_field_name('pause'); ?>" id="<?php echo $this->get_field_id('pause'); ?>">
      <option value="true" <?php if(attribute_escape($pause) == 'true'){echo 'selected';}?>>true</option>
      <option value="false" <?php if(attribute_escape($pause) == 'false'){echo 'selected';}?>>false</option>
    </select>
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('imgWidth'); ?>">imgWidth:
    <input class="custom" id="<?php echo $this->get_field_id('imgWidth'); ?>" name="<?php echo $this->get_field_name('imgWidth'); ?>" type="text" value="<?php echo attribute_escape($imgWidth); ?>" />
  </label>
  <label for="<?php echo $this->get_field_id('imgHeight'); ?>">imgHeight:
    <input class="custom" id="<?php echo $this->get_field_id('imgHeight'); ?>" name="<?php echo $this->get_field_name('imgHeight'); ?>" type="text" value="<?php echo attribute_escape($imgHeight); ?>" />
  </label>
</p>
<p class="fp_label">
  <label for="<?php echo $this->get_field_id('limit'); ?>">Slide Limit:
    <select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>">
      <option value="2" <?php if(attribute_escape($limit) == '2'){echo 'selected';}?>>2</option>
      <option value="3" <?php if(attribute_escape($limit) == '3'){echo 'selected';}?>>3</option>
      <option value="4" <?php if(attribute_escape($limit) == '4'){echo 'selected';}?>>4</option>
      <option value="5" <?php if(attribute_escape($limit) == '5'){echo 'selected';}?>>5</option>
      <option value="6" <?php if(attribute_escape($limit) == '6'){echo 'selected';}?>>6</option>
      <option value="7" <?php if(attribute_escape($limit) == '7'){echo 'selected';}?>>7</option>
      <option value="8" <?php if(attribute_escape($limit) == '8'){echo 'selected';}?>>8</option>
      <option value="9" <?php if(attribute_escape($limit) == '9'){echo 'selected';}?>>9</option>
      <option value="10" <?php if(attribute_escape($limit) == '10'){echo 'selected';}?>>10</option>
      <option value="15" <?php if(attribute_escape($limit) == '15'){echo 'selected';}?>>15</option>
      <option value="20" <?php if(attribute_escape($limit) == '20'){echo 'selected';}?>>20</option>
    </select>
  </label>
</p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['auto'] = $new_instance['auto'];
	$instance['speed'] = $new_instance['speed'];
	$instance['timeout'] = $new_instance['timeout'];
	$instance['pager'] = $new_instance['pager'];
	$instance['nav'] = $new_instance['nav'];
	$instance['random'] = $new_instance['random'];
	$instance['pause'] = $new_instance['pause'];
	$instance['maxwidth'] = $new_instance['maxwidth'];
	$instance['imgWidth'] = $new_instance['imgWidth'];
	$instance['imgHeight'] = $new_instance['imgHeight'];
	$instance['limit'] = $new_instance['limit'];
    return $instance;
	
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
	$auto = empty($instance['auto']) ? ' ' : apply_filters('widget_auto', $instance['auto']);
	$speed = empty($instance['speed']) ? ' ' : apply_filters('widget_speed', $instance['speed']);
	$timeout = empty($instance['timeout']) ? ' ' : apply_filters('widget_timeout', $instance['timeout']);
	$pager = empty($instance['pager']) ? ' ' : apply_filters('widget_pager', $instance['pager']);
	$nav = empty($instance['nav']) ? ' ' : apply_filters('widget_nav', $instance['nav']);
	$random = empty($instance['random']) ? ' ' : apply_filters('widget_random', $instance['random']);
	$pause = empty($instance['pause']) ? ' ' : apply_filters('widget_pause', $instance['pause']);	
	$maxwidth = empty($instance['maxwidth']) ? ' ' : apply_filters('widget_maxwidth', $instance['maxwidth']);
	$imgWidth = empty($instance['imgWidth']) ? ' ' : apply_filters('widget_imgWidth', $instance['imgWidth']);
	$imgHeight = empty($instance['imgHeight']) ? ' ' : apply_filters('widget_imgHeight', $instance['imgHeight']);
	$limit = empty($instance['limit']) ? ' ' : apply_filters('widget_limit', $instance['limit']);
	
 	add_image_size( 'fprslide', $imgWidth,$imgHeight, true);
	
	
    if (!empty($title))
      echo $before_title . $title . $after_title;

?>
<script type="text/javascript">
  jQuery(document).ready(function(){
    jQuery('.rslides').responsiveSlides({
		auto: <?php echo $auto; ?>,
		speed:<?php echo $speed; ?>,
		timeout: <?php echo $timeout; ?>,
		pager: <?php echo $pager; ?>,
		nav: <?php echo $nav; ?>,
		random: <?php echo $random; ?>,
		pause: <?php echo $pause; ?>,
		maxwidth:"<?php echo $maxwidth; ?>"
  });
});
</script>
<?php
	// WIDGET CODE GOES HERE
	query_posts('post_type=fprslider&posts_per_page='.$limit);
	if (have_posts()) : 
		echo "<ul class='rslides'>";
		while (have_posts()) : the_post(); 
			echo "<li>";
				the_post_thumbnail('fprslide');
			echo "</li>";
	 
		endwhile;
		echo "</ul>";
	endif; 
	wp_reset_query();
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("FprsliderWidget");') );

add_action('admin_menu', 'fprslider_menu');
function fprslider_menu (){
add_management_page('FP Responsive Slider', 'FP Responsive Slider', 10, 'fp-responsive-slider', 'fp_responsive_slider');
add_submenu_page('edit.php?post_type=fprslider', 'FP RSlider Settings', 'FP RSlider Settings', 'edit_posts', 'fp-responsive-slider', 'fp_responsive_slider');
}

function fp_responsive_slider (){
$maxWidth_saved = get_option('$maxWidth_op');
$speed_saved = get_option('$speed_op');
$img_width_saved = get_option('$img_width_op');
$img_height_saved = get_option('$img_height_op');
$slide_timeout_saved = get_option('$slide_timeout_op');
$fp_pager_saved = get_option('$fp_pager_op');
$slide_limit_saved = get_option('$slide_limit_op');
$control_arrow_saved = get_option('$control_arrow_op');
$hover_control_saved = get_option('$hover_control_op');
$auto_play_saved = get_option('$auto_play_op');
$random_play_saved = get_option('$random_play_op');
?>

<div class="wrap" style="font-family:Tahoma, Geneva, sans-serif;">
  <h2>FP Responsive Slider</h2>
  <h3>FP Responsive Slider's Settings pgae which works when you use Shortcode for Pages. [fprslider]</h3>
  <?php
if(isset($_POST['Submit']))  {
        $maxWidth_saved = $_POST["maxWidth"];
		$speed_saved = $_POST["speed"];
		$img_width_saved = $_POST["img_width"];
		$img_height_saved = $_POST["img_height"];
		$slide_timeout_saved = $_POST["slide_timeout"];
		$fp_pager_saved=$_POST["fp_pager"];
		$slide_limit_saved=$_POST["slide_limit"];
		$control_arrow_saved=$_POST["control_arrow"];
		$hover_control_saved=$_POST["hover_control"];
		$auto_play_saved=$_POST["auto_play"];
		$random_play_saved=$_POST["random_play"];
		update_option( '$maxWidth_op', $maxWidth_saved );
        update_option( '$speed_op', $speed_saved );
		update_option( '$img_width_op', $img_width_saved );
        update_option( '$img_height_op', $img_height_saved );
		update_option( '$slide_timeout_op', $slide_timeout_saved );
		update_option( '$fp_pager_op', $fp_pager_saved );
		update_option( '$slide_limit_op', $slide_limit_saved );
		update_option( '$control_arrow_op', $control_arrow_saved );
		update_option( '$hover_control_op', $hover_control_saved );
		update_option( '$auto_play_op', $auto_play_saved );
		update_option( '$random_play_op', $random_play_saved );
?>
  <div class="updated">
    <p><strong>
      <?php _e('Options saved.', 'mt_trans_domain' ); ?>
      </strong></p>
  </div>
  <?php  }  ?>
  <form method="post" name="options" target="_self">
    <table width="400px" class="custom_table">
      <tr>
        <td align="left" scope="row"><label>Maximum Width of Slider: </label></td>
        <td><input name="maxWidth" type="text" value="<?php echo $maxWidth_saved ?>" />
          px</td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Animation Speed: </label></td>
        <td><input name="speed" type="text" value="<?php echo $speed_saved ?>" />
          milliseconds</td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Featured Image Width: </label></td>
        <td><input name="img_width" type="text" value="<?php echo $img_width_saved ?>" />
          px</td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Featured Image Height: </label></td>
        <td><input name="img_height" type="text" value="<?php echo $img_height_saved ?>" />
          px</td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Slide Timeout: </label></td>
        <td><input name="slide_timeout" type="text" value="<?php echo $slide_timeout_saved ?>" />
          milliseconds</td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Pagination: </label></td>
        <td><select name="fp_pager" id="fp_pager">
            <option value="true" <?php if($fp_pager_saved == 'true'){echo 'selected';}?>>True</option>
            <option value="false" <?php if($fp_pager_saved == 'false'){echo 'selected';}?>>False</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Control Arrow: </label></td>
        <td><select name="control_arrow" id="control_arrow">
            <option value="true" <?php if($control_arrow_saved == 'true'){echo 'selected';}?>>True</option>
            <option value="false" <?php if($control_arrow_saved == 'false'){echo 'selected';}?>>False</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Control Mouse Hover: </label></td>
        <td><select name="hover_control" id="hover_control">
            <option value="true" <?php if($hover_control_saved == 'true'){echo 'selected';}?>>True</option>
            <option value="false" <?php if($hover_control_saved == 'false'){echo 'selected';}?>>False</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Slide Limit: </label></td>
        <td><select name="slide_limit" id="slide_limit">
            <option value="2" <?php if($slide_limit_saved == '2'){echo 'selected';}?>>2</option>
            <option value="3" <?php if($slide_limit_saved == '3'){echo 'selected';}?>>3</option>
            <option value="4" <?php if($slide_limit_saved == '4'){echo 'selected';}?>>4</option>
            <option value="5" <?php if($slide_limit_saved == '5'){echo 'selected';}?>>5</option>
            <option value="6" <?php if($slide_limit_saved == '6'){echo 'selected';}?>>6</option>
            <option value="7" <?php if($slide_limit_saved == '7'){echo 'selected';}?>>7</option>
            <option value="8" <?php if($slide_limit_saved == '8'){echo 'selected';}?>>8</option>
            <option value="9" <?php if($slide_limit_saved == '9'){echo 'selected';}?>>9</option>
            <option value="10" <?php if($slide_limit_saved == '10'){echo 'selected';}?>>10</option>
            <option value="15" <?php if($slide_limit_saved == '15'){echo 'selected';}?>>15</option>
            <option value="20" <?php if($slide_limit_saved == '20'){echo 'selected';}?>>20</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Auto Play: </label></td>
        <td><select name="auto_play" id="auto_play">
            <option value="true" <?php if($auto_play_saved == 'true'){echo 'selected';}?>>True</option>
            <option value="false" <?php if($auto_play_saved == 'false'){echo 'selected';}?>>False</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" scope="row"><label>Play Random Slide: </label></td>
        <td><select name="random_play" id="random_play">
            <option value="true" <?php if($random_play_saved == 'true'){echo 'selected';}?>>True</option>
            <option value="false" <?php if($random_play_saved == 'false'){echo 'selected';}?>>False</option>
          </select></td>
      </tr>
      <tr>
        <td align="left" colspan="2"><input type="submit" name="Submit" value="Update" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
}
//for using shortcode [fprslider limit=2 id=slider]
add_shortcode('fprslider', 'show_fprslider');


function show_fprslider(){
	$imgWidth = get_option('$img_width_op');
	$imgHeight = get_option('$img_height_op');
	$maxWidth = get_option('$maxWidth_op');
	$speedSlide = get_option('$speed_op');
	$slideTimeout = get_option('$slide_timeout_op');
	$fpPager = get_option('$fp_pager_op');
	$slideLimit = get_option('$slide_limit_op');
	$controlArrow = get_option('$control_arrow_op');
	$hoverControl = get_option('$hover_control_op');
	$autoPlay = get_option('$auto_play_op');
	$randomPlay = get_option('$random_play_op');
	
	add_image_size( 'fprslide_page', $imgWidth,$imgHeight, true);
?>
	<script type="text/javascript">
	  jQuery(document).ready(function(){
		jQuery('.rslides').responsiveSlides({
			auto: <?php echo $autoPlay; ?>,
			speed:<?php echo $speedSlide; ?>,
			timeout: <?php echo $slideTimeout; ?>,
			pager: <?php echo $fpPager; ?>,
			nav: <?php echo $controlArrow; ?>,
			random: <?php echo $randomPlay; ?>,
			pause: <?php echo $hoverControl; ?>,
			maxwidth:"<?php echo $maxWidth; ?>"
	  });
	});
	</script>
<?php	
	query_posts('post_type=fprslider&posts_per_page='.$slideLimit);
	if (have_posts()) : 
		echo "<ul class='rslides'>";
		while (have_posts()) : the_post(); 
			echo "<li>";
			echo the_post_thumbnail('fprslide_page');
			echo "</li>";
		endwhile;
		echo "</ul>";
		
	endif; 
	wp_reset_query();
}


add_image_size('featured_column_preview', 100, 100, true);

// GET FEATURED IMAGE
function ST4_get_featured_image($post_ID){
 $post_thumbnail_id = get_post_thumbnail_id($post_ID);
 if ($post_thumbnail_id){
  $post_thumbnail_img = the_post_thumbnail('featured_column_preview');
  return $post_thumbnail_img[0];
 }
}

// ADD NEW COLUMN
function ST4_columns_head($defaults) {
 $defaults['featured_image'] = 'Featured Image';
 return $defaults;
}

// SHOW INFO IN THE NEW COLUMN
function ST4_columns_content($column_name, $post_ID) {
 if ($column_name == 'featured_image') {
  $post_featured_image = ST4_get_featured_image($post_ID);
  if ($post_featured_image){
   echo '<img src="' . $post_featured_image . '" />'; 
  }
 }
}

add_filter('manage_fprslider_posts_columns', 'ST4_columns_head');
add_filter('manage_fprslider_posts_custom_column', 'ST4_columns_content', 10, 2);

?>
