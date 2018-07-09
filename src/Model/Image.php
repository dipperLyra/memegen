<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 7/2/18
 * Time: 11:10 AM
 */

namespace API\Model;

require_once __DIR__ . "/../../bootstrap.php";

class Image
{
    /*
     * Dimensions. If the image resource sent is more 600x600,
     * it will be resized to 600x600.
     */
    private $width;

    private $height;

    private $constWidth = 600;

    private $constHeight = 600;

    /*
     * Image file and resource.
     */
    public $imageSource; // Image file being written on

    private $imageCanvas; // Image resource returned by imagecreatetruecolor

    public $randomName; // Random name to store the image

    /*
     * Position and writing resources
     */
    public $text;

    private $titleBox;

    private $bodyBox;

    private $footerBox;

    private $titlePosition;

    private $bodyPosition;

    private $footerPosition;

    private $colours = array();

    private $fonts = array();

    private $fontSize = 20;

    private $writeAngle = 0;



    function __construct()
    {
        /*
         * Assign values for the colours and fonts.
         */
        $this->fonts();
        $this->colours();

        // Create box for writing texts.
        $this->box();

    }

    /*
     * Get the image file and check the dimensions.
     * Assign the values to class properties.
     */
    function resize()
    {
        // Get the height and width of the image file
        list($imageWidth, $imageHeight) = getimagesize($this->imageSource);

        // Check the ratio of the new image and
        // reassign new width and height if greater than the desired dimensions.
        $ratio = $imageWidth / $imageHeight;

        if (1 > $ratio) {
            $this->width = $this->constHeight * $ratio;
            return $this->width;
        }
        else {
            $this->height = $this->constWidth / $ratio;
            return $this->height;
        }
    }

    /*
     * Assign Font's path
     */
    private function fonts()
    {
        $this->fonts['aria'] = app_base_dir() . '/public/resources/fonts/aria/aria.ttf';
        $this->fonts['franchise'] = app_base_dir() . '/public/resources/fonts/franchise/Franchise-Bold-hinted.ttf';
        $this->fonts['pacifico'] = app_base_dir() . '/public/resources/fonts/pacifico/Pacifico.ttf';
        $this->fonts['prisma'] = app_base_dir() . '/public/resources/fonts/prisma/Prisma.ttf';
    }

    /*
     *
     */
    private function colours()
    {
        $this->colours['black'] = imagecolorallocate($this->imageCanvas, 0, 0, 0);
        $this->colours['white'] = imagecolorallocate($this->imageCanvas, 255, 255, 255);
        $this->colours['red'] = imagecolorallocate($this->imageCanvas, 255, 0, 0);
    }

    /*
     * The box to hold each text.
     * The box is drawn using the specified font size and style.
     */
    function box()
    {
        $this->titleBox = imagettfbbox($this->fontSize, $this->writeAngle, $this->fonts['franchise'], $this->text['title']);
        $this->bodyBox = imagettfbbox($this->fontSize, $this->writeAngle, $this->fonts['pacifico'], $this->text['body']);
        $this->footerBox = imagettfbbox($this->fontSize, $this->writeAngle, $this->fonts['prisma'], $this->text['footer']);
    }

    /*
     * Set the positions of the text boxes
     */
    function position()
    {
        /*
         * Set the position of the title
         */
        $x = $this->titleBox[2] + (imagesx($this->imageCanvas) / 8);
        $boundingHeight = abs($this->titleBox[1] - $this->titleBox[7]);
        $y = abs($this->titleBox[1]) + $boundingHeight;

        // Assign the coordinates to the title.
        $this->titlePosition = array($x, $y);


        /*
         * Set the position of the body text.
         */
        $x = $this->bodyBox[0] + (imagesx($this->imageCanvas) / 4);
        $y = abs($this->bodyBox[1]) + (imagesy($this->imageCanvas) / 2);

        // Set the coordinates of the body.
        $this->bodyPosition = array($x, $y);


        /*
         * Set the position of the footer
         */
        $x = $this->footerBox[0] + (imagesx($this->imageCanvas) / 4);
        //$x = $this->footerBox[2] + (imagesx($this->imageCanvas) );
        $y = (imagesy($this->imageCanvas)) - (imagesy($this->imageCanvas) / 16);

        // Assign the coordinates to the footer.
        $this->footerPosition = array($x, $y);
    }

    /*
     * Generate random names for the image
     */
    function getRandomWord($len = 5)
    {
        $this->randomName = array_merge(range('a', 'z'), range('A', 'Z'));
        shuffle($this->randomName);
        return substr(implode($this->randomName), 0, 5);
    }

    /*
     * Write text on image canvas
     */
    function writeText()
    {
        $this->imageCanvas =  imagecreatefromjpeg($this->imageSource);

        $this->resize(); // Resize the image if larger than the desired dimensions.

        // Text positions
        $this->position();

        // Get the array keys as variables with values.
        list($x, $y) = $this->titlePosition;
        // Write the title
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['franchise'], $this->text['title']);

        // Get the x and y coordinates
        list($x, $y) = $this->bodyPosition;
        // Write the body.
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['pacifico'], $this->text['body']);

        // Get the x and y coordinates
        list($x, $y) = $this->footerPosition;
        // Write the footer
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['prisma'], $this->text['footer']);


        // Set a random name that the image will be saved with.
        $this->getRandomWord();

        // Save image to a file
        $storagePath = app_base_dir().'/file_storage/'.$this->randomName[0].'.jpg';
        return imagejpeg($this->imageCanvas, $storagePath);

        // Free memory used for the image processing
        //imagedestroy($this->imageCanvas);
    }

    /*
     * Write text on preset image
     */
    function writeTextCanvas()
    {
        $this->imageCanvas = imagecreatetruecolor($this->constWidth, $this->constHeight);

        $this->position(); // Assign positions for the text

        // Get the array keys as variables with values.
        list($x, $y) = $this->titlePosition;
        // Write the title
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['franchise'], $this->text['title']);

        // Get the x and y coordinates
        list($x, $y) = $this->bodyPosition;
        // Write the body.
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['pacifico'], $this->text['body']);

        // Get the x and y coordinates
        list($x, $y) = $this->footerPosition;
        // Write the footer
        imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['prisma'], $this->text['footer']);


        // Set a random name that the image will be saved with.
        $this->getRandomWord();

        // Save image to a file
        $storagePath = app_base_dir().'/file_storage/'.$this->randomName[0].'.jpg';
        return imagejpeg($this->imageCanvas, $storagePath);
    }

    /*
     * Download the new image that has the text.
     */
    function imageDownload()
    {
        $file = 'meme.png';

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'. basename($file) . '"');
            header('Exprires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }
    }
}