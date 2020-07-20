#!/bin/bash
# FireFly Configurator
# System Installation Script
# (C) 2020, P5 Software

#Colors
GREEN='\033[0;32m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

installPackages=""

echo -e "\n${GREEN}+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+${NC}"
echo -e "\n${GREEN}   FireFly Configurator Installation     ${NC}"
echo -e "\n${GREEN}+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+${NC}"


# Make sure we're running as root
if ! [ $(id -u) = 0 ]; then
   echo -e "\n${RED}Please use sudo or root to run this script.${NC}\n"
   exit 1
fi

#Notify the user if a reboot is required
if [ -f /var/run/reboot-required ]; then
  echo -e "\n${RED}A reboot is required.  After reboot, execute this script again.${NC}\n"
  exit 1
fi

# Install the Linux tools if required
if ! [ -d /etc/P5Software/Linux-Tools/ ]; then
    echo -e "\n${BLUE}Getting Linux Tools${NC}"
    git clone https://github.com/BrentIO/Linux-Tools /etc/P5Software/Linux-Tools/

    status=$?

    if [ $status != 0 ]; then
      echo -e "\n${RED}Unable to git Linux Tools.${NC}\n"
      exit 1
    fi
fi

# Run Auto Update
bash /etc/P5Software/Linux-Tools/AutoUpdate.sh

#Notify the user if a reboot is required
if [ -f /var/run/reboot-required ]; then
  echo -e "\n${RED}A reboot is required.  After reboot, execute this script again.${NC}\n"

  exit 1
fi

# Install the required applications
echo -e "\n${BLUE}Installing Apache, MySQL, and PHP${NC}"
apt-get install -y apache2 mysql-server php libapache2-mod-php php-mysql

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}apt-get install failed.${NC}\n"
  exit 1
fi

# Delete the default index.html file delivered with php
if [ -f /var/www/html/index.html ]; then
  echo -e "\n${BLUE}Deleting /var/www/html/index.html${NC}"
  rm /var/www/html/index.html

  status=$?

  if [ $status != 0 ]; then
    echo -e "\n${RED}Unable to delete /var/www/html/index.html.  Was the installation sucessful?${NC}\n"
    exit 1
  fi
fi

# Copy FireFly Configurator Files
echo -e "\n${BLUE}Gitting FireFly Configurator Files${NC}"
#git clone https://github.com/BrentIO/FireFly_Configurator ~/firefly

# Move the contents of html out to Apache
echo -e "\n${BLUE}Moving files to www${NC}"
mv /tmp/firefly/www/* /var/www/

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed to move /tmp/firefly/www/* to /var/www/${NC}\n"
  exit 1
fi

# Prohibit directory listing
echo -e "\n${BLUE}Prohibiting directory listing in Apache${NC}"

a2dismod --quiet --force autoindex

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Unable to prohibit directory listing in Apache${NC}\n"
  exit 1
fi

echo -e "\n${BLUE}Restarting Apache${NC}"
systemctl restart apache2

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed to restart Apache${NC}\n"
  exit 1
fi

# Secure the MySQL Installation
echo -e "\n${BLUE}Securing MySQL Install${NC}"
mysql_secure_installation

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed securing MySQL installation${NC}\n"
  exit 1
fi

# Run the SQL scripts
echo -e "\n${BLUE}Creating database${NC}"
mysql < /tmp/firefly/sql/create.sql

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed to successfully create database.${NC}\n"
  exit 1
fi

echo -e "\n${BLUE}Creating user and assigning permissions${NC}"
mysql --database=firefly < /tmp/firefly/sql/user.sql

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed to successfully create user and permission.${NC}\n"
  exit 1
fi

echo -e "\n${BLUE}Creating default and configuration data${NC}"
mysql --database=firefly < /tmp/firefly/sql/defaults.sql

status=$?

if [ $status != 0 ]; then
  echo -e "\n${RED}Failed to successfully create default and configuration data.${NC}\n"
  exit 1
fi

echo -e "\n${BLUE}Deleting installation data${NC}"
rm -rf /tmp/firefly/

echo -e "\n${GREEN}FireFly Configurator Installation Complete${NC}\n"

