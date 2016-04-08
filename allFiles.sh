#!/bin/bash

for db in *.res.xml
 do
       name=${db%%.*}
       nameFile=${name%_*}
       number=${name##*_}
       withoutDate=${nameFile%_*}
       paper=${withoutDate%_*}
       date=${nameFile##*_}
       
       if [ $number -eq 001 ]; then
       	if [ $paper = L_Intransigeant ]; then
       		echo "L'Intransigeant" > "$nameFile.test"
       	fi

       	if [ $paper = Le_Petit_Journal ]; then
       		echo "Le Petit Journal" > "$nameFile.test"
       	fi

		echo ${date:0:4}-${date:4:2}-${date:6:2} >> "$nameFile.test"
		echo $name >> "$nameFile.test"

       elif [[ -f  "$nameFile.test" ]]; then

       	echo $name >> "$nameFile.test"

       fi
done