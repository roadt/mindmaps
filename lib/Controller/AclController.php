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
use OCA\Mindmaps\Service\AclService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\{IL10N, IRequest, IUserManager, IGroupManager, Share};
use OCP\Share\IManager;

class AclController extends Controller {

	/** @var AclService */
	private $aclService;
	/** @var IUserManager */
	private $userManager;
	/** @var IGroupManager */
	private $groupManager;
	/** @var IManager */
	private $shareManager;
	/** @var IL10N */
	private $l10n;
	/** @var string */
	private $userId;

	/**
	 * AclController constructor.
	 *
	 * @param string $appName
	 * @param IRequest $request
	 * @param AclService $aclService
	 * @param IUserManager $userManager
	 * @param IGroupManager $groupManager
	 * @param IManager $shareManager
	 * @param IL10N $l10n
	 * @param string $userId
	 */
	public function __construct(
		$appName,
		IRequest $request,
		AclService $aclService,
		IUserManager $userManager,
		IGroupManager $groupManager,
		IManager $shareManager,
		IL10N $l10n,
		$userId
	) {
		parent::__construct($appName, $request);
		$this->aclService = $aclService;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->shareManager = $shareManager;
		$this->l10n = $l10n;
		$this->userId = $userId;
	}

	/**
	 * Return all mindmap acls as json.
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
		return new DataResponse($this->aclService->findAll($mindmapId, $limit, $offset));
	}

	/**
	 * Create a mindmap acl entry with the given parameters.
	 *
	 * @NoAdminRequired
	 *
	 * @param integer $mindmapId
	 * @param integer $type
	 * @param string $participant
	 *
	 * @return DataResponse
	 */
	public function create($mindmapId, $type, $participant): DataResponse {
		try {
			if ($type === Share::SHARE_TYPE_USER) {
				// Valid user is required to share
				if ($participant === null || !$this->userManager->userExists($participant)) {
					throw new NotFoundException($this->l10n->t('Please specify a valid user'));
				}
			} else {
				if ($type === Share::SHARE_TYPE_GROUP) {
					if (!$this->shareManager->allowGroupSharing()) {
						throw new NotFoundException($this->l10n->t('Group sharing is disabled by the administrator'));
					}

					// Valid group is required to share
					if ($participant === null || !$this->groupManager->groupExists($participant)) {
						throw new NotFoundException($this->l10n->t('Please specify a valid group'));
					}
				} else {
					if ($type === Share::SHARE_TYPE_CIRCLE) {
						// Circles app is required to share with a circle
						if (!class_exists('\OCA\Circles\ShareByCircleProvider') ||
							!\OC::$server->getAppManager()->isEnabledForUser('circles')) {
							throw new NotFoundException($this->l10n->t('You cannot share to a circle if the app is not enabled'));
						}

						/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
						$circle = \OCA\Circles\Api\v1\Circles::detailsCircle($participant);

						// Valid circle is required to share
						if ($circle === null) {
							throw new NotFoundException($this->l10n->t('Please specify a valid circle'));
						}
					}
				}
			}

			return new DataResponse($this->aclService->create($mindmapId, $type, $participant));
		} catch (BadRequestException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		} catch (NotFoundException $ex) {
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
	 *
	 * @throws \Exception
	 */
	public function delete($aclId): DataResponse {
		try {
			return new DataResponse($this->aclService->delete($aclId));
		} catch (NotFoundException $ex) {
			return new DataResponse(array('msg' => $ex->getMessage()), $ex->getCode());
		}
	}
}
