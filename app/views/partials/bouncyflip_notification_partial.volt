<script type="text/javascript">
    {% if notification.notification() %}
        $(function () {
            {% for message in notification.getNotification()%}
                {{ partial("partials/notification_validator_partial") }}    
                bouncyFlip('{{message.message}}', 3500, '{{icon}}', '{{message.type}}');
            {% endfor %}
        });
    {% endif %}
</script>
