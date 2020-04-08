/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import BlockIcon from './components/block-icon';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import { DEFAULT_NUMBER_OF_CARDS, CARD_ATTRIBUTES_VALUE } from './constants';

export const name = 'material/cards-collection';

const defaultCardsProps = [];

for ( let index = 0; index < DEFAULT_NUMBER_OF_CARDS; index++ ) {
	defaultCardsProps.push( { ...CARD_ATTRIBUTES_VALUE } );
}

/**
 * @type {{edit: *, icon: (function(): *), description: string, title: string, category: string}}
 */
export const settings = {
	title: __( 'Cards Collection', 'material-theme-builder' ),
	description: __(
		'Display a list of custom cards.',
		'material-theme-builder'
	),
	category: 'material',
	supports: {
		align: [ 'wide', 'full' ],
	},
	icon: BlockIcon,
	attributes: {
		style: {
			type: 'string',
			default: 'masonry',
		},
		align: {
			type: 'string',
			default: 'wide',
		},
		columns: {
			type: 'number',
			default: 2,
		},
		contentLayout: {
			type: 'string',
			default: 'text-under-media',
		},
		numberOfCards: {
			type: 'number',
			default: DEFAULT_NUMBER_OF_CARDS,
		},
		cardsProps: {
			type: 'array',
			default: defaultCardsProps,
		},
		gutter: {
			type: 'object',
			default: {
				desktop: 24,
				tablet: 24,
				mobile: 16,
			},
		},
		cornerRadius: {
			type: 'number',
			default: 4,
		},
		lightbox: {
			type: 'boolean',
			default: false,
		},
		outlined: {
			type: 'boolean',
			default: false,
		},
		allowIndividualStyleOverride: {
			type: 'boolean',
			default: false,
		},
		allowIndividualContentOverride: {
			type: 'boolean',
			default: false,
		},
		displayTitle: {
			type: 'boolean',
			default: true,
		},
		displaySecondaryText: {
			type: 'boolean',
			default: true,
		},
		displayImage: {
			type: 'boolean',
			default: true,
		},
		displaySupportingText: {
			type: 'boolean',
			default: true,
		},
		displayActions: {
			type: 'boolean',
			default: true,
		},
		displaySecondaryActionButton: {
			type: 'boolean',
			default: false,
		},
	},
	edit,
	save,
};
