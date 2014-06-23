Discuss2.panel.Templates = function(config) {
    config = config || {};
    var oc = {
        'change':{fn:MODx.fireResourceFormChange}
        ,'select':{fn:MODx.fireResourceFormChange}
    };
    Ext.applyIf(config,{
        id : 'discuss2-panel-templates',
        border : false,
        title : _('discuss2.templates'),
        cls : 'modx-resource-tab',
        plain : true,
        deferredRender: false,
        anchor : '97%',
        items : [{
            title : _('discuss2.global_templates'),
            items : [{
                xtype : 'modx-combo-template',
                name : "properties[forum_template]",
                hiddenName : "properties[forum_template]",
                fieldLabel : _('discuss2.forum_template'),
                id : 'forum-template',
                description : _('discuss2.forum_template_desc'),
                listeners : oc
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'forum-template',
                html: _('discuss2.forum_template_desc'),
                cls: 'desc-under'
            }, {
                xtype : 'fieldset',
                title : _('discuss2.search_page'),
                autoHeight :true,
                defaults : {
                    anchor : '100%'
                },
                items : [{
                    xtype : 'numberfield',
                    fieldLabel : _('discuss2.search_page_id'),
                    name : "properties[search_page_id]",
                    id : 'search-page-id',
                    listeners : oc
                }, {
                    xtype : 'modx-combo-template',
                    name : "properties[search_page_template]",
                    hiddenName : "properties[search_page_template]",
                    fieldLabel : _('discuss2.template'),
                    id : 'search-page-template',
                    listeners : oc
                }, {
                    xtype : 'numberfield',
                    name : "properties[search_result_id]",
                    fieldLabel : _('discuss2.search_result_id'),
                    id : 'search-result-id',
                    listeners : oc
                }, {
                    xtype : 'modx-combo-template',
                    name : "properties[search_page_template]",
                    hiddenName : "properties[search_page_template]",
                    fieldLabel : _('discuss2.template'),
                    id : 'search-result-template',
                    listeners : oc
                }]
            }]
        },{
            title : _('discuss2.category_templates'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{}]
        },{
            title : _('discuss2.board_templates'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{}]
        },{
            title : _('discuss2.thread_templates'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{}]
        },{
            title : _('discuss2.post_templates'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{}]
        }]
    });
    this.loadCategories(config);
    Discuss2.panel.Templates.superclass.constructor.call(this, config);
}
Ext.extend(Discuss2.panel.Templates, MODx.VerticalTabs, {
    loadCategories : function() {
        items = [];
        var self = this;
        return items;
    },
    loadBoards : function(config) {
        items = [];
        var self = this;
        console.log(config);
    },
    createTemplateRow : function(row) {
        return {
            xtype : 'modx-combo-template',
            name : "templates['" + row.id + "']",
            hiddenName : "templates['" + row.id + "']",
            fieldLabel : row.pagetitle
        }
    }
});
Ext.reg('discuss2-panel-templates', Discuss2.panel.Templates);