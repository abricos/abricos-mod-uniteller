var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: '{C#MODNAME}', files: ['lib.js']}
    ]
};
Component.entryPoint = function(NS){

    var Y = Brick.YUI,
        COMPONENT = this,
        SYS = Brick.mod.sys;

    NS.InfoWidget = Y.Base.create('InfoWidget', SYS.AppWidget, [], {
        onInitAppWidget: function(err, appInstance, options){
        },
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'info'},
            form: {value: null}
        },
        CLICKS: {}
    });

};
