<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE.PHP
// -----------------------------------------------------------------------------
// Plugin options page.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Permissions Check
//   02. Require Options
//   03. Options Page Output
// =============================================================================

// Permissions Check
// =============================================================================

if ( ! current_user_can( 'manage_options' ) ) {
  wp_die( 'You do not have sufficient permissions to access this page.' );
}



// Require Options
// =============================================================================

require( X_UNDER_CONSTRUCTION_PATH . '/functions/options.php' );



// Options Page Output
// =============================================================================

//
// Setup array of all pages.
//

$x_under_construction_list_entries_args   = array( 'posts_per_page' => -1 );
$x_under_construction_list_entries        = get_pages( $x_under_construction_list_entries_args );
$x_under_construction_list_entries_master = array();

foreach ( $x_under_construction_list_entries as $post ) {
  $x_under_construction_list_entries_master[$post->ID] = $post->post_title;
}

asort( $x_under_construction_list_entries_master );
?>

<div class="wrap x-plugin x-comments">
  <h2><?php _e( 'Under Construction', '__x__' ); ?></h2>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <form name="x_under_construction_form" method="post" action="">
        <input name="x_under_construction_form_submitted" type="hidden" value="submitted">

        <?php require( 'options-page-main.php' ); ?>
        <?php require( 'options-page-sidebar.php' ); ?>

      </form>
    </div>
    <br class="clear">
  </div>
</div>
