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

namespace OCA\Mindmaps\Controller;

use OCA\Mindmaps\Exception\BadRequestException;
use OCA\Mindmaps\Exception\NotFoundException;
use OCA\Mindmaps\Service\MindmapService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MindmapController extends Controller {

    private $mindmapService;
    private $userId;

    /**
     * MindmapController constructor.
     *
     * @param string $appName
     * @param IRequest $request
     * @param MindmapService $mindmapService
     * @param string $userId
     */
    public function __construct($appName,
                                IRequest $request,
                                MindmapService $mindmapService,
                                $userId) {
        parent::__construct($appName, $request);
        $this->mindmapService = $mindmapService;
        $this->userId = $userId;
    }

    /**
     * Return all mindmaps as json.
     *
     * @NoAdminRequired
     *
     * @return DataResponse
     */
    public function index() {
        return new DataResponse($this->mindmapService->findAll($this->userId));
    }

    /**
     * Create a minmap with the given parameters.
     *
     * @NoAdminRequired
     *
     * @param string $title
     * @param string $description
     *
     * @return DataResponse
     */
    public function create($title, $description) {
        try {
            return new DataResponse($this->mindmapService->create($title, $description, $this->userId));
        } catch (BadRequestException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        }
    }

    /**
     * Update a given mindmap with the given parameters.
     *
     * @NoAdminRequired
     *
     * @param integer $mindmapId
     * @param string $title
     * @param string $description
     *
     * @return DataResponse
     */
    public function update($mindmapId, $title, $description) {
        try {
            return new DataResponse($this->mindmapService->update($mindmapId, $title, $description, $this->userId));
        } catch (NotFoundException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        } catch (BadRequestException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        }
    }

    /**
     * Delete the given mindmap.
     *
     * @NoAdminRequired
     *
     * @param integer $mindmapId
     *
     * @return DataResponse
     */
    public function delete($mindmapId) {
        try {
            return new DataResponse($this->mindmapService->delete($mindmapId));
        } catch (NotFoundException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        }
    }
}
