#!/bin/bash

cd pluginsDev/modules/

find . -maxdepth 1 -mindepth 1 -type d -exec sh -c '(echo {} && cd {} && git remote -v && git status -s && echo)' \;
