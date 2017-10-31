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

'use strict';

var MindmapNodeService = function (baseUrl) {
    this._baseUrl = baseUrl;
    this._nodes = [];
};

MindmapNodeService.prototype = {
    find: function (id) {
        var returnObj = null;
        this._nodes.forEach(function (node) {
            if (node.id === id) {
                returnObj = node;
            }
        });

        if (returnObj !== null) {
            return returnObj;
        }

        throw t('mindmaps', 'Could not find a mindmap node with the given id.');
    },
    getAll: function () {
        return this._nodes;
    },
    loadAll: function (id) {
        var deferred = $.Deferred();
        var self = this;
        $.get(this._baseUrl + '/' + id).done(function (nodes) {
            self._nodes = nodes;
            self._nodes.forEach(function (node) {
                node.title = t('mindmaps', 'Author: ') + node.userId;
                if (node.lockedBy !== null) {
                    node.title = node.title + t('mindmaps', ' / Locked by: ') + node.lockedBy;
                    node.color = 'red';
                }
            });
            deferred.resolve(nodes);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    create: function (node) {
        var deferred = $.Deferred();
        var self = this;
        $.ajax({
            url: this._baseUrl,
            data: node,
            method: 'POST',
            dataType: 'json'
        }).done(function (node) {
            self._nodes.push(node);
            deferred.resolve(node);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    update: function (node) {
        var deferred = $.Deferred();
        var self = this;
        var pos = this._nodes.indexOf(node);
        $.ajax({
            url: this._baseUrl + '/' + node.id,
            data: node,
            method: 'PUT',
            dataType: 'json'
        }).done(function (node) {
            self._nodes[pos] = node;
            deferred.resolve();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    delete: function (id) {
        var deferred = $.Deferred();
        var self = this;
        var node = this.find(parseInt(id));
        var pos = this._nodes.indexOf(node);
        if (node.parentId === 0) {
            deferred.reject();
        } else {
            $.ajax({
                url: this._baseUrl + '/' + id,
                method: 'DELETE'
            }).done(function () {
                self._nodes.splice(pos, 1);
                deferred.resolve();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                deferred.reject(jqXHR, textStatus, errorThrown);
            });
        }
        return deferred.promise();
    },
    lock: function (id) {
        var deferred = $.Deferred();
        var self = this;
        var node = this.find(parseInt(id));
        var pos = this._nodes.indexOf(node);
        $.ajax({
            url: this._baseUrl + '/' + id + '/locks',
            method: 'POST'
        }).done(function (node) {
            node.title = t('mindmaps', 'Author: ') + node.userId + t('mindmaps', ' / Locked by: ') + node.lockedBy;
            node.color = 'red';
            self._nodes[pos] = node;
            deferred.resolve(node);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    unlock: function (id) {
        var deferred = $.Deferred();
        var self = this;
        var node = this.find(parseInt(id));
        var pos = this._nodes.indexOf(node);
        $.ajax({
            url: this._baseUrl + '/' + id + '/locks',
            method: 'DELETE'
        }).done(function (node) {
            node.title = t('mindmaps', 'Author: ') + node.userId;
            node.color = '#97C2FC';
            self._nodes[pos] = node;
            deferred.resolve(node);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    }
};

