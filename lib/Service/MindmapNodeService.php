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

namespace OCA\Mindmaps\Service;

use Exception;
use OCA\Mindmaps\Db\MindmapNode;
use OCA\Mindmaps\Db\MindmapNodeMapper;
use OCA\Mindmaps\Exception\BadRequestException;

class MindmapNodeService extends Service {

    private $mindmapNodeMapper;

    /**
     * MindmapNodeService constructor.
     *
     * @param MindmapNodeMapper $mindmapNodeMapper
     */
    public function __construct(MindmapNodeMapper $mindmapNodeMapper) {
        parent::__construct($mindmapNodeMapper);

        $this->mindmapNodeMapper = $mindmapNodeMapper;
    }

    /**
     * Return all mindmap nodes from mapper class by user id and mindmap id.
     *
     * @param integer $mindmapId
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity[]
     */
    public function findAll($mindmapId, $userId) {
        return $this->mindmapNodeMapper->findAll($mindmapId);
    }

    /**
     * Create a new mindmap node object and insert it via mapper class.
     *
     * @param integer $mindmapId
     * @param integer $parentId
     * @param string $label
     * @param integer $x
     * @param integer $y
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     *
     * @throws BadRequestException if parameters are invalid
     */
    public function create($mindmapId, $parentId, $label, $x, $y, $userId) {
        if (!is_int($mindmapId) || $label === null || $label === '') {
            throw new BadRequestException();
        }

        $mindmapNode = new MindmapNode();
        $mindmapNode->setMindmapId($mindmapId);
        $mindmapNode->setParentId($parentId);
        $mindmapNode->setLabel($label);
        $mindmapNode->setX($x);
        $mindmapNode->setY($y);
        $mindmapNode->setUserId($userId);

        return $this->mindmapNodeMapper->insert($mindmapNode);
    }

    /**
     * Find and update a given mindmap node object.
     *
     * @param integer $mindmapNodeId
     * @param integer $parentId
     * @param string $label
     * @param integer $x
     * @param integer $y
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     *
     * @throws BadRequestException if parameters are invalid
     */
    public function update($mindmapNodeId, $parentId, $label, $x, $y, $userId) {
        if ($label === null || $label === '') {
            throw new BadRequestException();
        }

        try {
            $mindmapNode = $this->find($mindmapNodeId);
            $mindmapNode->setParentId($parentId);
            $mindmapNode->setLabel($label);
            $mindmapNode->setX($x);
            $mindmapNode->setY($y);

            return $this->mindmapNodeMapper->update($mindmapNode);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        return null;
    }

    /**
     * Find and lock a given mindmap node object.
     *
     * @param integer $mindmapNodeId
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     */
    public function lock($mindmapNodeId, $userId) {
        try {
            $mindmapNode = $this->find($mindmapNodeId);
            $mindmapNode->setLockedBy($userId);

            return $this->mindmapNodeMapper->update($mindmapNode);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        return null;
    }

    /**
     * Find and unlock a given mindmap node object.
     *
     * @param integer $mindmapNodeId
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     */
    public function unlock($mindmapNodeId, $userId) {
        try {
            $mindmapNode = $this->find($mindmapNodeId);
            $mindmapNode->setLockedBy(null);

            return $this->mindmapNodeMapper->update($mindmapNode);
        } catch (Exception $e) {
            $this->handleException($e);
        }
        return null;
    }
}
