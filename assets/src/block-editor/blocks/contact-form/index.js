/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;

export { metadata, name };

export const settings = {
	title: __( 'Contact Form (Material)', 'material-theme-builder' ),
	description: __(
		'A simple way for people to get in contact with you.',
		'material-theme-builder'
	),
	category: 'material',
	keywords: [ __( 'Material Contact Form', 'material-theme-builder' ) ],
	icon: <i className="material-icons-outlined">mail</i>,
	example: {
		attributes: {
			preview: true,
		},
	},
	edit,
	save,
};
