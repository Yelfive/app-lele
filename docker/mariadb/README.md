Master-Slave configure
===========

1.  Run command in container of master

    + Create slave account

            mysql -e 'GRANT REPLICATION SLAVE ON *.* TO slaves@"%" IDENTIFIED BY "ALiJianSlave"

    + Show master status

            mysql -e 'show master status \G'

        and you will get the following:

            *************************** 1. row ***************************
                         File: mysql-bin.000003
                     Position: 154
                 Binlog_Do_DB:
             Binlog_Ignore_DB:
            Executed_Gtid_Set:
    + Or clean up master log

        `mysql> reset master`
        then slave just set `master_log_file=mysql-bin.000001,master_log_pos=0`
2.  Run command in container of **slave**

        mysql -e 'CHANGE MASTER TO MASTER_HOST="master_host", MASTER_USER="slaves", MASTER_PASSWORD="ALiJianSlave", MASTER_LOG_FILE="mysql-bin.000003", MASTER_LOG_POS=154, MASTER_PORT=3306'

    Notice: the `MASTER_LOG_FILE` and the `MASTER_LOG_POS` need to be replaced by
    information queried by step 1.

3.  START SLAVE
4.  SHOW SLAVE STATUS \G

5.  proxy
    + Amoeba
    + mysql router
    + cat