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

var AclService = function (baseUrl) {
    this._baseUrl = baseUrl;
    this._acl = [];
};

AclService.prototype = {
    find: function (id) {
        var returnObj = null;
        this._acl.forEach(function (acl) {
            if (acl.id === id) {
                returnObj = acl;
            }
        });

        if (returnObj !== null) {
            return returnObj;
        }

        throw t('mindmaps', 'Could not find a acl with the given id.');
    },
    getAll: function () {
        return this._acl;
    },
    loadAll: function (id) {
        var deferred = $.Deferred();
        var self = this;
        $.get(this._baseUrl + '/' + id).done(function (acls) {
            self._acl = acls;
            deferred.resolve(acls);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    create: function (acl) {
        var deferred = $.Deferred();
        var self = this;
        $.ajax({
            url: this._baseUrl,
            data: acl,
            method: 'POST',
            dataType: 'json'
        }).done(function (acl) {
            self._acl.push(acl);
            deferred.resolve(acl);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    },
    delete: function (id) {
        var deferred = $.Deferred();
        var self = this;
        var acl = this.find(parseInt(id));
        var pos = this._acl.indexOf(acl);
        $.ajax({
            url: this._baseUrl + '/' + id,
            method: 'DELETE'
        }).done(function () {
            self._acl.splice(pos, 1);
            deferred.resolve();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            deferred.reject(jqXHR, textStatus, errorThrown);
        });
        return deferred.promise();
    }
};
