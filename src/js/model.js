var Component = new Brick.Component();
Component.requires = {
    mod: [
        {name: 'sys', files: ['appModel.js']}
    ]
};
Component.entryPoint = function(NS){
    var Y = Brick.YUI,
        SYS = Brick.mod.sys;

    NS.Request = Y.Base.create('request', SYS.AppModel, [], {
        structureName: 'Request'
    });

    NS.RequestList = Y.Base.create('requestList', SYS.AppModelList, [], {
        appItem: NS.Request
    });

    NS.Config = Y.Base.create('config', SYS.AppModel, [], {
        structureName: 'Config'
    });
};
