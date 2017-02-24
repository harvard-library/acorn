#!/bin/bash

# Bash shell script for generating self-signed certs. Run this in a folder, as it
# generates a few files. Large portions of this script were taken from the
# following artcile:
#
# http://usrportage.de/archives/919-Batch-generating-SSL-certificates.html
#
# Additional alterations by: Brad Landers
# Date: 2012-01-27
fail_if_error() {
  [ $1 != 0 ] && {
    unset PASSPHRASE
    exit 10
  }
}
# Generate a passphrase
export PASSPHRASE=$(head -c 500 /dev/urandom | tr -dc a-z0-9A-Z | head -c 128; echo)

# Certificate details; replace items in angle brackets with your own info
subj="
C=US
ST=US
O=Generic
localityName=Generic
commonName=localhost
organizationalUnitName=Library
emailAddress=adm@localhost
"

# Generate the server private key
openssl genrsa -des3 -out acorn.key -passout env:PASSPHRASE 2048
fail_if_error $?

# Generate the CSR
openssl req \
    -new \
    -batch \
    -subj "$(echo -n "$subj" | tr "\n" "/")" \
    -key acorn.key \
    -out acorn.csr \
    -passin env:PASSPHRASE
fail_if_error $?
cp acorn.key acorn.key.org
fail_if_error $?

# Strip the password so we don't have to type it every time we restart Apache
openssl rsa -in acorn.key.org -out acorn.key -passin env:PASSPHRASE
fail_if_error $?

# Generate the cert (good for 10 years)
openssl x509 -req -days 3650 -in acorn.csr -signkey acorn.key -out acorn.crt
fail_if_error $?
exit 0
