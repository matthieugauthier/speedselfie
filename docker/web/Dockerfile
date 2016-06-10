###### BASE
FROM debian:8.3

ENV TERM xterm
# Let the conatiner know that there is no tty
ENV DEBIAN_FRONTEND noninteractive

# Update system
RUN apt-get update

# French baguette
RUN apt-get install -y --force-yes --fix-missing locales
RUN echo 'fr_FR ISO-8859-1' >> /etc/locale.gen \
    && echo 'fr_FR.UTF-8 UTF-8' >> /etc/locale.gen \
    && cat /etc/locale.gen
RUN locale-gen \
	&& dpkg-reconfigure locales
ENV LANGUAGE fr_FR:fr
ENV LANG fr_FR.UTF-8
ENV LC_ALL fr_FR.UTF-8
ENV LC_CTYPE fr_FR.UTF-8
RUN echo "Europe/Paris" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata
RUN locale \
    && date

# Clean
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Update and upgrade system
RUN apt-get update \
    && apt-get upgrade -y --force-yes

# Basic Requirements
RUN apt-get install -y --force-yes --fix-missing vim nano wget curl htop telnet rsync lynx \
                                                 procps make gcc rsyslog ca-certificates zip daemon \
                                                 acl cpio net-tools apt-utils adduser debconf-utils \
                                                 bzip2 python emacs

# Basic need for docker
RUN apt-get install -y --force-yes --fix-missing supervisor

###### APP

# Add our user and group first to make sure their IDs get assigned consistently, regardless of whatever dependencies get added and specify user for correct right
RUN groupmod -g 2000 www-data \
    && usermod -s /bin/bash -u 2000 www-data

# Install
RUN apt-get install -f -y --force-yes apache2 apache2-mpm-prefork libapache2-mod-php5 \
										php5-common php5-cli php5-curl php5-intl php5-mcrypt php5-readline php5-mysql php5-xdebug

# MySQL
# Install
RUN echo PURGE | debconf-communicate mysql-server \
    && { \
        echo mysql-server mysql-server/root_password password ''; \
        echo mysql-server mysql-server/root_password_again password ''; \
    } | debconf-set-selections \
    && apt-get install -y --force-yes mysql-server
# Comment out a few problematic configuration values
# Don't reverse lookup hostnames, they are usually another container
RUN sed -Ei 's/^(bind-address|log)/#&/' /etc/mysql/my.cnf \
    && echo 'skip-host-cache\nskip-name-resolve' | awk '{ print } $1 == "[mysqld]" && c == 0 { c = 1; system("cat") }' /etc/mysql/my.cnf > /tmp/my.cnf \
    && mv /tmp/my.cnf /etc/mysql/my.cnf
# Conf mysql
RUN /bin/bash -c "/usr/bin/mysqld_safe &" \
  && sleep 5 \
  && mysql -uroot -e "SET @@SESSION.SQL_LOG_BIN=0; DELETE FROM mysql.user; CREATE USER 'root'@'%' IDENTIFIED BY 'c4rm4r00t'; GRANT ALL ON *.* TO 'root'@'%' WITH GRANT OPTION; DROP DATABASE IF EXISTS test; FLUSH PRIVILEGES;"

# Conf Php
RUN a2enmod php5 rewrite headers
RUN sed -i 's#;date.timezone =#date.timezone = Europe/Paris#g' /etc/php5/apache2/php.ini
RUN sed -i 's#;date.timezone =#date.timezone = Europe/Paris#g' /etc/php5/cli/php.ini

# Clean
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Conf Apache
ADD default.conf /etc/apache2/sites-available/000-default.conf

# Define working directory
WORKDIR /var/www

# Add shell scripts for starting mysql
ADD mysql-start.sh /mysql-start.sh
ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Port
EXPOSE 80
EXPOSE 3306
ADD run.sh /run.sh
RUN chmod 755 /*.sh
CMD ["/run.sh"]