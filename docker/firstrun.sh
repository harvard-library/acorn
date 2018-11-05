#!/bin/bash
if [-e /acorn/INITIALIZED]
then
  echo "Acorn Already Initialized: Starting"
else
  echo "Initializing Acorn"
  cd /acorn && \
#  source ./dockerenv.sh ; \
  ./acorn_setup.sh -s && \
  chmod a+rw application/config.ini && \
  sed -i 's/^require/#INSERTCHANGE\nrequire/' public/index.php && \
  sed -i '/#INSERTCHANGE/r index_update.php' public/index.php && \
  ./genssl.sh > keylog.txt 2>&1
  echo "Acorn Initialization Complete: Starting"
fi
supervisorctl start httpd
sleep 5
