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

namespace OCA\Mindmaps\Migration;

use Doctrine\DBAL\Types\Type;
use OC\DB\SchemaWrapper;
use OCA\Mindmaps\AppInfo\Application;
use OCP\Migration\{IOutput, SimpleMigrationStep};

/**
 * Installation class for the mindmaps app.
 */
class Version001Date20171202120000 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param \Closure $schemaClosure The `\Closure` returns a `SchemaWrapper`
	 * @param array $options
	 * @return null|SchemaWrapper
	 * @since 13.0.0
	 */
	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options): SchemaWrapper {
		/** @var SchemaWrapper $schema */
		$schema = $schemaClosure();

		// Create the main mindmaps table which holds basic information about the mindmap.
		if (!$schema->hasTable(Application::MINDMAPS_TABLE)) {
			$table = $schema->createTable(Application::MINDMAPS_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20
			]);
			$table->addColumn('title', Type::STRING, [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn('description', Type::STRING, [
				'notnull' => false,
				'length' => 255
			]);
			$table->addColumn('user_id', Type::STRING, [
				'notnull' => true,
				'length' => 64
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id']);
		}

		// Create the mindmap_nodes table which holds information about the mindmaps single nodes.
		if (!$schema->hasTable(Application::MINDMAP_NODES_TABLE)) {
			$table = $schema->createTable(Application::MINDMAP_NODES_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20
			]);
			$table->addColumn('mindmap_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 20
			]);
			$table->addColumn('parent_id', Type::INTEGER, [
				'notnull' => false,
				'length' => 20
			]);
			$table->addColumn('user_id', Type::STRING, [
				'notnull' => true,
				'length' => 64
			]);
			$table->addColumn('x', Type::INTEGER, [
				'notnull' => true,
				'length' => 10,
				'default' => 0
			]);
			$table->addColumn('y', Type::INTEGER, [
				'notnull' => true,
				'length' => 10,
				'default' => 0
			]);
			$table->addColumn('label', Type::STRING, [
				'notnull' => false,
				'length' => 255
			]);
			$table->addColumn('locked_by', Type::STRING, [
				'notnull' => false,
				'length' => 64
			]);
			$table->setPrimaryKey(['id']);
			$table->addForeignKeyConstraint(
				$schema->getTable(Application::MINDMAPS_TABLE),
				['mindmap_id'],
				['id']
			);
			$table->addForeignKeyConstraint(
				$schema->getTable(Application::MINDMAP_NODES_TABLE),
				['parent_id'],
				['id']
			);
		}

		// Create the mindmap_acl table which holds sharing information like user / group / circle name.
		if (!$schema->hasTable(Application::MINDMAP_ACL_TABLE)) {
			$table = $schema->createTable(Application::MINDMAP_ACL_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20
			]);
			$table->addColumn('mindmap_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 20
			]);
			$table->addColumn('type', Type::INTEGER, [
				'notnull' => true,
				'length' => 1
			]);
			$table->addColumn('participant', Type::STRING, [
				'notnull' => true,
				'length' => 64
			]);
			$table->setPrimaryKey(['id']);
			$table->addForeignKeyConstraint(
				$schema->getTable(Application::MINDMAPS_TABLE),
				['mindmap_id'],
				['id']
			);
		}
		return $schema;
	}
}
