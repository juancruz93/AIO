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
            message : '<span class="' + icon + '" ></span><p style="text-align: left;">' + message + '</p>',
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
