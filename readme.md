# WPSteamMarketExcerpt

WPSteamMarketExcerpt is a plugin used to add Steam Market item listings to WordPress pages. It displays the name, image, lowest listed price, median price and total number of listings for any given item. The plugin is utilized in posts and pages using its shortcode.

## Screenshots

![](http://www.tyrrrz.me/Projects/WPSteamMarketExcerpt/Images/1.png)

## Installation

Go to Wordpress Dashboard >> Plugins >> Add New. Press Upload Plugin and navigate to the .zip file of the plugin.

--or--

Copy the contents of the .zip file to `/your_wp_blog_ftp_location/wp-content/plugins/`.

## Usage

The minimal required shortcode format looks like this:
```php
[steam_market_excerpt game="730" name="AK-47 | Elite Build (Factory New)"]
```
Where `game="730"` defines Steam application ID of a particular game (730 is for CS:GO). You can look up game IDs in the store by checking the URL of their store pages.
The name parameter should specify the full name of the item with correct amount of spaces, preserving all the special symbols and casing. You can copy-paste the name from Steam Market listing to make sure you don't miss anything.

Full specification of the shortcode is as follows:
```php
[steam_market_excerpt game="" name="" displayname="" currency="1" showimage="true" imgwidth="240" imgheight="240"]
```
- `Game` and `Name` parameters are required and must always be present.
- `DisplayName` can be altered to change the displayed name of the item, without affecting its reference to the actual item.
- `Currency` defines the currency for lowest price and median price fields. They are assigned using their unique IDs. ID=1 belongs to US dollars, which is default.
- `ShowImage` defines whether the item image should be downloaded. If set to false, it cuts down on number of requests and computation time. It will also not generate the respective `<div>` fragment for the image if set to false.
- `ImgWidth` and `ImgHeight` define the image dimensions that will be queried from the Steam servers. Has no effect if `ShowImage` is set to false. The resulting image will always have the defined dimensions, with transparent background. If the dimensions exceed the size of the image, it will be centered on the canvas instead.

## Styling

The excerpts can be customized using a stylesheet that comes with the plugin. It's located in `assets/css/style.css`, relative to plugin installation directory, which is typically `/wp-content/plugins/Steam-Market-Excerpt`.
