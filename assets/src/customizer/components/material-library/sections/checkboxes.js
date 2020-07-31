import { __ } from '@wordpress/i18n';

const Checkboxes = () => (
	<div>
		<h4 className="mdc-typography--headline4" style={ { margin: 0 } }>
			{ __( 'Checkbox', 'material-theme-builder' ) }
		</h4>
		<p>
			{ __(
				'Selection controls allow the user to select options. It is unavailable as a block in WordPress.',
				'material-theme-builder'
			) }
		</p>
		<a href="https://material.io/develop/web/components/input-controls/checkboxes" target="_blank">
		    <span class="material-icons">open_in_new</span>
		</a>
		<br/>
		<div className="mdc-checkbox">
			<input type="checkbox" className="mdc-checkbox__native-control" />
			<div className="mdc-checkbox__background">
				<svg className="mdc-checkbox__checkmark" viewBox="0 0 24 24">
					<path
						className="mdc-checkbox__checkmark-path"
						fill="none"
						d="M1.73,12.91 8.1,19.28 22.79,4.59"
					/>
				</svg>
				<div className="mdc-checkbox__mixedmark"></div>
			</div>
			<div className="mdc-checkbox__ripple"></div>
		</div>
		<div className="mdc-checkbox">
			<input type="checkbox" className="mdc-checkbox__native-control" />
			<div className="mdc-checkbox__background">
				<svg className="mdc-checkbox__checkmark" viewBox="0 0 24 24">
					<path
						className="mdc-checkbox__checkmark-path"
						fill="none"
						d="M1.73,12.91 8.1,19.28 22.79,4.59"
					/>
				</svg>
				<div className="mdc-checkbox__mixedmark"></div>
			</div>
			<div className="mdc-checkbox__ripple"></div>
		</div>
		<div className="mdc-checkbox">
			<input type="checkbox" className="mdc-checkbox__native-control" />
			<div className="mdc-checkbox__background">
				<svg className="mdc-checkbox__checkmark" viewBox="0 0 24 24">
					<path
						className="mdc-checkbox__checkmark-path"
						fill="none"
						d="M1.73,12.91 8.1,19.28 22.79,4.59"
					/>
				</svg>
				<div className="mdc-checkbox__mixedmark"></div>
			</div>
			<div className="mdc-checkbox__ripple"></div>
		</div>
		<div className="mdc-checkbox">
			<input type="checkbox" className="mdc-checkbox__native-control" />
			<div className="mdc-checkbox__background">
				<svg className="mdc-checkbox__checkmark" viewBox="0 0 24 24">
					<path
						className="mdc-checkbox__checkmark-path"
						fill="none"
						d="M1.73,12.91 8.1,19.28 22.79,4.59"
					/>
				</svg>
				<div className="mdc-checkbox__mixedmark"></div>
			</div>
			<div className="mdc-checkbox__ripple"></div>
		</div>
	</div>
);

export default Checkboxes;
