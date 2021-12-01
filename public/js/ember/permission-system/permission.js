//* Permissions
App.Permission = DS.Model.extend({
    allowed: DS.attr('string')
});

App.PermissionsIndexRoute = Ember.Route.extend({
    model: function(){
        var role = App.get('role');
        console.log(App.role);
        return this.store.find('permission', role.id);
    }
});

App.PermissionsIndexController = Ember.ObjectController.extend(Ember.SaveHandlerMixin, {
    initialize: function () {
        var obj = JSON.parse(this.get('this.allowed'));
        App.set('permissions', obj);
    }.observes('this.content'),
    
    actions : {
        save: function() {
            var object = JSON.stringify(App.get('permissions'));
            this.model.set('allowed', object);
            this.handleSavePromise(this.model.save(), 'roles.index', 'Se han configurado los permisos y el role exitosamente', 'glyphicon glyphicon-info-sign', 'info');
        },
                
        cancel: function(){
            this.model.rollback();
            this.transitionToRoute('roles.index');
        }
    }
});

App.PermissionsIndexView = Ember.View.extend({
    
});

App.RadioButton = Ember.View.extend({
    tagName : "input",
    type : "checkbox",
    attributeBindings : [ "name", "type", "value", "id", "checked"],
    didInsertElement: function() {
        this.$().bootstrapToggle({
            size: 'small',
            onstyle: 'success',
            offstyle: 'danger'
        });
        
        this.$().change(function() {
            var id = $(this).attr('id');
            var checked = $(this).prop('checked');
            var object = refreshObject(App.get('permissions'), id, checked);
            App.set('permissions', object);
        });
    },
    
//    click : function() {
//        console.log('click');
//        var id = this.$().attr('id');
//        var checked = this.$().is(':checked');
//        var object = refreshObject(App.get('permissions'), id, checked);
//        App.set('permissions', object);
//    }
});

function refreshObject(object, id, checked) {
    for (var i = 0; i < object.length; i++) {
        for (var j = 0; j < object[i].actions.length; j++) {
            if (object[i].actions[j].id == id) {
                object[i].actions[j].allowed = (checked ? 1 : 0);
            }
        }
    }
    
    return object;
}
