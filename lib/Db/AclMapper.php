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

use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;

class AclMapper extends Mapper {

    /**
     * AclMapper constructor.
     *
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'mindmap_acl');
    }

    /**
     * Return a acl object by given id.
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
     * Return a acl object by given type and participant name.
     *
     * @param integer $type
     * @param string $participant
     *
     * @return \OCP\AppFramework\Db\Entity
     *
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function findByParticipant($type, $participant) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id = ? AND type = ? AND participant = ?';
        return $this->findEntity($sql, [$type, $participant]);
    }

    /**
     * Return all acl entities for a specific mindmap grouped by limit and offset.
     *
     * @param $mindmapId
     * @param integer|null $limit
     * @param integer|null $offset
     *
     * @return \OCP\AppFramework\Db\Entity[]
     */
    public function findAll($mindmapId, $limit = null, $offset = null) {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE mindmap_id = ?';
        return $this->findEntities($sql, [$mindmapId], $limit, $offset);
    }

	/**
	 * Delete all acls for a given mindmap.
	 *
	 * @param integer $mindmapId
	 */
	public function deleteByMindmapId($mindmapId) {
		$acls = $this->findAll($mindmapId);
		foreach ($acls as $acl) {
			$this->delete($acl);
		}
	}
}
