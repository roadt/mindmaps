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

use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUserManager;

class MindmapMapper extends Mapper {

	private $mindmapNodeMapper;
	private $aclMapper;
	private $groupManager;
	private $userManager;

    /**
     * MindmapMapper constructor.
     *
     * @param IDBConnection $db
	 * @param MindmapNodeMapper $mindmapNodeMapper
	 * @param AclMapper $aclMapper
	 * @param IGroupManager $groupManager
	 * @param IUserManager $userManager
     */
    public function __construct(
		IDBConnection $db,
		MindmapNodeMapper $mindmapNodeMapper,
		AclMapper $aclMapper,
		IGroupManager $groupManager,
		IUserManager $userManager
	) {
        parent::__construct($db, 'mindmaps');
		$this->mindmapNodeMapper = $mindmapNodeMapper;
		$this->aclMapper = $aclMapper;
		$this->groupManager = $groupManager;
		$this->userManager = $userManager;
    }

	/**
	 * Converts an array to an SQL string list sth. like 'test', 'test' which can be wrapped by IN ().
	 *
	 * @param array $array
	 * @return string
	 */
	private function arrayToSqlList(array $array) {
		$result = '';
		foreach ($array as $key => $value) {
			$result .= "'" . $value . "'" . (($key !== \count($array) -1) ? ', ' : '');
		}
		return $result;
	}

    /**
     * Return a mindmap object by given id.
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
     * Return all mindmaps for a specific user also includes shared mindmaps.
     *
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity[]
     */
    public function findAll($userId) {
    	// Get circle ids for the given user
		$circleIds = [];
		if (class_exists('\OCA\Circles\Api\v1\Circles')) {
			/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
			$userCircles = \OCA\Circles\Api\v1\Circles::listCircles(\OCA\Circles\Model\Circle::CIRCLES_ALL);
			foreach ($userCircles as $circle) {
				$circleIds[] =  $circle->getUniqueId();
			}
		}
		// Get group ids for the given user
		$user = $this->userManager->get($userId);
		$groupIds = $this->groupManager->getUserGroupIds($user);

		// Build the SQL string
        $sql = 'SELECT ' .
                '  DISTINCT(*PREFIX*mindmap_acl.mindmap_id) IS NOT NULL AS shared, ' .
                '  ' . $this->getTableName() . '.* ' .
                'FROM ' . $this->getTableName() . ' ' .
                '  LEFT JOIN *PREFIX*mindmap_acl ON ' . $this->getTableName() . '.id = *PREFIX*mindmap_acl.mindmap_id ' .
                'WHERE ' . $this->getTableName() . '.user_id = ? OR ' .
                '      *PREFIX*mindmap_acl.participant = ? AND *PREFIX*mindmap_acl.type = ? OR ' .
                '      *PREFIX*mindmap_acl.participant IN (' . $this->arrayToSqlList($groupIds) . ') AND *PREFIX*mindmap_acl.type = ? OR ' .
				'      *PREFIX*mindmap_acl.participant IN (' . $this->arrayToSqlList($circleIds) . ') AND *PREFIX*mindmap_acl.type = ? ' .
                'ORDER BY ' . $this->getTableName() . '.id';

        return $this->findEntities(
        	$sql,
			[
				$userId,
				$userId,
				\OCP\Share::SHARE_TYPE_USER,
				\OCP\Share::SHARE_TYPE_GROUP,
				\OCP\Share::SHARE_TYPE_CIRCLE
			]
		);
    }

	/**
	 * Deletes an entity from the table.
	 *
	 * @param \OCP\AppFramework\Db\Entity $entity the entity that should be deleted
	 *
	 * @return \OCP\AppFramework\Db\Entity the deleted entity
	 */
	public function delete(Entity $entity) {
		$this->mindmapNodeMapper->deleteByMindmapId($entity->getId());
		$this->aclMapper->deleteByMindmapId($entity->getId());
		return parent::delete($entity);
	}
}
