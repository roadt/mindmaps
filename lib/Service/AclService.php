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

use OCA\Mindmaps\Db\{Acl, AclMapper};
use OCA\Mindmaps\Exception\BadRequestException;
use OCP\AppFramework\Db\Entity;

class AclService extends Service {

	/** @var AclMapper */
	private $aclMapper;

	/**
	 * AclService constructor.
	 *
	 * @param AclMapper $aclMapper
	 */
	public function __construct(AclMapper $aclMapper) {
		parent::__construct($aclMapper);

		$this->aclMapper = $aclMapper;
	}

	/**
	 * Return all acl entities for a specific mindmap grouped by limit and offset.
	 *
	 * @param integer $mindmapId
	 * @param null|integer $limit
	 * @param null|integer $offset
	 *
	 * @return \OCP\AppFramework\Db\Entity[]
	 */
	public function findAll($mindmapId, $limit = null, $offset = null): array {
		return $this->aclMapper->findAll($mindmapId, $limit, $offset);
	}

	/**
	 * Create a new acl object and insert it via mapper class.
	 *
	 * @param integer $mindmapId
	 * @param integer $type
	 * @param string $participant
	 *
	 * @return \OCP\AppFramework\Db\Entity
	 *
	 * @throws BadRequestException if parameters are invalid
	 */
	public function create($mindmapId, $type, $participant): Entity {
		if ($participant === null || $participant === '') {
			throw new BadRequestException();
		}

		$acl = new Acl();
		$acl->setMindmapId($mindmapId);
		$acl->setType($type);
		$acl->setParticipant($participant);

		return $this->aclMapper->insert($acl);
	}
}
