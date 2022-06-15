<?php

namespace MetForm_Pro\Core\Integrations\Crm\Helpscout;

class Helpscout
{
    private $attachment;
    private $attachment_thread;
    private $attachments_files = [];

    /**
     * ====================================
     *      Create ticket on Helpscout
     * ====================================
     */
    public function create_ticket($form_data, $settings, $attachment = null, $file_data = null, $file_upload_info = null)
    {

        $this->attachment       = $attachment;
        $this->file_data        = $file_data;
        $this->file_upload_info = $file_upload_info;

        if ($attachment && $file_upload_info && $file_data) {                                              // If attachment available 

            foreach ($file_data as $key => $value) {
                $this->attachment_thread['fileName'] = $value['name'];  // Set attachment file name
            }

            foreach ($file_upload_info as $key => $value) {
                $this->attachment_thread['mimeType'] = $value['type'];
                $this->attachment_thread['data']     = base64_encode(file_get_contents($value['file']));
            }


            array_push($this->attachments_files, $this->attachment_thread);
        }



        $this->post_data(
            $settings['mf_helpscout_mailbox'],
            $form_data[$settings['mf_helpscout_conversation_subject']],
            $form_data[$settings['mf_helpscout_conversation_customer_email']],
            $form_data[$settings['mf_helpscout_conversation_customer_first_name']],
            $form_data[$settings['mf_helpscout_conversation_customer_last_name']],
            $form_data[$settings['mf_helpscout_conversation_customer_message']]
        );
    }

    private function post_data($mailbox_id, $subject, $email, $first_name, $last_name, $message)
    {

        $endpoint = 'https://api.helpscout.net/v2/';
        $edge     = 'conversations ';
        $token    = get_option('mf_helpscout_access_token');
        $url      = $endpoint . $edge;

        $data = [
            'subject'  => $subject,
            'customer' => [
                'email'     => $email,
                'firstName' => $first_name,
                'lastName'  => $last_name,
            ],
            'mailboxId' => $mailbox_id,
            'type'      => 'email',
            'status'    => 'active',
            'threads'   => [
                0 => [
                    'type'       => 'customer',
                    'customer'   => [
                        'email'  => $email,
                    ],
                    'text'       => $message
                ],
            ],
        ];

        if ($this->attachment) {
            $data = [
                'subject'  => $subject,
                'customer' => [
                    'email'     => $email,
                    'firstName' => $first_name,
                    'lastName'  => $last_name,
                ],
                'mailboxId' => $mailbox_id,
                'type'      => 'email',
                'status'    => 'active',
                'threads' => [
                    0 => [
                        'type'      => 'customer',
                        'customer'  => [
                            'email' => $email,
                        ],
                        'text'        => $message,
                        'attachments' => $this->attachments_files
                    ],
                ],
            ];
        }

        try {

            $response = wp_remote_post(
                $url,
                [
                    'method'      => 'POST',
                    'data_format' => 'body',
                    'redirection' => 5,
                    'timeout'     => 60,
                    'headers'     => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type'  => 'application/json; charset=UTF-8'
                    ],
                    'body'        => json_encode($data)
                ]
            );

            $conversation_id = wp_remote_retrieve_header($response, 'Resource-ID');
        } catch (\Exception $exception) {
        }
    }
}
