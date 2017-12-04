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
use OCA\Mindmaps\Exception\NotFoundException;
use OCP\AppFramework\Db\{DoesNotExistException, Entity, MultipleObjectsReturnedException};

abstract class Service {

	/** @var \OCP\AppFramework\Db\Mapper */
	protected $mapper;

	/**
	 * Service constructor.
	 *
	 * @param \OCP\AppFramework\Db\Mapper $mapper
	 */
	public function __construct($mapper) {
		$this->mapper = $mapper;
	}

	/**
	 * Catch different exceptions and convert them to our own NotFoundException.
	 *
	 * @param Exception $ex
	 *
	 * @throws NotFoundException
	 * @throws Exception
	 */
	protected function handleException($ex): void {
		if ($ex instanceof DoesNotExistException || $ex instanceof MultipleObjectsReturnedException) {
			throw new NotFoundException();
		}
		throw $ex;
	}

	/**
	 * Find the entity by given id and user id.
	 *
	 * @param integer $id
	 *
	 * @return null|\OCP\AppFramework\Db\Entity
	 *
	 * @throws NotFoundException
	 * @throws Exception
	 */
	public function find($id): Entity {
		try {
			return $this->mapper->find($id);
		} catch (Exception $e) {
			$this->handleException($e);
		}
		return null;
	}

	/**
	 * Find and delete the entity by given id and user id.
	 *
	 * @param integer $id
	 *
	 * @return null|\OCP\AppFramework\Db\Entity
	 *
	 * @throws NotFoundException
	 * @throws Exception
	 */
	public function delete($id): Entity {
		try {
			$entity = $this->find($id);
			return $this->mapper->delete($entity);
		} catch (Exception $e) {
			$this->handleException($e);
		}
		return null;
	}
}
