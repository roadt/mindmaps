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

namespace OCA\Mindmaps\Tests\Unit\Service;

use League\FactoryMuffin\Faker\Facade as Faker;
use OCA\Mindmaps\Db\AclMapper;
use OCA\Mindmaps\Db\Mindmap;
use OCA\Mindmaps\Db\MindmapMapper;
use OCA\Mindmaps\Db\MindmapNodeMapper;
use OCA\Mindmaps\Service\MindmapService;
use OCA\Mindmaps\Tests\Unit\UnitTestCase;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUserManager;

class MindmapServiceTest extends UnitTestCase {

	/** @var IDBConnection */
	private $con;
	/** @var MindmapService */
	private $mindmapService;
	/** @var MindmapMapper */
	private $mindmapMapper;
	/** @var MindmapNodeMapper */
	private $mindmapNodeMapper;
	/** @var AclMapper */
	private $aclMapper;
	/** @var IUserManager */
	private $userManager;
	/** @var IGroupManager */
	private $groupManager;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->con = \OC::$server->getDatabaseConnection();
		$this->aclMapper = new AclMapper($this->con);
		$this->mindmapNodeMapper = new MindmapNodeMapper($this->con);
		$this->userManager = $this->getMockBuilder(IUserManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->groupManager = $this->getMockBuilder(IGroupManager::class)
			->disableOriginalConstructor()
			->getMock();
		$this->mindmapMapper = new MindmapMapper(
			$this->con,
			$this->mindmapNodeMapper,
			$this->aclMapper,
			$this->groupManager,
			$this->userManager
		);
		$this->mindmapService = new MindmapService($this->mindmapMapper);
	}

	/**
	 * Test the creation of an mindmap object and save it to the database.
	 *
	 * @return Mindmap
	 */
	public function testCreate() {
		/** @var Mindmap $mindmap */
		$mindmap = $this->fm->instance(Mindmap::class);
		$mindmapTmp = $this->mindmapService->create($mindmap->getTitle(), $mindmap->getDescription(), $mindmap->getUserId());
		$this->assertInstanceOf(Mindmap::class, $mindmapTmp);
		return $mindmapTmp;
	}

	/**
	 * Update the previously created mindmap.
	 *
	 * @depends testCreate
	 * @param Mindmap $mindmap
	 * @return Mindmap
	 */
	public function testUpdate(Mindmap $mindmap) {
		$title = Faker::sentence(10);
		$description = Faker::sentence(20);
		$this->mindmapService->update($mindmap->getId(), $title(), $description(), $mindmap->getUserId());
		return $mindmap;
	}

	/**
	 * Delete the previously created mindmap from the database.
	 *
	 * @depends testUpdate
	 * @param Mindmap $mindmap
	 */
	public function testDelete(Mindmap $mindmap) {
		$this->mindmapService->delete($mindmap->id);
	}
}
