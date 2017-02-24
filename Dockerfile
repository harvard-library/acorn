FROM amazonlinux
MAINTAINER "Anthony Moulen <amoulen@g.harvard.edu>"
RUN yum -y install git php56 mod24_ssl mysql-server php56-mysqlnd python python-setuptools php56-xml php56-xmlrpc && \
  easy_install-2.6 supervisor && \
  chkconfig httpd on ; chkconfig mysqld on && \
  echo "NETWORKING=yes" >/etc/sysconfig/network && \
  service mysqld start && \
  mysqladmin -u root password 'docrootpw1'
RUN echo "[supervisord]" > /etc/supervisord.conf && \
    echo "nodaemon=true" >> /etc/supervisord.conf && \
    echo "" >> /etc/supervisord.conf && \
    echo "[program:mysqld]" >> /etc/supervisord.conf && \
    echo "command=/usr/bin/mysqld_safe" >> /etc/supervisord.conf && \
    echo "" >> /etc/supervisord.conf && \
    echo "[program:httpd]" >> /etc/supervisord.conf && \
    echo "command=/usr/sbin/apachectl -D FOREGROUND" >> /etc/supervisord.conf
RUN git clone https://github.com/captmiddy/acorn
ADD acornhost.conf /etc/httpd/conf.d/vhost_acorn.conf
ADD config.php /acorn/public/config.php
ADD dockerenv.sh /acorn/dockerenv.sh
ADD index_update.php /acorn/index_update.php
ADD genssl.sh /acorn/genssl.sh
ADD acornhostssl.conf /etc/httpd/conf.d/vhost_acorn_ssl.conf
WORKDIR /acorn
RUN service mysqld start && \
    chmod a+rw application && \
    chmod a+x *.sh && \
    source ./dockerenv.sh ; \
    ./acorn_setup.sh -s && \
    chmod a+rw application/config.ini && \
    sed -i 's/^require/#INSERTCHANGE\nrequire/' public/index.php && \
    sed -i '/#INSERTCHANGE/r index_update.php' public/index.php && \
    ./genssl.sh > keylog.txt 2>&1
CMD ["/usr/local/bin/supervisord", "--configuration=/etc/supervisord.conf"]
