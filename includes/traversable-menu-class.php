<?php

/**
 * Adds Foo_Widget widget.
 */
class Traversable_Menu_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$widget_ops = array(
			'description'                 => __( 'Add a navigation menu to your sidebar.' ),
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		);
		parent::__construct(
			'traversable_menu_widget', // Base ID
			esc_html__( 'Traversable Menu Title', 'tm_domain' ), // Name
			array( 'description' => esc_html__( 'A Traversable Menu Widget', 'tm_domain' ), ),
			'nav_menu',
			__( 'Navigation Menu' ),
			$widget_ops // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */

	public function travMenuDataPrepare ($items) {
		$travMenuData = array();
		$belowElements = array();

		for($i = 0; $i < count( $items ); $i++ ){

			$tmp = array();
			
				if( isset ($items[ $i + 1 ] ) && $items[ $i ]->ID == $items[ $i + 1 ]->menu_item_parent ){
					for ( $j = $i; $j < count( $items ); $j++ ) {
						if( isset( $items[ $j + 1 ]) && $items[ $i ]->ID == $items[ $j + 1 ]->menu_item_parent ) {

							array_push($tmp, $items[ $j + 1 ]);
						}
						
					}
	
				}

			$items[ $i ]->below = $tmp;
			array_push( $belowElements, $items[ $i ] ); 

		}


		for( $i = 0; $i < count( $belowElements ); $i++ ) {

			if( $belowElements[ $i ]->menu_item_parent == 0) {
				array_push( $travMenuData, $belowElements[ $i ]);
			}

		}


	 return $travMenuData;
	}
	
	public function traversMenuTemplate( $items, $menu_lvl ){
		

		if ( $items ){

			if ($menu_lvl == 0) {

				echo'<div class="traversable-menu">
						<div class="menu__panel">
						<div class="menu__panel__title"><!-- automatically replaced with javascript --></div>
						<ul>';
			} else {
				echo '<ul>';
			}
				for ( $i=0; $i < count( $items ); $i++ ){

					echo '<li class="menu__item">
						<a href="#/about" class="menu__item__link">' . $items[ $i ]->title . '</a>';
						$isset = isset( $items[ $i + 1 ]->menu_item_parent);
						
						if( isset( $items[$i]->below )  && !empty( $items[$i]->below ) ){
							
						echo'<a href="#" class="menu__panel__trigger--child">Explore ></a>
							<div class="menu__panel">
								<div class="menu__panel__title"></div>
								<a href="#" class="menu__panel__trigger--top"></a>
								<a href="#" class="menu__panel__trigger--parent">Up a level (this gets replaced in JS)</a>';
				
								$this->traversMenuTemplate( $items[$i]->below, $menu_lvl + 1 );
								
						echo '</div>';
						}
						echo '</li>';
						
				}
				
			echo'</ul>';
			
		}

	}
	
	public function widget( $args, $instance ) {

		// Get menu.
		$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		// $default_title = __( 'Menu' );
		// $title         = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		// $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo '<div style="width: 100%;">';

		// if ( $title ) {
		// 	echo $args['before_title'] . $title . $args['after_title'];
		// }

		// $format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

		
		/**
		 * Filters the HTML format of widgets with navigation links.
		 *
		 * @since 5.5.0
		 *
		 * @param string $format The type of markup to use in widgets with navigation links.
		 *                       Accepts 'html5', 'xhtml'.
		 */
		// $format = apply_filters( 'navigation_widgets_format', $format );

		// echo $format;

		// if ( 'html5' === $format ) {
		// 	// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
		// 	$title      = trim( strip_tags( $title ) );
		// 	$aria_label = $title ? $title : $default_title;

		// 	$nav_menu_args = array(
		// 		'fallback_cb'          => '',
		// 		'menu'                 => $nav_menu,
		// 		'container'            => 'nav',
		// 		'container_aria_label' => $aria_label,
		// 		'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		// 	);
		// } else {
		// 	$nav_menu_args = array(
		// 		'fallback_cb' => '',
		// 		'menu'        => $nav_menu,
		// 	);
		// }

		/**
		 * Filters the arguments for the Navigation Menu widget.
		 *
		 * @since 4.2.0
		 * @since 4.4.0 Added the `$instance` parameter.
		 *
		 * @param array   $nav_menu_args {
		 *     An array of arguments passed to wp_nav_menu() to retrieve a navigation menu.
		 *
		 *     @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
		 *     @type mixed         $menu        Menu ID, slug, or name.
		 * }
		 * @param WP_Term $nav_menu      Nav menu object for the current menu.
		 * @param array   $args          Display arguments for the current widget.
		 * @param array   $instance      Array of settings for the current widget.
		 */
		// wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );

	
		$menuItems = wp_get_nav_menu_items( $nav_menu );

		if( !empty( $menuItems ) ){

			$travMenuData = $this->travMenuDataPrepare($menuItems);

			$this->traversMenuTemplate( $travMenuData, 0 );


		} else {
			echo 'There are no menu selected';
		}
		
		echo '</div>';
	}

	/**
	 * Handles updating settings for the current Navigation Menu widget instance.
	 *
	 * @since 3.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function form( $instance ) {
		global $wp_customize;
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

		// Get menus.
		$menus = wp_get_nav_menus();

		$empty_menus_style     = '';
		$not_empty_menus_style = '';
		if ( empty( $menus ) ) {
			$empty_menus_style = ' style="display:none" ';
		} else {
			$not_empty_menus_style = ' style="display:none" ';
		}

		$nav_menu_style = '';
		if ( ! $nav_menu ) {
			$nav_menu_style = 'display: none;';
		}

		// If no menus exists, direct the user to go and create some.
		?>
		<p class="nav-menu-widget-no-menus-message" <?php echo $not_empty_menus_style; ?>>
			<?php
			if ( $wp_customize instanceof WP_Customize_Manager ) {
				$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
			} else {
				$url = admin_url( 'nav-menus.php' );
			}

			/* translators: %s: URL to create a new menu. */
			printf( __( 'No menus have been created yet. <a href="%s">Create some</a>.' ), esc_attr( $url ) );
			?>
		</p>
		<div class="nav-menu-widget-form-controls" <?php echo $empty_menus_style; ?>>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php _e( 'Select Menu:' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>" name="<?php echo $this->get_field_name( 'nav_menu' ); ?>">
					<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
					<?php foreach ( $menus as $menu ) : ?>
						<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $nav_menu, $menu->term_id ); ?>>
							<?php echo esc_html( $menu->name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php if ( $wp_customize instanceof WP_Customize_Manager ) : ?>
				<p class="edit-selected-nav-menu" style="<?php echo $nav_menu_style; ?>">
					<button type="button" class="button"><?php _e( 'Edit Menu' ); ?></button>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}
		if ( ! empty( $new_instance['nav_menu'] ) ) {
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		}
		return $instance;
	}

} // class Foo_Widget

