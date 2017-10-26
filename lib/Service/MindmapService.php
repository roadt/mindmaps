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

namespace OCA\Mindmaps\Service;

use Exception;
use OCA\Mindmaps\Db\Mindmap;
use OCA\Mindmaps\Db\MindmapMapper;
use OCA\Mindmaps\Exception\BadRequestException;

class MindmapService extends Service {

    private $mindmapMapper;

    /**
     * MindmapService constructor.
     *
     * @param MindmapMapper $mindmapMapper
     */
    public function __construct(MindmapMapper $mindmapMapper) {
        parent::__construct($mindmapMapper);

        $this->mindmapMapper = $mindmapMapper;
    }

    /**
     * Return all mindmaps from mapper class by user id.
     *
     * @param string $userId
     *
     * @return array
     */
    public function findAll($userId) {
        return $this->mindmapMapper->findAll($userId);
    }

    /**
     * Create a new mindmap object and insert it via mapper class.
     *
     * @param string $title
     * @param string $description
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     *
     * @throws BadRequestException if parameters are invalid
     */
    public function create($title, $description, $userId) {
        if ($title === null || $title === '') {
            throw new BadRequestException();
        }

        $mindmap = new Mindmap();
        $mindmap->setTitle($title);
        $mindmap->setDescription($description);
        $mindmap->setUserId($userId);

        return $this->mindmapMapper->insert($mindmap);
    }

    /**
     * Find and update a given mindmap object.
     *
     * @param integer $mindmapId
     * @param string $title
     * @param string $description
     * @param string $userId
     *
     * @return \OCP\AppFramework\Db\Entity
     *
     * @throws BadRequestException if parameters are invalid
     */
    public function update($mindmapId, $title, $description, $userId) {
        if ($title === null || $title === '') {
            throw new BadRequestException();
        }

        try {
            $mindmap = $this->find($mindmapId);
            $mindmap->setTitle($title);
            if ($description !== null && $mindmap->getDescription() !== $description) {
                $mindmap->setDescription($description);
            }

            return $this->mindmapMapper->update($mindmap);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}
