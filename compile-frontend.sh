#!/bin/sh

if [ "$1" != "develop" -a "$1" != "master" ]; then
    echo "Incorrect git branch. Available branches: master, develop"
    exit
fi

if [ "$2" = "" ]; then
    echo "Incorrect container name"
    exit
fi

cd resources/frontend
git checkout $1 && git pull origin $1
cd ../../docker && docker exec $2 bash -c '
    rm -rf public/build
    cd resources/frontend
    rm -rf node_modules
    rm yarn.lock
    yarn install
    export REACT_APP_API_URL=http://localhost:8001 && yarn run build
    git checkout .
    mv ./build ../../public/build
    cd ../../public/build
    rm -rf ../icon
    rm -rf ../script
    mv ./icon ../icon
    mv ./script ../script
    mv ./index.html ../../resources/views/generated/index.blade.php
'
