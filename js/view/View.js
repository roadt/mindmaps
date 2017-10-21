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

var View = function (mindmaps, nodes, acl) {
    this._mindmaps = mindmaps;
    this._nodes = nodes;
    this._acl = acl;
    this._network = null;
};

View.prototype = {
	setContentDimensions: function () {
		var $mindmap = $('#app-content-mindmap');
		var $wrapper = $('#app-content-wrapper');
		var $sidebar = $('#app-sidebar');
		var sidebarWidth = 0;
		if ($sidebar.is(':visible')) {
			sidebarWidth = $sidebar.width();
		}
		$mindmap.width($wrapper.width() - sidebarWidth);
		$mindmap.height($wrapper.height());
	},
    renderContent: function () {
        var self = this;
        var container = document.getElementById('app-content-mindmap');
        var options = {
            physics: {enabled: false},
            interaction: {dragNodes: false},
			// TODO: Get NC locale as 2 chat code and pass it.
            locale: 'en'
        };

        this.setContentDimensions();
		$(window).off().resize(function () {
			self.setContentDimensions();
		});

        if (vis !== null) {
            var nodes = this._nodes.getAll();
            var edges = [];

            $.each(nodes, function (key, val) {
                if (val.parentId !== 0) {
                    edges.push({from: val.parentId, to: val.id});
                }
            });

            if (this._network !== null) {
                this._network.destroy();
                this._network = null;
            }
            this._network = new vis.Network(container, {nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges)}, options);

            this._network.on('doubleClick', function (params) {
                // TODO: Check if the node really exists.
                if (document.cookie !== '') {
                    self.renderCreateEditNodeModal(params.pointer.canvas.x, params.pointer.canvas.y);
                }
            });

            this._network.on('click', function (params) {
                var $popover = $('.popovermenu');

                if (params.nodes.length === 1) {
                    document.cookie = params.nodes[0];

                    $popover.css('display', 'block');
                    // FIXME: For some reason the automatic width is wrong.
                    $popover.css('width', '72px');
                    $popover.css('top', params.pointer.DOM.y + 30);
                    $popover.css('left', params.pointer.DOM.x - 60);
                } else {
                    $popover.css('display', 'none');
                }
            });

            $('.node-rename').off().click(function () {
                self.renderCreateEditNodeModal();
            });

            $('.node-delete').off().click(function () {
                self.renderDeleteNodeModal();
            });
        } else {
            OC.dialogs.alert(t('mindmaps', 'The vis.js Framework is not available!'), t('mindmaps', 'Error'));
        }
    },
    renderNavigation: function () {
        var self = this;
        var $mindmapList = $('#mindmap-list');
        var source = $('#navigation-tpl').html();
        var template = Handlebars.compile(source);
        var html = template({mindmaps: this._mindmaps.getAll()});

        $mindmapList.html(html);

        var $mindmapLinks = $mindmapList.find('li > a');
        $mindmapLinks.off().click(function () {
            var mindmapId = parseInt($(this).parent().attr('data-id'));

            $mindmapList.find('li.active').removeClass('active');
            $(this).parent().addClass('active');

            self._mindmaps.load(mindmapId);
            self._nodes.loadAll(mindmapId).done(function () {
                self.renderContent();
                self.renderSidebar();
            }).fail(function () {
                OC.dialogs.alert(t('mindmaps', 'Could not load mindmap nodes.'), t('mindmaps', 'Error'));
            });
        });

        $('#new-mindmap-button').off().click(function () {
            self.renderCreateEditMindmapModal();
        });

        $('.app-navigation-entry-utils-menu-button button').off().click(function (e) {
            $(this).parent().parent().parent().parent().find('.app-navigation-entry-menu').css('display', 'block');
            e.stopPropagation();
        });

        $('body, html').click(function () {
            $('.app-navigation-entry-menu').css('display', 'none');
        });

        $('.mindmap-rename').off().click(function () {
            var mindmapId = parseInt($(this).parent().parent().parent().parent().attr('data-id'));
            self.renderCreateEditMindmapModal(mindmapId);
        });

        $('.mindmap-delete').off().click(function () {
            var mindmapId = parseInt($(this).parent().parent().parent().parent().attr('data-id'));
            self.renderDeleteMindmapModal(mindmapId);
        });
    },
    renderSidebar: function () {
        var self = this;
        var source = $('#sidebar-tpl').html();
        var template = Handlebars.compile(source);
        var active = this._mindmaps.getActive();
        var html = template({title: active.title, description: active.description});

        $('#sidebar-content').html(html);
        $('#app-sidebar').show();

        $('.tabHeader').off().click(function () {
            $('.tabHeader.selected').removeClass('selected');
            $(this).addClass('selected');

            var tabId = $(this).attr('data-tabid');

            $('.tabsContainer > div').hide();
            $('#' + tabId).show();
        });

        $('.detailsTabView > h3 > .save').off().click(function () {
            var active = self._mindmaps.getActive();
            active.description = $('#description').val();
            self._mindmaps.update(active).done(function () {
                $('#description').css('border', '1px solid green');
            }).fail(function () {
                OC.dialogs.alert(t('mindmaps', 'Error while saving description.'), t('mindmaps', 'Error'));
            });
        });

        $('#shareWith').off().keyup(function (e) {
            if (e.keyCode === 13) {
                self._acl.create({
                    mindmapId: self._mindmaps.getActive().id,
                    type: 0,
                    participant: $(this).val()
                }).done(function () {
                    $('#shareWith').val('');
                    self.renderShares();
                }).fail(function () {
                    OC.dialogs.alert(t('mindmaps', 'Error while sharing mindmap.'), t('mindmaps', 'Error'));
                });
            }
        });

        this.renderShares();

        $('.sidebar-header > .close').off().click(function () {
            $('#app-sidebar').hide();
            self.setContentDimensions();
        });
    },
    renderShares: function() {
        var self = this;
        this._acl.loadAll(this._mindmaps.getActive().id).done(function (acl) {
            var source = $('#shareWithList-tpl').html();
            var template = Handlebars.compile(source);
            var html = template({shares: acl});

            $('#shareWithList').html(html);

            $('.avatar').each(function () {
                $(this).avatar($(this).attr('data-username'), 32);
            });

            $('.shareWithList > li > a.delete').off().click(function () {
                var shareId = parseInt($(this).parent().attr('data-id'));
                self._acl.delete(shareId).done(function () {
                    self.renderShares();
                }).fail(function () {
                    OC.dialogs.alert(t('mindmaps', 'Error while deleting share.'), t('mindmaps', 'Error'));
                });
            });
        }).fail(function () {
            OC.dialogs.alert(t('mindmaps', 'Error while loading mindmap shares.'), t('mindmaps', 'Error'));
        });
    },
    renderCreateEditMindmapModal: function (id) {
        var self = this;
        var data = null;
        if (typeof id !== 'undefined') {
            data = this._mindmaps.find(id);
        }
        this.renderModal(
            t('mindmaps', 'Create / Edit Mindmap'),
            t('mindmaps', 'Please enter a name'),
            data,
            function (result, label) {
                if (result === true) {
                    if(data !== null) {
                        data.title = label;
                        self._mindmaps.update(data).done(function () {
                            self.renderNavigation();
                        }).fail(function () {
                            OC.dialogs.alert(
                                t('mindmaps', 'Error while updating mindmap.'),
                                t('mindmaps', 'Error')
                            );
                        });
                    } else {
                        self._mindmaps.create({title: label}).done(function (mindmap) {
                            self._nodes.loadAll(mindmap.id);
                            document.cookie = '';
                            self.renderCreateEditNodeModal();
                        }).fail(function () {
                            OC.dialogs.alert(
                                t('mindmaps', 'Error while creating mindmap.'),
                                t('mindmaps', 'Error')
                            );
                        });
                    }
                }
            }
        );
    },
    renderDeleteMindmapModal: function (id) {
        var self = this;
        if (typeof id !== 'undefined') {
            OC.dialogs.confirm(
                t('mindmaps', 'Are you sure you want to delete the mindmap?'),
                t('mindmaps', 'Delete Node'),
                function (result) {
                    if (result === true) {
                        // FIXME: Delete mindmap nodes for given mindmap.
                        self._mindmaps.delete(id).done(function () {
                            self.render();
                        }).fail(function () {
                            OC.dialogs.alert(
                                t('mindmaps', 'Error while deleting mindmap.'),
                                t('mindmaps', 'Error')
                            );
                        });
                    }
                }
            );
        } else {
            OC.dialogs.alert(
                t('mindmaps', 'Please select the mindmap you want to delete first.'),
                t('mindmaps', 'Error')
            );
        }
    },
    renderCreateEditNodeModal: function (x, y) {
        var self = this;
        var data = null;
        var userId = OC.getCurrentUser().uid;
        var selectedId = parseInt(document.cookie);
        $('.popovermenu').css('display', 'none');
        if (selectedId > 0 && typeof x === 'undefined' && typeof y === 'undefined') {
            data = this._nodes.find(selectedId);
            this._nodes.lock(selectedId).done(function () {
                self.renderContent();
            }).fail(function () {
                OC.dialogs.alert(
                    t('mindmaps', 'Node locking failed.'),
                    t('mindmaps', 'Error')
                );
            });
        }
        this.renderModal(
            t('mindmaps', 'Create / Edit Node'),
            t('mindmaps', 'Please enter the label'),
            data,
            function (result, label) {
                if (result === true) {
                    if (document.cookie !== '') {
                        if (typeof x !== 'undefined' && typeof y !== 'undefined') {
                            self._nodes.create({
                                x: parseInt(x),
                                y: parseInt(y),
                                parentId: selectedId,
                                mindmapId: self._mindmaps.getActive().id,
                                label: label,
                                title: t('mindmaps', 'Author: ') + userId,
                                userId: userId
                            }).done(function () {
                                self.render();
                            }).fail(function () {
                                OC.dialogs.alert(
                                    t('mindmaps', 'Error while creating node.'),
                                    t('mindmaps', 'Error')
                                );
                            });
                        } else {
                            data.label = label;
                            self._nodes.update(data).done(function () {
                                self.render();
                            }).fail(function () {
                                OC.dialogs.alert(
                                    t('mindmaps', 'Error while editing node.'),
                                    t('mindmaps', 'Error')
                                );
                            });
                        }
                    } else {
                        self._nodes.create({
                            x: 0,
                            y: 0,
                            parentId: null,
                            mindmapId: self._mindmaps.getActive().id,
                            label: label,
                            title: t('mindmaps', 'Author: ') + userId,
                            userId: userId
                        }).done(function () {
                            self.render();
                        }).fail(function () {
                            OC.dialogs.alert(
                                t('mindmaps', 'Error while creating root node.'),
                                t('mindmaps', 'Error')
                            );
                        });
                    }
                }
                self._nodes.unlock(selectedId).done(function () {
                    self.renderContent();
                }).fail(function () {
                    OC.dialogs.alert(
                        t('mindmaps', 'Node unlocking failed.'),
                        t('mindmaps', 'Error')
                    );
                });
            }
        );
    },
    renderDeleteNodeModal: function () {
        var self = this;
        if (document.cookie !== '') {
            OC.dialogs.confirm(
                t('mindmaps', 'Are you sure you want to delete the node?'),
                t('mindmaps', 'Delete Node'),
                function (result) {
                    if (result === true) {
                        self._nodes.delete(document.cookie).done(function () {
                            self.renderContent();
                        }).fail(function () {
                            OC.dialogs.alert(
                                t('mindmaps', 'Error while deleting mindmap node.'),
                                t('mindmaps', 'Error')
                            );
                        });
                    }
                }
            );
        } else {
            OC.dialogs.alert(
                t('mindmaps', 'Please select the mindmap node you want to delete first.'),
                t('mindmaps', 'Error')
            );
        }
    },
    renderModal: function (title, desc, data, callback) {
        OC.dialogs.prompt(
            desc,
            title,
            callback,
            true,
            t('mindmaps', 'Label'),
            false
        ).then(function () {
            var $dialog = $('.oc-dialog:visible');
            $dialog.find('.ui-icon').remove();

            var $buttons = $dialog.find('button');
            $buttons.eq(0).text(t('core', 'Cancel'));
            $buttons.eq(1).text(t('core', 'Confirm'));

            var $input = $dialog.find('input');
            $input.val((data === null) ? '' : (typeof data.label !== 'undefined') ? data.label : data.title);
        });
    },
    render: function () {
        var self = this;
        var active = self._mindmaps.getActive();
        this._mindmaps.loadAll().done(function () {
            if (typeof active === 'undefined') {
                active = self._mindmaps.getAll()[0];
            }
            if (typeof active !== 'undefined') {
                self._mindmaps.load(active.id);
                // Does the previous active node still exist?
                if (typeof self._mindmaps.getActive() !== 'undefined') {
                    self._nodes.loadAll(active.id).done(function () {
                        self.renderNavigation();
                        self.renderContent();
                        self.renderSidebar();
                    }).fail(function () {
                        OC.dialogs.alert(
                            t('mindmaps', 'Could not load mindmap nodes.'),
                            t('mindmaps', 'Error')
                        );
                    });
                }
            }
        }).fail(function () {
            OC.dialogs.alert(
                t('mindmaps', 'Could not load mindmaps.'),
                t('mindmaps', 'Error')
            );
        });
    }
};
