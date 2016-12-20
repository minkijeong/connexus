#!/bin/sh
# Dependencies: sed, jq
# jq: https://stedolan.github.io/jq/
#
search_dir=`dirname $0`

for qsfile in "$search_dir"/*.querystring*
do
        urlpath=`echo "$qsfile" | sed 's/^.//' | sed 's/\./\//g' | sed 's/\/querystring[0-9]*$/\/\?/'`$(<$qsfile)
        echo "# $qsfile:"
        echo "curl \"http://localhost$urlpath\" | jq ."
        echo
done

for jsonfile in "$search_dir"/*.json*
do
	urlpath=`echo "$jsonfile" | sed 's/^.//' | sed 's/\./\//g' | sed 's/\/json[0-9]*$/\//'`
	echo "# $jsonfile:"
	echo "curl \"http://localhost$urlpath\" -X POST -d @\"$jsonfile\" | jq ."
	echo
done
