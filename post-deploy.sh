#!/bin/sh

echo "Update: running post deployment scripts"

#Create log directory
if [ ! -d app/log ]; then
        mkdir -m 0775 app/log
fi

#Handle composer
alias comp='/usr/bin/php composer.phar'

if [ ! -f composer.phar ]; then
        curl https://getcomposer.org/installer -o composer.phar
fi

comp selfupdate
comp update