=== GeoMap ===
Tags: geo, geographic, map, longitude, latitude
Contributors: graeme

GeoMap generates a map image based on the geographical coordiantes associated with a post using Owen Winkler's Geo plugin. It can generate a dot and/or crosshairs of any size, color and various shapes. It can also crap the image from a larger world map, creating a subset of the larger image. It is configured using an Options sub-menu. It is beta -- YMMV.

Owen Winkler's Geo plugin is here: http://www.asymptomatic.net/wp-hacks

GeoMap itself will eventually live somewhere, too.

== Installation ==

1. Have Owen's Geo plugin installed and working.
2. Upload 'geomap.php' to your plugins folder, usually 'wp-content/plugins/'.
3. Create folders 'wp-content/geomap' and 'wp-content/geomap/cache'.
4. Upload at least one map image (JPG, PNG or BMP) to 'wp-content/geomap'. One is provided with the distribution, but you can use your own by placing it in 'wp-content/geomap'.
5. Activate the plugin on the plugin screen.
6. Configure with your desired options using the GeoMap Options sub-menu.
7. Add a geomap_image() tag to your template.

== Frequently Asked Questions ==

= That's quite a few install steps =

Yeah.

= Can I other world map images? =

Yes. Just place them in 'wp-content/geomap' and they will appear in the drop-down on the options page.

= What is the template tag to use? =

Use this:

<?php geomap_image(); ?>

It will output an <img src="..." /> tag into your HTML, with proper height and width attributes set, and a class of 'geomap', which you can use to style the image using CSS.

= Does it re-render the image every time? =

No, it uses a simple caching mechanism so that each combination of longitude, latitude and image settings will produce the image only once.

= My cache directory is very big =

Stop playing around with your settings all the time. Just erase all the files in 'wp-content/geomap/cache' and let it regenerate the current images next time the page is accessed.

== Screenshots ==
