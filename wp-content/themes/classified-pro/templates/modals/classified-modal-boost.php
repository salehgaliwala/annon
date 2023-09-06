<?php
defined( 'ABSPATH' ) || exit;

$type = get_query_var( 'type', 'post_id' );
$id = get_query_var( 'id', get_the_ID() );
if ( $type == 'user_id' ) {
	$title = classified_get_userdata( $id, 'name' );
	$content = classified_get_userdata( $id, 'role' );
}else {
	$title = get_the_title( $id );
	$content = get_post_type( $id );
}
?>
<div class="modal fade classified-boost-modal" id="classified-boost-modal-<?php echo absint( $id ) ?>" tabindex="-1"
     aria-labelledby="classified-boost-modal-<?php echo absint( $id ) ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <i class="fa-solid fa-xmark classified-close-modal" aria-hidden="true" data-bs-dismiss="modal"
               aria-label="Close"></i>
            <div class="modal-header">
                <?php echo sprintf( esc_html__( 'Boost %s', 'classified-pro' ), $title ); ?>
            </div>
            <div class="modal-body classified-frontend-form-container">
				<?php echo do_shortcode( '[cwpForm type="cwp_booster" content="booster_type_' . $content . '"]' ); ?>
            </div>
        </div>
    </div>
</div>
