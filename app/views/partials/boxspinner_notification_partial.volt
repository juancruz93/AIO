<script type="text/javascript">
    {% if notification.notification() %}
        $(function () {
            {% for message in notification.getNotification()%}
                boxSpinner('{{message.message}}', 3500, '{{message.type}}');
            {% endfor %}
        });
    {% endif %}
</script>
