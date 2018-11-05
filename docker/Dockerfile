FROM centos:7
ENV MYSQL_ROOT_PASSWORD='docrootpw1'
RUN yum -y install mod_ssl mariadb git php mod24_ssl php-mysqlnd python27 python2-setuptools php-xml php-xmlrpc && \
  easy_install supervisor
ADD application /acorn/application
ADD public /acorn/public
ADD acorn_sessions /acorn/acorn_sessions
ADD library /acorn/library
ADD logs /acorn/logs
ADD test /acorn/test
ADD .htaccess /acorn/.htaccess
ADD docker/dbconfig.ini_template /acorn/application/dbconfig.ini_template
ADD docker/acorn_setup.sh /acorn/acorn_setup.sh
ADD docker/acorn_tables.sql /acorn/acorn_tables.sql
ADD docker/supervisord.conf /etc/supervisord.conf
ADD docker/acornhost.conf /etc/httpd/conf.d/vhost_acorn.conf
ADD docker/config.php /acorn/public/config.php
ADD docker/dockerenv.sh /acorn/dockerenv.sh
ADD docker/index_update.php /acorn/index_update.php
ADD docker/genssl.sh /acorn/genssl.sh
ADD docker/acornhostssl.conf /etc/httpd/conf.d/vhost_acorn_ssl.conf
ADD docker/firstrun.sh /acorn/firstrun.sh
WORKDIR /acorn
RUN chmod a+rw application && \
    chmod a+x *.sh
EXPOSE 8443 80
CMD ["/usr/bin/supervisord", "--configuration=/etc/supervisord.conf"]
