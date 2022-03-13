ARG mysql_version

FROM mysql:${mysql_version}


#RUN apt-get update && apt-get install nano
RUN echo 'federated' >> '/etc/mysql/conf.d/docker.cnf'
RUN echo 'event_scheduler=ON' >> '/etc/mysql/my.cnf'