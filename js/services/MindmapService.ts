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
import {Mindmap} from '../models/Mindmap';

export class MindmapService extends Service<Mindmap> {
	private active: Mindmap | null;

	constructor() {
		super('/apps/mindmaps/mindmaps');
		this.active = null;
	}

	getActive(): Mindmap | null {
		return this.active;
	}

	setActive(obj: Mindmap): void {
		this.active = obj;
	}
}
