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
$klein->post('/cards', function ($request, $response)
{
    $cardContrl = new CardController();
    $request->body($cardContrl->writePresetImage());
    $message = ['message' => 'The record was successfully added to the db'];
    $response->body(json_encode($message));
});

// Get a particular card using the id
$klein->put('/cards', function ()
{
    $cardContrl = new CardController();
    $cardContrl->writeReceivedImage();
//    $message = ['message' => 'The download link:  http://'];
//    $response->body(json_encode($message));

});

// Get the list of cards
$klein->get('/cards', function ()
{
    $cardContrl = new CardController();
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