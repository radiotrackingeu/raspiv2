#!/bin/bash
#params
# 1: device

idfile="/tmp/run_id_"$1
read -r run_id < $idfile

globalconfig="/tmp2/globalconfig"

while read line; do
  if [[ $line =~ ^"["(.+)"]"$ ]]; then
    arrname=${BASH_REMATCH[1]}
    declare -A $arrname
  elif [[ $line =~ ^([_[:alnum:]]*)\ =\ \"?([^\"]*)\"?$ ]]; then
    declare ${arrname}[${BASH_REMATCH[1]}]="${BASH_REMATCH[2]}"
  fi
done < $globalconfig

if [[ $run_id =~ ^[0-9]+$ ]]; then
  cmd_sql=" --sql --db_host ${database["db_host"]} --db_port ${database["db_port"]} --db_user ${database["db_user"]} --db_pass ${database["db_pass"]} --db_run_id $run_id"
else
  cmd_sql=""
fi

cmd_rtl_sdr="rtl_sdr -d $1 -f ${logger["center_freq_$1"]} -s ${logger["freq_range_$1"]} -g ${logger["log_gain_$1"]} -"

cmd_liquidsdr="/tmp/liquidsdr/rtlsdr_signal_detect -s -t ${logger["threshold_$1"]} -r ${logger["freq_range_$1"]} -b ${logger["nfft_$1"]} -n ${logger["timestep_$1"]} --ll ${logger["minDuration_$1"]} --lu ${logger["maxDuration_$1"]}"

cmd_matched_filters="/tmp/liquidsdr/matched_signal_detect -s -t ${logger["threshold_$1"]} -r ${logger["freq_range_$1"]} -p 22"

file_name="${logger["antenna_id_$1"]}_`date +%Y_%m_%d_%H_%M`"
file_path="/tmp/record/$file_name"

cmd="$cmd_rtl_sdr 2> $file_path | $cmd_liquidsdr $cmd_sql >> $file_path 2>&1"
eval $cmd
