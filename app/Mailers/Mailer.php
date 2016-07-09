<?php

namespace App\Mailers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
abstract class Mailer
{

    protected $from_email = '';
    protected $from_name = '';

    protected $to_email = '';
    protected $to_name = '';

    /**
     * Mailer constructor.
     * @param string $from_email
     * @param string $from_name
     * @param string $to_email
     * @param string $to_name
     */
    public function __construct($from_email, $from_name)
    {
        $this->from_email = $from_email;
        $this->from_name = $from_name;
    }

    public function sendEmail($view, $subject, $data, $attache = [])
    {
        $data['from_email'] = $this->getFromEmail();
        $data['from_name'] = $this->getFromName();

        $data['to_email'] = $this->getToEmail();
        $data['to_name'] = $this->getToName();

        return Mail::send($view, $data, function ($m) use ($data, $attache, $subject) {

            $m->from($data['from_email'], $data['from_name']);
            $m->to($data['to_email'], $data['to_name'])->subject($subject);

            if(!empty($attache)){
                foreach ($attache as $item_attache){
                    $m->attach($item_attache['path'], ['as' => $item_attache['name'], 'mime' => $item_attache['mime']]);
                }
            }
        });
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->from_email;
    }

    /**
     * @param string $from_email
     * @return $this
     */
    public function setFromEmail($from_email)
    {
        $this->from_email = $from_email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * @param string $from_name
     * @return $this
     */
    public function setFromName($from_name)
    {
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getToEmail()
    {
        return $this->to_email;
    }

    /**
     * @param string $to_email
     * @return $this
     */
    public function setToEmail($to_email)
    {
        $this->to_email = $to_email;
        return $this;
    }

    /**
     * @return string
     */
    public function getToName()
    {
        return $this->to_name;
    }

    /**
     * @param string $to_name
     * @return $this
     */
    public function setToName($to_name)
    {
        $this->to_name = $to_name;
        return $this;
    }

}