/**
 * WordPress dependencies
 */
import { useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import StepContext from '../../context';
import { Check } from '../svg/check';

/**
 * Bullet inside progress bar
 *
 * @param {*} props Inherited props
 */
const Step = props => {
	const { state } = useContext( StepContext );
	const isActive = props.id === state.active;
	const isChecked = state.previous.includes( props.id );
	let status = '';

	if ( isActive ) {
		status = 'active';
	} else if ( isChecked ) {
		status = 'checked';
	}

	return (
		<li className={ status }>
			<span className="material-wizard__progress-dot"></span>
			<span className="material-wizard__progress-check">
				<Check />
			</span>
		</li>
	);
};

export default Step;
