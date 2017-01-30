<?php

namespace JodyBoucher\WordPress\Plugins;

use WP_Widget;

/**
 * (j9r) Text widget
 *
 * The (j9r) Text widget is an enhanced version of the built-in WordPress Text widget.
 *
 * @wordpress-plugin
 * Plugin Name:  (j9r) Text Widget
 * Plugin URI:   https://github.com/jodyboucher/wp-widget-text
 * Description:  An enhanced version of the built-in text widget.
 * Author:       Jody Boucher
 * Author URI:   https://jodyboucher.com
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:  /languages
 * Text Domain:  j9r-text
 */
class TextWidget
	extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructs a new Text widget instance.
	 */
	public function __construct() {
		$this->defaults = array(
			'title'             => '',
			'titleShow'         => true,
			'titleUrl'          => '',
			'titleNewWindow'    => true,
			'textShowEmpty'     => false,
			'textAddParagraphs' => false,
			'textWrapCss'       => 'textwidget'
		);

		$widget_ops  = array(
			'classname'   => 'widget_text',
			'description' => __( 'Arbitrary text or HTML.', 'j9r-text' )
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );
		parent::__construct( 'j9r_text', __( '(j9r) Text', 'j9r-text' ), $widget_ops, $control_ops );
		\load_plugin_textdomain( 'j9r-text', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Outputs the content for the current Text widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {
		/**
		 * Filters the widget title.
		 *
		 * @param string $title    The widget title. Default 'Pages'.
		 * @param array  $instance An array of the widget's settings.
		 * @param mixed  $id_base  The widget ID.
		 */
		$title          = \apply_filters( 'widget_title',
		                                  empty( $instance['title'] ) ? '' : $instance['title'],
		                                  $instance,
		                                  $this->id_base
		);
		$titleShow      = ! empty( $instance['titleShow'] );
		$titleUrl       = empty( $instance['titleUrl'] ) ? '' : $instance['titleUrl'];
		$titleNewWindow = ! empty( $instance['titleNewWindow'] );

		/**
		 * Filters the content of the Text widget.
		 *
		 * @param string          $widget_text The widget content.
		 * @param array           $instance    Array of settings for the current widget.
		 * @param \WP_Widget_Text $this        Current Text widget instance.
		 */
		$text              = \apply_filters( 'widget_text',
		                                     empty( $instance['text'] ) ? '' : $instance['text'],
		                                     $instance,
		                                     $this
		);
		$textShowEmpty     = ! empty( $instance['textShowEmpty'] );
		$textAddParagraphs = ! empty( $instance['textAddParagraphs'] );
		$textWrapCss       = empty( $instance['textWrapCss'] ) ? '' : $instance['textWrapCss'];

		echo $args['before_widget'];
		if ( ! empty( $title ) && $titleShow ) {
			echo $args['before_title'];
			if ( ! empty( $titleUrl ) ) {
				$linkTarget = $titleNewWindow ? "target='_blank'" : '';
				$title      = "<a href='$titleUrl' $linkTarget>$title</a>";
			}
			echo $title;
			echo $args['after_title'];
		}

		if ( ! empty( $text ) || $textShowEmpty ) {
			$wrapperCss = empty( $textWrapCss ) ? '' : 'class="' . $textWrapCss . '"';
			echo "<div $wrapperCss>";
			echo $textAddParagraphs ? \wpautop( $text ) : $text;
			echo '</div>';
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Text widget instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']          = \sanitize_text_field( $new_instance['title'] );
		$instance['titleShow']      = isset( $new_instance['titleShow'] );
		$instance['titleUrl']       = \esc_url_raw( strip_tags( $new_instance['titleUrl'] ) );
		$instance['titleNewWindow'] = isset( $new_instance['titleNewWindow'] );

		if ( \current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = \wp_filter_post_kses( $new_instance['text'] );
		}

		$instance['textShowEmpty']     = isset( $new_instance['textShowEmpty'] );
		$instance['textAddParagraphs'] = isset( $new_instance['textAddParagraphs'] );
		$instance['textWrapCss']       = \sanitize_text_field( $new_instance['textWrapCss'] );

		return $instance;
	}

	/**
	 * Outputs the Text widget settings form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance          = \wp_parse_args( (array) $instance, $this->defaults );
		$title             = isset( $instance['title'] ) ? $instance['title'] : $this->defaults['title'];
		$titleUrl          = isset( $instance['titleUrl'] ) ? $instance['titleUrl'] : $this->defaults['titleUrl'];
		$titleShow         = isset( $instance['titleShow'] ) ? $instance['titleShow'] : $this->defaults['titleShow'];
		$text              = \format_to_edit( $instance['text'] );
		$titleNewWindow    = isset( $instance['titleNewWindow'] )
			? $instance['titleNewWindow']
			: $this->defaults['titleNewWindow'];
		$textShowEmpty     = isset( $instance['textShowEmpty'] )
			? $instance['textShowEmpty']
			: $this->defaults['textShowEmpty'];
		$textAddParagraphs = isset( $instance['textAddParagraphs'] )
			? $instance['textAddParagraphs']
			: $this->defaults['textAddParagraphs'];
		$textWrapCss       = isset( $instance['textWrapCss'] )
			? $instance['textWrapCss']
			: $this->defaults['textWrapCss'];
		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php \_e( 'Title', 'j9r-text' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>"
                   class="widefat"
                   type="text"
                   value="<?php echo \esc_attr( $title ); ?>"
            />
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'titleShow' ); ?>"
                   name="<?php echo $this->get_field_name( 'titleShow' ); ?>"
                   type="checkbox" <?php checked( $titleShow ); ?>
            />
            <label for="<?php echo $this->get_field_id( 'titleShow' ); ?>">
				<?php \_e( 'Display the title', 'j9r-text' ); ?>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'titleUrl' ); ?>">
				<?php \_e( 'Title URL', 'j9r-text' ); ?>:
            </label>
            <input id="<?php echo $this->get_field_id( 'titleUrl' ); ?>"
                   name="<?php echo $this->get_field_name( 'titleUrl' ); ?>"
                   class="widefat"
                   type="text"
                   value="<?php echo \esc_url( $titleUrl ); ?>"
            />
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'titleNewWindow' ); ?>"
                   name="<?php echo $this->get_field_name( 'titleNewWindow' ); ?>"
                   type="checkbox" <?php \checked( $titleNewWindow ); ?> />
            <label for="<?php echo $this->get_field_id( 'titleNewWindow' ); ?>">
				<?php \_e( 'Open the title URL in a new window', 'j9r-text' ); ?>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content', 'j9r-text' ); ?>:</label>
            <textarea id="<?php echo $this->get_field_id( 'text' ); ?>"
                      name="<?php echo $this->get_field_name( 'text' ); ?>"
                      class="widefat"
                      rows="16"
                      cols="20">
                <?php echo \esc_textarea( $text ); ?>
            </textarea>
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'textShowEmpty' ); ?>"
                   name="<?php echo $this->get_field_name( 'textShowEmpty' ); ?>"
                   type="checkbox" <?php \checked( isset( $textShowEmpty ) ); ?> />
            <label for="<?php echo $this->get_field_id( 'textShowEmpty' ); ?>">
				<?php \_e( 'Display widget when content is empty', 'j9r-text' ); ?>
            </label>
        </p>
        <p>
            <input id="<?php echo $this->get_field_id( 'textAddParagraphs' ); ?>"
                   name="<?php echo $this->get_field_name( 'textAddParagraphs' ); ?>"
                   type="checkbox" <?php \checked( $textAddParagraphs ); ?> />
            <label for="<?php echo $this->get_field_id( 'textAddParagraphs' ); ?>">
				<?php \_e( 'Automatically add paragraphs to the content', 'j9r-text' ); ?>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'textWrapCss' ); ?>">
				<?php \_e( 'Wrapper Css', 'j9r-text' ); ?>:
            </label>
            <input id="<?php echo $this->get_field_id( 'textWrapCss' ); ?>"
                   name="<?php echo $this->get_field_name( 'textWrapCss' ); ?>"
                   class="widefat"
                   type="text"
                   value="<?php echo \esc_attr( $textWrapCss ); ?>"
            />
        </p>
		<?php
	}
}

/**
 * Register the widget
 */
function text_widget_init() {
	\register_widget( 'JodyBoucher\WordPress\Plugins\TextWidget' );
}

\add_action( 'widgets_init', 'JodyBoucher\WordPress\Plugins\text_widget_init' );
