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

return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

        // mindmaps
        ['name' => 'mindmap#index', 'url' => '/mindmaps', 'verb' => 'GET'],
        ['name' => 'mindmap#create', 'url' => '/mindmaps', 'verb' => 'POST'],
        ['name' => 'mindmap#update', 'url' => '/mindmaps/{mindmapId}', 'verb' => 'PUT'],
        ['name' => 'mindmap#delete', 'url' => '/mindmaps/{mindmapId}', 'verb' => 'DELETE'],

        // mindmap nodes
        ['name' => 'mindmapNode#index', 'url' => '/nodes/{mindmapId}', 'verb' => 'GET'],
        ['name' => 'mindmapNode#create', 'url' => '/nodes', 'verb' => 'POST'],
        ['name' => 'mindmapNode#update', 'url' => '/nodes/{mindmapNodeId}', 'verb' => 'PUT'],
        ['name' => 'mindmapNode#delete', 'url' => '/nodes/{mindmapNodeId}', 'verb' => 'DELETE'],

        // mindmap node locks
        ['name' => 'mindmapNode#lock', 'url' => '/nodes/{mindmapNodeId}/locks', 'verb' => 'POST'],
        ['name' => 'mindmapNode#unlock', 'url' => '/nodes/{mindmapNodeId}/locks', 'verb' => 'DELETE'],

        // mindmap acls
        ['name' => 'acl#index', 'url' => '/acl/{mindmapId}', 'verb' => 'GET'],
        ['name' => 'acl#create', 'url' => '/acl', 'verb' => 'POST'],
        ['name' => 'acl#delete', 'url' => '/acl/{aclId}', 'verb' => 'DELETE']
    ]
];
