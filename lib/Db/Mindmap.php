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

namespace OCA\Mindmaps\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method int getAclId()
 * @method void setAclId(int $aclId)
 */
class Mindmap extends Entity implements JsonSerializable {

    protected $title;
    protected $description;
    protected $userId;
    protected $shared;

    /**
     * Mindmap constructor.
     */
    public function __construct() {
        $this->addType('shared', 'boolean');
    }

    /**
     * Return object as json string.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'userId' => $this->userId,
            'shared' => $this->shared
        ];
    }
}
