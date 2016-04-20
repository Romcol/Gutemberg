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
              elif [[ $paper = L_Aurore ]]; then
                     echo "L'Aurore" > "$nameFile.test"
              else
                     echo $paper | tr _ " " > "$nameFile.test"
              fi

                     echo ${date:0:4}-${date:4:2}-${date:6:2} >> "$nameFile.test"
                     echo $name >> "$nameFile.test"

       elif [[ -f  "$nameFile.test" ]]; then

       	echo $name >> "$nameFile.test"

       fi
done