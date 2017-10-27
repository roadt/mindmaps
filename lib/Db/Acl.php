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

/**
 * @method string getParticipant()
 * @method void setParticipant(string $participant)
 * @method int getType()
 * @method void setType(int $type)
 * @method string getMindmapId()
 * @method void setMindmapId(int $mindmapId)
 */
class Acl extends Model implements JsonSerializable {

    const PERMISSION_TYPE_USER = 0;
    const PERMISSION_TYPE_GROUP = 1;

    protected $participant;
    protected $type;
    protected $mindmapId;

    /**
     * Acl constructor.
     */
    public function __construct() {
        $this->addType('type', 'integer');
        $this->addType('mindmapId', 'integer');
    }

    /**
     * Return object as json string.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'participant' => $this->participant,
			'type' => $this->type,
            'mindmapId' => $this->mindmapId
        ];
    }
}
