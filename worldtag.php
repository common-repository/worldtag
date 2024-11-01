<?php
/*
Plugin Name: WorldTag
Plugin URI: http://worldtag.co.uk
Description: Use tags to show people what you are talking about, and easily find other people talking about the same things!
Author: David Edwards
Version: 1.1
*/

/******************************************************************************

Copyright 2011  a531016 : a531016@hotmail.com

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at http://www.gnu.org/copyleft/gpl.html

*********************************************************************************/


function wtlink($content) {
	$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$site = $_SERVER['SERVER_NAME'];
	$pieces = explode("wt:", $content);
	$result = count($pieces);
	if ($result>0){
	for ($i = 1; $i < $result; $i++) {
	  if ($tag) {
		unset($tag);
	  }
	  $j = mb_strlen($pieces[$i]);
	  for ($k = 0; $k < $j; $k++) {
		$char = mb_substr($pieces[$i], $k, 1);
		if (preg_match("/^[a-z]$/i", $char)) {
		  $tag .= $char;
		} else {
		break;
		}
	  }
		if (!empty($_SERVER['HTTPS'])) {
		  $prot = 'https';
		} else {
		  $prot = 'http';
		}
		if (get_option('wt_showGlobe') == 'yes') {
		  $pic = "<img src='".$prot."://worldtag.co.uk/c.php?t=".$tag."&u=".urlencode($url)."&s=".urlencode($site)."' />";
		} else {
		  $pic = "<img src='".$prot."://worldtag.co.uk/c.php?t=".$tag."&u=".urlencode($url)."&s=".urlencode($site)."' style='display:none;' />";
		}
	  if (get_option('wt_newWindow') == 'yes') {$winTarget = "target='_blank'";}
	  $tag_ref = "<a href='http://worldtag.co.uk/s.php?t=".$tag."' title='WorldTag: ".get_option('wt_linkText')."' ".$winTarget.">".$pic.$tag."</a>";
	  $content = str_replace("wt:".$tag, $tag_ref, $content);
	  }
	}
	return($content);
}

add_filter( "the_content", "wtlink" );

//adding the menu item to choose options

add_action('admin_menu', 'worldtag_plugin_menu');

function worldtag_plugin_menu() {
	add_submenu_page('plugins.php', 'WorldTag', 'WorldTag', 'manage_options', 'worldtag_plugin', 'worldtag_plugin_options');
	//call register settings function
	add_action( 'admin_init', 'register_wtsettings');
}

function register_mysettings() {
	//check option already exisits
	add_option('wt_showGlobe', 'yes');
	add_option('wt_newWindow', 'no');
	add_option('wt_linkText', 'see who else is talking about this');
	
	//register our settings
	register_setting( 'worldtag-settings-group', 'wt_showGlobe' );
	register_setting( 'worldtag-settings-group', 'wt_newWindow' );
	register_setting( 'worldtag-settings-group', 'wt_linkText' );
}

function worldtag_plugin_options() {
 //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt1_name = 'wt_showGlobe';
	$opt2_name = 'wt_newWindow';
	$opt3_name = 'wt_linkText';
	$data1_field_name = 'wt_globeOption';
	$data2_field_name = 'wt_windowOption';
	$data3_field_name = 'wt_linkText';
	$hidden_field_name = 'wt_postCheck';

    // Read in existing option value from database
    $opt1_val = get_option( $opt1_name );
	$opt2_val = get_option( $opt2_name );
	$opt3_val = get_option( $opt3_name );
	
    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt1_val = $_POST[ $data1_field_name ];
		$opt2_val = $_POST[ $data2_field_name ];
		$opt3_val = $_POST[ $data3_field_name ];

        // Save the posted value in the database
        update_option( $opt1_name, $opt1_val );
		update_option( $opt2_name, $opt2_val );
		update_option( $opt3_name, $opt3_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'WorldTag Options', 'menu-test' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Show Globe with WorldTag Links:", 'menu-test' ); ?> 

<select name="<?php echo $data1_field_name; ?>">
	<option value='yes' <?php if ($opt1_val == 'yes'){echo 'selected';} ?>>yes</option>
	<option value='no' <?php if ($opt1_val == 'no'){echo 'selected';} ?>>no</option>
</select>

</p>
<p><?php _e("Open WorldTag in a new window:", 'menu-test' ); ?> 

<select name="<?php echo $data2_field_name; ?>">
	<option value='yes' <?php if ($opt2_val == 'yes'){echo 'selected';} ?>>yes</option>
	<option value='no' <?php if ($opt2_val == 'no'){echo 'selected';} ?>>no</option>
</select>

</p>
<p><?php _e("Text to use as a title for the links:", 'menu-test' ); ?> 

<input type='test' name='<?php echo $data3_field_name; ?>' value='<?php echo $opt3_val; ?>' />

</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php
}

?>