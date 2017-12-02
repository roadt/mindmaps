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

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Installation class for the mindmaps app.
 */
class Version001Date20171202132906 extends SimpleMigrationStep {

	const  MINDMAPS_TABLE = 'mindmaps';
	const  MINDMAP_NODES_TABLE = 'mindmap_nodes';
	const  MINDMAP_ACL_TABLE = 'mindmap_acl';

	/**
	 * @param IOutput $output
	 * @param \Closure $schemaClosure The `\Closure` returns a `Schema`
	 * @param array $options
	 * @return null|Schema
	 * @since 13.0.0
	 */
	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/** @var Schema $schema */
		$schema = $schemaClosure();

		// Create the main mindmaps table which holds basic information about the mindmap.
		if (!$schema->hasTable(static::MINDMAPS_TABLE)) {
			$table = $schema->createTable(static::MINDMAPS_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 11
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
			$table->addIndex(['user_id'], 'mindmaps_user_id_idx');
		}

		// Create the mindmap_nodes table which holds information about the mindmaps single nodes.
		if (!$schema->hasTable(static::MINDMAP_NODES_TABLE)) {
			$table = $schema->createTable(static::MINDMAP_NODES_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 11
			]);
			$table->addColumn('mindmap_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 11
			]);
			$table->addColumn('parent_id', Type::INTEGER, [
				'notnull' => false,
				'length' => 11
			]);
			$table->addColumn('user_id', Type::STRING, [
				'notnull' => true,
				'length' => 64
			]);
			$table->addColumn('x', Type::INTEGER, [
				'notnull' => true,
				'length' => 11,
				'default' => 0
			]);
			$table->addColumn('y', Type::INTEGER, [
				'notnull' => true,
				'length' => 11,
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
			$table->addForeignKeyConstraint(static::MINDMAP_NODES_TABLE, ['parent_id'], ['id']);
			$table->addForeignKeyConstraint(static::MINDMAPS_TABLE, ['mindmap_id'], ['id']);
		}

		// Create the mindmap_acl table which holds sharing information like user / group / circle name.
		if (!$schema->hasTable(static::MINDMAP_ACL_TABLE)) {
			$table = $schema->createTable(static::MINDMAP_ACL_TABLE);
			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 11
			]);
			$table->addColumn('mindmap_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 11
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
			$table->addForeignKeyConstraint(static::MINDMAPS_TABLE, ['mindmap_id'], ['id']);
		}
		return $schema;
	}
}
