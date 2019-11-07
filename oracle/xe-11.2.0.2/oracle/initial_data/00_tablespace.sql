-- connect "SYSTEM"/"123456"@//localhost:1521/XE

create temporary tablespace pmm_temporary
tempfile
'pmm_temporary.dat' size 1024m autoextend on next 1024m
extent management local;

create tablespace pmm_index
datafile
'pmm_index.dbf' size 1024m autoextend on next 1024m
nologging
extent management local;

create tablespace pmm_data
datafile
'pmm_data.dbf' size 1024m autoextend on next 1024m
nologging
extent management local;
