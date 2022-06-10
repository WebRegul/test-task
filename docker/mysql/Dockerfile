ARG MYSQL_VERSION
FROM mysql:${MYSQL_VERSION}

RUN chown -R mysql:root /var/lib/mysql/
RUN mkdir -p /var/log/mysql/
RUN chown -R mysql:root /var/log/mysql/
RUN chmod 0777 /var/log/mysql/
COPY conf.d/my.cnf /etc/mysql/conf.d/my.cnf
RUN chmod 0775 /etc/mysql/conf.d/my.cnf

EXPOSE 3306
CMD ["mysqld"]
