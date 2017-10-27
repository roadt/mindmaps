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

namespace OCA\Mindmaps\Tests\Unit\Db;

use OCA\Mindmaps\Db\Mindmap;
use OCA\Mindmaps\Db\MindmapMapper;
use OCP\IDBConnection;
use PHPUnit_Framework_TestCase;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

class MindmapMapperTest extends PHPUnit_Framework_TestCase {

	/** @var IDBConnection */
	private $con;
	/** @var MindmapMapper */
	private $mindmapMapper;
	/** @var FactoryMuffin */
	private	$fm;

	/**
	 * {@inheritDoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->con = \OC::$server->getDatabaseConnection();
		$this->mindmapMapper = new MindmapMapper($this->con);
		$this->fm = new FactoryMuffin();
		$this->fm->loadFactories(__DIR__ . '/../Factories');
	}

	/**
	 * Test the creation of an mindmap object and save it to the database.
	 *
	 * @return Mindmap
	 */
	public function testCreate() {
		/** @var Mindmap $mindmap */
		$mindmap = $this->fm->instance('OCA\Mindmaps\Db\Mindmap');
		$this->assertInstanceOf(Mindmap::class, $this->mindmapMapper->insert($mindmap));
		return $mindmap;
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
		$mindmap->setTitle($title());
		$mindmap->setDescription($description());
		$this->mindmapMapper->update($mindmap);
		return $mindmap;
	}


	/**
	 * Delete the previously created mindmap from the database.
	 *
	 * @depends testUpdate
	 * @param Mindmap $mindmap
	 */
	public function testDelete(Mindmap $mindmap) {
		$this->mindmapMapper->delete($mindmap);
	}
}
