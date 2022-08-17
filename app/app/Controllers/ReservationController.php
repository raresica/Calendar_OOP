<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Entities\Location;
use App\Entities\User;
use App\Entities\Appointment;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use JetBrains\PhpStorm\NoReturn;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservationController extends Controller
{
    public function __construct(protected View $view, protected EntityManager $db, protected Auth $auth)
    {

    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $locations = $this->db->getRepository(Location::class)->findAll();

        return $this->view->render(new Response, 'templates/reservation.twig',['locations'=> $locations]);
    }
//----Create appointment----




     public function store(ServerRequestInterface $request): ResponseInterface

    {
        $data = $this->validateAppointment($request);

        $this->createAppointment($data);

        //$this->auth->attempt($data['email'], $data['password']);

        return $this->view->render(new Response, 'templates/calendar.twig');
    }


    protected function createAppointment(array $data): Appointment
    {

        $appointment = new Appointment();
        $locations = $this->db->getRepository(Location::class)->findAll();
        $x = \DateTime::createFromFormat('Y-m-d', $data['date']);

        $appointment->fill([
            'reservation' => $x,
            'location' => $locations[$data['location']-1],
            'user' => $this->auth->user(),
        ]);

        $this->db->persist($appointment);
        $this->db->flush();
        return $appointment;
    }
    private function validateAppointment(ServerRequestInterface $request): array
    {

        return $this->validate($request, [
            'date' => ['required'],
            'location' => ['required'],
            //'user' => ['required']

        ]);

    }

}