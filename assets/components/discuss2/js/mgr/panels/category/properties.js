Discuss2.panel.CategoryProperties = function(config) {
    config = config || {};
    var oc = {
        'change':{fn:MODx.fireResourceFormChange}
        ,'select':{fn:MODx.fireResourceFormChange}
    };
    Ext.applyIf(config, {
        id : 'discuss2-category-properties',
        title : _('discuss2.category_properties'),
        border : false,
        plain : true,
        deferredRender : false,
        anchor : '97%',
        items : [{
            title : _('discuss2.general'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items :[{
                xtype : 'textfield',
                name : "properties[time_format]",
                id : 'time-format',
                fieldLabel : _('discuss2.time_format_category'),
                description : _('discuss2.time_format_category_desc'),
                listeners : oc,
                value : config.forum.properties.time_format || 'Y-m-d H:i:s'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'time-format',
                html: _('discuss2.time_format_category_desc'),
                cls: 'desc-under'
            },{
                xtype : 'textfield',
                name : "properties[default_language]",
                id : 'default-language',
                fieldLabel : _('discuss2.category_language'),
                description : _('discuss2.category_language_desc'),
                listeners : oc,
                value : config.forum.properties.default_language || 'en'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'default-language',
                html: _('discuss2.category_language_desc'),
                cls: 'desc-under'
            }]
        }]
    });
    Discuss2.panel.CategoryProperties.superclass.constructor.call(this,config);
}

Ext.extend(Discuss2.panel.CategoryProperties, MODx.VerticalTabs, {});
Ext.reg('discuss2-category-properties', Discuss2.panel.CategoryProperties);