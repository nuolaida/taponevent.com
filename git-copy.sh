#!/bin/bash
# Target directory
CURRENT_DIR="$( cd "$( dirname "$0" )" && pwd )"
TARGET=~/changesets/$(date '+%d_%b_%Y_%H_%M_%S')
echo "Finding and copying files and folders to $TARGET"
echo ""
echo "Changed files:"
for i in $(git diff --name-only $1 $2)
    do
        # First create the target directory, if it doesn't exist.
        mkdir -p "$TARGET/$(dirname $i)"
         # Then copy over the file.
        cp "$i" "$TARGET/$i"
        echo "\033[30m$i";
    done
echo ""
echo "\033[36mFiles copied to target directory";
cd "$TARGET"
# scp -r . bar-prod:/hfs/virtual/h512vidm/bestpub.lt/data/
cd "$CURRENT_DIR"
