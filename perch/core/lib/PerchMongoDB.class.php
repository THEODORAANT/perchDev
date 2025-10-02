<?php

class PerchMongoDB {
	 private $manager;
        private $dbName;
        private $collectionName;


    function __construct($config=null)
    	{
    		if ($config) $this->config = $config;

    		/*if (!defined('PERCH_DB_CHARSET')) 	define('PERCH_DB_CHARSET', 'utf8');
    		if (!defined('PERCH_DB_PORT')) 		define('PERCH_DB_PORT', NULL);
    		if (!defined('PERCH_DB_SOCKET')) 	define('PERCH_DB_SOCKET', NULL);*/
    		 $uri = 'mongodb+srv://nlclinicisleofwightuk:DetHede6HJL0OZi8@questionnaire.vfumfg0.mongodb.net';
             // $uri = 'questionnaire.vfumfg0.mongodb.net';
             $dbName = 'questionnaire';
              $collectionName = 'answer_logs';
                        //nlclinicisleofwightuk
                        //password:DetHede6HJL0OZi8

    		 try {
                 $this->manager = new MongoDB\Driver\Manager($uri);
                 echo "Connected to MongoDB!";
             } catch (MongoDB\Driver\Exception\Exception $e) {
                 echo "Connection error: ", $e->getMessage();
             }
                        $this->dbName = $dbName;
                        $this->collectionName = $collectionName;
    	}
        public function logAnswer($userId, $questionnaireId, $step, $answer)
        {
            $bulk = new MongoDB\Driver\BulkWrite;
         $bulk->update(
             ['user_id' => $userId, 'questionnaire_id' => $questionnaireId],
             [
                 '$set' => [
                     "answers.$step" => $answer,
                     "updated_at" => new MongoDB\BSON\UTCDateTime(),
                 ],
                 '$setOnInsert' => [
                     "created_at" => new MongoDB\BSON\UTCDateTime(),
                 ]
             ],
             ['upsert' => true]
         );
try{
            $this->manager->executeBulkWrite("{$this->dbName}.{$this->collectionName}", $bulk);
               }catch (Exception $e) {
               echo "executeBulkWrite";
                           echo $e->getMessage();

                }


        }

        public function getAnswers($userId, $questionnaireId)
        {
            $query = new MongoDB\Driver\Query([
                'user_id' => $userId,
                'questionnaire_id' => $questionnaireId
            ]);

            $cursor = $this->manager->executeQuery("{$this->dbName}.{$this->collectionName}", $query);

            foreach ($cursor as $document) {
                return $document;
            }

            return null;
        }


	}
