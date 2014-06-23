Discuss2.page.Forum = function(config) {
    config = config || {record:{}};
    Ext.applyIf(config,{
        panelXType: 'discuss2-panel-forum'
    });
    config.canDuplicate = false;
    config.canDelete = false;
    Discuss2.page.Forum.superclass.constructor.call(this, config);
}

Ext.extend(Discuss2.page.Forum, MODx.page.CreateResource, {

});

Ext.reg('discuss2-page-forum', Discuss2.page.Forum);