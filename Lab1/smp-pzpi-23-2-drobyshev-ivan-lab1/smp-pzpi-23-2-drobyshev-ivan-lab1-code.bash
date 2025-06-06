#!/bin/bash
VERSION="1.0.0"

declare -a args=()

while [[ $# -gt 0 ]]; do
    case "$1" in
        --help)
            echo "Usage: $(basename $0) [--help | --version] | [[-q|--quiet] [академ_група] файл_із_cist.csv]"
            exit 0
            ;;
        --version)
            echo "$(basename $0) version $VERSION"
            exit 0
            ;;
        *)
            args+=("$1")
            ;;
    esac
    shift
done

case "${#args[@]}" in
    0) 
        ;;
    1) 
        input="${args[0]}"
        ;;
    2) 
        selected_group="${args[0]}"
        input="${args[1]}"
        ;;
    *)
        echo "Error: Too many arguments" >&2
        exit 1
        ;;
esac

if [[ -z "$input" ]]; then
    files=($(ls -1 TimeTable_??_??_20??.csv 2>/dev/null | sort));
    if [[ ${#files[@]} -eq 0 ]]; then
        echo "Error: No matching CSV files found!" >&2;
        exit 1;
    fi
    echo "Select a file:"
    select file in "${files[@]}"; do
        if [[ -n "$file" ]]; then
            input="$file";
            break;
        fi
        echo "Invalid selection."
    done
fi

if [[ ! -f "$input" ]]; then
    echo "Error: file '$input' not found." >&2;
    exit 1;
fi

if [[ ! -r "$input" ]]; then
    echo "Error: cannot read '$input'." >&2;
    exit 2;
fi

content=$(sed 's/\r/\n/g' "$input" | iconv -f cp1251 -t utf-8)

found_groups=$(
  echo "$content" |
  awk -v FPAT='[^,]*|"[^"]*"' '
    NR>1 {
      gsub(/^"/,"",$1)
      if(match($1,/- /)){
        split($1,parts," - ")
        print parts[1]
      }
    }
  ' | sort -u
)

group_count=$(echo "$found_groups" | wc -l)
readarray -t groups_array <<<"$found_groups"

if [[ -z "$selected_group" ]]; then
  if (( group_count == 1 )); then
      selected_group="${groups_array[0]}"
  else
      echo "Select a group:"
      select group_choice in "${groups_array[@]}"; do
          if [[ -n "$group_choice" ]]; then
              selected_group="$group_choice"
              break
          else
              echo "Invalid selection."
          fi
      done
  fi
fi

if ! grep "$selected_group" <<<"$found_groups"; then
    echo "Warning: Group $selected_group might not be in this file"
fi

output_file="Google_${input}.csv"

echo "$content" |
awk -v group="$selected_group" '
BEGIN {
  print "Subject,Start Date,Start Time,End Date,End Time,Description"
  FPAT = "([^,]*)|(\"[^\"]*\")"
}
function ft(t){
  gsub(/:|"/," ",t);
  return strftime("%I:%M %p", mktime("1970 01 01" t));
}
function fd(d){
  gsub(/"/, "", d);
  split(d, parts, ".")
  return strftime("%m/%d/%Y", mktime(parts[3] " " parts[2] " " parts[1] " 00 00 00"));
}
$0 ~ "\""group" - " {
  sub("^\""group" - ","",$1); gsub("\"$","",$1)
  sd=fd($2); st=ft($3); ed=fd($4); et=ft($5)
  lsnum = ++lesson_per_day[$2];
  
  printf("\"%s; №%d\",%s,%s,%s,%s,%s\n",
    $1, lsnum, sd, st, ed, et, $12)
}' | tee "$output_file"