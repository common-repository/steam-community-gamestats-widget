<?php
/*Plugin Name: Steam Commuity Widget
Plugin URI: http://nicholaspier.com
Description: An xml parser for steam community data.
Version: 1.02
Author: Nicholas Pier
Author URI: http://nicholaspier.com
*/

/*Widgets_init hook. */
add_action( 'widgets_init', 'initialize_widget' );
/* Function that registers widget. */
function initialize_widget() {
	register_widget( 'sc_widget' );

/* Steam Community Widget
*/	
}
class sc_widget extends WP_Widget {
	//Widget Processes
	function sc_widget() {
		$widget_ops = array( 'classname' => 'Steam Community Widget', 'description' => 'A Wordpress widget for displaying an individual\'s Steam statistics and account information.' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 480, 'height' => 300, 'id_base' => 'sc-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'sc-widget', 'Steam Community Widget', $widget_ops, $control_ops );
	}
	
	//Output Content
	function widget($args, $instance) {
		extract( $args );
		$ProfileURL = $instance['url'];
		$xml = simplexml_load_file($ProfileURL . '?xml=1');
		$steamID64 = $xml->steamID64;
        $title = apply_filters('widget_title', $instance['title']);
			//TITLE, if Provided
			echo $before_widget;
				if ( $title ){
					echo $before_title . $title . $after_title;
				}
				else {
					echo $before_title . $after_title;
				}
			//Begin Widget Content
					echo '<p><strong>SteamID:</strong> ';
					echo $xml->steamID . '<br /><strong>Member since:</strong> ';
					echo $xml->memberSince . '<br /><strong>Steam Rating:</strong> ';
					echo $xml->steamRating . '<br /><strong>Playing time:</strong> ';
					echo $xml->hoursPlayed2Wk . " hrs past 2 weeks</p>";
					for($i = 0; $i < 4; $i++){
						if ($xml->mostPlayedGames->mostPlayedGame[$i]->gameLink != "") {
							echo '<p><a href="' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameLink . '"><img src="' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameIcon . '" /></a> ' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameName . ' ' . $xml->mostPlayedGames->mostPlayedGame[$i]->hoursPlayed . ' hrs</p>';
						}
					}
					echo '<p><a href="steam://friends/add/' . $steamID64 . '" alt="Add to Friends">Add to Friends</a> | <a href="' . $ProfileURL . 'games">View all Games</a></p><!--<p align="center"><em>&copy ' . date(Y) . ' <a href="http://nicholaspier.com">nicholaspier.com</a></em></p>-->';			//End Widget Content
			echo $after_widget;
	}

	//Process Options to be saved
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
        return $instance;
	}
	
	//Options form on admin
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Steam Community Widget', 'url' => 'http://steamcommunity.com/profiles/76561197970479548/' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		
        ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>

			<p>Please enter the URL to your Steam Community webpage. It should look similar to this: <a>http://steamcommunity.com/profiles/**************/</a></p>
				<p>The asterisks represent your unique community identifier.</p>
				<label for="<?php echo $this->get_field_id( 'url' ); ?>">Profile URL:</label>
				<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:100%;" />
			</p>
			<p align="center"><em>&copy <?php echo date(Y); ?> <a href="http://nicholaspier.com"> nicholaspier.com</a></em></p>
		<?php 
	}
}
?>