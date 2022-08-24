<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Appointment;
use App\Entities\Location;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CalendarController
{
    public function __construct(protected View $view, protected EntityManager $db)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        if (array_key_exists('date', $request->getQueryParams())) {
            $urldate = $request->getQueryParams()["date"];
            $appointments = $this->db->getRepository(Appointment::class)->findAll();
            $appointments = array_filter($appointments, function($appointment) use($urldate) {
                return $appointment->reservation->format('Y-m-d') == $urldate;
            });
            return $this->view->render(new Response, 'templates/calendar.twig',['appointments'=>$appointments]);
        } else {
            return $this->view->render(new Response, 'templates/calendar.twig',['appointments'=>[]]);
        }
    }

}