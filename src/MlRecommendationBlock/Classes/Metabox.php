<?php

namespace MlRecommendationBlock\Classes;


class Metabox
{
    public static $form_attr_name = '_ml_recommendation';

    function __construct() {

        if ( current_user_can( 'publish_posts' ) ) {
            add_action( 'admin_init', [ $this, 'metabox_fields' ], 1 );
//			add_action( 'save_post', [ $this, 'fields_update' ], 0 );
        }

    }

    function metabox_fields() {
        $post_types = apply_filters( 'MlRecommendationBlock__post_types', [ 'post' ] );

        add_meta_box(
            'recommendation_block_metabox',
            __( 'Post visibility in recommendation block', 'MlRecommendationBlock' ),
            [ $this, 'fields_html' ],
            $post_types
            ,
            'side',
            'low'
        );
    }

    function fields_html( $post ) {
        $val = get_post_meta( $post->ID, self::$form_attr_name . '_flag', true );

        ?>
        <p>
            <label>
                <?php _e( 'Visibility', 'MlRecommendationBlock' ); ?>
                <select name="<?php echo self::$form_attr_name; ?>[<?php echo self::$form_attr_name . '_flag'; ?>]">
                    <option value="default" <?php selected( $val, 'default', true ); ?> >
                        <?php _e( 'default', 'MlRecommendationBlock' ); ?>
                    </option>
                    <option value="include" <?php selected( $val, 'include', true ); ?> >
                        <?php _e( 'include', 'MlRecommendationBlock' ); ?>
                    </option>
                    <option value="exclude" <?php selected( $val, 'exclude', true ); ?> >
                        <?php _e( 'exclude', 'MlRecommendationBlock' ); ?>
                    </option>
                </select>
            </label>
        </p>
        <input type="hidden"
               name="<?php echo self::$form_attr_name; ?>_fields_nonce"
               value="<?php echo wp_create_nonce( __FILE__ ); ?>"/>
        <?php
    }

    function fields_update( $post_id ) {
        if ( is_array( $_POST ) && array_key_exists( self::$form_attr_name . '_fields_nonce', $_POST )
            && ! wp_verify_nonce( $_POST[ self::$form_attr_name . '_fields_nonce' ], __FILE__ ) ) {
            return false;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }
        if ( ! isset( $_POST[ self::$form_attr_name ] ) ) {
            return false;
        };

        $_POST[ self::$form_attr_name ] = array_map( 'trim', $_POST[ self::$form_attr_name ] );

        foreach ( $_POST[ self::$form_attr_name ] as $key => $value ) {
            if ( empty( $value ) ) {
                delete_post_meta( $post_id, $key );
                continue;
            }
            update_post_meta( $post_id, $key, $value );
        }

        return $post_id;
    }
}