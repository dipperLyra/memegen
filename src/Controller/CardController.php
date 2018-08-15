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

class CardController
{

    public $list; // The list of images in the database.

    public $card; // The last card made.

    function imageObject()
    {
        $image = new Image();
        return $image;
    }

    // Call the write function from the Image class
    // Pass the value received from the use.
    function colourCanvas()
    {
        // image object
        $image = new Image();

        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        // Assign the texts to be written
        $image->text['title'] = $texts['title'];
        $image->text['body'] = $texts['body'];
        $image->text['footer'] = $texts['footer'];

        // Assign the values to be sent to the db
        $storage = new Storage();
        $storage->receiveTexts($texts);

        // Write text on image.
        $image->writeTextOnColourCanvas();

        // Pass the file path to be stored in the database.
        $storage->file_path = $image->storagePath;

        // Save the texts written.
        $storage->saveTexts();

        return $storage->file_path;
    }

    function imageCanvas()
    {
        // image object
        $image = new Image();

        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        $image_url= $texts['image_url'];

        // Save the uploaded to the system tmp_path
        $tmpName = tempnam('/tmp', 'meme-');
        file_put_contents($tmpName, file_get_contents($image_url));

        // Path the url of the source image
        $image->imageSource = $image_url;

        // Assign the texts to be written
        $image->text['title'] = $texts['title'];
        $image->text['body'] = $texts['body'];
        $image->text['footer'] = $texts['footer'];

        // Pass the values to the Storage class.
        $storage = new Storage();
        $storage->receiveTexts($texts);

        // Write text on the uploaded image.
        $image->writeTextOnImageCanvas();

        // Pass the file path to stored in the database.
        $storage->file_path = $image->storagePath;

        //$storage->saveImage();
        $storage->saveTexts();

        return $storage->file_path;
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