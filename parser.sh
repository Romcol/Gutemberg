#!/bin/bash



if [[ $# != 1 ]]; then
	echo "Usage : parser.sh datafile"
	exit 1
fi

dos2unix $1

i=-1
IFS=$'\n'
for line in `cat $1`
do
	if [ "$i" -eq -1 ]; then
		title=$line
	elif [[ "$i" -eq 0 ]]; then
		date=$line
	else
		page[$i]=$line 
	fi
	((i++))
done < $1

>id.temp

for j in ${!page[*]} ; 
do
	echo "Parse --> $title ( $date ) page $j"
	php parser.php -f ${page[$j]} $title $date $j 2>/dev/null
done

i=0
IFS=$'\n'
for line in $(cat id.temp)
do
	mongoId[$i]=$line
	((i++))
done < id.temp


size=${#mongoId[@]}
echo "updating the pages..."
for ((it = 0 ; it < $size ; it++ )); 
do
	if [ $it -eq 0 ]; then
		php update.php ${mongoId[$it]} -1 ${mongoId[$(($it+1))]}
	elif [ $it -eq $(($size - 1)) ]; then
		php update.php ${mongoId[$it]} ${mongoId[$(($it-1))]} -1
	else
		php update.php ${mongoId[$it]} ${mongoId[$(($it-1))]} ${mongoId[$(($it+1))]}
	fi
done
echo "DONE"

rm id.temp

exit 0