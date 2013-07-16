=== FP Responsive Slider ===
Contributors: Flourish Pixel
Tags: plugin, slider, image, gallery, slideshow, widget, responsive, shortcode
Requires at least: 2.0.0
Tested up to: 3.5.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will display image as slideshow with several effects. You can manage the options from FP Resposive Slider's Settings page or from widget settings. Also you can use Shortcode for pages and template code for your template and also you can use widget for your sidebar position.

== Description ==

FP Responsive Slider will display image as slideshow with several effects. You can manage the options from FP Resposive Slider's Settings page or from widget settings. Also you can use Shortcode for pages and template code for your template and also you can use widget for your sidebar position.

You can control over your sliding transion effect, time, navigation, slide number etc.

Demo: http://responsiveslides.com/

A few notes about the sections above:
* Fully Responsive
* Here we use custom post types.
* Template Code: <?php echo show_fprslider(); ?>
* Shortcode: [fprslider]

FP Responsive Slider is a tiny plugin that creates a responsive slider using elements inside a container. FP Responsive Slider works with wide range of browsers including all IE versions from IE6 and up. It also adds css max-width support for IE6 and other browsers that don't natively support it. Only dependency is jQuery (1.6 and up supported) and that all the images are same size.

Responsive Slides has basically only two different modes: Either it just automatically fades the images, or operates as a responsive image container with pagination and/or navigation to fade between slides.

== Installation ==

This section describes how to install the plugin and get it working.

1. Download the plugin file to your computer and Unzip the downloaded archive
2. Upload the folder **fp-responsive-slider** to your */wp-content/plugins/* directory
3. Activate the plugin from *Plugins > FP Responsive Slider > Activate*, under WordPress admin interface
4. Settings page values will use when shortcode use or if you use template code. 
5. Use this shortcode in your post or page to display FP Responsive Slider *[fprslider]*.
6.Template Code: <?php echo show_fprslider(); ?>
7. Manage Widget Settings.

== Frequently Asked Questions ==
= What is maxwidth? =
Answer: It must be Integer. Max-width of the slideshow, in pixels.

= What is Speed? =
Answer: It must be Integer. Speed of the transition, in milliseconds
	
= What is Timeout? =
Answer: It must be Integer, Time between slide transitions, in milliseconds.

= What is imgWidth? =
Answer: The width of Featured Image.

= What is imgHeight? =
Answer: The height of Featured Image.

= How to use in template? =
Answer: <?php echo show_fprslider(); ?>

= How to use into Page? =
Answer: [fprslider]
	

== Screenshots ==

1. View of Responsive view of slider.
2. View Full View of slider.
3. View Backend Settings page for shortcode/template code.
4. View Backend Settings for widget.

== Changelog ==

= 1.0.0 =
* initial release of FP Responsive Slider.

== Features == 

1. Fully responsive 

2. CSS3 transitions with JavaScript fallback 

3. Simple markup using unordered list 

4. Settings for transition and timeout durations 

5. Multiple slideshows supported 

6. Automatic and manual fade 

7. Works in all major desktop and mobile browsers 

8. Captions and other html-elements supported inside slides 

9. Separate pagination and next/prev controls 

10. Possibility to choose where the controls append to 

11. Possibility to randomize the order of the slides 

12. Possibility to use custom markup for pagination 

13. Can be paused while hovering slideshow and/or controls 

14. Images can be wrapped inside links 

15. Optional 'before' and 'after' callbacks 
