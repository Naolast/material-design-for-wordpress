/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	InspectorControls,
	BlockControls,
	RichText,
	AlignmentToolbar,
} from '@wordpress/block-editor';
import {
	Button,
	DropdownMenu,
	PanelBody,
	Placeholder,
	TextControl,
	ToggleControl,
	ToolbarGroup,
} from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	alignLeft,
	alignRight,
	alignCenter,
	tableColumnAfter,
	tableColumnBefore,
	tableColumnDelete,
	tableRowAfter,
	tableRowBefore,
	tableRowDelete,
	table,
} from '@wordpress/icons';

/**
 * Internal dependencies
 */
import './styles.css';
import {
	createTable,
	updateSelectedCell,
	getCellAttribute,
	insertRow,
	deleteRow,
	insertColumn,
	deleteColumn,
	toggleSection,
	isEmptyTableSection,
} from './state';

const ALIGNMENT_CONTROLS = [
	{
		icon: alignLeft,
		title: __( 'Align Column Left', 'material-theme-builder' ),
		align: 'left',
	},
	{
		icon: alignCenter,
		title: __( 'Align Column Center', 'material-theme-builder' ),
		align: 'center',
	},
	{
		icon: alignRight,
		title: __( 'Align Column Right', 'material-theme-builder' ),
		align: 'right',
	},
];

const Section = ( { name, rows, onChange, createOnFocus, selectedCell } ) => {
	if ( isEmptyTableSection( rows ) ) {
		return null;
	}

	const Tag = `t${ name }`;
	const tagClass = 'body' === name ? 'mdc-data-table__content' : '';
	const trClass = classnames( {
		'mdc-data-table__header-row': 'head' === name,
		'mdc-data-table__row': 'head' !== name,
	} );

	return (
		<Tag className={ tagClass }>
			{ rows.map( ( { cells }, rowIndex ) => (
				<tr key={ rowIndex } className={ trClass }>
					{ cells.map(
						( { content, tag: CellTag, scope, align }, columnIndex ) => {
							const cellLocation = {
								sectionName: name,
								rowIndex,
								columnIndex,
							};

							const cellClasses = classnames(
								{
									[ `has-text-align-${ align }` ]: align,
								},
								'wp-block-table__cell-content'
							);

							const tdClasses = classnames( {
								'mdc-data-table__cell': 'head' !== name,
								'mdc-data-table__header-cell': 'head' === name,
								'is-selected':
									selectedCell &&
									selectedCell.sectionName === cellLocation.sectionName &&
									selectedCell.rowIndex === cellLocation.rowIndex &&
									selectedCell.columnIndex === cellLocation.columnIndex,
							} );

							let placeholder = '';
							if ( name === 'head' ) {
								placeholder = __( 'Header label', 'material-theme-builder' );
							} else if ( name === 'foot' ) {
								placeholder = __( 'Footer label', 'material-theme-builder' );
							}

							return (
								<CellTag
									key={ columnIndex }
									className={ tdClasses }
									onClick={ createOnFocus( cellLocation ) }
								>
									<RichText
										className={ cellClasses }
										scope={ CellTag === 'th' ? scope : undefined }
										value={ content }
										onChange={ onChange }
										unstableOnFocus={ createOnFocus( cellLocation ) }
										placeholder={ placeholder }
									/>
								</CellTag>
							);
						}
					) }
				</tr>
			) ) }
		</Tag>
	);
};

const DataTableEdit = ( { attributes, setAttributes, hasCaption } ) => {
	const { className, hasFixedLayout, caption, head, body, foot } = attributes;
	const [ selectedCell, setSelectedCell ] = useState( null );
	const [ initialColumnCount, setinitialColumnCount ] = useState( 2 );
	const [ initialRowCount, setinitialRowCount ] = useState( 2 );

	/**
	 * Changes the content of the currently selected cell.
	 *
	 * @param {Array} content A RichText content value.
	 */
	const onChange = content => {
		/* istanbul ignore next */
		if ( ! selectedCell ) {
			return;
		}

		setAttributes(
			updateSelectedCell( attributes, selectedCell, cellAttributes => ( {
				...cellAttributes,
				content,
			} ) )
		);
	};

	const createOnFocus = cellLocation => {
		return () => {
			setSelectedCell( {
				...cellLocation,
				type: 'cell',
			} );
		};
	};

	const isEmpty =
		isEmptyTableSection( head ) &&
		isEmptyTableSection( body ) &&
		isEmptyTableSection( foot );

	/**
	 * Handle create table form submit.
	 *
	 * @param {Event} event
	 */
	const onCreateTable = event => {
		event.preventDefault();

		setAttributes(
			createTable( {
				rowCount: initialRowCount,
				columnCount: initialColumnCount,
			} )
		);
	};

	/**
	 * Get the alignment of the currently selected cell.
	 *
	 * @return {string} The new alignment to apply to the column.
	 */
	const getCellAlignment = () => {
		if ( ! selectedCell ) {
			return;
		}

		return getCellAttribute( attributes, selectedCell, 'align' );
	};

	/**
	 * Add or remove a `head` table section.
	 */
	const onToggleHeaderSection = () => {
		setAttributes( toggleSection( attributes, 'head' ) );
	};

	/**
	 * Add or remove a `foot` table section.
	 */
	const onToggleFooterSection = () => {
		setAttributes( toggleSection( attributes, 'foot' ) );
	};

	/**
	 * Align text within the a column.
	 *
	 * @param {string} align The new alignment to apply to the column.
	 */
	const onChangeColumnAlignment = align => {
		if ( ! selectedCell ) {
			return;
		}

		// Convert the cell selection to a column selection so that alignment
		// is applied to the entire column.
		const columnSelection = {
			type: 'column',
			columnIndex: selectedCell.columnIndex,
		};

		const newAttributes = updateSelectedCell(
			attributes,
			columnSelection,
			cellAttributes => ( {
				...cellAttributes,
				align,
			} )
		);
		setAttributes( newAttributes );
	};

	/**
	 * Inserts a row at the currently selected row index, plus `delta`.
	 *
	 * @param {number} delta Offset for selected row index at which to insert.
	 */
	const onInsertRow = delta => {
		if ( ! selectedCell ) {
			return;
		}

		const { sectionName, rowIndex } = selectedCell;

		setSelectedCell( null );
		setAttributes(
			insertRow( attributes, {
				sectionName,
				rowIndex: rowIndex + delta,
			} )
		);
	};

	/**
	 * Inserts a row before the currently selected row.
	 */
	const onInsertRowBefore = () => {
		onInsertRow( 0 );
	};

	/**
	 * Inserts a row after the currently selected row.
	 */
	const onInsertRowAfter = () => {
		onInsertRow( 1 );
	};

	/**
	 * Deletes the currently selected row.
	 */
	const onDeleteRow = () => {
		if ( ! selectedCell ) {
			return;
		}

		const { sectionName, rowIndex } = selectedCell;

		setSelectedCell( null );
		setAttributes( deleteRow( attributes, { sectionName, rowIndex } ) );
	};

	/**
	 * Inserts a column at the currently selected column index, plus `delta`.
	 *
	 * @param {number} delta Offset for selected column index at which to insert.
	 */
	const onInsertColumn = ( delta = 0 ) => {
		if ( ! selectedCell ) {
			return;
		}

		const { columnIndex } = selectedCell;

		setSelectedCell( null );
		setAttributes(
			insertColumn( attributes, {
				columnIndex: columnIndex + delta,
			} )
		);
	};

	/**
	 * Inserts a column before the currently selected column.
	 */
	const onInsertColumnBefore = () => {
		onInsertColumn( 0 );
	};

	/**
	 * Inserts a column after the currently selected column.
	 */
	const onInsertColumnAfter = () => {
		onInsertColumn( 1 );
	};

	/**
	 * Deletes the currently selected column.
	 */
	const onDeleteColumn = () => {
		if ( ! selectedCell ) {
			return;
		}

		const { sectionName, columnIndex } = selectedCell;

		setSelectedCell( null );
		setAttributes( deleteColumn( attributes, { sectionName, columnIndex } ) );
	};

	const tableClasses = classnames( {
		'has-fixed-layout': hasFixedLayout,
	} );

	/**
	 * Gets the table controls to display in the block toolbar.
	 *
	 * @return {Array} Table controls.
	 */
	const getTableControls = () => {
		return [
			{
				icon: tableRowBefore,
				title: __( 'Add Row Before', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onInsertRowBefore,
			},
			{
				icon: tableRowAfter,
				title: __( 'Add Row After', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onInsertRowAfter,
			},
			{
				icon: tableRowDelete,
				title: __( 'Delete Row', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onDeleteRow,
			},
			{
				icon: tableColumnBefore,
				title: __( 'Add Column Before', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onInsertColumnBefore,
			},
			{
				icon: tableColumnAfter,
				title: __( 'Add Column After', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onInsertColumnAfter,
			},
			{
				icon: tableColumnDelete,
				title: __( 'Delete Column', 'material-theme-builder' ),
				isDisabled: ! selectedCell,
				onClick: onDeleteColumn,
			},
		];
	};

	if ( isEmpty ) {
		return (
			<Placeholder
				label={ __( 'Material Data Table', 'material-theme-builder' ) }
				icon={ <i className="material-icons-outlined">grid_on</i> }
				instructions={ __(
					'Data tables display sets of data across rows and columns.',
					'material-theme-builder'
				) }
			>
				<form
					className="blocks-table__placeholder-form"
					onSubmit={ onCreateTable }
				>
					<TextControl
						type="number"
						label={ __( 'Column count', 'material-theme-builder' ) }
						value={ initialColumnCount }
						onChange={ setinitialColumnCount }
						min="1"
						className="blocks-table__placeholder-input"
					/>
					<TextControl
						type="number"
						label={ __( 'Row count', 'material-theme-builder' ) }
						value={ initialRowCount }
						onChange={ setinitialRowCount }
						min="1"
						className="blocks-table__placeholder-input"
					/>
					<Button
						className="blocks-table__placeholder-button"
						isPrimary
						type="submit"
					>
						{ __( 'Create Table', 'material-theme-builder' ) }
					</Button>
				</form>
			</Placeholder>
		);
	}

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<DropdownMenu
						hasArrowIndicator
						icon={ table }
						label={ __( 'Edit table', 'material-theme-builder' ) }
						controls={ getTableControls() }
					/>
				</ToolbarGroup>
				<AlignmentToolbar
					label={ __( 'Change column alignment', 'material-theme-builder' ) }
					alignmentControls={ ALIGNMENT_CONTROLS }
					value={ getCellAlignment() }
					onChange={ nextAlign => onChangeColumnAlignment( nextAlign ) }
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={ __( 'Table settings', 'material-theme-builder' ) }
					className="blocks-table-settings"
				>
					<ToggleControl
						label={ __( 'Header section', 'material-theme-builder' ) }
						checked={ !! ( head && head.length ) }
						onChange={ onToggleHeaderSection }
					/>
					<ToggleControl
						label={ __( 'Footer section', 'material-theme-builder' ) }
						checked={ !! ( foot && foot.length ) }
						onChange={ onToggleFooterSection }
					/>
				</PanelBody>
			</InspectorControls>
			<figure
				className={ classnames(
					'wp-block-table',
					( className || '' ).replace( 'wp-block-table', '' ),
					{
						'is-selected': !! selectedCell,
					}
				) }
			>
				<div className="mdc-data-table">
					<table className={ tableClasses }>
						<Section
							name="head"
							rows={ head }
							onChange={ onChange }
							createOnFocus={ createOnFocus }
							selectedCell={ selectedCell }
						/>
						<Section
							name="body"
							rows={ body }
							onChange={ onChange }
							createOnFocus={ createOnFocus }
							selectedCell={ selectedCell }
						/>
						<Section
							name="foot"
							rows={ foot }
							onChange={ onChange }
							createOnFocus={ createOnFocus }
							selectedCell={ selectedCell }
						/>
					</table>
				</div>

				{ hasCaption && (
					<div className="mdc-data-table__caption">
						<RichText
							tagName="figcaption"
							placeholder={ __( 'Write caption…', 'material-theme-builder' ) }
							value={ caption }
							onChange={
								/* istanbul ignore next */
								value => setAttributes( { caption: value } )
							}
							// Deselect the selected table cell when the caption is focused.
							unstableOnFocus={
								/* istanbul ignore next */
								() => setSelectedCell( null )
							}
						/>
					</div>
				) }
			</figure>
		</>
	);
};

export default compose( [
	withSelect( select => {
		const tableBlock = select( 'core/blocks' ).getBlockType( 'core/table' );

		return {
			hasCaption:
				tableBlock &&
				tableBlock.attributes &&
				'caption' in tableBlock.attributes,
		};
	} ),
] )( DataTableEdit );
