<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 7/2/18
 * Time: 11:10 AM
 */

namespace API\Model;

require_once __DIR__ . "/../../bootstrap.php";

use GDText\Box;
use GDText\Color;

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

    private $receivedImages = 'file_storage/images/'; // Location to store the images uploaded by the client

    public $storagePath; // Storage path of the written image

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

    public $headerColour;

    public $bodyColour;

    public $footerColour;

    private $fonts = array();

    public $fontSize;

    public $headerFont;

    public $bodyFont;

    public $footerFont;

    private $writeAngle = 0;



    function __construct()
    {
        /*
         * Assign values for the colours and fonts.
         */
        $this->fonts();


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
        } else {
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
     *  Assign colours
     */
    private function colours()
    {
        $this->colours['black'] = imagecolorallocate($this->imageCanvas, 0, 0, 0);
        $this->colours['white'] = imagecolorallocate($this->imageCanvas, 255, 255, 255);
        $this->colours['red'] = imagecolorallocate($this->imageCanvas, 255, 0, 0);
    }

    /*
     *  Handles the logic to choose a particular colour.
     */
    public function whichColour()
    {
        // Check the colour for the header
        if ($this->headerColour == "r") $this->headerColour = $this->colours['red'];
        if ($this->headerColour == "w") $this->headerColour = $this->colours['white'];
        if ($this->headerColour == "b") $this->headerColour = $this->colours['black'];

        // Check the colour to asssign to the body
        if ($this->bodyColour == "r") $this->bodyColour = $this->colours['red'];
        if ($this->bodyColour == "w") $this->bodyColour = $this->colours['white'];
        if ($this->bodyColour == "b") $this->bodyColour = $this->colours['black'];

        // Check the colour to assign to the footer
        if ($this->footerColour == "r") $this->footerColour = $this->colours['red'];
        if ($this->footerColour == "w") $this->footerColour = $this->colours['white'];
        if ($this->footerColour == "b") $this->footerColour = $this->colours['black'];
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
    function getRandomWord($len = 2)
    {
        $arrayMerge = array_merge(range('a', 'z'));
        shuffle($arrayMerge);
        $string = implode($arrayMerge);
        $randomName = substr($string, 0, $len);
        return $randomName;
    }

    /*
     * Wrap the text
     */
    function wrapText($text)
    {
        $text['title'] = wordwrap($this->text['title'], 15, "\n", false);
        $text['body'] = wordwrap($this->text['body'], 40, "\n", false);
        $text['footer'] = wordwrap($this->text['footer'], 25, "\n", false);
        return $text;
    }

    /*
     * Write text on an image
     */
    function writeTextOnImageCanvas()
    {
        $this->imageCanvas =  imagecreatefromjpeg($this->imageSource);

        // Resize the image if larger than the desired dimensions.
        $this->resize();

        // Create box for writing texts.
        $this->box();

        // Text positions
        $this->position();

        // Assign colours
        $this->colours();

        $this->whichColour();

        // Get the array keys as variables with values.
        list($x, $y) = $this->titlePosition;

        // Write the title: check if the user set a font type, if no use a fixed font size (20).
        if (!isset($this->headerFont, $this->headerColour)) {
            imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['red'], $this->fonts['franchise'], wordwrap($this->text['title'], 15, "\n", false));
        } else {
            imagettftext($this->imageCanvas, $this->headerFont, $this->writeAngle, $x, $y, $this->headerColour, $this->fonts['franchise'], wordwrap($this->text['title'],  15, "\n", false));
        }

        // Get the x and y coordinates
        list($x, $y) = $this->bodyPosition;
        // Write the body.
        if (!isset($this->bodyFont, $this->bodyColour)) {
            imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['black'], $this->fonts['pacifico'], wordwrap($this->text['body'],  35, "\n", false));
        } else {
            imagettftext($this->imageCanvas, $this->bodyFont, $this->writeAngle, $x, $y, $this->bodyColour, $this->fonts['pacifico'], wordwrap($this->text['body'], 35, "\n", false));
        }

        // Get the x and y coordinates
        list($x, $y) = $this->footerPosition;

        // Write the footer
        if (!isset($this->footerFont, $this->footerColour)) {
            imagettftext($this->imageCanvas, $this->fontSize, $this->writeAngle, $x, $y, $this->colours['white'], $this->fonts['prisma'], wordwrap($this->text['footer'],  15, "\n", false));
        } else {
            imagettftext($this->imageCanvas, $this->footerFont, $this->writeAngle, $x, $y, $this->footerColour, $this->fonts['prisma'], wordwrap($this->text['footer'],  15, "\n", false));
        }

        // Save image to a file
        $this->storagePath = app_base_dir() .'/'. $this->receivedImages . $this->getRandomWord(5).'.jpg';
        imagejpeg($this->imageCanvas, $this->storagePath);
        return $this->storagePath;
    }

    // Write on a canvas
    function writeTextOnColourCanvas()
    {
        $img = imagecreatetruecolor(500, 500);
        $backgroundColor = imagecolorallocate($img, 0, 18, 64);
        imagefill($img, 0, 0, $backgroundColor);

        // Write on the image
        $box = new Box($img);
        $box->setFontFace(app_base_dir().'/public/resources/fonts/franchise/Franchise-Bold-hinted.ttf');
        $box->setFontColor(new Color(255, 75, 140));
        $box->setTextShadow(new Color(0,0,0,50), 2, 2);
        $box->setFontSize(40);
        $box->setBox(20, 20, 460, 460);
        $box->setTextAlign('left', 'top');
        $box->draw($this->text['title']);

        $box = new Box($img);
        $box->setFontFace(app_base_dir().'/public/resources/fonts/pacifico/Pacifico.ttf');
        $box->setFontSize(40);
        $box->setFontColor(new Color(255, 2555, 2555));
        $box->setTextShadow(new Color(0, 0, 0, 50), 0, -2);
        $box->setBox(20, 20, 460, 460);
        $box->setTextAlign('center', 'center');
        $box->draw($this->text['body']);

        $box = new Box($img);
        $box->setFontFace(app_base_dir().'/public/resources/fonts/prisma/Prisma.otf');
        $box->setFontSize(40);
        $box->setFontColor(new Color(148, 212, 1));
        $box->setTextShadow(new Color(0, 0, 0, 50), 0, -2);
        $box->setBox(20, 20, 460, 460);
        $box->setTextAlign('right', 'bottom');
        $box->draw($this->text['footer']);


        // Save the file and return to the user.
        $this->storagePath = app_base_dir() .'/'. $this->receivedImages . $this->getRandomWord(5).'.jpg';
        imagejpeg($img, $this->storagePath);
        return $this->storagePath;
    }

    /*
     * Download the new image that has the text.
     */
    function imageDownload()
    {
        if (file_exists($this->storagePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'. basename($this->storagePath) . '"');
            header('Exprires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($this->storagePath));
            readfile($this->storagePath);
        }
    }
}