<?php

namespace MyBot;

include __DIR__ . "/../vendor/autoload.php";
include "botway.php";

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;

$botConfig = new Botway();

// Create a $discord BOT
$discord = new Discord([
    "token" => $botConfig->GetToken(),
]);

// Create a $browser with same loop as $discord
$browser = new Browser(null, $discord->getLoop());

// When the Bot is ready
$discord->on("ready", function (Discord $discord) {
    // Listen for messages
    $discord->on("message", function (Message $message, Discord $discord) {
        // Ignore messages from any Bots
        if ($message->author->bot) return;

        // If message is "ping"
        if ($message->content == "ping") {
            // Reply with "pong"
            $message->reply("pong");
        }

        // If message is "discordstatus"
        if ($message->content == "discordstatus") {
            // Get the $browser from global scope
            global $browser;

            // Make GET request to API of discordstatus.com
            $browser->get("https://discordstatus.com/api/v2/status.json")->then(
                function (ResponseInterface $response) use ($message) { // Request success
                    // Get response body
                    $result = (string) $response->getBody();

                    var_dump($result);

                    // Parse JSON
                    $discordstatus = json_decode($result);

                    // Send reply about the discord status
                    $message->reply("Discord status: " . $discordstatus->status->description);
                },
                function (Exception $e) use ($message) { // Request failed
                    var_dump($e);

                    // Send reply about the discord status
                    $message->reply("Unable to acesss the Discord status API :(");
                }
            );
        }
    });
});

// Start the Bot (must be at the bottom)
$discord->run();
