<?php

namespace App\Mailers;

use Illuminate\Support\Facades\Input;

class ReportsMailer extends Mailer
{

    /**
     * ReportsMailer constructor.
     */
    public function __construct()
    {
        parent::__construct(
            config('mail.reports.from_email'),
            config('mail.reports.from_name')
        );
    }

    public function sendReportEmail($view, $subject, $data)
    {
        $image_content = base64_decode(str_replace("data:image/png;base64,","", Input::get('image')));
        $tempfile = tmpfile();
        fwrite($tempfile, $image_content);
        $metaDatas = stream_get_meta_data($tempfile);

        $file[] = [
            'mime' => 'image/png',
            'name' => 'Graphic.png',
            'path' => $metaDatas['uri']
        ];

        return $this->sendEmail($view, $subject, $data, $file);
    }


}