<?php

require_once __DIR__ . '/vendor/autoload.php';

use API\Controller\CardController;

$klein = new \Klein\Klein();


/**
 * Send the text to be written on the card.
 */
$klein->post('/canvas', function ($request, $response)
{
    
    $message = ['message' => 'The image url is: '.'http://local.cardstorage.com/cards'];

    $cardContrl = new CardController();
    $request->body($cardContrl->colourCanvas());
    $response->body(json_encode($message));
});

/**
 *  Send the text to be written on the card.
 */
$klein->post('/cards', function ($request, $response)
{
    $message = ['message' => 'The url to the resource is: '];

    $cardContrl = new CardController();
    $request->body($cardContrl->imageCanvas());
    return $message['message']."\n".$cardContrl->filepath;
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
    include "public/dist/";
});


$klein->dispatch();