<?php
/**
 * @copyright Copyright (c) 2017 Kai Schröer <kai@schroeer.co>
 *
 * @author Kai Schröer <kai@schroeer.co>
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

use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;

class MindmapNodeMapper extends Mapper {

    /**
     * MindmapNodeMapper constructor.
     *
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'mindmap_nodes');
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
    public function find($id) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?';
        return $this->findEntity($sql, [$id]);
    }

    /**
     * Return all mindmap nodes for a given mindmap.
     *
     * @param integer $mindmapId
     *
     * @return array
     */
    public function findAll($mindmapId) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE mindmap_id = ?';
        return $this->findEntities($sql, [$mindmapId]);
    }
}
