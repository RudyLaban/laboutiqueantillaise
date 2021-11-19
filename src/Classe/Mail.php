<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $apiKey = '625bd77fa4ea3b062db074e87319787c';
    private $apiSecret = 'b6256e59ce75c65efc8fe5a8cc22ef2c';

    public function send($toEmail, $toName, $subject, $content)
    {

        $mj = new Client($this->apiKey, $this->apiSecret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "mejaro9808@nefacility.com",
                        'Name' => "Depuis LBA"
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toName,
                        ]
                    ],
                    'TemplateID' => 3359002,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ],
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && $response->getData();
    }
}