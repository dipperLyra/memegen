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
        // image object
        $image = new Image();

        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        // Assign the texts to be written
        $image->text['title'] = htmlentities($texts['title'], ENT_QUOTES, "UTF-8");
        $image->text['body'] = htmlentities($texts['body'], ENT_QUOTES, "UTF-8");
        $image->text['footer'] = htmlentities($texts['footer'], ENT_QUOTES, "UTF-8");

        if (isset($texts['headerColour'], $texts['bodyColour'], $texts['footerColour'])) {
            $image->headerColour = htmlentities($texts['headerColour'], ENT_QUOTES, "UTF-8");
            $image->bodyColour = htmlentities($texts['bodyColour'], ENT_QUOTES, "UTF-8");
            $image->footerColour = htmlentities($texts['footerColour'], ENT_QUOTES, "UTF-8");
        }

        if (isset($texts['fontSize'])) {
            $image->headerFont = htmlentities($texts['headerFont'], ENT_QUOTES, "UTF-8");
            $image->bodyFont = htmlentities($texts['bodyFont'], ENT_QUOTES, "UTF-8");
            $image->footerFont = htmlentities($texts['footerFont'], ENT_QUOTES, "UTF-8");
        } else {
            $image->fontSize = htmlentities($texts['fontSize'], ENT_QUOTES, "UTF-8");
        }


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

    /*
     *  Handles call to the function to write on an image. It passes the required arguments.
     */
    function imageCanvas()
    {
        // image object
        $image = new Image();

        // Storage object.
        $storage = new Storage();

        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        $image_url = $texts['image_url'];

        // Save the uploaded to the system tmp_path
        $tmpName = tempnam('/tmp', 'meme-');
        file_put_contents($tmpName, file_get_contents($image_url));

        // Path the url of the source image
        $image->imageSource = $image_url;

        // Assign the texts to be written
        $image->text['title'] = htmlentities($texts['title'], ENT_QUOTES, "UTF-8");
        $image->text['body'] = htmlentities($texts['body'], ENT_QUOTES, "UTF-8");
        $image->text['footer'] = htmlentities($texts['footer'], ENT_QUOTES, "UTF-8");

        // Pass the font size to be used
        if (!isset($texts['fontSize'])) {
            $image->headerFont = htmlentities($texts['headerFont'], ENT_QUOTES, "UTF-8");
            $image->bodyFont = htmlentities($texts['bodyFont'], ENT_QUOTES, "UTF-8");
            $image->footerFont = htmlentities($texts['footerFont'], ENT_QUOTES, "UTF-8");
        } else {
            $image->fontSize = htmlentities($texts['fontSize'], ENT_QUOTES, "UTF-8");
        }

        // Assign the chosen colour
        if (isset($texts['headerColour'])) $image->headerColour = htmlentities($texts['headerColour'], ENT_QUOTES, "UTF-8");
        if (isset($texts['bodyColour'])) $image->bodyColour = htmlentities($texts['bodyColour'], ENT_QUOTES, "UTF-8");
        if (isset($texts['footerColour'])) $image->footerColour = htmlentities($texts['footerColour'], ENT_QUOTES, "UTF-8");

        // Pass the texts to the Storage Class
        $storage->receiveTexts($texts);

        // Write text on the uploaded image.
        $image->writeTextOnImageCanvas();

        // Pass the file path to stored in the database.
        $storage->file_path = $image->storagePath;

        $this->filepath = & $storage->file_path;

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