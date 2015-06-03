<?php

namespace PrestaFaker\Webservice\Sql;

class Category extends SqlAbstract implements SqlInterface
{
    static private $first_call = true;

    public function build($id, array $values)
    {
        $replace = $this->getDefaults();
        $replace['id'] = $id;

        foreach ($values as $key => $value) {
            if (substr($key, 0, 5) === 'meta_' || in_array($key, ['name', 'link_rewrite', 'description']) === true) {
                foreach ($value as $id_lang => $string) {
                    // Hummm... very, very dirty, but it's only for demo !
                    // You're not ok with that : Code it yourself !!!
                    $replace[$key] = $string;
                    $replace['id_lang'] = $id_lang;
                }
                continue;
            }

            $replace[$key] = $value;
        }

        $sql = '';
        if (true === self::$first_call) {
            $sql = $this->getNestedTreeProcedure();
            self::$first_call = false;
        }

        return $sql.$this->replaceSql(self::CATEGORY_SQL, $replace);
    }

    /**
     * Build the nested tree repair procedure
     *
     * @return string
     */
    private function getNestedTreeProcedure()
    {
        $table_prefix = $this->table_prefix;

        // Create procedure
        // @view : http://stackoverflow.com/a/3634268
        $procedure = <<<EOF
DROP PROCEDURE IF EXISTS repair_nested_tree;
DELIMITER //

CREATE PROCEDURE repair_nested_tree ()
MODIFIES SQL DATA
BEGIN

    DECLARE currentId, currentParentId  INT;
    DECLARE currentLeft                 INT;
    DECLARE startId                     INT DEFAULT 1;

    # Determines the max size for MEMORY tables.
    SET max_heap_table_size = 1024 * 1024 * 512;

    START TRANSACTION;

    # Temporary MEMORY table to do all the heavy lifting in,
    # otherwise performance is simply abysmal.
    CREATE TABLE `tmp_category_tree` (
        `id_category` int(10) unsigned NOT NULL DEFAULT '',
        `id_parent`   int(10)          DEFAULT NULL,
        `nleft`       int(10) unsigned DEFAULT NULL,
        `nright`      int(10) unsigned DEFAULT NULL,
        PRIMARY KEY      (`id_category`),
        INDEX USING HASH (`id_parent`),
        INDEX USING HASH (`nleft`),
        INDEX USING HASH (`nright`)
    ) ENGINE = MEMORY
    SELECT `id_category`,
           `id_parent`,
           `nleft`,
           `nright`
    FROM   `${table_prefix}category`;

    # Leveling the playing field.
    UPDATE  `tmp_category_tree`
    SET     `nleft`  = NULL,
            `nright` = NULL;

    # Establishing starting numbers for all root elements.
    WHILE EXISTS (SELECT * FROM `tmp_category_tree` WHERE `id_parent` IS NULL AND `nleft` IS NULL AND `nright` IS NULL LIMIT 1) DO

        UPDATE `tmp_category_tree`
        SET    `nleft`  = startId,
               `nright` = startId + 1
        WHERE  `id_parent` IS NULL
          AND  `nleft`       IS NULL
          AND  `nright`      IS NULL
        LIMIT  1;

        SET startId = startId + 2;

    END WHILE;

    # Numbering all child elements
    WHILE EXISTS (SELECT * FROM `tmp_category_tree` WHERE `nleft` IS NULL LIMIT 1) DO

        # Picking an unprocessed element which has a processed parent.
        SELECT     `tmp_category_tree`.`id`
          INTO     currentId
        FROM       `tmp_category_tree`
        INNER JOIN `tmp_category_tree` AS `parents`
                ON `tmp_category_tree`.`id_parent` = `parents`.`id`
        WHERE      `tmp_category_tree`.`nleft` IS NULL
          AND      `parents`.`nleft`  IS NOT NULL
        LIMIT      1;

        # Finding the element's parent.
        SELECT  `id_parent`
          INTO  currentParentId
        FROM    `tmp_category_tree`
        WHERE   `id` = currentId;

        # Finding the parent's nleft value.
        SELECT  `nleft`
          INTO  currentLeft
        FROM    `tmp_category_tree`
        WHERE   `id` = currentParentId;

        # Shifting all elements to the right of the current element 2 to the right.
        UPDATE `tmp_category_tree`
        SET    `nright` = `nright` + 2
        WHERE  `nright` > currentLeft;

        UPDATE `tmp_category_tree`
        SET    `nleft` = `nleft` + 2
        WHERE  `nleft` > currentLeft;

        # Setting nleft and nright values for current element.
        UPDATE `tmp_category_tree`
        SET    `nleft`  = currentLeft + 1,
               `nright` = currentLeft + 2
        WHERE  `id`   = currentId;

    END WHILE;

    # Writing calculated values back to physical table.
    UPDATE `${table_prefix}category`, `tmp_category_tree`
    SET    `${table_prefix}category`.`nleft`  = `tmp_category_tree`.`nleft`,
           `${table_prefix}category`.`nright` = `tmp_category_tree`.`nright`
    WHERE  `${table_prefix}category`.`id`   = `tmp_category_tree`.`id`;

    COMMIT;

    DROP TABLE `tmp_category_tree`;

END//

DELIMITER ;

EOF;

        return $procedure;
    }

    private function getDefaults()
    {
        return [
            // Category
            'id_parent' => 0,
            'id_shop_default' => 1,
            'level_depth' => 0,
            'nleft' => 0,
            'nright' => 0,
            'active' => 1,

            // Lang
            'id_shop' => 1,
            'id_lang' => 1,
            'description' => '',
            'link_rewrite' => '', // required
            'meta_description' => '',
            'meta_keywords' => '',
            'meta_title' => '',
            'name' => '', // required

            // Shop
            'position' => 0,
        ];
    }

    const CATEGORY_SQL = <<<EOF
INSERT INTO :prefix:category (`id_category`, `id_parent`, `id_shop_default`, `level_depth`, `nleft`, `nright`, `active`, `date_add`, `date_upd`, `is_root_category`)
VALUES (:id:, :id_parent:, :id_shop_default:, :level_depth:, :nleft:, :nright:, :active:, NOW(), NOW(), :is_root_category:);
INSERT INTO :prefix:category_lang (`id_category`, `id_shop`, `id_lang`, `name`, `description`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`)
VALUES (:id:, :id_shop:, :id_lang:, :name:, :description:, :link_rewrite:, :meta_description:, :meta_keywords:, :meta_title:);
INSERT INTO :prefix:category_shop (`id_category`, `id_shop`, `position`)
VALUES (:id:, :id_shop:, :position:);
INSERT INTO :prefix:category_group (`id_category`, `id_group`)
VALUES (:id:, 1), (:id:, 2), (:id:, 3);

EOF;
}
