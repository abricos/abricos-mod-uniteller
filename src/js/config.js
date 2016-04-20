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

    NS.ConfigWidget = Y.Base.create('configWidget', SYS.AppWidget, [
        SYS.Form,
        SYS.FormAction
    ], {
        onInitAppWidget: function(err, appInstance, options){
            this.reloadConfig();
        },
        reloadConfig: function(){
            this.set('waiting', true);

            this.get('appInstance').config(function(err, result){
                this.set('waiting', false);
                if (!err){
                    this.set('config', result.config);
                }
                this.renderConfig();
            }, this);
        },
        renderConfig: function(){
            var config = this.get('config');
            this.set('model', config);
        },
        showMoreHelpBlock: function(e){
            var node = e.defineTarget,
                nodeHelpBlock = node.ancestor('.help-block'),
                nodeMoreLink = node.ancestor('.help-block-more-link');
            if (!nodeHelpBlock || !nodeMoreLink){
                return;
            }
            var nodeMoreText = nodeHelpBlock.one('.help-block-more-text');
            if (!nodeMoreText){
                return;
            }

            nodeMoreLink.addClass('hide');
            nodeMoreText.removeClass('hide');
        },
        onSubmitFormAction: function(){
            this.set('waiting', true);

            var model = this.get('model');

            this.get('appInstance').configSave(model, function(err, result){
                this.set('waiting', false);
            }, this);
        },
    }, {
        ATTRS: {
            component: {value: COMPONENT},
            templateBlockName: {value: 'widget'},
            config: {value: null}
        },
        CLICKS: {
            showMore: 'showMoreHelpBlock'
        }
    });

};