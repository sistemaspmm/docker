-- connect "SYSTEM"/"123456"@//localhost:1521/XE

create user sispmm
identified by 123456
default tablespace pmm_index
quota unlimited on pmm_index;

alter user sispmm
identified by 123456
default tablespace pmm_data
temporary tablespace pmm_temporary
quota unlimited on pmm_data;
