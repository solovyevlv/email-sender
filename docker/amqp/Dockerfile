FROM rabbitmq:3.10.6-management-alpine
ADD ./plugins /opt/rabbitmq/plugins
RUN rabbitmq-plugins enable rabbitmq_delayed_message_exchange
