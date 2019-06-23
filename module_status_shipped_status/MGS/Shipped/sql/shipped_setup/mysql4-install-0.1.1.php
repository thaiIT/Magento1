<?php

$installer = $this;
$installer->startSetup();
$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('shipped_table')};

    CREATE TABLE {$this->getTable('shipped_table')} (
    `shipped_id` int(11) unsigned NOT NULL auto_increment,
    `order_id` int(10) unsigned NOT NULL,
    `shipped_status` int(10) NOT NULL,
    PRIMARY KEY (`shipped_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
// $installer->run("
//     ALTER TABLE  {$this->getTable('sales_flat_order_grid')} 
//     ADD COLUMN  `shipped_id` varchar(255) NOT NULL AFTER `status`;
// ");
$installer->endSetup();