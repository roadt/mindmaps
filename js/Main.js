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

(function (OC, window, $, undefined) {
    'use strict';

    $(document).ready(function () {
        var mindmaps = new MindmapService(OC.generateUrl('/apps/mindmaps/mindmaps'));
        var nodes = new MindmapNodeService(OC.generateUrl('/apps/mindmaps/nodes'));
        var acl = new AclService(OC.generateUrl('/apps/mindmaps/acl'));
        var view = new View(mindmaps, nodes, acl);

        view.render();

        setInterval(function () {
            view.render();
        }, 30 * 1000);
    });
})(OC, window, jQuery);
