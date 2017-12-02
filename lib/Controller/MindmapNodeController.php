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

use OCA\Mindmaps\Exception\BadRequestException;
use OCA\Mindmaps\Exception\NotFoundException;
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
		$appName,
		IRequest $request,
		MindmapNodeService $mindmapNodeService,
		$userId
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
	 * @param integer $mindmapId
	 * @param null|integer $limit
	 * @param null|integer $offset
	 *
	 * @return DataResponse
	 */
	public function index($mindmapId, $limit = null, $offset = null): DataResponse {
		return new DataResponse($this->mindmapNodeService->findAll($mindmapId, $this->userId, $limit, $offset));
	}

	/**
	 * Create a mindmap node with the given parameters.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapId
	 * @param integer $parentId
	 * @param string $label
	 * @param integer $x
	 * @param integer $y
	 *
	 * @return DataResponse
	 */
	public function create($mindmapId, $parentId, $label, $x, $y): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->create($mindmapId, $parentId, $label, $x, $y,
				$this->userId));
		} catch (BadRequestException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Update a given mindmap node with the given parameters.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapNodeId
	 * @param integer $parentId
	 * @param string $label
	 * @param integer $x
	 * @param integer $y
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function update($mindmapNodeId, $parentId, $label, $x, $y) {
		try {
			return new DataResponse($this->mindmapNodeService->update($mindmapNodeId, $parentId, $label, $x, $y,
				$this->userId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		} catch (BadRequestException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Delete the given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapNodeId
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function delete($mindmapNodeId): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->delete($mindmapNodeId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Lock a given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapNodeId
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function lock($mindmapNodeId): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->lock($mindmapNodeId, $this->userId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}

	/**
	 * Unlock a given mindmap node.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapNodeId
	 *
	 * @return DataResponse
	 *
	 * @throws \Exception
	 */
	public function unlock($mindmapNodeId): DataResponse {
		try {
			return new DataResponse($this->mindmapNodeService->unlock($mindmapNodeId, $this->userId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}
}
