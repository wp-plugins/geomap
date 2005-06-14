<?php
/*
Plugin Name: GeoMap
Plugin URI: http://metafugitive.com/plugins/geomap/
Description: Generates a map from a source image based on coordinates entered using <a href="http://www.asymptomatic.net">Owen Winkler&rsquo;s</a> <a href="http://www.asymptomatic.net/wp-hacks">Geo plugin</a>. Map markings can be configured on the <a href="admin.php?page=geomap.php">options page</a>.
Version: 0.9&alpha;
Author: Graeme Lennon
Author URI: http://metafugitive.com/
WordPress Version Required: 1.5.1

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
/*
  TODO:
	Cache management features.
	Better filesystem and URL path handling.
	Check for permission faults.
*/

// Localization? Not really sure what this does...
load_plugin_textdomain('GeoMap');

// DEFINE THE CLASS HERE
class GeoMap
{
     function options_page()
     {
     	if(isset($_POST['GeoMapOptions']))
     	{
     		$use_marker = $_POST['use_marker'] == 1 ? 1 : 0;
     		$use_crosshairs = $_POST['use_crosshairs'] == 1 ? 1 : 0;
     
     		update_option('geomap_mapfile', $_POST['mapfile']);
     		update_option('geomap_gen_image_size_x', $_POST['gen_image_size_x']);
     		update_option('geomap_gen_image_size_y', $_POST['gen_image_size_y']);
     		update_option('geomap_use_marker', $_POST['use_marker']);
     		update_option('geomap_dot_type', $_POST['dot_type']);
     		update_option('geomap_dot_size', $_POST['dot_size']);
     		update_option('geomap_dot_color', $_POST['dot_color']);
     		update_option('geomap_use_crosshairs', $use_crosshairs);
     		update_option('geomap_line_color', $_POST['line_color']);
     		update_option('geomap_maxcache', $_POST['maxcache']);
     
     		echo '<div class="updated"><p><strong>' . __('Options updated.', 'GeoMap') . '</strong></p></div>';
     	}
     
     	$mapfile = get_settings('geomap_mapfile');
     	$gen_image_size_x = get_settings('geomap_gen_image_size_x');
     	$gen_image_size_y = get_settings('geomap_gen_image_size_y');
     	$use_marker = get_settings('geomap_use_marker');
     	$dot_type = get_settings('geomap_dot_type');
     	$dot_size = get_settings('geomap_dot_size');
     	$dot_color = get_settings('geomap_dot_color');
     	$use_crosshairs = get_settings('geomap_use_crosshairs');
     	$line_color = get_settings('geomap_line_color');
     	$maxcache = get_settings('geomap_maxcache');
     
     	$ck_dot_type[intval($dot_type)] = ' checked="checked"';
     	$ck_use_marker = $use_marker == 1 ? ' checked="checked"' : '';
     	$ck_use_crosshairs = $use_crosshairs == 1 ? ' checked="checked"' : '';
     
     	echo '
		<div class="wrap">
		<h2>' . __('Geo Map Generator', 'GeoMap') . '</h2>
		<form method="post">
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr>
					<th width="33%" scope="row">' . __('Map File', 'GeoMap') . ':</th>
					<td><select name="mapfile">';
          $mapfiles = glob(ABSPATH."/wp-content/geomap/*.{jpg,png,bmp,JPG,PNG,BMP}", GLOB_BRACE);
     	if (is_array($mapfiles)) {
     		foreach ($mapfiles as $file) {
     			$filen = basename($file);
     			echo '<option value="'.$filen.'"';
      			if ($filen == $mapfile) echo ' selected="selected"';
      			echo '>'.$filen.'</option>';
     		}
     	}
     	echo '
		 		</select></td>
				</tr>
				<tr valign="top">
					<th width="33%" scope="row">' . __('Generated Image Width', 'GeoMap') . ':</th>
					<td><input type="text" name="gen_image_size_x" size="6" style="width: 2.5em;" value="'. $gen_image_size_x .'" /> px<br />
					Width of the image cropped from the world map. Set to the width of your world map if you don\'t want cropping.</td>
				</tr>
				<tr valign="top">
				    <th width="33%" scope="row">' . __('Generated Image Height', 'GeoMap') . ':</th>
				    <td><input type="text" name="gen_image_size_y" size="6" style="width: 2.5em;" value="'. $gen_image_size_y .'" /> px<br />
					Height of the image cropped from the world map. Set to the height of your world map if you don\'t want cropping.</td>
				</tr>
			</table>
			<fieldset class="options">
				<legend><input type="checkbox" name="use_marker" id="use_marker" ' . $ck_use_marker . ' value="1" />
				<label for="use_marker">'. __('Draw Marker', 'GeoMap') . '</label></legend>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr>
					<th width="33%" scope="row">' . __('Marker Type', 'GeoMap') . ':</th>
					<td>
					<label for="dot_type0"><input type="radio" name="dot_type" id="dot_type0" ' . $ck_dot_type[0] . ' value="0" /> ' . __('Dot', 'GeoMap') . '</label>&nbsp;
					<label for="dot_type1"><input type="radio" name="dot_type" id="dot_type1" ' . $ck_dot_type[1] . ' value="1" /> ' . __('Filled Dot', 'GeoMap') . '</label>&nbsp;
					<label for="dot_type2"><input type="radio" name="dot_type" id="dot_type2" ' . $ck_dot_type[2] . ' value="2" /> ' . __('Box', 'GeoMap') . '</label>&nbsp;
					<label for="dot_type3"><input type="radio" name="dot_type" id="dot_type3" ' . $ck_dot_type[3] . ' value="3" /> ' . __('Filled Box', 'GeoMap') . '</label>
					</td>
				</tr>
				<tr>
					<th width="33%" scope="row">' . __('Marker Size', 'GeoMap') . ':</th>
					<td><input type="text" name="dot_size" size="2" style="width: 1.5em;" value="'. $dot_size .'" /> px</td>
				</tr>
				<tr valign="top">
					<th width="33%" scope="row">' . __('Marker Color', 'GeoMap') . ':</th>
					<td><input type="text" name="dot_color" size="10" value="'. $dot_color .'" /><br />
					Hexadecimal (e.g. <code>FF0000</code>)</td>
				</tr>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend><input type="checkbox" name="use_crosshairs" id="use_crosshairs" ' . $ck_use_crosshairs . ' value="1" />
				<label for="use_crosshairs">'. __('Draw Crosshair Lines', 'GeoMap') . '</label></legend>
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr valign="top">
					<th width="33%" scope="row">' . __('Line Color', 'GeoMap') . ':</th>
					<td><input type="text" name="line_color" size="10" value="'. $line_color .'" /><br />
					Hexadecimal (e.g. <code>FF0000</code>)</td>
				</tr>
				</table>
			</fieldset>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr>
					<th width="33%" scope="row">' . __('Max Cache Size', 'GeoMap') . ':</th>
					<td><input type="text" name="maxcache" size="6" value="'. $maxcache .'" />Kilobytes (KB)</td>
				</tr>
			</table>
               <p class="submit"><input type="submit" name="GeoMapOptions" value="' . __('Update Options', 'GeoMap') . ' &raquo;" /></p>
		</form>
		</div>
	';
     }

	function add_options()
	{
		add_option('geomap_mapfile', 'world_map-mini.jpg');
		add_option('geomap_gen_image_size_x', '100');
		add_option('geomap_gen_image_size_y', '75');
		add_option('geomap_use_marker', '1');
		add_option('geomap_dot_type', '1');
		add_option('geomap_dot_size', '8');
		add_option('geomap_dot_color', 'FF0000');
		add_option('geomap_use_crosshairs', 0);
		add_option('geomap_line_color', 'FF0000');
		add_option('geomap_maxcache', '500');
	}

	function admin_head($not_used)
	{
	 	if (!get_settings('geomap_mapfile'))
	 	   GeoMap::add_options();
	 	
	 	if (!file_exists(ABSPATH.'/wp-content/geomap/cache') mkdir(ABSPATH.'/wp-content/geomap/cache');
	}

	function admin_menu($not_used)
	{
		add_options_page(__('Geo Map Generator', 'GeoMap'), __('Geo Map', 'GeoMap'), 5, basename(__FILE__), array('GeoMap', 'options_page'));
	}

}	// End class GeoMap

// Function lifted from article by Simon Moss on PHPBuilder.com
// http://www.phpbuilder.com/columns/moss20031023.php3
function getlocationcoords($lat, $lon, $width, $height)
{
	$x = (($lon + 180) * ($width / 360));
	$y = ((($lat * -1) + 90) * ($height / 180));
	return array("x"=>round($x),"y"=>round($y));
}

function geomap_print_url($url,$width,$height,$alt='')
{
 	echo '<img class="geomap" src="'.$url.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'" />';
}

function geomap_image()
{
	global $post;

	list($lat, $lon) = split(',', get_post_meta($post->ID, '_geo_location', true));

	$mapfile = get_settings('geomap_mapfile');
	$size_x = get_settings('geomap_gen_image_size_x');
	$size_y = get_settings('geomap_gen_image_size_y');
	$use_marker = get_settings('geomap_use_marker');
	$dot_type = get_settings('geomap_dot_type');
	$dot_size = get_settings('geomap_dot_size');
	$dot_color = get_settings('geomap_dot_color');
	$use_crosshairs = get_settings('geomap_use_crosshairs');
	$line_color = get_settings('geomap_line_color');
	$maxcache = get_settings('geomap_maxcache');

	if(empty($lon)) $lon = get_settings($default_geourl_lon);
	if(empty($lat)) $lat = get_settings($default_geourl_lat);

	$hash = md5($lon.$lat.$mapfile.$size_x.$size_y.$use_marker.$dot_type.$dot_size.$dot_color.$use_crosshairs.$line_color);
	$url = get_settings('home')."/wp-content/geomap/cache/".$hash.".png";
	$filename = ABSPATH.'/wp-content/geomap/cache/'.$hash.".png";

	// If we've done this combination before, we can use the cached version.
	if (file_exists($filename)) {
         geomap_print_url($url,$size_x,$size_y);
	    return;
	}

     /*
     // Check if we've overrun our cache size.
     $sizeofdir = sizeof(ABSPATH.'/wp-content/geomap/cache/');
     while ($sizeofdir > $maxcache) {
           foreach (glob(ABSPATH.'/wp-content/geomap/cache/') as $file) {
                   isset($oldest) : $oldest = min($oldest,fileatime($file) ? $oldest = fileatime($file);
           }
           unlink($oldest);
           $sizeofdir = sizeof($cachedir);
     }
     */

	// Load the world map
	if (!$img = imagecreatefromjpeg(ABSPATH.'/wp-content/geomap/'.$mapfile)) {
	    echo "Unable to open world map.";
	    return 1;
	}

	// We need these variables to be able scale the long/lat coordinates.
	$scale_x = imagesx($img);
	$scale_y = imagesy($img);

	// Now we convert the long/lat coordinates into screen coordinates
	$pt = getlocationcoords($lat, $lon, $scale_x, $scale_y);

	// Now mark the point on the map.
	if ($use_marker) {
		sscanf($dot_color, "%2x%2x%2x", $red, $green, $blue);
		$colour = imagecolorallocate($img, $red, $green, $blue);
		$rad = ceil($dot_size/2);
		switch($dot_type) {
			// Dot
			case '0':
				imageellipse($img,$pt["x"],$pt["y"],$rad,$rad,$colour);
				break;
			// Filled dot
			case '1':
				imagefilledellipse($img,$pt["x"],$pt["y"],$rad,$rad,$colour);
				break;
			// Box
			case '2':
				imagerectangle($img,$pt["x"]-$rad,$pt["y"]-$rad,$pt["x"]+$rad,$pt["y"]+$rad,$colour);
				break;
			// Filled box
			case '3':
				imagefilledrectangle($img,$pt["x"]-$rad,$pt["y"]-$rad,$pt["x"]+$rad,$pt["y"]+$rad,$colour);
				break;
		}
	}

	// If we're using crosshairs, draw the lines.
	if ($use_crosshairs) {
          sscanf($line_color, "%2x%2x%2x", $red, $green, $blue);
	     $colour = imagecolorallocate($img, $red, $green, $blue);
		imageline($img,0,$pt["y"],$scale_x-1,$pt["y"],$colour);
		imageline($img,$pt["x"],0,$pt["x"],$scale_y-1,$colour);
	}

	// Create a crop of the image around the region
	$img_new = imagecreatetruecolor($size_x,$size_y);
	imagecopy($img_new,$img,0,0,max($pt["x"]-ceil($size_x/2),0),max($pt["y"]-ceil($size_y/2),0),$size_x,$size_y);
	imagedestroy($img);

	// Make a new cached version.
	imagepng($img_new,$filename);
	imagedestroy($img_new);

	// Return the map image.
     geomap_print_url($url,$size_x,$size_y);
}

add_action('admin_menu', array('GeoMap', 'admin_menu'));

?>
