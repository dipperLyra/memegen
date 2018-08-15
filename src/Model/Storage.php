<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 6/29/18
 * Time: 11:48 AM
 */

namespace API\Model;

use API\Helpers\Database;

include_once __DIR__ . '/../../bootstrap.php';

class Storage
{
    public $message_id; // ID of the message saved in the database.

    public $image_id; // ID of the image

    public $title; // The header of the message

    public $body; // The content or body

    public $footer; // Name of the person

    public $file_name; // Name that the image was saved with.

    public $file_path; // The image file path.

    public $list; // The list retrieved from the db.

    public $lastRecord; // The last card made.


    /*
     * Pass the received texts to class properties
     */
    function receiveTexts($text)
    {
        $json = json_encode($text);
        $decode = json_decode($json);

        $this->title = $decode->title;
        $this->body = $decode->body;
        $this->footer = $decode->footer;
    }

    /*
    * Saves messages to the database.
    */
    public function saveTexts()
    {
        // Pass the received texts to class properties

        $myDb = new Database();

        // Query to store the message written on the card
        $sql = "INSERT INTO content (header, body, footer) 
                VALUES (
                :header, :body, :footer
                )";

        $stmt = $myDb->conn->prepare($sql);
        $stmt->bindValue(":header", $this->title, \PDO::PARAM_STR);
        $stmt->bindValue(":body", $this->body, \PDO::PARAM_STR);
        $stmt->bindValue(":footer", $this->footer, \PDO::PARAM_STR);
        $textSaver = $stmt->execute();
        $this->message_id = $myDb->conn->lastInsertId();

        // Query to insert the file path to the database.
        $sql = "INSERT INTO image (file_path) VALUES (:filepath)";
        $stmt = $myDb->conn->prepare($sql);
        //$stmt->bindValue(":filename", $this->file_name, \PDO::PARAM_STR);
        $stmt->bindValue(":filepath", $this->file_path, \PDO::PARAM_STR);
        $stmt->execute();

        return $textSaver;
    }

    /*
     * Save image properties
     */
    public function saveImage()
    {
        $myDb = new Database();

        $sql = "INSERT INTO image (file_name, file_path) VALUES (:filename, :location)";

        $stmt = $myDb->conn->prepare($sql);
        $stmt->bindValue(":filename", $this->file_name, \PDO::PARAM_STR);
        $stmt->bindValue(":location", $this->file_path, \PDO::PARAM_STR);
        $imageSaver = $stmt->execute();
        $this->image_id = $myDb->conn->lastInsertId();

        return $imageSaver;
    }

    public function retrieveLast()
    {
        $myDb = new Database();

        $sql = "SELECT file_path FROM image ORDER BY id DESC LIMIT 1 ";

        $stmt = $myDb->conn->query($sql);

        // Retrieve the last record
        $this->lastRecord = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->lastRecord;
    }

    /*
     * Retrieves a list of all the cards built.
     */
    public function getCards()
    {
        $myDb = new Database();

        $sql = "SELECT file_path FROM image"; // Query to select all from the db.

        $stmt = $myDb->conn->query($sql); // Execute the query.

        // Fetch all the data returned.
        $this->list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->list;
    }

    /*
     * Retrieves a particular card from the database based on the $id.
     */
    public function getCard($id)
    {
        $connection = new Database();

        $sql = "SELECT * FROM image WHERE id = :id";

        $stmt = $connection->conn->prepare($sql);
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        // Fetch a single record.
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row;
    }

    /*
     * Update a card
     */
    public function updateCard($id)
    {
        $connection = new Database();

        $sql = "UPDATE content 
         SET header = :header, 
             body = :body,
             footer = :footer
             WHERE id = :id";

        $stmt = $connection->conn->prepare($sql);
        $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        $updater = $stmt->execute();
        return $updater;
    }

    /*
     * Delete a card record
     */
    public function deleteCard($card_id)
    {
        $connection = new Database();

        // Delete the texts used for the card
        $sql = "DELETE FROM content WHERE image_id = :card_id";

        $stmt = $connection->conn->prepare($sql);
        $stmt->bindValue(":card_id", $card_id, \PDO::PARAM_INT);
        $stmt->execute();

        // Delete the card record.
        $sql = "DELETE image WHERE id = :card_id";

        $stmt = $connection->conn->prepare($sql);
        $stmt->bindValue(":card_id", $card_id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

