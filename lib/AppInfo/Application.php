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

namespace OCA\Mindmaps\AppInfo;

use OCA\Mindmaps\Db\AclMapper;
use OCP\AppFramework\App;
use OCP\IGroup;
use OCP\IUser;
use OCP\IUserManager;

class Application extends App {

	// The used table names.
	public const  MINDMAPS_TABLE = 'mindmaps';
	public const  MINDMAP_NODES_TABLE = 'mindmap_nodes';
	public const  MINDMAP_ACL_TABLE = 'mindmap_acl';

	/**
	 * Application constructor.
	 *
	 * @param array $urlParams
	 */
	public function __construct(array $urlParams = array()) {
		parent::__construct('mindmaps', $urlParams);

		$container = $this->getContainer();
		$server = $container->getServer();

		// Delete user acl entries when they get deleted
		/** @var IUserManager $userManager */
		$userManager = $server->getUserManager();
		$userManager->listen('\OC\User', 'postDelete', function (IUser $user) use ($container) {
			/** @var AclMapper $aclMapper */
			$aclMapper = $container->query(AclMapper::class);
			$acls = $aclMapper->findByParticipant(Share::SHARE_TYPE_USER, $user->getUID());
			foreach ($acls as $acl) {
				$aclMapper->delete($acl);
			}
		});

		// Delete group acl entries when they get deleted
		/** @var IUserManager $userManager */
		$groupManager = $server->getGroupManager();
		$groupManager->listen('\OC\Group', 'postDelete', function (IGroup $group) use ($container) {
			/** @var AclMapper $aclMapper */
			$aclMapper = $container->query(AclMapper::class);
			$acls = $aclMapper->findByParticipant(Share::SHARE_TYPE_GROUP, $group->getGID());
			foreach ($acls as $acl) {
				$aclMapper->delete($acl);
			}
		});
	}

	/**
	 * Register navigation entry for main navigation.
	 *
	 * @throws \OCP\AppFramework\QueryException
	 */
	public function registerNavigationEntry() {
		$container = $this->getContainer();
		$container->query('OCP\INavigationManager')->add(function () use ($container) {
			$urlGenerator = $container->query('OCP\IURLGenerator');
			$l10n = $container->query('OCP\IL10N');
			return [
				'id' => 'mindmaps',
				'order' => 10,
				'href' => $urlGenerator->linkToRoute('mindmaps.page.index'),
				'icon' => $urlGenerator->imagePath('mindmaps', 'app.svg'),
				'name' => $l10n->t('Mindmaps')
			];
		});
	}
}
