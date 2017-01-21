Steam Market Excerpt
===================

**Description**

Steam Market Excerpt (SME) plugin is used to add Steam Market item listings to your Wordpress posts. It shows and automatically updates the item's name, image, lowest listed price, median price and total number of listings for the given item.
This plugin is utilized via a special shortcode - [steam_market_excerpt]

**Installation**

Go to Wordpress Dashboard -> Plugins -> Add New. Press Upload Plugin and navigate to the .zip file of the plugin.
--or--
Copy the contents of the .zip file to /your_wp_blog_ftp_location/wp-content/plugins/

**Usage**

This plugin is utilized via its own shortcode.

The minimal format for the shortcode looks like this:
[steam_market_excerpt game="730" name="AK-47 | Elite Build (Factory New)"]

Where game="730" defines Steam appId of a particular game (730 is CS:GO). You can look up game IDs in the store by checking the URL of their store pages.
Name should specify the name of the item with correct amount of spaces, preserving all the special symbols and casing. You can copy-paste the name from Steam Market listing to make sure you don't miss anything.

Full specification of the shortcode is as follows:
[steam_market_excerpt game="" name="" displayname="" currency="1" showimage="true" imgwidth="240" imgheight="240"]

Game and Name are required and must always be present.
Displayname can be altered to change the displayed name of the item, without affecting it's reference to the actual item.
Currency defines the currency for lowest price and median price fields. They are assigned using their unique IDs. ID=1 belongs to US dollars, which is default.
Showimage defines whether the item image should be downloaded. If set to false, it can save up considerable amount of bandwith. It will also not generate the respective <div> fragment for the image if set to false.
Imgwidth and Imgheight defines the maximum image dimensions that will be queried from the Steam servers. Has no effect if Showimage is set to false. Reducing the values can potentially reduce bandwith.

**Styling**

It's really easy to change how each part of the excerpt looks. To do that, you need to locate the file called style.css that's located in /wp-content/plugins/SteamMarketExcerpt/assets/css/style.css by default. Each part of the excerpt has its own class that you can use to write your own CSS styles for it.

**Screenshots**

![](http://tyrrrz.me/projects/images/wpsme_1.png)
