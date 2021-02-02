<?php

if(isset($inline))
{
    bot('answerInlineQuery', [
        'inline_query_id' => $iid,
        'results' => json_encode([[
            'type' => 'article',
            'id' => random_chars(),
            'title' => "Title",
            'input_message_content' => [
                'parse_mode' => 'HTML',
                'message_text' => Text,
            ],
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        ['text'=>'Button','callback_data'=>'Query'],
                    ],
                ],
            ],
        ]]),
    ]);
}

if(isset($message))
{
    bot('sendMessage', [
        'chat_id' => $where,
        'text' => 'Inline Mode is available!',
    ]);
}

if(isset($data))
{
    bot('editMessageText',[
        'inline_message_id'=>$mid,
        'text'=> 'Changed',
    ]);
}