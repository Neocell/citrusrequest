<?php

namespace ARG\Models;
use ARG\App;

/**
 * Class BDDModel
 * Model qui va stocker toute les méthodes qui font appel à la base de donnée.
 */
class BDDModel 
{
    /**
     * @var string $bdd | Variable contenant la base de donnée qui va être intérrogé.
     */
    private static $bdd;



    /**
     * @param string $bdd | Nom de la base de donnée
     * @return void
     */
    public static function useBDD($bdd) {
        self::$bdd = $bdd;
    }

    /**
     * @return array $datas | Contient toute les base de données 
     */
    public static function getAllDatabases() {
        $datas = App::getDB()->query('SHOW DATABASES');
        return $datas;
    }

    /**
     * @return date $date | Contient toute les base de données 
     */
    public static function getDateTable($table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        $date = App::getDB()->query("SELECT MIN(create_time) as Create_time FROM INFORMATION_SCHEMA.TABLES
                                    WHERE table_name = '".$table."'");
        $dateFormat = new \DateTime($date[0]->Create_time);
        $result = date_format($dateFormat, "d/m/Y H:i:s");
        return $result;
    }

    /**
     * @return int $nb_tables | Nombre de tables
     */
    public static function countTables() {
        $nb_tables = App::getDB()->query('SELECT COUNT(*) as nbr_Table FROM information_schema.tables WHERE table_schema = \''.self::$bdd.'\'');
        return $nb_tables;
    }

    /**
     * @return float $em | Espace mémoire de la base de donnée passé en paramétre
     */
    public static function memorySpaceDatabase() {
        $em = App::getDB()->query('SELECT Round(Sum(data_length + index_length) / 1024 / 1024, 1) as em FROM information_schema.tables WHERE table_schema = \''.self::$bdd.'\' GROUP BY table_schema');
        return $em;
    }

    /**
     * @return array $tables | Liste des tables de la base de donnée passé en paramétre
     */
    public static function getAllTables() {
        App::getDB()->prepare('use '.self::$bdd.';');
        $tables = App::getDB()->query('SHOW table status;');
        return $tables;
    }

    /**
     * @param string $table | Nom de la table
     * @return array $tables | Liste des lignes contenu dans la table
     */
    public static function getAllContentTable($table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        $contents = App::getDB()->query("SELECT * FROM $table;");
        return $contents;
    }

    /**
     * @param string $table | Nom de la table
     * @return array $tables | Liste des lignes contenu dans la table
     */
    public static function getAllColumnTable($table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        $columns = App::getDB()->query("SHOW columns FROM $table;");
        return $columns;
    }

    /**
     * @param string $oldName | Encient nom de la base de donnée 
     * @param string $newName | Nouveau nom de la base de donnée 
     * @return void
     */
    public static function renameBDD($oldName, $newName) {
        App::getDB()->query("SHOW columns FROM $table;");
    }

    /**
     * @param string $oldName | Encient nom de la table
     * @param string $newName | Nouveau nom de la table
     * @return void
     */
    public static function renameTable($oldName, $newName) {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("RENAME TABLE " . $oldName . " TO " . $newName . ";");
    }

    /**
     * @param string $oldName | Encient nom de la table
     * @return void
     */
    public static function renameStructure() {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("RENAME TABLE " . $oldName . " TO " . $newName . ";");
    }

    /**
     * @param array $config | Contient les configuration de l'ajout
     * @param array $datas | Contient les datas à ajouter 
     * @return void
     */
    public static function modifContent($config, $datas) {
        foreach($datas as $key => $value) {
            if($value !== '') {
                $arrayDiff[$key] = $value;
            }
        }
        if(isset($arrayDiff)){
            App::getDB()->prepare('use '.self::$bdd.';');
            $query = "UPDATE ".$config['tableName']." SET ";
            end($arrayDiff);
            $lastKey = key($arrayDiff);
            foreach($datas as $key => $value) {
                if($value !== '') {
                    $query .= "`" .$key . "` = ";
                    $query .= "'" .$value . "'";
                    if($key !== $lastKey){
                        $query .= " ,";
                    }
                }
            }
            $query .= " WHERE `".$config['tableName']."`.`".$config['idCurrentName']."` = ".$config['idCurrentValue'].";";
            App::getDB()->query($query);
        }
    }

    /**
     * @param array $config | Contient les configuration pour la modification
     * @param array $datas | Contient les données à modifié
     * @return void
     */
    public static function modifColumn($config, $datas) {
        if($datas['EditColumnName'] !== '') {
            App::getDB()->prepare('use '.self::$bdd.';');
            $query = "ALTER TABLE ".$config['tableName'];
            $query .= " CHANGE `".$config['columnName']."` ";
            $query .= "`".$datas['EditColumnName']."` ".$datas['EditColumnType'];
            if($datas['EditColumnSize'] !== '') 
                $query .= "(".$datas['EditColumnSize'].") ";
            else 
                $query .= "(10) ";
            if($datas['EditColumnDefaultValue'] === 'Null')
                $query .= "NULL DEFAULT NULL";
            else 
                $query .= "NULL";
            if(isset($datas['EditColumnAI']))
                $query .= " AUTO_INCREMENT";
            $query .= ";";
            if(isset($datas['EditColumnIndex']) && $datas['EditColumnIndex'] === 'PRIMARY') {
                App::getDB()->query("ALTER TABLE `".$config['tableName']."` ADD PRIMARY KEY(`".$config['columnName']."`);");
            }
            App::getDB()->query($query);
        }
    }

    /**
     * @param string $bdd | Nom de la base de donnée à supprimer
     * @return void
     */
    public static function deleteBDD($bdd) {
        App::getDB()->query("DROP DATABASE " . $bdd . ";");
    }

    /**
     * @param string $table | Nom de la table à supprimer
     * @return void
     */
    public static function deleteTable($table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("DROP TABLE " . $table . ";");
    }

    /**
     * @param string $table | Nom de la table
     * @param string $column | Nom de la colone à supprimer
     * @return void
     */
    public static function deleteColumn($table, $column) {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("ALTER TABLE ".$table." DROP ".$column.";");
    }

    /**
     * @param string $bdd | Nom de la base de donnée
     * @param string $table | Nom de la table
     * @param string $id | Clef primaire du contenue à supprimer
     * @return void
     */
    public static function deleteContent($table, $id) {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("DELETE FROM ".$table." WHERE id=".$id.";");
    }

    /**
     * @param string $bdd | Nom de la base de donnée à ajouter
     * @return void
     */
    public static function addBDD($bdd) {
        App::getDB()->query("CREATE DATABASE " . $bdd . ";");
    }

    /**
     * @param string $table | Nom de la table a ajouter
     * @return void
     */
    public static function addTable($table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        App::getDB()->query("CREATE TABLE " . $table . " ( id INT PRIMARY KEY NOT NULL );");
    }

    /**
     * @param string $c_name | Nom de la nouvelle colone 
     * @param string $c_type | Type de la nouvelle colone 
     * @param int $c_size | Taille de la nouvelle colone 
     * @param string $c_defaultValue | Valeur par défault de la nouvelle colone 
     * @param string $c_index | Type de l'index de la nouvelle colone 
     * @param string $bdd | Nom de la base de donnée
     * @param string $table | Nom de la table
     * @return void
     */
    public static function addColumn($c_name, $c_type, $c_size, $c_defaultValue, $c_index, $c_ai, $table) {
        App::getDB()->prepare('use '.self::$bdd.';');
        $query = "ALTER TABLE " . $table . " ADD " . $c_name . " " . $c_type;
        if($c_type === 'VARCHAR' && $c_size === ''){
            $query .= "(10)";
        } 
        if($c_size !== ''){
            $query .= "(" . $c_size . ")";
        }
        if($c_defaultValue === 'Null')
            $query .= ' NULL DEFAULT NULL';
        if($c_ai === 'true')
            $query .= ' AUTO_INCREMENT, ADD PRIMARY KEY (`' . $c_name . '`)';
        else if($c_index === 'PRIMARY')
            $query .= ', ADD PRIMARY KEY (`' . $c_name . '`)';
        $query .= ';';
        App::getDB()->query($query);
    }

    /**
     * @param string $table | Nom de la table a ajouter
     * @param array $datas | Contient les datas à ajouter 
     * @return void
     */
    public static function addContent($table, $datas) {
        App::getDB()->prepare('use '.self::$bdd.';');
        foreach($datas as $key => $value) {
            if($value !== '') {
                $column[] = $key;
                $values[] = $value;
            }
        }
        if(isset($column) && isset($value))
            App::getDB()->query("INSERT INTO ".$table." (" .implode(", ", $column). ") VALUES (".implode(", ", $values).");");
    }

    /**
     * @param string $request | Requete a executer.
     * @return void
     */
    public static function getSQLResult($array) {
        if($array['database'] != 'all') {
            self::useBDD($array['database']);
            App::getDB()->prepare('use '.self::$bdd.';');
        }
        $result = App::getDB()->query($array['request']);
        return $result;
    }

}
