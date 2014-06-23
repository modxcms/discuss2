var Discuss2 = function(config) {
    config = config || {};
    Discuss2.superclass.constructor.call(this,config);
};
Ext.extend(Discuss2,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {},view: {}, store: {}
});
Ext.reg('Discuss2',Discuss2);

Discuss2 = new Discuss2();