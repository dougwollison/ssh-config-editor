#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rm -f $DIR/build/sshedit.phar

$DIR/compile.sh
$DIR/build/sshedit.phar $@
