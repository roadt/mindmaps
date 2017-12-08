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

namespace OCA\Mindmaps\Controller;

use OCA\Mindmaps\Exception\{BadRequestException, NotFoundException};
use OCA\Mindmaps\Service\MindmapNodeService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MindmapNodeController extends Controller {

	/** @var MindmapNodeService */
	private $mindmapNodeService;
	/** @var string */
	private $userId;

	/**
	 * MindmapNodeController constructor.
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param MindmapNodeService $mindmapNodeService
	 * @param string $userId
	 */
	public function __construct(
		string $appName,
		IRequest $request,
		MindmapNodeService $mindmapNodeService,
		string $userId
	) {
		parent::__construct($appName, $request);
		$this->mindmapNodeService = $mindmapNodeService;
		$this->userId = $userId;
	}

	/**
	 * Return all nodes for a given mindmap as json.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $mindmapId
	 * @param null|int $limit
	 * @param null|int $offset
	 *
	 * @return DataResponse
	 */
	public function index(int $mindmapId, int $limit = null, int $offset = null): DataResponse {
		return new DataResponse(
			$this->mindmapNodeService->findAll($mindmapId, $this->userId, $limit, $offset)
		);
	}

	/**
	 * Create a mindmap node with the given parameters.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $mindmapId
	 * @param int $parentId
	 * @param string $label
	 * @param int $x
	 * @param int $y
	 *
	 * @return DataResponse
	 */
	public function create(int $mindmapId, int $parentId, string $label, int $x, int $y): DataResponse {
		try {
			return new DataResponse(
				$this->mindmapNodeService->create(
					$mindmapId,
					$parentId,
					$label,
					$x,
					$y,
					$this->userId
				)
			);
		} catch (BadRequestException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Update a given mindmap node with the given parameters.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @param int $parentId
	 * @param string $label
	 * @param int $x
	 * @param int $y
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function update(int $id, int $parentId, string $label, int $x, int $y) {
		try {
			return new DataResponse(
				$this->mindmapNodeService->update(
					$id,
					$parentId,
					$label,
					$x,
					$y,
					$this->userId
				)
			);
		} catch (BadRequestException | NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Delete a given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $id
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function delete(int $id): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->delete($id, $this->userId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Lock a given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $id
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function lock(int $id): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->lock($id, $this->userId));
		} catch (BadRequestException | NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Unlock a given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param int $id
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function unlock(int $id): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->unlock($id, $this->userId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}
}
