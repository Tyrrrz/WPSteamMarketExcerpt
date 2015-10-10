<?php
/*
Plugin Name: Steam Market Excerpt
Plugin URI:  https://github.com/Tyrrrz/WPSteamMarketExcerpt
Description: This plugin is used to make excerpts of Steam Market item listings
Version:     1.2.1
Author:      Alexey Golub
Author URI:  http://www.tyrrrz.me
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Abspath
defined('ABSPATH') or die('RIP skins');

// Disable warnings
error_reporting(E_ERROR);

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
    if (!isset($game) or !isset($name)) return '<div class="steam-market-excerpt-error">Game and Name parameters must be set</div>';

    // Set display name
    if (!isset($displayname))
    	$displayname = $name;

    // Query steam market API
    $url =
        'http://steamcommunity.com/market/priceoverview/'.
        '?appid='.$game.
        '&currency='.$currency.
        '&market_hash_name='.rawurlencode($name);
    $response = file_get_contents($url);
    $json = json_decode($response);

    // Determine success
    if (!$json->success) return '<div class="steam-market-excerpt-error">No listings found for this item</div>';

    // Store results
    $lowest_price = $json->lowest_price;
    $median_price = $json->median_price;
    $volume = $json->volume;

    // Query the render to find the image src
    $market_page_url = 'http://steamcommunity.com/market/listings/'.$game.'/'.rawurlencode($name);
    $imgurl = null;
    if ($showimage) {
        // Get the API query that returns the render html
        $url = $market_page_url.'/render?start=0&count=1&currency='.$currency.'format=json';
        $response = file_get_contents($url);
        $render_html = json_decode($response)->results_html;

        // Parse the html
        $dom = new DOMDocument;
        $dom->loadHTML($render_html);
        $xpath = new DOMXpath($dom);

        // Get the image
        $img = $xpath->query('//img[@class="market_listing_item_img"]')->item(0);
        if (isset($img)) {
        	// Get image src
	        $imgurl = $img->getAttribute('src');

	        // Remove the pre-set size
	        $imgurl = substr($imgurl, 0, strrpos($imgurl, '/'));

	        // Replace with our size
	        $imgurl .= '/'.$imgwidth.'fx'.$imgheight.'f';
    	}
    }

    // Output result
    $output = '';
    $output .= '<div class="steam-market-excerpt">';
    $output .= '<div class="steam-market-excerpt-name"><a class="steam-market-excerpt-name" target="_blank" href="'.esc_attr($market_page_url).'">'.$displayname.'</a></div>';
    if ($showimage and isset($imgurl))
        $output .= '<div class="steam-market-excerpt-image"><img class="steam-market-excerpt-image" src="'.esc_attr($imgurl).'"/></div>';
    $output .= '<div class="steam-market-excerpt-fields">';
    if (isset($lowest_price))
    	$output .= '<div class="steam-market-excerpt-lowestprice">Lowest Price: '.$lowest_price.'</div>';
    if (isset($median_price))
    	$output .= '<div clas="steam-market-excerpt-medianprice">Median Price: '.$median_price.'</div>';
    if (isset($volume))
    	$output .= '<div class="steam-market-excerpt-volume">Volume: '.$volume.'</div>';
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