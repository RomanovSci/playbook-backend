#!/bin/sh

rm -rf public/build
cd resources/frontend
rm -rf node_modules
rm yarn.lock

yarn install
yarn run build

git checkout .

mv ./build ../../public/build

cd ../../public/build

mv ./icon ../icon
mv ./script ../script
mv ./index.html ../../resources/views/generated/index.blade.php
