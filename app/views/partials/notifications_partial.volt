

{{ stylesheet_link('library/notification-styles/css/ns-default.min.css') }}
{{ stylesheet_link('library/notification-styles/css/ns-style-bar.min.css') }}
{{ stylesheet_link('library/notification-styles/css/ns-style-attached.min.css') }}
{{ stylesheet_link('library/notification-styles/css/ns-style-other.min.css') }}
{{ javascript_include('library/notification-styles/js/notificationFx.min.js') }}

<script type="text/javascript">
    function boxSpinner(message, time, type) {
        var notification = new NotificationFx({
            wrapper : document.body,
            message : '<p>' + message + '</p>',
            layout : 'other',
            effect : 'boxspinner',
            type : type, // notice, warning or error
            ttl : time,
            onClose : function() {

            }
        });

        // show the notification
        notification.show();
    };
    
    function slideOnTop(message, time, icon, type) {
        var notification = new NotificationFx({
            wrapper : document.body,
            message : '<span class="' + icon + '"></span><p>' + message + '</p>',
            layout : 'bar',
            effect : 'slidetop',
            type : type, // notice, warning or error
            ttl : time,
            onClose : function() {

            }
        });

        // show the notification
        notification.show();
    };
        
    function bouncyFlip(message, time, icon, type) {
        var notification = new NotificationFx({
            wrapper : document.body,
            message : '<span class="' + icon + '"></span><p>' + message + '</p>',
            layout : 'attached',
            effect : 'bouncyflip',
            type : type, // notice, warning or error
            ttl : time,
            onClose : function() {

            }
        });

        // show the notification
        notification.show();
    };
</script>