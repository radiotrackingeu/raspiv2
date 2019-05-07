#!/bin/bash
set -e

if [ "${1:0:1}" = '-' ]; then
	set -- mysqld "$@"
fi

if [ "$1" = 'mysqld' ]; then
	# read DATADIR from the MySQL config
	DATADIR="$("$@" --verbose --help 2>/dev/null | awk '$1 == "datadir" { print $2; exit }')"
	
	if [ ! -d "$DATADIR/mysql" ]; then
		if [ -z "$MYSQL_ROOT_PASSWORD" -a -z "$MYSQL_ALLOW_EMPTY_PASSWORD" ]; then
			echo >&2 'error: database is uninitialized and MYSQL_ROOT_PASSWORD not set'
			echo >&2 '  Did you forget to add -e MYSQL_ROOT_PASSWORD=... ?'
			exit 1
		fi
		
		echo 'Initializing database'
    mysql_install_db --datadir="$DATADIR"
		echo 'Database initialized'
		
		# These statements _must_ be on individual lines, and _must_ end with
		# semicolons (no line breaks or comments are permitted).
		# TODO proper SQL escaping on ALL the things D:
		
		tempSqlFile='/tmp/mysql-first-time.sql'
		cat > "$tempSqlFile" <<-EOSQL
			DELETE FROM mysql.user ;
			CREATE USER 'root'@'%' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}' ;
			GRANT ALL ON *.* TO 'root'@'%' WITH GRANT OPTION ;
			DROP DATABASE IF EXISTS test ;
		EOSQL
		
		if [ "$MYSQL_DATABASE" ]; then
			echo "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\` ;" >> "$tempSqlFile"
		fi
		
		if [ "$MYSQL_USER" -a "$MYSQL_PASSWORD" ]; then
			echo "CREATE USER '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD' ;" >> "$tempSqlFile"
			
			if [ "$MYSQL_DATABASE" ]; then
				echo "GRANT ALL ON \`$MYSQL_DATABASE\`.* TO '$MYSQL_USER'@'%' ;" >> "$tempSqlFile"
			fi
		fi
		
		# create user, database and tables for radiotrackingeu project
		cat >> "$tempSqlFile" <<-EOSQL
				CREATE USER 'rteu'@'%' IDENTIFIED BY 'rteuv2!' ;
				CREATE DATABASE rteu ;
				CREATE TABLE rteu.runs (\`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, \`hostname\` VARCHAR(60), \`PiSN\` VARCHAR(20), \`device\` VARCHAR(60), \`pos_x\` FLOAT, \`pos_y\` FLOAT, \`orientation\` SMALLINT UNSIGNED, \`beam_width\` SMALLINT UNSIGNED, \`gain\` TINYINT UNSIGNED, \`center_freq\` INT UNSIGNED, \`freq_range\` INT UNSIGNED, \`threshold\` TINYINT UNSIGNED, \`fft_bins\` SMALLINT UNSIGNED, \`fft_samples\` TINYINT UNSIGNED);
				CREATE TABLE rteu.signals (\`id\` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, \`timestamp\` CHAR(30), \`samples\` BIGINT, \`duration\` FLOAT, \`signal_freq\` FLOAT, \`signal_bw\` FLOAT, \`max_signal\` FLOAT, \`noise\` FLOAT, \`run\` INT UNSIGNED NOT NULL, FOREIGN KEY(\`run\`) REFERENCES rteu.runs(\`id\`) ON DELETE CASCADE);
				GRANT ALL ON rteu.* TO 'rteu'@'%';
		EOSQL
		
		echo 'FLUSH PRIVILEGES ;' >> "$tempSqlFile"
		
		set -- "$@" --init-file="$tempSqlFile"
	fi
	
	chown -R mysql:mysql "$DATADIR"
fi

exec "$@"