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
use OCA\Mindmaps\Service\AclService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class AclController extends Controller {

    private $aclService;
    private $userId;

    /**
     * AclController constructor.
     *
     * @param string $appName
     * @param IRequest $request
     * @param AclService $aclService
     * @param string $userId
     */
    public function __construct($appName,
                                IRequest $request,
                                AclService $aclService,
                                $userId) {
        parent::__construct($appName, $request);
        $this->aclService = $aclService;
        $this->userId = $userId;
    }

    /**
     * Return all mindmap acls as json.
     *
     * @NoAdminRequired
     *
     * @param integer $mindmapId
     * @param integer|null $limit
     * @param integer|null $offset
     *
     * @return DataResponse
     */
    public function index($mindmapId, $limit = null, $offset = null) {
        return new DataResponse($this->aclService->findAll($mindmapId, $limit, $offset));
    }

    /**
     * Create a minmap acl entry with the given parameters.
     *
     * @NoAdminRequired
     *
     * @param integer $mindmapId
     * @param integer $type
     * @param string $participant
     *
     * @return DataResponse
     */
    public function create($mindmapId, $type, $participant) {
        try {
            return new DataResponse($this->aclService->create($mindmapId, $type, $participant));
        } catch (BadRequestException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        }
    }

    /**
     * Delete the given mindmap acl entry.
     *
     * @NoAdminRequired
     *
     * @param integer $aclId
     *
     * @return DataResponse
     */
    public function delete($aclId) {
        try {
            return new DataResponse($this->aclService->delete($aclId));
        } catch (NotFoundException $ex) {
            return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
        }
    }
}
