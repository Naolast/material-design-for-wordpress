/**
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import TextInputElement from './text-input-element';

/**
 * Text Input Field Block Save component.
 *
 * @param {Object} props - Component props.
 * @param {Object} props.attributes - Component attributes.
 * @param {string} props.className - Component classes.
 * @param {number} props.instanceId - Component instance id.
 *
 * @return {Function} Function returning the HTML markup for the component.
 */
const TextInputSave = props => {
	const {
		attributes: {
			id,
			label,
			inputValue,
			outlined,
			fullWidth,
			displayLabel,
			isRequired,
			inputType,
			inputRole,
		},
		className,
	} = props;

	const textInputProps = {
		editMode: false,
		inputValue,
		id,
		inputRole,
		inputType,
		displayLabel,
		label,
		onChange: () => {},
		isRequired,
	};

	return (
		<>
			<div
				className={ classnames( 'mdc-text-field-container', {
					'mdc-text-field-container--not-required': ! isRequired,
				} ) }
			>
				{ outlined ? (
					<div
						className={ classnames(
							className,
							'mdc-text-field',
							'mdc-text-field--outlined',
							{ 'mdc-text-field--no-label': ! displayLabel },
							{ 'mdc-text-field--custom-full': fullWidth }
						) }
					>
						<TextInputElement { ...textInputProps } />
						<div className="mdc-notched-outline">
							<div className="mdc-notched-outline__leading"></div>
							{ displayLabel && (
								<div className="mdc-notched-outline__notch">
									<label
										htmlFor={ id }
										className="mdc-floating-label"
										id={ `label-${ id }` }
									>
										{ label }
									</label>
								</div>
							) }
							<div className="mdc-notched-outline__trailing"></div>
						</div>
					</div>
				) : (
					<div
						className={ classnames(
							className,
							'mdc-text-field',
							{ 'mdc-text-field--no-label': ! displayLabel },
							{ 'mdc-text-field--custom-full': fullWidth }
						) }
					>
						<span className="mdc-text-field__ripple"></span>
						<TextInputElement { ...textInputProps } />
						<div className="mdc-line-ripple"></div>
						{ displayLabel && (
							<label
								htmlFor={ id }
								className="mdc-floating-label"
								id={ `label-${ id }` }
							>
								{ label }
							</label>
						) }
					</div>
				) }
			</div>
		</>
	);
};

export default TextInputSave;
