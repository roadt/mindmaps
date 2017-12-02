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
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Update class for the mindmaps app.
 */
class Version001Date20171202130000 extends SimpleMigrationStep {

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

		// To create parent id foreign key the mindmap nodes table need to already exist.
		if (!$schema->hasTable(Application::MINDMAP_NODES_TABLE)) {
			$table = $schema->getTable(Application::MINDMAP_NODES_TABLE);
			$table->addForeignKeyConstraint(Application::MINDMAP_NODES_TABLE, ['parent_id'], ['id']);
		}
		return $schema;
	}
}
