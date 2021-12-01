var App = Ember.Application.create({
    rootElement: '#app-container'
});

//App.ApplicationAdapter = DS.FixtureAdapter;
App.ApplicationAdapter = DS.RESTAdapter.extend();

App.ApplicationAdapter.reopen({
    namespace: apiUrl,
    serializer: App.AplicationSerializer
});

// Store (class)
App.Store = DS.Store.extend({});

App.Router.map(function() {
    this.resource('roles', function(){
        this.route('add'),
        this.resource('roles.update', { path: '/update/:role_id'}),
        this.resource('roles.remove', { path: '/remove/:role_id'});
    });
    
    this.resource('resources', function(){
        this.route('add'),
        this.resource('resources.update', { path: '/update/:resource_id'}),
        this.resource('resources.remove', { path: '/remove/:resource_id'});
    });
    
    this.resource('actions', function() {
        this.route('add'),
        this.resource('actions.update', {path: '/update/:action_id'}),
        this.resource('actions.remove', {path: '/remove/:action_id'});
    });
    
    this.resource('permissions', function() {
        
    });
});
