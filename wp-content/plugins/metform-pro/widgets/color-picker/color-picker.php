<?php
namespace Elementor;
defined( 'ABSPATH' ) || exit;

Class MetForm_Input_Color_Picker extends Widget_Base{

	use \MetForm\Traits\Common_Controls;
	use \MetForm\Traits\Conditional_Controls;

    public function get_name() {
		return 'mf-color-picker';
    }
    
	public function get_title() {
		return esc_html__( 'Color picker', 'metform-pro' );
	}
	
	public function show_in_panel() {
        return 'metform-form' == get_post_type();
	}

	public function get_categories() {
		return [ 'metform-pro' ];
	}
	    
	public function get_keywords() {
        return ['metform-pro', 'input', 'color', 'picker', 'select color', 'choose color'];
    }

    protected function register_controls() {
        
        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'metform-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->input_content_controls(['NO_PLACEHOLDER']);

        $this->end_controls_section();

        $this->start_controls_section(
			'settings_section',
			[
				'label' => esc_html__( 'Settings', 'metform-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->input_setting_controls(['MAX_MIN']);

		$this->add_control(
			'mf_input_validation_type',
			[
				'label' => esc_html__( 'Validation Type', 'metform-pro' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'none',
			]
		);

		$this->end_controls_section();
		
		if(class_exists('\MetForm_Pro\Base\Package')){
			$this->input_conditional_control();
		}
		
        $this->start_controls_section(
			'label_section',
			[
				'label' => esc_html__( 'Label', 'metform-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
                    'mf_input_label_status' => 'yes',
                ],
			]
        );

		$this->input_label_controls();

        $this->end_controls_section();

        $this->start_controls_section(
			'help_text_section',
			[
				'label' => esc_html__( 'Help Text', 'metform-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mf_input_help_text!' => ''
				]
			]
		);
		
		$this->input_help_text_controls();

        $this->end_controls_section();

        
	}

    protected function render($instance = []){
		$settings = $this->get_settings_for_display();
		$inputWrapStart = $inputWrapEnd = '';
		extract($settings);

		$render_on_editor = true;
		$is_edit_mode = 'metform-form' === get_post_type() && \Elementor\Plugin::$instance->editor->is_edit_mode();

		/**
		 * Loads the below markup on 'Editor' view, only when 'metform-form' post type
		 */
		if ( $is_edit_mode ):
			$inputWrapStart = '<div class="mf-form-wrapper"></div><script type="text" class="mf-template">return html`';
			$inputWrapEnd = '`</script>';
		endif;
		
		$class = (isset($settings['mf_conditional_logic_form_list']) ? 'mf-conditional-input' : '');
		
		$configData = [
			'message' 		=> $errorMessage 	= isset($mf_input_validation_warning_message) ? !empty($mf_input_validation_warning_message) ? $mf_input_validation_warning_message : esc_html__('This field is required.', 'metform-pro') : esc_html__('This field is required.', 'metform-pro'),
			'required'		=> isset($mf_input_required) && $mf_input_required == 'yes' ? true : false
		];
		?>

		<?php echo $inputWrapStart; ?>

		<div className="mf-input-wrapper">
			<?php if ( 'yes' == $mf_input_label_status ): ?>
				<label className="mf-input-label" htmlFor="mf-input-map-<?php echo esc_attr( $this->get_id() ); ?>">
					<?php echo \MetForm\Utils\Util::react_entity_support( esc_html($mf_input_label), $render_on_editor ); ?>
					<span className="mf-input-required-indicator"><?php echo esc_html( ($mf_input_required === 'yes') ? '*' : '' );?></span>
				</label>
			<?php endif; ?>

			
			<${props.InputColor}
				initialValue=${parent.state.formData['<?php echo esc_attr($mf_input_name); ?>'] || '#000000'}
				onChange=${(setColor) => parent.colorChange(setColor, '<?php echo esc_attr($mf_input_name); ?>')}
				/>

			<input type="hidden"
				className="mf-input mf-input-color-picker-react <?php echo $class; ?>"
				id="mf-input-map-<?php echo esc_attr($this->get_id()); ?>"
				name="<?php echo esc_attr($mf_input_name); ?>"
				value=${parent.state.formData['<?php echo esc_attr($mf_input_name); ?>'] || '#000000'}
				onInput=${parent.colorChangeInput}
				aria-invalid=${validation.errors['<?php echo esc_attr($mf_input_name); ?>'] ? 'true' : 'false'}
				ref=${ el => parent.activateValidation(<?php echo json_encode($configData); ?>, el) }
				readOnly
				/>

			<${validation.ErrorMessage} errors=${validation.errors} name="<?php echo esc_attr($mf_input_name); ?>" as=${html`<span className="mf-error-message"></span>`} />
			
			<?php echo '' != $mf_input_help_text ? '<span className="mf-input-help">'. \MetForm\Utils\Util::react_entity_support( esc_html($mf_input_help_text), $render_on_editor ) .'</span>' : ''; ?>
		</div>

		<?php echo $inputWrapEnd; ?>

		<?php
    }
    
}
