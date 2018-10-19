<?php
/**
 * Created by PhpStorm.
 * User: eche
 * Date: 6/29/18
 * Time: 4:59 PM
 */

require_once __DIR__ . '/vendor/autoload.php';

use API\Controller\CardController;


$klein = new \Klein\Klein();

// Send the text to be written on the card.
$klein->get('/canvas', function ($request, $response) use ($klein)
{
    
    $message = ['message' => 'The image url is: '.'http://local.cardstorage.com/cards'];

 //   $klein->response()->body();

    $cardContrl = new CardController();
    $request->body($cardContrl->colourCanvas());
    $response->body(json_encode($message));
});

// Send an image and text to be written on it.
$klein->post('/cards', function ($request, $response)
{
    $message = ['message' => 'The url to the resource is: '];

    $cardContrl = new CardController();
    $request->body($cardContrl->imageCanvas());
    echo ($message['message']."\n".$cardContrl->filepath);
});

// Get the list of cards available.
$klein->get('/cards', function ($request, $response)
{
    $cardContrl = new CardController();
    $request->body($cardContrl->listCards());
    $message = "The link to the" .' '.json_encode($cardContrl->list);
    $response->body($message);
});

// Get the url of a single card
$klein->get('/card', function ($request, $response)
{
    $cardContrl = new CardController();
    $request->body($cardContrl->lastRecord());
    $message = "Link to resource" .' '.json_encode($cardContrl->card);
    $response->body($message);
});

// Get the api documentation
$klein->get('/api', function ($request, $response){
    include_once 'swagger.json';
});

// Update a card record
$klein->put('cards/', function ()
{
    $cardContrl = new CardController();
});

// Delete a card record
$klein->delete('cards/', function ()
{
    $cardContrl = new CardController();
});

$klein->dispatch();