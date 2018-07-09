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
    private $connection;

    public $message_id; // ID of the message saved in the database.

    public $image_id; // ID of the image

    public $title; // The header of the message

    public $body; // The content or body

    public $author; // Name of the person

    public $file_name; // Name that the image was saved with.

    public $file_path; // The image file path.


    /*
     * Constructor to receive all the values for the class properties
     */
    function __construct($text = array(), $file_name, $file_path)
    {
        $this->connection = new Database();

        $this->title = $text['title'];
        $this->body = $text['body'];
        $this->author = $text['author'];
        $this->file_name = $file_name;
        $this->file_path = $file_path;
    }

    /*
    * Saves messages to the database.
    */
    public function saveTexts()
    {
       // $connection = new Database();

        $sql = "INSERT INTO content (header, body, footer, image_id) 
                VALUES (
                :header, :body, :footer, 
                (SELECT id FROM image)
                )";

        $stmt = $this->connection->conn->prepare($sql);
        $stmt->bindValue(":header", $this->title, \PDO::PARAM_STR);
        $stmt->bindValue(":body", $this->body, \PDO::PARAM_STR);
        $stmt->bindValue(":footer", $this->author, \PDO::PARAM_STR);
        $stmt->bindValue(":image_id", $this->image_id, \PDO::PARAM_STR);
        $textSaver = $stmt->execute();
        $this->message_id = $this->connection->conn->lastInsertId();
        return $textSaver;
    }

    /*
     * Save image properties
     */
    public function saveImage()
    {
       // $connection = new Database();

        $sql = "INSERT INTO image (file_name, file_path) VALUES (:filename, :location)";

        $stmt = $this->connection->conn->prepare($sql);
        $stmt->bindValue(":filename", $this->file_name, \PDO::PARAM_STR);
        $stmt->bindValue(":location", $this->file_path, \PDO::PARAM_STR);
        $imageSaver = $stmt->execute();
        $this->image_id = $this->connection->conn->lastInsertId();
        return $imageSaver;
    }

    /*
     * Retrieves a list of all the cards built.
     */
    public function getCards()
    {
        $connection =  new Database();

        $sql = "SELECT * FROM image"; // Query to select all from the db.

        $stmt = $connection->conn->query($sql); // Execute the query.

        // Fetch all the data returned.
        $list = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $list;
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

