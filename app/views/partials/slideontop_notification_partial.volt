<script type="text/javascript">
    {% if notification.notification() %}
        $(function () {
            {% for message in notification.getNotification()%}
                {{ partial("partials/notification_validator_partial") }}
                slideOnTop('{{message.message}}', 5000, '{{icon}}', '{{message.type}}');
            {% endfor %}
        });
    {% endif %}
</script>
