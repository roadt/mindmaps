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

import {Service} from './Service';
import Axios from "axios";
import {MindmapNode} from '../models/MindmapNode';

export class MindmapNodeService extends Service<MindmapNode> {
	constructor() {
		super('/apps/mindmaps/nodes');
	}

	load(mindmapId?: number) {
		if (typeof mindmapId === 'undefined') {
			throw new Error(t('mindmaps', 'Please specify a mindmapId.'));
		}

		return Axios.get(this.baseUrl + '/' + mindmapId, {
			headers: this.headers
		}).then((response) => {
			this.data = response.data;
			this.data.forEach((node) => {
				node.title = t('mindmaps', 'Author: ') + node.userId;
				if (node.lockedBy !== null) {
					node.title = node.title + t('mindmaps', ' / Locked by: ') +
						node.lockedBy;
					node.color = 'red';
				}
			});
			return response;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}

	remove(id: number) {
		let node: any = this.find(id);
		if (node.parentId === 0) {
			throw new Error(t('mindmaps', 'Root Node can´t be deleted.'));
		}
		return super.remove(id).then((response) => {
			let index = this.data.indexOf(node);
			this.data.splice(index, 1);
			return response;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}

	lock(id: number) {
		return Axios.post(this.baseUrl + '/' + id + '/locks',
			{},
			{
				headers: this.headers
			}
		).then((response) => {
			let node: any = this.find(id);
			let index = this.data.indexOf(node);
			node.title = t('mindmaps', 'Author: ') + node.userId +
				t('mindmaps', ' / Locked by: ') + node.lockedBy;
			node.color = 'red';
			this.data[index] = node;
			return response;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}

	unlock(id: number) {
		return Axios.delete(this.baseUrl + '/' + id + '/locks',
			{
				headers: this.headers
			}
		).then((response) => {
			let node: any = this.find(id);
			let index = this.data.indexOf(node);
			node.title = t('mindmaps', 'Author: ') + node.userId;
			node.color = '#97C2FC';
			this.data[index] = node;
			return response.data;
		}).catch((error) => {
			return Promise.reject(error.response);
		});
	}
}
