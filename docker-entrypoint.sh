#!/bin/bash

debug=$(cat config/config.yml | grep "debug: true")
if [ "$debug" = "debug: true" ]
then
    $(cd resources && npm run watch &)
else
    $(cd resources && npm run production &)
fi

echo "WTF";