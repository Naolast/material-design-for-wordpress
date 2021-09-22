/**
 * Copyright 2021 Google LLC
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
import { omit } from 'lodash';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import save from './save';

const { attributes } = metadata;

const deprecated = [
	{
		attributes: { ...omit( attributes, [ 'imageElement', 'outlined' ] ) },
		save,
		migrate( attr ) {
			if ( 'undefined' === typeof attr.imageElement ) {
				attr = {
					...attr,
					...{
						imageElement: true,
					},
				};
			}

			if ( 'undefined' === typeof attr.outlined ) {
				attr = {
					...attr,
					...{
						cardStyle: 'filled',
					},
				};
			}
			return attr;
		},
		isEligible( attr ) {
			return (
				'undefined' === typeof attr.imageElement ||
				'undefined' === typeof attr.outlined
			);
		},
	},
];

export default deprecated;
