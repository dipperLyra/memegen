<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 6/29/18
 * Time: 4:58 PM
 */

namespace API\Controller;

require_once __DIR__ . '/../../bootstrap.php';

use API\Model\Image;
use API\Model\Storage;
use http\Exception\BadMessageException;

class CardController
{

    public $list; // The list of images in the database.

    public $card; // The last card made.

    public $filepath;

    // Call the write function from the Image class
    // Pass the value received from the use.
    function colourCanvas()
    {
        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);
        
        // image object
        $image = new Image($texts);
        $image->assignPosition();

        // Write text on image.
        return $image->writeTextOnColourCanvas();
    }

    /*
     *  Handles call to the function to write on an image. It passes the required arguments.
     */
    function imageCanvas()
    {
        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        // image object
        $image = new Image($texts);
        $image->assignPosition();
        
        // Write text on the uploaded image.
        return $image->writeTextOnPic();
    }

    /*
     * Makes a call to Storage to retrieve the last record
     */
    public function lastRecord()
    {
        $storage = new Storage();
        $storage->retrieveLast();
        $this->card = $storage->lastRecord;
        return $this->card;
    }

    /*
     * Makes a call to the Storage class to retrieve samples of cards made
     */
    public function listCards()
    {
        $storage = new Storage();
        $storage->getCards();
        $this->list = $storage->list;
        return $this->list;
    }
}