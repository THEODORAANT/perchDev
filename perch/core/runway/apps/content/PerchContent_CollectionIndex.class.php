<?php

class PerchContent_CollectionIndex extends PerchBase
{
    protected $table = 'collection_indexes';
    protected $pk    = 'indexID';

    public function ensureTableIsCreated()
    {
        $tableName = $this->getTableName();
           echo PERCH_DB_PREFIX . $tableName;
        $sql  = 'SHOW TABLES LIKE ' . $this->db->pdb(PERCH_DB_PREFIX . $tableName);
        $rows = $this->db->get_rows($sql);

        if (PerchUtil::count($rows) === 0) {
            return $this->createTable();
        }

        return true;
    }

    public function getTableName()
    {
        $tableName = $this->indexTable();
        if ($tableName == '') {
            $tableName = $this->createTableName($this->indexSlug());
            $this->update([
                              'indexTable' => $tableName,
                          ]);
        }

        return $tableName;
    }

    private function createTableName($slug)
    {
        $slug = str_replace('-', '_', $slug);

        return 'idx_c' . (int) $this->collectionID() . '_' . $slug;
    }

    private function createTable()
    {
        $fields = PerchUtil::json_safe_decode($this->indexFields());
        if (!PerchUtil::count($fields)) {
            return false;
        }

        $fieldDefs = $this->getFieldDefinitions($fields);

        $sql = "CREATE TABLE IF NOT EXISTS `".$this->getTableName()."` (
              `indexID` int(10) NOT NULL AUTO_INCREMENT,
              `itemID` int(10) NOT NULL DEFAULT '0',
              `itemRev` int(10) NOT NULL DEFAULT '0',
              `_id` int(10) NOT NULL DEFAULT '0',
              `_order` char(16) DEFAULT '0',";

        $lines =[];

        foreach ($fieldDefs as $field) {

            $lines[] = "`".$this->columnName($field['id'])."` " . $field['sql_type'] . " DEFAULT '',";

        }

        $sql .= implode("\n", $lines);

        $sql .= " PRIMARY KEY (`itemID`),
              KEY `idx_id` (`_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        PerchUtil::debug($sql);

    }

    private function getFieldDefinitions($fields)
    {
        $Collections = new PerchContent_Collections();
        $Collection = $Collections->find($this->collectionID());

        $Template = new PerchTemplate('content/' . $Collection->collectionTemplate(), 'content');
        $tags     = $Template->find_all_tags('(content|categories)');
        $out      = [];

        foreach ($tags as $Tag) {
            if (in_array($Tag->id, $fields)) {
                $FieldType = new PerchFieldType(null, $Tag, 'content');
                $out[]     = [
                    'id'       => $Tag->id,
                    'type'     => $Tag->type,
                    'sql_type' => $FieldType->sql_type,
                ];
            }
        }

        return $out;
    }

    private function columnName($id)
    {
        $id = PerchUtil::urlify($id);
        $id = str_replace('-', '_', $id);
        return 'c_'.$id;
    }



}
