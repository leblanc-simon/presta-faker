#!/bin/bash

FOLDER=$1
SIZES="cart_default small_default medium_default home_default large_default thickbox_default"

cd "${FOLDER}"
if [ $? -ne 0 ]; then
    echo "Fail to change directory : ${FOLDER}"
fi

files=`find ${FOLDER} -type f -name '*.jpg' | grep -Ee '[0-9]+\.jpg$'`

for file in ${files}; do
    echo "$file"
    for size in ${SIZES}; do
        filename=`echo "$file" | sed "s/\.jpg$/-${size}.jpg/"`
        ln -s ${file} ${filename}
    done
done