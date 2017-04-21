<?php
/**
 * Add the DIY box to the content and style it
 *
 */

$settings         = get_option( 'diy-settings' );

$background_color = ( isset( $settings['background_color'] ) && ! empty( $settings['background_color'] ) ) ? $settings['background_color'] : '#fff';

?>

<style type="text/css">

	.diy-posts-container {
		margin-bottom: 40px;
		padding: 30px;
		height: auto;
		width: auto;
		font-size: inherit;
		font-family: inherit;
		background-color: <?php echo esc_attr( $background_color ) ?>;
	}

	.diy-posts-container h2 {
		color: inherit;
	}

	.diy-posts-container ul {
		padding-left: 20px;
	}

	.diy-posts-container ul li {
		padding-bottom: 5px;
		color: inherit;
	}


</style>

<?php

/**
 * Filter the WordPress content and add our DIY custom fields 
 * above the post content
 *
 * @param $content
 *
 * @filter the_content
 *
 * @return $content
 */
function diy_filter_content( $content ) {

	global $post;

	$post_id = $post->ID;

	$time   = get_post_custom_values( 'diy_time', $post_id );

	$cost   = get_post_custom_values( 'diy_cost', $post_id );

	$rating = get_post_custom_values( 'diy_rating', $post_id );

	if ( ! is_single() ) {

		return;

	}

	if ( ! isset( $time ) || ! isset( $cost ) || ! isset( $rating ) ) {

        return;

    }

	// Add DIY post meta to top of content
    $content = sprintf(

        '<div class="diy-posts-container">

        	<h2>DIY Job Requirements</h2>

        	<ul>

        		<li>Time Required : %s</li>

        		<li>Cost : %s</li>

        		<li>Skill Level : %s</li>

        	</ul>

        </div>%s',

        $time[0],

        $cost[0],

        $rating[0],

        $content

    );

	return $content;

}
add_filter( 'the_content', 'diy_filter_content' );
