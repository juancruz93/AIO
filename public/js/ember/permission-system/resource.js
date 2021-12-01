//Model
App.Resource = DS.Model.extend({
    name: DS.attr('string'),
    created: DS.attr('string'),
    updated: DS.attr('string'),
    actions: DS.hasMany('action')
});

//Index
App.ResourcesIndexRoute = Ember.Route.extend({
    model: function(){
        return this.store.find('resource');
    }
});

App.ResourcesIndexController = Ember.ArrayController.extend({
    actions : {
        addaction: function(resource) {
            App.set('resource', resource);
            this.transitionToRoute("actions.add");
        }
    }
});

//Add
App.ResourcesAddRoute = Ember.Route.extend({
    model: function(){
        return this.store.createRecord('resource');
    },
    
    deactivate: function () {
        if (this.currentModel.get('isNew') && this.currentModel.get('isSaving') == false) {
            this.currentModel.rollback();
        }
    }
});

App.ResourcesAddController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions : {
        save: function() {
            var n = this.get('name');
            var n = n.replace(/\s/g, '');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('El Recurso debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
                this.model.set('name', n);
                this.handleSavePromise(this.model.save(), 'resources.index', 'El Recurso fue creado exitosamente', 'glyphicon glyphicon-ok', 'success');
            }
        },
				
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute("resources.index");
        }
    }	
});

//Update
App.ResourcesUpdateRoute = Ember.Route.extend({});

App.ResourcesUpdateController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        update: function() {
            var n = this.get('name');
            if (n === "" || n === undefined || n === null) {
                this.showNotification('El Recurso debe tener un nombre', 6000, 'glyphicon glyphicon-remove', 'danger');
            }
            else {
                this.model.set('name', n);
            	this.handleSavePromise(this.model.save(), 'resources.index', 'El Recurso ha sido actualizado exitosamente', 'glyphicon glyphicon-info-sign', 'info');
            }
        },
        cancel: function() {
            this.model.rollback();
            this.transitionToRoute('resources.index');
        }
    }
});


//Remove
App.ResourcesRemoveController = Ember.ObjectController.extend(Ember.SaveHandlerMixin,{
    actions: {
        remove: function() {
//            this.model.deleteRecord();
            this.handleSavePromise(this.model.destroyRecord(), 'resources.index', 'El role fue eliminado exitosamente', 'glyphicon glyphicon-info-sign', 'warning');
        },

        cancel: function(item) {
            item.rollback();
            this.transitionToRoute('resources.index');
        }
    }
});

App.ResourcesIndexView = Ember.View.extend({
    didInsertElement: function() {
        $('.tool-tip').tooltip({});
    }
});
