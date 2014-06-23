Discuss2.page.Category = function(config) {
    config = config || {record:{}};
    Ext.applyIf(config,{
        panelXType: 'discuss2-panel-category'
    });
    config.canDuplicate = false;
    config.canDelete = false;
    Discuss2.page.Category.superclass.constructor.call(this, config);
}

Ext.extend(Discuss2.page.Category, MODx.page.CreateResource, {

});

Ext.reg('discuss2-page-category', Discuss2.page.Category);