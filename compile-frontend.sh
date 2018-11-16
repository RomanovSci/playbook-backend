#!/bin/sh

cd resources/frontend
rm -rf node_modules

npm install
npm run build

cp -R ./build ../../public
cp ../../public/build/index.html ../../resources/views/generated/index.blade.php
