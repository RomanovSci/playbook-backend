#!/bin/sh
cd ./resources/frontend/ \
    && npm run build \
    && cp -R ./build ../../public \
    && cp ../../public/build/index.html ../../resources/views/generated/index.blade.php \
    && perl -pi -e 's/ISport/build/g' ../../resources/views/generated/index.blade.php
