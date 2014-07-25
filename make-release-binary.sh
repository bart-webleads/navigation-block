#!/bin/bash

DIR="$( pwd )"
VERSION=`cat VERSION.md`
echo $VERSION
rm -f $DIR/navigation-block-*.zip
zip -r $DIR/navigation-block-$VERSION.zip . -i "/src/*"
