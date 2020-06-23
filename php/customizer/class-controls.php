<?php
/**
 * Class Controls.
 *
 * @package MaterialThemeBuilder
 */

namespace MaterialThemeBuilder\Customizer;

use MaterialThemeBuilder\Module_Base;
use MaterialThemeBuilder\Google_Fonts;
use MaterialThemeBuilder\Blocks\Blocks_Frontend;
use MaterialThemeBuilder\Helpers;

/**
 * Class Controls.
 */
class Controls extends Module_Base {

	/**
	 * The slug used as a prefix for all settings and controls.
	 *
	 * @var string
	 */
	public $slug = 'mtb';

	/**
	 * WP_Customize_Manager object reference.
	 *
	 * @var \WP_Customize_Manager
	 */
	public $wp_customize;

	/**
	 * List of added controls.
	 *
	 * @var array
	 */
	public $added_controls = [];

	/**
	 * Register customizer options.
	 *
	 * @action customize_register
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function register( $wp_customize ) {
		$this->wp_customize = $wp_customize;

		// Register custom control types.
		$this->wp_customize->register_control_type( Material_Color_Palette_Control::class );

		// Add the panel.
		$this->add_panel();

		// Add all the customizer sections.
		$this->add_sections();

		// Add all controls in the "Theme" section.
		$this->add_theme_controls();

		// Add all controls in the "Colors" section.
		$this->add_color_controls();

		// Add all controls in the "Typography" section.
		$this->add_typography_controls();

		// Add all controls in the "Corner Styles" section.
		$this->add_corner_styles_controls();

		// Add all controls in the "Icon Styles" section.
		$this->add_icon_collection_controls();
	}

	/**
	 * Add the base panel.
	 *
	 * @return void
	 */
	public function add_panel() {
		/**
		 * Add "Your Material Theme" custom panel.
		 */
		$this->wp_customize->add_panel(
			$this->slug,
			[
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'title'       => esc_html__( 'Material Theme Editor', 'material-theme-builder' ),
				'description' => esc_html__( 'Change the color, shape, typography, and icons below to customize your theme style. Navigate to the Material Library to see your custom styles applied across Material Components..', 'material-theme-builder' ),
			]
		);
	}

	/**
	 * Add all our customizer sections.
	 *
	 * @return void
	 */
	public function add_sections() {
		$sections = [
			'style'         => __( 'Style', 'material-theme-builder' ),
			'colors'        => __( 'Color Palettes', 'material-theme-builder' ),
			'typography'    => __( 'Typography (Font Styles)', 'material-theme-builder' ),
			'corner_styles' => __( 'Shape Size', 'material-theme-builder' ),
			'icons'         => __( 'Icon Style', 'material-theme-builder' ),
		];

		foreach ( $sections as $id => $label ) {
			$id = $this->prepend_slug( $id );

			$args = [
				'priority'   => 10,
				'capability' => 'edit_theme_options',
				'title'      => esc_html( $label ),
				'panel'      => $this->slug,
				'type'       => 'collapse',
			];

			/**
			 * Filters the customizer section args.
			 *
			 * This allows other plugins/themes to change the customizer section args.
			 *
			 * @param array  $args Array of section args.
			 * @param string $id   ID of the section.
			 */
			$section = apply_filters( $this->slug . '_customizer_section_args', $args, $id );

			if ( is_array( $section ) ) {
				$this->wp_customize->add_section(
					$id,
					$section
				);
			} elseif ( $section instanceof \WP_Customize_Section ) {
				$section->id = $id;
				$this->wp_customize->add_section( $section );
			}
		}
	}

	/**
	 * Add all controls in the "Theme" section.
	 *
	 * @return void
	 */
	public function add_theme_controls() {
		/**
		 * List of all the control settings in the Theme section.
		 */
		$settings = [
			'style'          => [
				'default' => 'baseline',
			],
			// This setting does not have an associated control.
			'previous_style' => [
				'default' => 'baseline',
			],
			/**
			 * This setting does not have an associated control
			 * it's used to display material components notification.
			 */
			'notify'         => [
				'default' => 0,
			],
		];

		$this->add_settings( $settings );

		/**
		 * List of all the controls in the Theme section.
		 */
		$controls = [
			'style' => new Image_Radio_Control(
				$this->wp_customize,
				$this->prepend_slug( 'style' ),
				[
					'section'  => 'style',
					'priority' => 10,
					'choices'  => [
						'baseline'    => [
							'label' => __( 'Baseline', 'material-theme-builder' ),
							'url'   => $this->plugin->asset_url( 'assets/images/baseline.svg' ),
						],
						'crane'       => [
							'label' => __( 'Crane', 'material-theme-builder' ),
							'url'   => $this->plugin->asset_url( 'assets/images/crane.svg' ),
						],
						'fortnightly' => [
							'label' => __( 'Fortnightly', 'material-theme-builder' ),
							'url'   => $this->plugin->asset_url( 'assets/images/fortnightly.svg' ),
						],
						'blossom'     => [
							'label' => __( 'Blossom', 'material-theme-builder' ),
							'url'   => $this->plugin->asset_url( 'assets/images/blossom.svg' ),
						],
						'custom'      => [
							'label' => __( 'Custom', 'material-theme-builder' ),
							'url'   => $this->plugin->asset_url( 'assets/images/custom.svg' ),
						],
					],
				]
			),
		];

		$this->add_controls( $controls );
	}

	/**
	 * Add all controls in the "Colors" section.
	 *
	 * @return void
	 */
	public function add_color_controls() {
		/**
		 * Generate list of all the settings in the colors section.
		 */
		$settings = [];

		foreach ( $this->get_color_controls() as $control ) {
			$settings[ $control['id'] ] = [
				'sanitize_callback' => 'sanitize_hex_color',
			];
		}

		$this->add_settings( $settings );

		/**
		 * Generate list of all the controls in the colors section.
		 */
		$controls = [];

		foreach ( $this->get_color_controls() as $control ) {
			$controls[ $control['id'] ] = new Material_Color_Palette_Control(
				$this->wp_customize,
				$this->prepend_slug( $control['id'] ),
				[
					'label'                => $control['label'],
					'section'              => 'colors',
					'priority'             => 10,
					'related_text_setting' => ! empty( $control['related_text_setting'] ) ? $control['related_text_setting'] : false,
					'related_setting'      => ! empty( $control['related_setting'] ) ? $control['related_setting'] : false,
					'css_var'              => $control['css_var'],
					'a11y_label'           => ! empty( $control['a11y_label'] ) ? $control['a11y_label'] : '',
				]
			);
		}

		$this->add_controls( $controls );
	}

	/**
	 * Add all controls in the "Typography" section.
	 *
	 * @return void
	 */
	public function add_typography_controls() {
		/**
		 * Generate list of all the settings in the Typography section.
		 */
		$settings = [];

		foreach ( $this->get_typography_controls() as $control ) {
			$settings[ $control['id'] ] = [];
		}

		$this->add_settings( $settings );

		/**
		 * Generate list of all the controls in the Typography section.
		 */
		$controls = [];

		foreach ( $this->get_typography_controls() as $control ) {
			$controls[ $control['id'] ] = new Google_Fonts_Control(
				$this->wp_customize,
				$this->prepend_slug( $control['id'] ),
				[
					'section'  => 'typography',
					'priority' => 10,
					'label'    => $control['label'],
					'css_vars' => $control['css_vars'],
				]
			);
		}

		$this->add_controls( $controls );
	}

	/**
	 * Add all controls in the "Corner Styles" section.
	 *
	 * @return void
	 */
	public function add_corner_styles_controls() {
		/**
		 * Generate list of all the settings in the Corner Styles section.
		 */
		$settings = [];

		foreach ( $this->get_corner_styles_controls() as $control ) {
			$settings[ $control['id'] ] = [];
		}

		$this->add_settings( $settings );

		/**
		 * Generate list of all the controls in the Corner Styles section.
		 */
		$controls        = [];
		$corner_controls = $this->get_corner_styles_controls();

		$control = $corner_controls[0];

		$corner_controls = array_slice( $corner_controls, 1 );
		foreach ( $corner_controls as $i => $ctrl ) {
			$corner_controls[ $i ]['id'] = $this->prepend_slug( $ctrl['id'] );
		}

		$controls[ $control['id'] ] = new Range_Slider_Control(
			$this->wp_customize,
			$this->prepend_slug( $control['id'] ),
			[
				'section'       => 'corner_styles',
				'priority'      => 10,
				'label'         => isset( $control['label'] ) ? $control['label'] : '',
				'description'   => isset( $control['description'] ) ? $control['description'] : '',
				'min'           => isset( $control['min'] ) ? $control['min'] : 0,
				'max'           => isset( $control['max'] ) ? $control['max'] : 100,
				'initial_value' => isset( $control['initial_value'] ) ? $control['initial_value'] : 0,
				'css_var'       => isset( $control['css_var'] ) ? $control['css_var'] : '',
				'extra'         => isset( $control['extra'] ) ? $control['extra'] : '',
				'children'      => $corner_controls,
			]
		);

		$this->add_controls( $controls );
	}

	/**
	 * Add all controls in the "Icon Styles" section.
	 *
	 * @return void
	 */
	public function add_icon_collection_controls() {
		$settings = [
			'icon_collection' => [
				'default' => 'filled',
			],
		];

		$this->add_settings( $settings );

		/**
		 * List of all the controls in the Theme section.
		 */
		$controls = [
			'icon_collection' => new Icon_Radio_Control(
				$this->wp_customize,
				$this->prepend_slug( 'icon_collection' ),
				[
					'section'  => 'icons',
					'priority' => 10,
					'choices'  => $this->get_icon_collection_controls(),
					'css_var'  => '--mdc-icons-font-family',
				]
			),
		];

		$this->add_controls( $controls );
	}

	/**
	 * Add settings to customizer.
	 *
	 * @param array $settings Array of settings to add to customizer.
	 *
	 * @return void
	 */
	public function add_settings( $settings = [] ) {
		foreach ( $settings as $id => $setting ) {
			$id = $this->prepend_slug( $id );

			if ( is_array( $setting ) ) {
				$defaults = [
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
					'default'           => $this->get_default( $id ),
				];

				$setting = array_merge( $defaults, $setting );
			}

			/**
			 * Filters the customizer setting args.
			 *
			 * This allows other plugins/themes to change the customizer setting args.
			 *
			 * @param array  $setting Array of setting args.
			 * @param string $id      ID of the setting.
			 */
			$setting = apply_filters( $this->slug . '_customizer_setting_args', $setting, $id );

			if ( is_array( $setting ) ) {
				$this->wp_customize->add_setting(
					$id,
					$setting
				);
			} elseif ( $setting instanceof \WP_Customize_Setting ) {
				$setting->id = $id;
				$this->wp_customize->add_setting( $setting );
			}
		}
	}

	/**
	 * Add controls to customizer.
	 *
	 * @param array $controls Array of controls to add to customizer.
	 *
	 * @return void
	 */
	public function add_controls( $controls = [] ) {
		foreach ( $controls as $id => $control ) {
			$id = $this->prepend_slug( $id );

			/**
			 * Filters the customizer control args.
			 *
			 * This allows other plugins/themes to change the customizer controls args.
			 *
			 * @param array  $control Array of control args.
			 * @param string $id      ID of the control.
			 */
			$control = apply_filters( $this->slug . '_customizer_control_args', $control, $id );

			if ( is_array( $control ) ) {
				$control['section'] = isset( $control['section'] ) ? $this->prepend_slug( $control['section'] ) : '';
				$this->wp_customize->add_control(
					$id,
					$control
				);
				$this->added_controls[] = $id;
			} elseif ( $control instanceof \WP_Customize_Control ) {
				$control->id      = $id;
				$control->section = isset( $control->section ) ? $this->prepend_slug( $control->section ) : '';
				$this->wp_customize->add_control( $control );
				$this->added_controls[] = $id;
			}
		}
	}

	/**
	 * Enqueue Customizer scripts.
	 *
	 * @action customize_controls_enqueue_scripts
	 */
	public function scripts() {
		wp_enqueue_script(
			'material-theme-builder-customizer-js',
			$this->plugin->asset_url( 'assets/js/customize-controls.js' ),
			[ 'jquery', 'wp-color-picker', 'customize-controls', 'wp-element', 'wp-components', 'wp-i18n' ],
			$this->plugin->asset_version(),
			false
		);

		wp_localize_script(
			'material-theme-builder-customizer-js',
			'mtb',
			[
				'slug'                   => $this->slug,
				'designStyles'           => $this->get_design_styles(),
				'controls'               => $this->added_controls,
				'styleControl'           => $this->prepend_slug( 'style' ),
				'prevStyleControl'       => $this->prepend_slug( 'previous_style' ),
				'iconCollectionsControl' => $this->prepend_slug( 'icon_collection' ),
				'iconCollectionsOptions' => $this->get_icon_collection_controls(),
				'l10n'                   => [
					'confirmChange'    => esc_html__( 'You will lose any custom theme changes. Would you like to continue ?', 'material-theme-builder' ),
					'componentsNotice' => __( 'Customize Material Components and styles throughout your site.<br/><a href="#">View example page</a>', 'material-theme-builder' ),
				],
				'googleFonts'            => Google_Fonts::get_font_choices(),
				'notify_nonce'           => wp_create_nonce( 'mtb_notify_nonce' ),
				'pluginPath'             => $this->plugin->asset_url( '' ),
				'themeStatus'            => $this->plugin->material_theme_status(),
				'themeSearchUrl'         => esc_url( admin_url( '/theme-install.php?search=Material Theme' ) ),
			]
		);

		wp_enqueue_style(
			'material-theme-builder-customizer-css',
			$this->plugin->asset_url( 'assets/css/customize-controls-compiled.css' ),
			[ 'wp-components' ],
			$this->plugin->asset_version()
		);

		wp_enqueue_style(
			'material-theme-builder-icons-css',
			esc_url( '//fonts.googleapis.com/icon?family=Material+Icons' ),
			[],
			$this->plugin->asset_version()
		);
	}

	/**
	 * Get Google Fonts CDN URL to be enqueued based on the selected settings.
	 *
	 * @param string $context The context of the request.
	 *
	 * @return string
	 */
	public function get_google_fonts_url( $context = '' ) {
		$icons_style   = $this->get_icon_style( '+' );
		$font_families = [ 'Material+Icons' . $icons_style ];

		if ( 'block-editor' === $context ) {
			$font_families[] = 'Material+Icons+Outlined';
		}

		foreach ( $this->get_typography_controls() as $control ) {
			$value = $this->get_theme_mod( $control['id'] );

			$font_families[] = str_replace( ' ', '+', $value ) . ':300,400,500';
		}

		return add_query_arg( 'family', implode( '|', array_unique( $font_families ) ), '//fonts.googleapis.com/css' );
	}

	/**
	 * Get CSS variable which should set the font-family for material-icons.
	 *
	 * @return string
	 */
	public function get_icon_collection_css() {
		return sprintf( '--mdc-icons-font-family: "Material Icons%s";', esc_html( $this->get_icon_style() ) );
	}

	/**
	 * Get Icon style.
	 *
	 * @param string $replace String to replace `-` with.
	 *
	 * @return string
	 */
	public function get_icon_style( $replace = ' ' ) {
		$icons_style = $this->get_theme_mod( 'icon_collection' );
		return ( $icons_style && 'filled' !== $icons_style )
			? $replace . str_replace( '-', $replace, ucwords( $icons_style, '-' ) ) : '';
	}

	/**
	 * Enqueue Customizer preview scripts.
	 *
	 * @action customize_preview_init, 100
	 */
	public function preview_scripts() {
		wp_enqueue_script(
			'material-theme-builder-customizer-preview-js',
			$this->plugin->asset_url( 'assets/js/customize-preview.js' ),
			[ 'jquery' ],
			$this->plugin->asset_version(),
			true
		);
	}

	/**
	 * Render custom templates.
	 *
	 * @action customize_controls_print_footer_scripts
	 */
	public function templates() {
		Material_Color_Palette_Control::tabs_template();
	}

	/**
	 * Get custom frontend CSS based on the customizer theme settings.
	 */
	public function get_frontend_css() {
		$color_vars         = [];
		$corner_styles_vars = [];
		$font_vars          = [];
		$google_fonts       = Google_Fonts::get_fonts();

		foreach ( $this->get_color_controls() as $control ) {
			$value = $this->get_theme_mod( $control['id'] );
			$rgb   = Helpers::hex_to_rgb( $value );
			if ( ! empty( $rgb ) ) {
				$rgb = implode( ',', $rgb );
			}

			$color_vars[] = sprintf( '%s: %s;', esc_html( $control['css_var'] ), esc_html( $value ) );
			$color_vars[] = sprintf( '%s: %s;', esc_html( $control['css_var'] . '-rgb' ), esc_html( $rgb ) );
		}

		// Generate additional surface variant vars required by some components.
		$surface    = $this->get_theme_mod( 'surface_color' );
		$on_surface = $this->get_theme_mod( 'surface_text_color' );

		if ( ! empty( $surface ) && ! empty( $on_surface ) ) {
			$mix_4        = Helpers::mix_colors( $on_surface, $surface, 0.04 );
			$color_vars[] = esc_html( "--mdc-theme-surface-mix-4: $mix_4;" );

			$mix_12       = Helpers::mix_colors( $on_surface, $surface, 0.12 );
			$color_vars[] = esc_html( "--mdc-theme-surface-mix-12: $mix_12;" );
		}

		foreach ( $this->get_typography_controls() as $control ) {
			$value    = $this->get_theme_mod( $control['id'] );
			$fallback = array_key_exists( $value, $google_fonts ) ? $google_fonts[ $value ]['category'] : 'sans-serif';

			if ( ! empty( $control['css_vars']['family'] ) ) {
				foreach ( $control['css_vars']['family'] as $var ) {
					$font_vars[] = sprintf(
						'%s: "%s", %s;',
						esc_html( $var ),
						esc_html( $value ),
						esc_html( $fallback )
					);
				}
			}
		}

		foreach ( $this->get_corner_styles_controls() as $control ) {
			if ( empty( $control['css_var'] ) ) {
				continue;
			}

			$value = $this->get_theme_mod( $control['id'] );

			if ( isset( $control['max'] ) || isset( $control['min'] ) ) {
				if ( isset( $control['min'] ) && $value < $control['min'] ) {
					$value = $control['min'];
				}

				if ( isset( $control['max'] ) && $value > $control['max'] ) {
					$value = $control['max'];
				}
			}

			$corner_styles_vars[] = sprintf(
				'%s: %spx;',
				esc_html( $control['css_var'] ),
				esc_html( $value )
			);
		}

		$glue               = "\n\t\t\t\t";
		$icon_collection    = $this->get_icon_collection_css();
		$color_vars         = implode( $glue, $color_vars );
		$corner_styles_vars = implode( $glue, $corner_styles_vars );
		$font_vars          = implode( $glue, $font_vars );

		return "
			:root {
				/* Theme color vars */
				{$color_vars}

				/* Icon collection type var */
				{$icon_collection}

				/* Typography vars */
				{$font_vars}

				/* Corner Styles vars */
				{$corner_styles_vars}
			}
		";
	}

	/**
	 * Get default value for a setting.
	 *
	 * @param string $setting Name of the setting.
	 *
	 * @return mixed
	 */
	public function get_default( $setting ) {
		$setting  = str_replace( "{$this->slug}_", '', $setting );
		$styles   = $this->get_design_styles();
		$baseline = $styles['baseline'];

		return isset( $baseline[ $setting ] ) ? $baseline[ $setting ] : '';
	}

	/**
	 * Get the design styles with their default values.
	 *
	 * @return array
	 */
	public function get_design_styles() {
		return [
			'baseline'    => [
				'primary_color'         => '#6200ee',
				'secondary_color'       => '#03dac6',
				'primary_text_color'    => '#ffffff',
				'secondary_text_color'  => '#000000',
				'surface_color'         => '#ffffff',
				'surface_text_color'    => '#000000',
				'background_color'      => '#ffffff',
				'background_text_color' => '#000000',
				'head_font_family'      => 'Roboto',
				'body_font_family'      => 'Roboto',
				'global_radius'         => '4',
				'button_radius'         => '4',
				'card_radius'           => '4',
				'chip_radius'           => '4',
				'data_table_radius'     => '0',
				'image_list_radius'     => '4',
				'nav_drawer_radius'     => '0',
				'text_field_radius'     => '4',
				'icon_collection'       => 'filled',
			],
			'crane'       => [
				'primary_color'         => '#5d1049',
				'secondary_color'       => '#e30425',
				'primary_text_color'    => '#ffffff',
				'secondary_text_color'  => '#ffffff',
				'surface_color'         => '#ffffff',
				'surface_text_color'    => '#000000',
				'background_color'      => '#ffffff',
				'background_text_color' => '#000000',
				'head_font_family'      => 'Raleway',
				'body_font_family'      => 'Raleway',
				'global_radius'         => '0',
				'button_radius'         => '0',
				'card_radius'           => '16',
				'chip_radius'           => '0',
				'data_table_radius'     => '20',
				'image_list_radius'     => '16',
				'nav_drawer_radius'     => '20',
				'text_field_radius'     => '0',
				'icon_collection'       => 'outlined',
			],
			'fortnightly' => [
				'primary_color'         => '#121212',
				'secondary_color'       => '#6b38fb',
				'primary_text_color'    => '#ffffff',
				'secondary_text_color'  => '#ffffff',
				'surface_color'         => '#ffffff',
				'surface_text_color'    => '#000000',
				'background_color'      => '#ffffff',
				'background_text_color' => '#000000',
				'head_font_family'      => 'Merriweather',
				'body_font_family'      => 'Merriweather',
				'global_radius'         => '0',
				'button_radius'         => '0',
				'card_radius'           => '0',
				'chip_radius'           => '0',
				'data_table_radius'     => '0',
				'image_list_radius'     => '0',
				'nav_drawer_radius'     => '0',
				'text_field_radius'     => '0',
				'icon_collection'       => 'outlined',
			],
			'blossom'     => [
				'primary_color'         => '#e56969',
				'secondary_color'       => '#ef9a9a',
				'primary_text_color'    => '#ffffff',
				'secondary_text_color'  => '#442c2e',
				'surface_color'         => '#fff1ee',
				'surface_text_color'    => '#442c2e',
				'background_color'      => '#fff1ee',
				'background_text_color' => '#442c2e',
				'head_font_family'      => 'Rubik',
				'body_font_family'      => 'Rubik',
				'global_radius'         => '0',
				'button_radius'         => '0',
				'card_radius'           => '0',
				'chip_radius'           => '0',
				'data_table_radius'     => '0',
				'image_list_radius'     => '0',
				'nav_drawer_radius'     => '0',
				'text_field_radius'     => '0',
				'icon_collection'       => 'outlined',
			],
		];
	}

	/**
	 * Get list of all the control settings in the Colors section.
	 */
	public function get_color_controls() {
		return [
			[
				'id'                   => 'primary_color',
				'label'                => __( 'Primary Color', 'material-theme-builder' ),
				'a11y_label'           => __( 'On Primary', 'material-theme-builder' ),
				'related_text_setting' => $this->prepend_slug( 'primary_text_color' ),
				'css_var'              => '--mdc-theme-primary',
			],
			[
				'id'                   => 'secondary_color',
				'label'                => __( 'Secondary Color', 'material-theme-builder' ),
				'a11y_label'           => __( 'On Secondary', 'material-theme-builder' ),
				'related_text_setting' => $this->prepend_slug( 'secondary_text_color' ),
				'css_var'              => '--mdc-theme-secondary',
			],
			[
				'id'              => 'primary_text_color',
				'label'           => __( 'On Primary Color (text and icons)', 'material-theme-builder' ),
				'a11y_label'      => __( 'On Primary', 'material-theme-builder' ),
				'related_setting' => $this->prepend_slug( 'primary_color' ),
				'css_var'         => '--mdc-theme-on-primary',
			],
			[
				'id'              => 'secondary_text_color',
				'label'           => __( 'On Secondary Color (text and icons)', 'material-theme-builder' ),
				'a11y_label'      => __( 'On Secondary', 'material-theme-builder' ),
				'related_setting' => $this->prepend_slug( 'secondary_color' ),
				'css_var'         => '--mdc-theme-on-secondary',
			],
			[
				'id'                   => 'surface_color',
				'label'                => __( 'Surface Color', 'material-theme-builder' ),
				'a11y_label'           => __( 'On Surface', 'material-theme-builder' ),
				'related_text_setting' => $this->prepend_slug( 'surface_text_color' ),
				'css_var'              => '--mdc-theme-surface',
			],
			[
				'id'              => 'surface_text_color',
				'label'           => __( 'On Surface Color (text and icons)', 'material-theme-builder' ),
				'a11y_label'      => __( 'On Surface', 'material-theme-builder' ),
				'related_setting' => $this->prepend_slug( 'surface_color' ),
				'css_var'         => '--mdc-theme-on-surface',
			],
		];
	}

	/**
	 * Get list of all the control settings in the Typography section.
	 *
	 * @return array
	 */
	public function get_typography_controls() {
		return [
			[
				'id'       => 'head_font_family',
				'label'    => __( 'Headlines & Subtitles', 'material-theme-builder' ),
				'css_vars' => [
					'family' => [
						'--mdc-typography-headline1-font-family',
						'--mdc-typography-headline2-font-family',
						'--mdc-typography-headline3-font-family',
						'--mdc-typography-headline4-font-family',
						'--mdc-typography-headline5-font-family',
						'--mdc-typography-headline6-font-family',
						'--mdc-typography-subtitle1-font-family',
						'--mdc-typography-subtitle2-font-family',
					],
				],
			],
			[
				'id'       => 'body_font_family',
				'label'    => __( 'Body & Captions', 'material-theme-builder' ),
				'css_vars' => [
					'family' => [
						'--mdc-typography-font-family',
						'--mdc-typography-body1-font-family',
						'--mdc-typography-body2-font-family',
						'--mdc-typography-caption-font-family',
						'--mdc-typography-button-font-family',
						'--mdc-typography-overline-font-family',
					],
				],
			],
		];
	}

	/**
	 * Get list of all the control settings in the Typography section.
	 *
	 * @return array
	 */
	public function get_corner_styles_controls() {
		return [
			[
				'id'            => 'global_radius',
				'label'         => __( 'Global corner styles', 'material-theme-builder' ),
				'description'   => __( 'Change the global shape size for all components, expand to customize the shape size for individual components.', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 36,
				'initial_value' => 4,
			],
			[
				'id'            => 'button_radius',
				'label'         => __( 'Button', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 20,
				'initial_value' => 4,
				'css_var'       => '--mdc-button-radius',
				'blocks'        => [
					'material/button',
				],
			],
			[
				'id'            => 'card_radius',
				'label'         => __( 'Card', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 24,
				'initial_value' => 0,
				'css_var'       => '--mdc-card-radius',
				'blocks'        => [
					'material/card',
					'material/cards-collection',
					'material/image-list',
				],
			],
			[
				'id'            => 'chip_radius',
				'label'         => __( 'Chip', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 16,
				'initial_value' => 0,
				'css_var'       => '--mdc-chip-radius',
			],
			[
				'id'            => 'data_table_radius',
				'label'         => __( 'Data table', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 36,
				'initial_value' => 0,
				'css_var'       => '--mdc-data-table-radius',
				'blocks'        => [
					'material/data-table',
				],
			],
			[
				'id'            => 'image_list_radius',
				'label'         => __( 'Image List', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 24,
				'initial_value' => 0,
				'css_var'       => '--mdc-image-list-radius',
				'blocks'        => [
					'material/image-list',
				],
			],
			[
				'id'            => 'nav_drawer_radius',
				'label'         => __( 'Nav Drawer', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 36,
				'initial_value' => 0,
				'css_var'       => '--mdc-nav-drawer-radius',
			],
			[
				'id'            => 'text_field_radius',
				'label'         => __( 'Text Field', 'material-theme-builder' ),
				'min'           => 0,
				'max'           => 20,
				'initial_value' => 0,
				'css_var'       => '--mdc-text-field-radius',
			],
		];
	}

	/**
	 * Get list of all the control settings in the Icon Collections section.
	 *
	 * @return array
	 */
	public function get_icon_collection_controls() {
		return [
			'filled'   => [
				'label' => __( 'Filled', 'material-theme-builder' ),
				'icon'  => $this->plugin->asset_url( 'assets/images/icon-collections/filled.svg' ),
			],
			'outlined' => [
				'label' => __( 'Outlined', 'material-theme-builder' ),
				'icon'  => $this->plugin->asset_url( 'assets/images/icon-collections/outlined.svg' ),
			],
			'round'    => [
				'label' => __( 'Rounded', 'material-theme-builder' ),
				'icon'  => $this->plugin->asset_url( 'assets/images/icon-collections/rounded.svg' ),
			],
			'two-tone' => [
				'label' => __( 'Two-tone', 'material-theme-builder' ),
				'icon'  => $this->plugin->asset_url( 'assets/images/icon-collections/two-tone.svg' ),
			],
			'sharp'    => [
				'label' => __( 'Sharp', 'material-theme-builder' ),
				'icon'  => $this->plugin->asset_url( 'assets/images/icon-collections/sharp.svg' ),
			],
		];
	}


	/**
	 * Prepend the slug name if it does not exist.
	 *
	 * @param string $name The name of the setting/control.
	 *
	 * @return string
	 */
	public function prepend_slug( $name ) {
		return false === strpos( $name, "{$this->slug}_" ) ? "{$this->slug}_{$name}" : $name;
	}

	/**
	 * Get theme mod with fallback to the default value.
	 *
	 * @param string $name Name of the mod.
	 *
	 * @return mixed
	 */
	public function get_theme_mod( $name ) {
		$value = get_theme_mod( $this->prepend_slug( $name ) );

		if ( empty( $value ) ) {
			$value = $this->get_default( $name );
		}

		return $value;
	}

	/**
	 * Maybe show the material components notification if the current previewed
	 * page does not contain any material blocks.
	 *
	 * @action customize_sanitize_js_mtb_notify
	 *
	 * @param mixed $value Saved value.
	 *
	 * @return mixed
	 */
	public function show_material_components_notification( $value ) {
		if ( is_customize_preview() && is_singular() && Blocks_Frontend::has_material_blocks() ) {
			return false;
		}

		return $value;
	}

	/**
	 * Update notification dismiss count.
	 *
	 * @action wp_ajax_mtb_notification_dismiss
	 */
	public function notification_dismiss() {
		if ( ! check_ajax_referer( 'mtb_notify_nonce', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}

		$count = $this->get_theme_mod( 'notify' );
		$count = empty( $count ) ? 0 : $count;
		set_theme_mod( $this->prepend_slug( 'notify' ), ++ $count );
		wp_send_json_success(
			[
				'count' => $count,
			]
		);
	}

	/**
	 * Move background color contorls to Material "Color Palettes" section.
	 *
	 * @action material_customizer_control_args, 10, 2
	 *
	 * @param array|WP_Customize_Control $control Control arguments.
	 * @param string                     $id     Control ID.
	 *
	 * @return array
	 */
	public function background_color_controls( $control, $id ) {
		if ( in_array( $id, [ 'material_background_color', 'material_background_text_color' ], true ) ) {
			$label = 'material_background_text_color' === $id ? esc_html__( 'On Background Color (text and icons)', 'material-theme-builder' ) : false;
			if ( is_array( $control ) ) {
				$control['section']  = $this->prepend_slug( 'colors' );
				$control['priority'] = 20;
				$control['label']    = $label ? $label : $control['label'];
			} elseif ( $control instanceof \WP_Customize_Control ) {
				$control->section  = $this->prepend_slug( 'colors' );
				$control->priority = 20;
				$control->label    = $label ? $label : $control->label;
			}
		}

		return $control;
	}
}
