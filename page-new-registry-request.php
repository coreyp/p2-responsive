<?php
/**
 * Template Name: New Registry Request
 *
 * @subpackage P2
 */
 
acf_form_head(); 
 
get_header(); ?>

<?php global $current_user;
      get_currentuserinfo();
?>

<div id="header">

    <div class="sleeve">
        <h1>New Reporters' Registry Request</h1>
        <?php // if ( get_bloginfo( 'description' ) ) : ?>
            <small>Welcome <?php echo $current_user->user_login; ?> | <a href="/">Back to No Noise News</a> | <a href="/room/">Join the News/room</a> | 
        <?php // endif; ?>
        <a class="secondary" href="#"><a href="/room/wordpress/wp-login.php?action=lostpassword">Lost Password?</a> | <a href="<?php echo wp_logout_url(); ?>">Log Out and Cancel</a></small></a>
    </div>

</div>

<div id="wrapper">
<!-- above was header -->
<!-- the_post(); ?> -->

	<div id="primary">
		<div id="content" role="main">
 
<?php 
$options = array(
    'post_id' => 'new', // post id to get field groups from and save data to
    'field_groups' => array(70), // this will find the field groups for this post (post ID's of the acf post objects)
    'form' => true, // set this to false to prevent the <form> tag from being created
    /* 'form_attributes' => array( // attributes will be added to the form element
        'id' => 'post',
        'class' => '',
        'action' => '',
        'method' => 'post',
    ),*/
    /* 'return' => add_query_arg( 'updated', 'true', get_permalink() ), // return url */
    'return' => '/thanks/', 
    'html_before_fields' => '', // html inside form before fields
    'html_after_fields' => '', // html inside form after fields
    'submit_value' => 'Submit request', // value for submit field
    'updated_message' => 'Request sent. An editor will review it shortly.<p>You may make another request below.</p>', // default updated message. Can be false to show no message
);
?>

			<?php // the_post(); ?>
 
			<?php acf_form( $options ); ?>
 
		</div><!-- #content -->
	</div><!-- #primary -->
 
<?php // get_sidebar(); ?>
<?php // get_footer(); ?>
