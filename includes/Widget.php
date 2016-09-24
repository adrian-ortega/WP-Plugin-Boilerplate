<?php

namespace AOD;

class Widget extends \WP_Widget {
    protected $widgetName;
    protected $widgetId;
    protected $widgetDescription;

    /**
     * @var Container
     */
    protected $container;

    public function __construct() {
        parent::__construct(
            $this->widgetId,
            $this->widgetName,
            [
                'classname'   => "{$this->widgetId} gck-widget",
                'description' => $this->widgetDescription
            ]
        );
    }

    /**
     * Sets the container
     * @param Container $container
     */
    public function setContainer( Container $container ) {
        $this->container = $container;
    }

    /**
     * Sets the widget name
     * @param string $name
     * @return $this
     */
    public function setName( $name ) {
        $this->widgetName = sprintf( __( 'GC - %s', $this->textDomain() ), $name );
        $this->widgetId   = 'gck-' . sanitize_title( $name );

        return $this;
    }

    /**
     * Sets the widget's description
     * @param string $description
     * @return $this
     */
    public function setDescription( $description ) {
        $this->widgetDescription = $description;

        return $this;
    }

    /**
     * Returns the text domain for the plugin
     * @return mixed|null
     */
    public function textDomain() {
        return $this->container->get( 'localization' )->getDomain();
    }

    ////////////////////////////////////////////////////////////////////////
    // Just in case
    ////////////////////////////////////////////////////////////////////////

    /**
     * This is meant to be overridden by the extending class
     * @param array $instance
     * @return void
     */
    public function form( $instance ) {
        echo '<p>Looks like you forgot to over-ride the `GcKit\\Widget::form()` method in the `' . get_called_class() . '` class.</p>';
    }

    /**
     * This is meant to be overridden by the extending class
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance ) {
        echo '<p>Looks like you forgot to over-ride the `GcKit\\Widget::widget()` method in the `' . get_called_class() . '` class.</p>';
    }

    ////////////////////////////////////////////////////////////////////////
    //  HTML Inputs
    ////////////////////////////////////////////////////////////////////////

    /**
     * Returns an HTML Label element
     * @param string $id
     * @param string $label
     * @return string
     */
    public function label( $id, $label ) {
        $id    = $this->get_field_id( $id );
        $label = sprintf( __( '%s', $this->textDomain() ), $label );

        return "<label for=\"{$id}\">{$label}</label>";
    }

    /**
     * Returns a paragraph HTMl Element with a help class to match
     * WordPress's admin styling
     * @param string $text
     * @param string $arrow
     * @return string
     */
    public function helpBlock( $text = '', $arrow = 'up' ) {
        $html = '<p class="help">';
        $html .= '<small>';
        if ( $arrow !== false ) {
            $html .= "<span class=\"dashicons dashicons-arrow-{$arrow}\"></span>";
        }
        $html .= $text;
        $html .= '</small>';
        $html .= '</p>';

        return $html;
    }

    /**
     * Returns a text input HTML box
     * @param string $_id
     * @param string $value
     * @param string $class
     * @param string $type
     * @return string
     */
    public function textInput( $_id, $value, $class = "", $type = 'text' ) {
        $class = implode( " ", array_merge( [ 'widefat' ], (array) $class ) );
        $id    = $this->get_field_id( $_id );
        $name  = $this->get_field_name( $_id );

        return "<input type=\"{$type}\" class=\"{$class}\" id=\"{$id}\" name=\"{$name}\" value=\"{$value}\">";
    }

    /**
     * Returns a textarea HTMl input box
     * @param string $_id
     * @param string $value
     * @param string $class
     * @return string
     */
    public function textareaInput( $_id, $value, $class = '' ) {
        $class = implode( " ", array_merge( [ 'widefat' ], (array) $class ) );
        $id    = $this->get_field_id( $_id );
        $name  = $this->get_field_name( $_id );

        return "<textarea class=\"{$class}\" id=\"$id\" name=\"$name\" rows=\"7\">{$value}</textarea>";
    }

    /**
     * Returns a hidden input HTML element
     * @param string $id
     * @param string $value
     * @return string
     */
    public function hiddenInput( $id, $value ) {
        return $this->textInput( $id, $value, 'hidden' );
    }

    /**
     * Returns an HTML select dropdown element
     * @param string $_id
     * @param int|string $value
     * @param array $options
     * @return string
     */
    public function dropdownInput( $_id, $value = 0, $options = [] ) {
        $id   = $this->get_field_id( $_id );
        $name = $this->get_field_name( $_id );
        $html = "<select name=\"{$name}\" id=\"{$id}\">";
        foreach ( $options as $ovalue => $otext ) {
            $selected = selected( $ovalue, $value, false );
            $html .= "<option value=\"{$ovalue}\"{$selected}>{$otext}</option>";
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Returns the HTML for the upload media elements
     * @param $instance
     */
    public function imageUpload( $instance ) {
        ?>
        <div data-gck-upload-image>
            <p>
                <button class="button gck-image-upload"><?php _e( 'Select an Image' ) ?></button>
            </p>
            <div class="image-preview">
                <?php if ( $instance['image_url'] && $instance['attachment_id'] ): ?>
                    <?php $attachment = wp_get_attachment_metadata( $instance['attachment_id'] ) ?>
                    <div class="image-preview-inner gck-image-upload">
                        <img src="<?php echo $instance['image_url'] ?>" alt="">
                        <div class="image-info">
                            width: <?php echo $attachment['width'] ?>
                            height: <?php echo $attachment['height'] ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
            <?php echo $this->hiddenInput( 'attachment_id', $instance['attachment_id'] ) ?>
            <?php echo $this->hiddenInput( 'image_url', $instance['image_url'] ) ?>
        </div>
        <?php
    }

    /**
     * Returns a checkbox or radio input HTML element
     * @param string $_id
     * @param string $label
     * @param int|string $current
     * @param string $type
     * @return string
     */
    public function selectionInput( $_id, $label, $current, $type = 'checkbox' ) {
        if ( is_array( $_id ) ) {
            $id   = $this->get_field_id( $_id[1] );
            $name = $this->get_field_name( $_id[0] );
        } else {
            $id   = $this->get_field_id( $_id );
            $name = $this->get_field_name( $_id );
        }

        if(is_array($current)) {
            $current = $current[1];
            $value = $current[0];
        } else {
            $value = 1;
        }

        $checked = checked( $current, $value, false );

        $html = "<label for=\"{$id}\">";
        $html .= "<input type=\"{$type}\" id=\"{$id}\" name=\"{$name}\" value=\"{$value}\" {$checked}>";
        $html .= "<span>{$label}</span>";
        $html .= "</label>";

        return $html;
    }

    /**
     * Returns html for a set of checkboxes or radio inputs to select from
     * @param string $_id
     * @param array $options
     * @param array $selected
     * @param string $type
     * @return string
     */
    public function selectionList( $_id, $options = [], $selected = [], $type = 'checkbox' ) {

        $id   = $this->get_field_id( $_id );
        $name = $this->get_field_name( $_id );
        $html = '';
        if ( count( $options ) > 0 ) {
            $html .= '<div class="gck-selection-list">';
            $html .= '<ul>';
            foreach ( $options as $value => $text ) {
                $checked = checked( in_array( $value, $selected ), 1, false );

                $html .= '<li>';
                $html .= "<label for=\"{$id}-{$value}\">";
                $html .= "<input type=\"{$type}\" id=\"{$id}-{$value}\" name=\"{$name}[]\" value=\"1\" {$checked}>";
                $html .= "<span>{$text}</span>";
                $html .= "</label>";
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }

        return $html;
    }
}