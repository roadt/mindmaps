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

var MindmapService = function (baseUrl) {
    this._baseUrl = baseUrl;
    this._mindmaps = [];
    this._activeMindmap = undefined;
};

MindmapService.prototype = {
    find: function (id) {
        var returnObj = null;
        this._mindmaps.forEach(function (mindmap) {
            if (mindmap.id === id) {
                returnObj = mindmap;
            }
        });

        if (returnObj !== null) {
            return returnObj;
        }

        throw t('mindmaps', 'Could not find a mindmap with the given id.');
    },
    load: function (id) {
        var self = this;
        this._mindmaps.forEach(function (mindmap) {
            if (mindmap.id === id) {
                mindmap.active = true;
                self._activeMindmap = mindmap;
            } else {
                mindmap.active = false;
            }
        });
    },
    getActive: function () {
        return this._activeMindmap;
    },
    getAll: function () {
        return this._mindmaps;
    },
    loadAll: function () {
        var deferred = $.Deferred();
        var self = this;
        $.get(this._baseUrl).done(function (mindmaps) {
            self._activeMindmap = undefined;
            self._mindmaps = mindmaps;
            deferred.resolve(mindmaps);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    create: function (mindmap) {
        var deferred = $.Deferred();
        var self = this;
        $.ajax({
            url: this._baseUrl,
            data: mindmap,
            method: 'POST',
            dataType: 'json'
        }).done(function (mindmap) {
            self._mindmaps.push(mindmap);
            self._activeMindmap = mindmap;
            self.load(mindmap.id);
            deferred.resolve(mindmap);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    update: function (mindmap) {
        var deferred = $.Deferred();
        var self = this;
        var pos = this._mindmaps.indexOf(mindmap);
        $.ajax({
            url: this._baseUrl + '/' + mindmap.id,
            data: mindmap,
            method: 'PUT',
            dataType: 'json'
        }).done(function (mindmap) {
            self._mindmaps[pos] = mindmap;
            deferred.resolve(mindmap);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    delete: function (id) {
        var deferred = $.Deferred();
        var self = this;
        var pos = this._mindmaps.indexOf(this.find(parseInt(id)));
        $.ajax({
            url: this._baseUrl + '/' + id,
            method: 'DELETE'
        }).done(function () {
            self._mindmaps.splice(pos, 1);
            deferred.resolve();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    }
};
