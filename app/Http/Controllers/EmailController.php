<?php

namespace App\Http\Controllers;

use App\Mailers\Mailer;
use App\Mailers\ReportsMailer;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    protected $mailer = null;
    protected $repository = null;

    /**
     * EmailController constructor.
     * @param null $maile
     */
    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
        $this->mailer = new ReportsMailer();
    }

    /**
     * @return ReportsMailer|null
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param ReportsMailer|null $mailer
     * @return EmailController
     */
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }


    public function send(){

        $data['table'] = $this->repository->with(['product', 'client'])->searchAllField()->orderBy('created_at', 'asc')->all()->toArray();

        $result = $this->getMailer()
            ->setToEmail(config('mail.reports.to_email'))
            ->setToName(config('mail.reports.to_name'))
            ->sendReportEmail('emails.reports', 'You Order', $data);

        return response()->json([
            'status' => $result
        ]);

    }
}
