<?php
/*
Plugin Name: Steam Market Excerpt
Plugin URI:  https://github.com/Tyrrrz/WPSteamMarketExcerpt
Description: This plugin is used to make excerpts of Steam Market item listings
Version:     1.3.1
Author:      Alexey Golub
Author URI:  http://www.tyrrrz.me
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Abspath
defined('ABSPATH') or die('RIP skins');

// Disable warnings
error_reporting(E_ERROR);

function get_page_html($url) {
	$cache_dir = plugin_dir_path(__FILE__).'cache/';
	$cache_time = 10*60; // 10 minutes

	// Check if cache directory exists
	if (file_exists($cache_dir)) {
		// Delete old cache files
		foreach (glob($cache_dir.'*.ch') as $file) {
			if (is_file($file)) {
				if (time() - filemtime($file) > $cache_time)
					unlink($file);
			}
		}
	}
	// If not - make it
	else {
		mkdir($cache_dir);
	}

	// Get hash of the url
	$url_hash = hash('sha256', $url);

	// Try to find cache file
	$file = $cache_dir.$url_hash.'.ch';
	if (file_exists($file))
		return file_get_contents($file);

	// If no cache - just use a GET request and save cache
	$data = file_get_contents($url);
	file_put_contents($file, $data);
	return $data;
}

function get_info($game, $name, $currency) {
	$url =
		'http://steamcommunity.com/market/priceoverview/'.
		'?appid='.$game.
		'&currency='.$currency.
		'&market_hash_name='.rawurlencode($name);
	$response = get_page_html($url);
	$jobject = json_decode($response);
	if (!isset($jobject) or !$jobject->success) return null;
	return $jobject;
}

function get_image($game, $name, $width, $height) {
	$url =
		'http://steamcommunity.com/market/listings/'
		.$game.'/'
		.rawurlencode($name).
		'/render?start=0&count=1&currency=1&format=json';
	$response = get_page_html($url);
	$jobject = json_decode($response);
	if (!isset($jobject)) return null;

	// Get the HTML render
	$render_html = $jobject->results_html;

	// Parse the html
	$dom = new DOMDocument;
	$dom->loadHTML($render_html);
	$xpath = new DOMXpath($dom);

	// Get the image
	$img = $xpath->query('//img[@class="market_listing_item_img"]')->item(0);
	if (!isset($img)) return null;

	// Get image src
	$imgurl = $img->getAttribute('src');

	// Remove the pre-set size
	$imgurl = substr($imgurl, 0, strrpos($imgurl, '/'));

	// Replace with our size
	$imgurl .= '/'.$width.'fx'.$height.'f';

	return $imgurl;
}

// Shortcode function
function steam_market_excerpt_shortcode($atts) {
	// Get attributes
	$a = shortcode_atts(array(
		'game' => null,
		'name' => null,
		'displayname' => null,
		'currency' => 1,
		'showimage' => true,
		'imgwidth' => 240,
		'imgheight' => 240
	), $atts);	

	// Save attributes to local vars
	$game = $a['game'];
	$name = $a['name'];
	$displayname = $a['displayname'];
	$currency = $a['currency'];
	$showimage = $a['showimage'];
	$imgwidth = $a['imgwidth'];
	$imgheight = $a['imgheight'];

	// Make sure everything is set
	if (!isset($game) or !isset($name))
		return '<div class="steam-market-excerpt-error">Game and Name parameters must be set</div>';

	// Set display name
	if (!isset($displayname))
		$displayname = $name;

	// Query steam market API
	$info = get_info($game, $name, $currency);

	// Check for error
	if (!isset($info))
		return '<div class="steam-market-excerpt-error">No listings found for this item</div>';

	// Query the render to find the image src
	$imgurl = null;
	if ($showimage)
		$imgurl = get_image($game, $name, $imgwidth, $imgheight);

	// Output result
	$market_page_url = 'http://steamcommunity.com/market/listings/'.$game.'/'.rawurlencode($name);
	$output = '';
	$output .= '<div class="steam-market-excerpt">';
	$output .= '<div class="steam-market-excerpt-name"><a class="steam-market-excerpt-name" target="_blank" href="'.esc_attr($market_page_url).'">'.$displayname.'</a></div>';
	if ($showimage and isset($imgurl))
		$output .= '<div class="steam-market-excerpt-image"><img class="steam-market-excerpt-image" src="'.esc_attr($imgurl).'"/></div>';
	$output .= '<div class="steam-market-excerpt-fields">';
	if (isset($info->lowest_price))
		$output .= '<div class="steam-market-excerpt-lowestprice">Lowest Price: '.$info->lowest_price.'</div>';
	if (isset($info->median_price))
		$output .= '<div clas="steam-market-excerpt-medianprice">Median Price: '.$info->median_price.'</div>';
	if (isset($info->volume))
		$output .= '<div class="steam-market-excerpt-volume">Volume: '.$info->volume.'</div>';
	$output .= '</div>';
	$output .= '</div>';
	return $output;
}

// Plugin load function
function steam_market_excerpt_load() {
	wp_register_style('steam-market-excerpt-style', plugin_dir_url(__FILE__).'assets/css/style.css');
	wp_enqueue_style('steam-market-excerpt-style');
}

// Init
add_action('wp_enqueue_scripts', 'steam_market_excerpt_load');

// Shortcodes
add_shortcode('steam_market_excerpt', 'steam_market_excerpt_shortcode');
?>
