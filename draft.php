<?php

/**
 * Get Content
 * Widget
 */

class nh_widget_content extends WP_Widget {

	/**
	 * Register widget
	 */

	function __construct() {
		parent::__construct(
			'nh_widget_content', // Base ID
			__( 'Show content', 'nh'), // Name
			array( 'description' => __( 'Show latest content site.', 'nh' ), ) // Args
		);
	}

	/**
	 * Widget
	 */

	public function widget( $args, $instance ) {

		// Polylang translation support
		$instance = $this->widget_translate( $instance );

		// Fields
		$title = apply_filters( 'widget_title', $instance['title'] );
		$hide__title = apply_filters( 'widget_title', $instance['hide__title'] );
		$content = apply_filters( 'widget_title', $instance['content'] );
		$posttype = apply_filters( 'widget_title', $instance['posttype'] );
		$taxonomy_terms = apply_filters( 'widget_title', $instance['taxonomy_terms'] );
		$limit = apply_filters( 'widget_title', $instance['limit'] );
		$page = apply_filters( 'widget_title', $instance['page'] );

		// Get content
		$posts = array();
		$posts['post_type'] = $posttype;
		$posts['posts_per_page'] = $limit;
		if ( $taxonomy_terms != '0' ) $posts['status'] = $taxonomy_terms;
		if ( isset( $instance['lang'] ) ) $posts['lang'] = $instance['lang'];
		$posts = new WP_Query( $posts );

		// Widget contents
		if ( $posts->have_posts() ) {

			// Widget prefix
			echo $args['before_widget'];

			// Title
			if ( ! empty( $title ) && 'no' == $hide__title )
				echo $args['before_title'] . $title . $args['after_title'];

			// Content
			if ( ! empty( $content ) )
				echo '<p class="get-content__content">' . $content . '</p>';

			// Profile exception
			// TODO: more flexible implementation
			if ( 'nh_profiel' == $posttype )
				echo '<ul class="case-contributors-list contributors-list -case">';
			else
				echo '<div class="get-content__items">';

			// Content items
			while ( $posts->have_posts() ) {
				$posts->the_post();
				get_template_part( 'content', get_post_type() );
			}

			// Profile exception
			// TODO: more flexible implementation
			if ( 'nh_profiel' == $posttype )
				echo '</ul><!-- .case-contributors-list .contributors-list .-case -->';
			else
				echo '</div><!-- .get-content__items -->';

			// Reset post data
			wp_reset_postdata();

			// Button
			if ( 'none' != $page )
				echo '<div class="more"><a href="' . get_permalink( $page ) . '" class="button -light -wider -center">' . __( 'More' ) . ' ' . $title . ' &rarr;</a></div>';

			// Widget suffix
			echo $args['after_widget'];
		}
	}

	/**
	 * Widget Translate
	 */

	private function widget_translate( $instance ) {

		// Polylang is not installed
		if ( ! function_exists( 'pll_current_language' ) )
			return $instance;

		// This post type is not managed by Polylang
		if ( ! pll_is_translated_post_type( $instance['posttype'] ) ) {
			$instance['lang'] = '';
			return $instance;
		}

		// Set query language filter
		$instance['lang'] = pll_current_language();

		// Current language is default, no further translation needed
		if ( pll_current_language() == pll_default_language() )
			return $instance;

		// Translate taxonomy terms when needed
		if ( '0' != $instance['taxonomy_terms'] ) {
			$term_translation = get_term_by( 'slug', $instance['taxonomy_terms'], 'status' );
			$term_translation = pll_get_term( $term_translation->term_id );
			$term_translation = get_term_by( 'id', $term_translation, 'status' );
			$instance['taxonomy_terms'] = $term_translation->slug;
		}

		// Translate page permalink when needed
		if ( 'none' != $instance['page'] ) $instance['page'] = pll_get_post( $instance['page'] );

		// Done, return instance
		return $instance;
	}

	/**
	 * Admin form
	 */

	public function form( $instance ) {

		// Fields
		$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Title' );
		$hide__title = ( isset( $instance[ 'hide__title' ] ) ) ? $instance[ 'hide__title' ] : __( 'Hide title', 'nh' );
		$content = ( isset( $instance[ 'content' ] ) ) ? $instance[ 'content' ] : __( 'Content' );
		$posttype = ( isset( $instance[ 'posttype' ] ) ) ? $instance[ 'posttype' ] : __( 'Posttype', 'nh' );
		$taxonomy_terms = ( isset( $instance['taxonomy_terms'] ) ) ? $instance[ 'taxonomy_terms' ] : __( 'Terms', 'nh' );
		$limit = ( isset( $instance[ 'limit' ] ) ) ? $instance[ 'limit' ] : __( 'Limit', 'nh' );

		// Generate options
		$pages = $this->field_pages( $instance );
		$posttypes = $this->field_posttypes( $instance );
		$taxonomy_terms_options = $this->field_taxonomy_terms( $instance );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?>:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'hide__title' ); ?>" name="<?php echo $this->get_field_name( 'hide__title' ); ?>" type="checkbox" value="yes" <?php if ( $hide__title == 'yes' ) echo 'checked'; ?> />
			<label for="<?php echo $this->get_field_id( 'hide__title' ); ?>"><?php _e( 'Hide title', 'nh' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content' ); ?>:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text" value="<?php echo esc_attr( $content ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Posttype' ); ?>:</label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'posttype' ); ?>" name="<?php echo $this->get_field_name( 'posttype' ); ?>">
			<?php echo $posttypes; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy_terms' ); ?>"><?php _e( 'Terms' ); ?>:</label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy_terms' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy_terms' ); ?>">
			<?php echo $taxonomy_terms_options; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit' ); ?>:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Page' ); ?>:</label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'page' ); ?>" name="<?php echo $this->get_field_name( 'page' ); ?>">
			<?php echo $pages; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Field
	 * Options: Pages
	 */

	private function field_pages( $instance ) {

		// Get pages
		$pages = $this->get_pages();

		// Create options
		$options = '';

		// No page selected
		if ( ! isset( $instance['page'] ) ) {
			$options .= '<option disabled selected>' . __( 'Select page', 'nh' ) . '</option>';
		}

		// Option: None
		$options .= '<option value="none"';
		if ( 'none' == $instance['page'] )
			$options .= ' selected';
		$options .= '>' . __( 'None', 'nh' ) . '</option>';

		// Generate options
		foreach ($pages as $page) {
			$options .= '<option value="' . $page->ID . '"';
			$options .= ( $page->ID == $instance['page'] ) ? ' selected>' : '>';
			$options .= $page->post_title . '</option>';
		}

		// Return options
		return $options;
	}

	// Helper: Get pages

	private function get_pages() {
		$pages = array(
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'parent' => -1,
			'post_type' => 'page',
			'post_status' => 'publish'
		);
		return get_pages($pages);
	}

	/**
	 * Field
	 * Options: Post Types
	 */

	private function field_posttypes( $instance ) {

		// Get post types
		$types = $this->get_post_types();

		// Create options
		$options = '';

		// No post type selected
		if ( ! isset( $instance['posttype'] ) ) {
			$options .= '<option disabled selected>' . __( 'Select post type', 'nh' ) . '</option>';
		}

		// Generate options
		foreach ($types as $key => $item) {
			$options .= '<option value="' . $key . '"';
			$options .= ( $key == $instance['posttype'] ) ? ' selected>' : '>';
			$options .= $item->labels->name . '</option>';
		}

		// Return options
		return $options;
	}

	// Helper: Get post types

	private function get_post_types() {
		return get_post_types(
			array(
				'public' => true,
				'show_ui' => true
				),
			'objects'
			);
	}

	/**
	 * Field
	 * Options: Taxonomy terms
	 */

	private function field_taxonomy_terms( $instance ) {

		// Get terms
		$terms = $this->get_taxonomy_terms();

		// Create options
		$options = '';

		// No term
		$options .= '<option value="0"';
		$options .= ( ! isset( $instance['taxonomy_terms'] ) || $instance['taxonomy_terms'] == 0 ) ? ' selected>' : '>';
		$options .= __( 'None' ) . '</option>';

		// Generate options
		foreach ($terms as $key => $item) {
			$options .= '<option value="' . $key . '"';
			$options .= ( $key == $instance['taxonomy_terms'] ) ? ' selected>' :  '>';
			$options .= $item . '</option>';
		}

		// Return options
		return $options;
	}

	// Helper: Get taxonomy terms

	private function get_taxonomy_terms() {

		// Get taxonomies
		$taxonomies = get_categories( array(
			'taxonomy' => 'status',
			'orderby' => 'name',
			) );

		// Create terms
		$terms = array();

		// Generate terms
		foreach ($taxonomies as $item) {

			// Fields
			$terms[ $item->slug ] = $item->name;
		}

		// Return terms
		return $terms;
	}

	/**
	 * Update data
	 */

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['hide__title'] = ( ! empty( $new_instance['hide__title'] ) ) ? 'yes' : 'no';
		$instance['content'] = ( ! empty( $new_instance['content'] ) ) ? strip_tags( $new_instance['content'] ) : '';
		$instance['posttype'] = ( ! empty( $new_instance['posttype'] ) ) ? strip_tags( $new_instance['posttype'] ) : '';
		$instance['taxonomy_terms'] = ( !  empty( $new_instance['taxonomy_terms'] ) ) ? strip_tags( $new_instance['taxonomy_terms'] ) : '0';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';
		$instance['page'] = ( ! empty( $new_instance['page'] ) ) ? strip_tags( $new_instance['page'] ) : '';
		return $instance;
	}
}

// Register widgets
function nh_theme_widgets() {
	register_widget( 'nh_widget_content' );
	register_widget( 'nh_widget_filter_content' );
	register_widget( 'nh_widget_icon_text' );
	register_widget( 'nh_widget_section' );
}
add_action( 'widgets_init', 'nh_theme_widgets' );

