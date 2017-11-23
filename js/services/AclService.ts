/**
 * @copyright Copyright (c) 2017 Kai Schröer <git@schroeer.co>
 *
 * @author Kai Schröer <git@schroeer.co>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

import Service from './Service';
import Acl from '../models/Acl';
import {default as Axios, AxiosPromise} from 'axios';
import System from '../System';

export default class AclService extends Service<Acl> {
	constructor() {
		super('/apps/mindmaps/acl');
	}

	load(mindmapId?: number): AxiosPromise {
		if (typeof mindmapId === 'undefined') {
			throw new Error(System.t('Please specify a mindmapId.'));
		}

		return Axios.get(this.baseUrl + '/' + mindmapId, {
			headers: this.headers
		}).then((response) => {
			this.data = response.data;
			return response;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}

	getAutocomplete(search: string): AxiosPromise {
		return Axios.get(
			OC.linkToOCS('apps/files_sharing/api/v1') + 'sharees',
			{
				params: {
					format: 'json',
					search: search.trim(),
					perPage: 10,
					itemType: [
						OC.Share.SHARE_TYPE_USER,
						OC.Share.SHARE_TYPE_GROUP,
						OC.Share.SHARE_TYPE_CIRCLE
					]
				},
				headers: this.headers
			}
		).then((response) => {
			if (response.data.ocs.meta.statuscode !== 100) {
				return Promise.reject('Error while searching.');
			}
			return response;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}
}
