<?php

namespace App\Controllers;

use App\Repositories\ApiRepository;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\FillerRepository;

final class HomeController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function dispatch(Request $request, Response $response, $args)
    {

        try {
            //At first We need to
            //FillDb with dummy data
            for ($i = 0; $i < 25; $i++) {
                FillerRepository::fillEm();
            }
        } catch (\Exception $exception) {
            $r = '<h2>Aww snap!</h2>';
            $r .= 'You have to configure connection to DB on /smod/config/setting.php !<br />';
            $r .= 'Then execute sql dump, then try again';
            return $response->write($r);
        }

        echo '<pre><br /><br />';
        echo "<h2>1. We'll get list of all conversations </h2><br />";

        $conversations = ApiRepository::getConversationList();

        foreach ($conversations as $conversation) {
            echo $conversation->id . '<br />';
        }


        echo '<br /><br />';
        echo '<h2>2. Get all members</h2> <br />';

        $conversationMembers = ApiRepository::getConversationMembers($conversations[0]->id);


        foreach ($conversationMembers as $conversationMember) {
            echo $conversationMember->phone . '<br />';
        }


        echo '<br /><br />';
        echo '<h2>3. Get list of all messages for a conversation </h2><br />';

        $conversationMessages = ApiRepository::getConversationMessages($conversations[0]->id);

        foreach ($conversationMessages as $conversationMessage) {
            echo '<br /><strong> Message object:</strong><br />';
            echo 'text : <h3 style="padding:0; margin:0;">' . $conversationMessage->text . '</h3><br />';
            echo 'message_id : ' . $conversationMessage->message_id . '<br />';
            echo 'timestamp : ' . $conversationMessage->timestamp . '<br />';
            echo 'conversation_id : ' . $conversationMessage->conversation_id . '<br />';
            echo 'service_name : ' . $conversationMessage->service_name . '<br />';

            if ($conversationMessage->is_attachment) {
                $attachments = ApiRepository::getMessageAttachments($conversationMessage->message_id);

                if ($attachments)
                    foreach ($attachments as $attachment) {
                        echo '<i>Message attachment:</i><br />';
                        echo ' attachment_id: ' . $attachment->attachment_id . '<br />';
                        echo ' type: ' . $attachment->type . '<br />';
                        echo ' path: ' . $attachment->path . '<br />';
                        echo ' filename: ' . $attachment->filename . '<br />';
                        echo ' size: ' . $attachment->size . '<br />';
                    }
            }
        }


        echo '<br /><br />';
        echo '<h2>CleanUp DB.</h2> <br />';
        //claanUp database
        FillerRepository::truncate();

        return $response->write('It works!');
    }
}