#!/bin/sh
cd ./resources/frontend/ \
    && npm install \
    && npm run build \
    && cp -R ./build ../../public \
    && cp ../../public/build/index.html ../../resources/views/generated/index.blade.php
