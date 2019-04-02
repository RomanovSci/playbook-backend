#!/bin/sh

if [ "$1" != "develop" -a "$1" != "master" ]; then
    echo "Incorrect git branch. Available branches: master, develop"
    exit
fi

cd resources/frontend
git checkout $1 && git pull origin $1
cd ../../docker && docker-compose exec workspace bash -c '
    rm -rf public/build
    cd resources/frontend
    rm -rf node_modules
    rm yarn.lock
    yarn install
    yarn run build
    git checkout .
    mv ./build ../../public/build
    cd ../../public/build
    rm -rf ../icon
    rm -rf ../script
    mv ./icon ../icon
    mv ./script ../script
    mv ./index.html ../../resources/views/generated/index.blade.php
'
