<?php
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

namespace OCA\Mindmaps\Db;

use OCA\Mindmaps\AppInfo\Application;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;

class MindmapNodeMapper extends Mapper {

	/**
	 * MindmapNodeMapper constructor.
	 *
	 * @param IDBConnection $db
	 */
	public function __construct(IDBConnection $db) {
		parent::__construct($db, Application::MINDMAP_NODES_TABLE);
	}

	/**
	 * Return a mindmap node object by given id.
	 *
	 * @param integer $id
	 *
	 * @return \OCP\AppFramework\Db\Entity
	 *
	 * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
	 */
	public function find($id): Entity {
		$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?';
		return $this->findEntity($sql, [$id]);
	}

	/**
	 * Return all mindmap nodes for a given mindmap.
	 *
	 * @param integer $mindmapId
	 * @param null|integer $limit
	 * @param null|integer $offset
	 *
	 * @return \OCP\AppFramework\Db\Entity[]
	 */
	public function findAll($mindmapId, $limit = null, $offset = null): array {
		$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE mindmap_id = ?';
		return $this->findEntities($sql, [$mindmapId], $limit, $offset);
	}

	/**
	 * Delete all child nodes for a given mindmap.
	 *
	 * @param integer $mindmapId
	 */
	public function deleteByMindmapId($mindmapId) {
		$mindmapNodes = $this->findAll($mindmapId);
		foreach ($mindmapNodes as $node) {
			$this->delete($node);
		}
	}
}
