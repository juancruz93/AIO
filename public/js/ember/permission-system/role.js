//Model
App.Role = DS.Model.extend({
    name: DS.attr('string'),
    created: DS.attr('string'),
    updated: DS.attr('string')
});

//Index
App.RolesIndexRoute = Ember.Route.extend({
    model: function(){
        return this.store.find('role');
    }
});

App.RolesIndexController = Ember.ArrayController.extend({
    actions : {
        configure: function(role) {
            App.set('role', role);
            this.transitionToRoute("permissions.index");
        }
    }
});

//Add
App.RolesAddRoute = Ember.Route.extend({
    model: function(){
        return this.store.createRecord('role');
    },
    deactivate: function () {
        if (this.currentModel.get('isNew') && this.currentModel.get('isSaving') == false) {
                this.currentModel.rollback();
        }
    }
});

App.RolesAddController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions : {
        save: function() {
            var n = this.get('name');
//            var n = n.replace(/\s/g, '');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('El Role debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
                n = n.replace(/\s/g, '');
                this.model.set('name', n);
                this.handleSavePromise(this.model.save(), 'roles.index', 'El Role fue creado exitosamente', 'glyphicon glyphicon-ok', 'success');
            }
        },
				
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute("roles.index");
        }
    }	
});

//Update
App.RolesUpdateRoute = Ember.Route.extend({
    
});

App.RolesUpdateController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        update: function() {
            var n = this.get('name');
            var n = n.replace(/\s/g, '');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('El Role debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
            	this.handleSavePromise(this.model.save(), 'roles.index', 'El Role ha sido actualizado exitosamente', 'glyphicon glyphicon-info-sign', 'info');
            }
        },
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute('roles.index');
        }
    }
});

App.RolesRemoveRoute = Ember.Route.extend({
    
});

//Remove
App.RolesRemoveController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        remove: function() {
            var self = this;
            this.store.find('role', this.model.id).then(function (role) {
                role.deleteRecord();
                role.get('isDeleted'); // => true
                role.save(); // => DELETE to /roles/1
                self.showNotification('El role fue eliminado exitosamente', 6000, 'glyphicon glyphicon-warning-sign', 'warning');
                self.transitionToRoute('roles.index');
            });
        },

        cancel: function() {
            this.model.rollback();
            this.transitionToRoute('roles.index');
        }
    }
});

App.RolesIndexView = Ember.View.extend({
    didInsertElement: function() {
        $('.tool-tip').tooltip({});
    }
});

App.RolesAddView = Ember.View.extend({
    didInsertElement: function() {
        $('.tool-tip').tooltip({});
    }
});
