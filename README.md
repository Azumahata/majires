# Majires

## Description
* 簡易コメントツールです。
  * This is easy commentation tool.

## Requirement
```
PHP 5.4.16
```

## Usage

* スレをつくり、コメントと返信といいねができます。
  * This tools Can create thread, to comment and replies and like.

## Install and getting started

* CentOS7
  * ルート権限で実行してください
    * Do root permission
```console

$ yum install -y epel-release
$ yum update -y; yum install -y apache php php-devel git php-pdo  php-mysqlnd mariadb mariadb-server
$ sudo sh -c 'echo "extension=pdo.so" >> /etc/php.ini'

$ systemctl start mariadb
$ systemctl enable mariadb

$ systemctl start httpd
$ systemctl enable httpd

$ firewall-cmd --add-service http
$ firewall-cmd --add-service http --permanent

$ setenforce 0
$ sed -i 's/SELINUX=enforcing/SELINUX=permissive/' /etc/selinux/config

$ cd /var/www/html
$ git clone https://github.com/Azumahata/majires.git
```
access to http://localhost/majires/index.php

## Licence
* MIT

## Author
[Azumahata](https://github.com/Azumahata)

