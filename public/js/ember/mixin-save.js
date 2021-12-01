Ember.SaveHandlerMixin = Ember.Mixin.create({
    /*
     * Cuando hay un error muestra un gritter y luego redirige a "troute"
     */
    handleSavePromise: function(p, troute, message, icon, type, fn) {
        this.actuallyHandlePromise(p, troute, message, icon, type, fn, this.showGritter, false);
    },

    /*
     * Cuando hay un error lo graba en App.errormessage y luego redirige a "troute"
     */
    handleSavePromiseAppError: function(p, troute, message, fn) {
        this.actuallyHandlePromise(p, troute, message, fn, this.showAppError, false);
    },


    /*
     * Cuando hay un error muestra un gritter pero NO redirige a "troute"
     */
    handleSavePromiseNoRollback: function(p, troute, message, fn) {
        this.actuallyHandlePromise(p, troute, message, fn, this.showGritter, true);
    },

    /*
     * Cuando hay un error lo graba en App.errormessage pero NO redirige a "troute"
     */
    handleSavePromiseAppErrorNoRollback: function(p, troute, message, fn) {
        this.actuallyHandlePromise(p, troute, message, fn, this.showAppError, true);
    },

    /*
     * Este es el metodo que realmente hace el manejo de las promesas
     */
    actuallyHandlePromise: function(p, troute, message, icon, type, fn, callmeback, norollback) {
        var self = this;
        p.then(function() {
            self.transitionToRoute(troute);
            self.showNotification(message, 6000, icon, type);
        }, 
        function(error) {
            var msg = error.responseJSON;
            self.showNotification(msg.error, 6000, 'glyphicon glyphicon-remove', 'danger');
        });
    },

    /*
     * Esta es la funcion que muestra el gritter
     */
    showNotification: function(msg, time, icon, type) {
            slideOnTop(msg, time, icon, type);
    }

});

Ember.AclMixin = Ember.Mixin.create({  
    createDisabled: function() {
        if (this.acl !== 0 && this.acl.canCreate !== 0){
            return false;
        }
        else {
            return true;
        }
    }.property(),

    readDisabled: function() {
        if (this.acl !== 0 && this.acl.canRead !== 0){
            return false;
        }
        else {
            return true;
        }
    }.property(),

    updateDisabled: function() {
        if (this.acl !== 0 && this.acl.canUpdate !== 0){
            return false;
        }
        else {
            return true;
        }
    }.property(),

    deleteDisabled: function() {
        if (this.acl !== 0 && this.acl.canDelete !== 0){
            return false;
        }
        else {
            return true;
        }
    }.property(),

    allowBlockedemail: function() {
        if(this.acl !== 0 && this.acl.allowBlockedemail !== 0) {
            return false;
        }
        else{
            return true;
        }
    }.property(),

    allowContactlist: function() {
        if(this.acl !==0 && this.acl.allowContactlist !== 0) {
            return false;
        }
        else {
            return true;
        }
    }.property(),

    importBatchDisabled: function() {
        if(this.acl !== 0 && this.acl.canImportBatch !== 0){
            return false;
        }
        else {
            return true;
        }
    }.property(),

    importDisabled: function() {
        if(this.acl !== 0 && this.acl.canImport !== 0) {
            return false;
        }
        else {
            return true;
        }
    }.property()
});

Ember.TextField.reopen({
 attributeBindings: ["required"] 
});
