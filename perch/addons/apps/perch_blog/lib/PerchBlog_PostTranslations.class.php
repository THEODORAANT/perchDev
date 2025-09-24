<?php

class PerchBlog_PostTranslations extends PerchAPI_Factory
{
    protected $table               = 'blog_post_translations';
    protected $pk                  = 'translationID';
    protected $singular_classname  = 'PerchBlog_PostTranslation';
    protected $default_sort_column = 'language';

    public function __construct($api = false)
    {
        parent::__construct($api);
        $this->ensure_table_exists();
    }

    public function ensure_table_exists()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
            translationID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            basePostID INT(10) UNSIGNED NOT NULL,
            translationPostID INT(10) UNSIGNED NOT NULL,
            language VARCHAR(16) NOT NULL,
            created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (translationID),
            UNIQUE KEY base_lang (basePostID, language),
            KEY translation_post (translationPostID)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8';

        $this->db->execute($sql);
    }

    public function find_for_post($postID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE translationPostID=' . $this->db->pdb((int)$postID) . ' LIMIT 1';
        $row = $this->db->get_row($sql);

        return $this->return_instance($row);
    }

    public function find_base_id_for_post($postID)
    {
        $Translation = $this->find_for_post($postID);
        if ($Translation && $Translation->basePostID()) {
            return (int) $Translation->basePostID();
        }

        return (int) $postID;
    }

    public function get_for_base($basePostID)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE basePostID=' . $this->db->pdb((int)$basePostID) . ' ORDER BY language ASC';
        $rows = $this->db->get_rows($sql);

        return $this->return_instances($rows);
    }

    public function register_translation($basePostID, $translationPostID, $language)
    {
        $data = [
            'basePostID'         => (int) $basePostID,
            'translationPostID'  => (int) $translationPostID,
            'language'           => $language,
            'created'            => date('Y-m-d H:i:s'),
        ];

        $sql = 'INSERT INTO ' . $this->table . ' (basePostID, translationPostID, language, created) VALUES ('
            . $this->db->pdb($data['basePostID']) . ', '
            . $this->db->pdb($data['translationPostID']) . ', '
            . $this->db->pdb($data['language']) . ', '
            . $this->db->pdb($data['created']) . ')
            ON DUPLICATE KEY UPDATE translationPostID=VALUES(translationPostID), created=VALUES(created)';

        return $this->db->execute($sql);
    }
}

