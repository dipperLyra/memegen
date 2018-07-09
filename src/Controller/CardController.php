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
    private $imagePath;

    private $texts;

    private $image;

    private $storageObject;



    function __construct()
    {
        $this->image = new Image();
    }

    // Call the write function from the Image class
    // Pass the value received from the use.
    function writePresetImage()
    {
        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $texts = json_decode($json, true);

        // Assign the texts to be written
        $this->image->text['title'] = $texts['title'];
        $this->image->text['body'] = $texts['body'];
        $this->image->text['footer'] = $texts['footer'];

        // Assign the values to be sent to the db
        $storage = new Storage($this->image->text,$this->image->randomName[0].'jpg', app_base_dir().'file_storage/'.$this->image->randomName[0].'jpg');

        $storage->title = $this->texts['title'];
        $storage->body = $this->texts['body'];
        $storage->author = $this->texts['footer'];

        // Save the texts to be written.
         $storage->saveTexts();

        // Write text on image.
        return $this->image->writeTextCanvas();
    }

    function writeReceivedImage()
    {
        // Read in the json file sent and decode it.
        $json = file_get_contents("php://input");
        $decodeJson = json_decode($json, true);

        $image_url= $decodeJson['image_url'];

        // Save the uploaded to the system tmp_path
        $tmpName = tempnam('/tmp', 'meme-');
        file_put_contents($tmpName, file_get_contents($image_url));

        // Path the url of the source image
        $this->image->imageSource = $image_url;

        // Assign the texts to be written
        $this->image->text['title'] = $decodeJson['title'];
        $this->image->text['body'] = $decodeJson['body'];
        $this->image->text['footer'] = $decodeJson['footer'];

        // Pass the values to the Storage class.
        $storage = new Storage($this->image->text,$this->image->randomName[0].'jpg', app_base_dir().'file_storage/'.$this->image->randomName[0].'jpg');
        $storage->saveImage();
        $storage->saveTexts();

        // Write text on the uploaded image.
        return $this->image->writeText();
    }
}