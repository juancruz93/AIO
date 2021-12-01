//Model
App.Action = DS.Model.extend({
    name: DS.attr('string'),
    created: DS.attr('string'),
    updated: DS.attr('string'),
    resource: DS.belongsTo('resource')
});

//Index
App.ActionsIndexRoute = Ember.Route.extend({
//    model: function(){
//        return this.store.find('action');
//    }
});

App.ActionsIndexController = Ember.ArrayController.extend({});

//Add
App.ActionsAddRoute = Ember.Route.extend({
    model: function(){
        return this.store.createRecord('action');
    }
});

App.ActionsAddController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions : {
        save: function() {
            var n = this.model.get('name');
            var n = n.replace(/\s/g, '');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('La acción debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
                this.model.set('name', n);
                var resource = App.get('resource');
                this.model.set('resource', resource);
                this.handleSavePromise(this.model.save(), 'resources.index', 'La acció fue creada exitosamente', 'glyphicon glyphicon-ok', 'success');
            }
        },
				
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute("resources.index");
        }
    }	
});

//Update
App.ActionsUpdateRoute = Ember.Route.extend({});

App.ActionsUpdateController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        update: function() {
            var n = this.get('name');
            var n = n.replace(/\s/g, '');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('El Action debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
                this.model.set('name', n);
            	this.handleSavePromise(this.model.save(), 'resources.index', 'El Action ha sido actualizado exitosamente', 'glyphicon glyphicon-info-sign', 'info');
            }
        },
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute('resources.index');
        }
    }
});


//Remove
App.ActionsRemoveController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        remove: function() {
            var self = this;
            this.store.find('action', this.model.id).then(function (action) {
                action.deleteRecord();
                action.get('isDeleted'); // => true
                action.save(); // => DELETE to /actions/1
                self.showNotification('La acción fue eliminada exitosamente', 6000, 'glyphicon glyphicon-warning-sign', 'warning');
                self.transitionToRoute('resources.index');
            });
        },

        cancel: function() {
            this.model.rollback();
            this.transitionToRoute('resources.index');
        }
    }
});
