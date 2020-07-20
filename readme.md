#FireFly Configurator

##About
FireFly Configurator assists with the configuration of the FireFly Controller(s) and Clients.  Utilizing the Configurator, you will be presented with a GUI tool where all configurations can be centrally managed, including settings, inputs, outputs, breakers, switch and button assignments, as well as visualization for the entire network.

##Server Requirements
Operating System: Ubuntu 20.04 Server
HTTP Server: Apache 2.4.41
Database: MySQL 8.0.20
Language Processor: PHP 7.4.3

Hardware requirements are minimal (single core processor, 2GB RAM, 40GB hard drive is more than sufficient); This server handles few simultaneous requests, and they are very lightweight.

##FireFly Configurator Installation
To install, download and copy install.sh from GitHub.  This script will clone the necessary files and will automatically configure your installation.

`wget https://raw.githubusercontent.com/BrentIO/FireFly_Configurator/master/install.sh`

Run the downloaded file with elevated permissions
`sudo bash ~/install.sh`

MySQL will prompt for various input to secure the MySQL server.  Recommended settings:

>Would you like to setup VALIDATE PASSWORD component?
>Press y|Y for Yes, any other key for No: `N`

>Please set the password for root here.
>**This is the new root password, not the firefly password.  Do not lose this password.**
>New password:

>Remove anonymous users? (Press y|Y for Yes, any other key for No) : `Y`

>Disallow root login remotely? (Press y|Y for Yes, any other key for No) : `Y`

>Remove test database and access to it? (Press y|Y for Yes, any other key for No) : `Y`

>Reload privilege tables now? (Press y|Y for Yes, any other key for No) : `Y`

###For development access only

> [!IMPORTANT]
> Do not execute this section on a production server.  The following steps must be taken to enable MySQL Workbench access.  If you do not need access to MySQL Workbench for development purposes, do not execute this section.

Login to MySQL with sudo:
`$ sudo mysql`

Change the root user to use a native password:
`mysql> ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password';`

Edit the configuration file to change the binding from 127.0.0.1 to 0.0.0.0:
`$ sudo sed -i 's/127.0.0.1/0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf`

Restart MySQL:
`$ sudo systemctl restart mysql`

Login to MySQL with the updated root account, and enter the password when prompted:
`$ mysql -u root -p`

Create the new remote account:
`mysql> CREATE USER 'remote'@'%' IDENTIFIED BY 'password';`

Grant the user super admin privileges:
`mysql> GRANT ALL PRIVILEGES ON *.* TO 'remote'@'%' WITH GRANT OPTION;`

Flush the privileges table to pick up the changes:
`mysql> FLUSH PRIVILEGES;`

You can now log into MySQL with username `remote`.


##Test the installation
To test the installation, point your browser to `http://{serverIP}/api/heartbeat.php`.  You should see the current date and time with `{"success":true}`