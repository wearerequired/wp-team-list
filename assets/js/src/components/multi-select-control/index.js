/**
 * External dependencies
 */
import { isEmpty, filter, includes } from 'lodash';
import Select from 'react-select';

/**
 * WordPress dependencies
 */
import { withInstanceId } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.css';

function MultiSelectControl( {
	help,
	instanceId,
	label,
	value,
	onChange,
	options = [],
	className,
	...props
} ) {
	const id = `inspector-multi-select-control-${ instanceId }`;

	const onChangeValue = ( values ) => {
		const newValues = values.map( ( { value } ) => value );
		onChange( newValues );
	};

	const optionsByValue = ( values ) => {
		return filter( options, ( option ) => includes( values, option.value ) );
	};

	return ! isEmpty( options ) && (
		<BaseControl label={ label } id={ id } help={ help } className={ className }>
			<Select
				className="components-multi-select-control"
				classNamePrefix="components-multi-select-control"
				value={ optionsByValue( value ) }
				onChange={ onChangeValue }
				options={ options }
				isMulti={ true }
				{ ...props }
			/>
		</BaseControl>
	);
}

export default withInstanceId( MultiSelectControl );
