=== GeoMap ===
Tags: geo, geographic, map, longitude, latitude
Contributors: graeme

GeoMap generates a map image based on the geographical coordiantes associated with a post using Owen Winkler's Geo plugin. It can generate a dot and/or crosshairs of any size, color and various shapes. It can also crop the image from a large world map, creating a subset of the larger image. It is configured using the Geo Info Options sub-menu. It is beta -- YMMV.

The Geo plugin is here: http://www.asymptomatic.net/wp-hacks

== Installation ==

1. Have Owen's Geo plugin installed and working.
2. Upload 'geomap.php' to your plugins folder, usually 'wp-content/plugins/'.
3. Create the folder 'wp-content/geomap' and upload at least one map image (JPG, PNG or BMP) into it. One is provided with the distribution, but you can use your own by placing it in this folder.
4. Activate the plugin on the plugin screen.
5. Configure with your desired options using the options which will now appear at the bottom of the Geo Info Options sub-menu.
6. Add a geomap_image() tag to your template.

== Frequently Asked Questions ==

= That's a bit complicated to install =

Maybe. If so, sorry.

= Can I other world map images? =

Yes. Just place them in 'wp-content/geomap' and they will appear in the drop-down on the options page.

Two good sources of map images are:

* http://www.radcyberzine.com/xglobe/
* http://awka.sourceforge.net/xglobe.html

= What is the template tag to use? =

Use this:

<?php geomap_image(); ?>

It will output an <img ... /> tag into your HTML, with proper height and width attributes set, and a class of 'geomap', which you can use to style the image using CSS.

= Does it re-render the image every time? =

No, it uses a simple caching mechanism so that each combination of longitude, latitude and image settings will produce the image only once.

= My cache directory is very big =

There is a maximum cache size settings. Set this to something reasonable. Stop playing around with your settings all the time! If you want to clear the cache, just erase all the files in 'wp-content/geomap/cache' and let it regenerate the current images next time the page is accessed.

Note: Cache management is disabled at the moment. You can either uncomment the untested code or clear it out yourself now and then.

== Screenshots ==
