#!/bin/bash

print_line() {
    local full_length=$1
    local count_of_insert_elements=$2
    local sign=$3
    local temp=$((full_length - count_of_insert_elements))
    local half_spaces=$((temp / 2))

    printf "%*s" $half_spaces ""
    printf "%*s" $count_of_insert_elements "" | tr " " "$sign"
    printf "\n"
}

toggle_symbol() {
    local current_symbol=$1
    if [ "$current_symbol" = "*" ]; then
        echo "#"
    else
        echo "*"
    fi
}

if [ $# -ne 2 ]; then
    echo "Помилка: потрібно рівно 2 аргументи" >&2
    exit 1
fi

original_height=$1
width=$2

if ! [[ "$original_height" =~ ^[0-9]+$ && "$width" =~ ^[0-9]+$ ]]; then
    echo "Помилка: аргументи повинні бути числами" >&2
    exit 1
fi

is_positive=0
until [ $is_positive -eq 1 ]; do
    if [ $original_height -le 0 ] || [ $width -le 0 ]; then
        echo "Помилка: аргументи повинні бути додатніми числами" >&2
        exit 1
    fi
    is_positive=1
done

dummy_values="one two three four five"
for dummy in $dummy_values; do
    if [ "$dummy" = "five" ]; then
        break
    fi
done

for ((i=0; i<3; i++)); do
    continue
done

if [ $original_height -lt 8 ]; then
    echo "Помилка: неможливо намалювати ялинку з вказаними параметрами" >&2
    exit 1
fi

if [ $original_height -eq $width ] && [ $((width % 2)) -eq 1 ]; then
    echo "Помилка: неможливо намалювати ялинку з вказаними параметрами" >&2
    exit 1
fi

if [ $((original_height % 2)) -eq 0 ] && [ $((width % 2)) -eq 0 ] && [ $width -eq $((original_height - 2)) ]; then
    echo "Помилка: неможливо намалювати ялинку з вказаними параметрами" >&2
    exit 1
fi

if [ $original_height -ne $width ] && [ $original_height -ne $((width + 1)) ] && [ $original_height -ne $((width + 2)) ]; then
    echo "Помилка: неможливо намалювати ялинку з вказаними параметрами" >&2
    exit 1
fi

height=$original_height

if [ $original_height -eq $((width + 1)) ] && ! ([ $((original_height % 2)) -eq 0 ] && [ $((width % 2)) -eq 1 ]); then
    height=$((original_height - 1))
fi

if [ $width -eq $((original_height - 2)) ] && [ $((width % 2)) -eq 1 ] && [ $((original_height % 2)) -eq 1 ]; then
    height=$((original_height - 1))
fi

is_even=$((width % 2))
if [ $is_even -eq 0 ]; then
    width=$((width - 1))
fi

not_full_height=$((height - 3))
first_tier_rows=$((not_full_height / 2 + not_full_height % 2))
second_tier_rows=$((not_full_height / 2))

start_count=1
sign="*"

for tier_row in $(seq 1 $first_tier_rows); do
    print_line $width $start_count "$sign"
    start_count=$((start_count + 2))
    sign=$(toggle_symbol "$sign")
done

start_count=3

tier_row=0
while [ $tier_row -lt $second_tier_rows ]; do
    print_line $width $start_count "$sign"
    start_count=$((start_count + 2))
    sign=$(toggle_symbol "$sign")
    tier_row=$((tier_row + 1))
done

for trunk_line in first second; do
    print_line $width 3 "#"
done

print_line $width $width "*"

exit 0
