<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Entities\Location;
use App\Entities\Appointment;
use App\Session\Flash;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservationController extends Controller
{
    public function __construct(protected View $view, protected Router $router, protected Flash $flash, protected EntityManager $db, protected Auth $auth)
    {

    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $locations = $this->db->getRepository(Location::class)->findAll();
        $urldate = $request->getQueryParams()["date"];
        $urldate = \DateTime::createFromFormat('Y-m-d', $urldate);

        $urldate = $urldate->format('Y-m-d');
        return $this->view->render(new Response, 'templates/reservation.twig', ['locations' => $locations, 'urldate' => $urldate]);

    }

    public function store(ServerRequestInterface $request): ResponseInterface

    {
        $data = $this->validateAppointment($request);
        if ($this->validateTodayReservation($data) && $this->validatePreviousReservation($request->getParsedBody()['date'])) {
            $this->createAppointment($data);
            return redirect($this->router->getNamedRoute('calendar')->getPath());
        } else {
            $this->flash->now('error', 'You can not make an appointment on the same day or in a previous day');
        }
        return redirect($this->router->getNamedRoute('calendar')->getPath());
    }

    private function validateAppointment(ServerRequestInterface $request): array
    {
        return $this->validate($request, [
            'date' => ['required'],
            'location' => ['required'],

        ]);
    }

    private function validateTodayReservation(array $data): bool
    {
        return $this->db->getRepository(Appointment::class)->count([
                'reservation' => \DateTime::createFromFormat('Y-m-d', $data['date']),
                'user' => $this->auth->user()
            ]) == 0;
    }

    private function validatePreviousReservation(string $date): bool
    {
        return date('Y-m-d', strtotime($date)) < date('Y-m-d');
    }

    protected function createAppointment(array $data): Appointment
    {

        $appointment = new Appointment();
        $location = $this->db->getRepository(Location::class)->find($data['location']);
        $reservation = \DateTime::createFromFormat('Y-m-d', $data['date']);

        //foreach (['reservation'=>$reservation,'location'=>$location,'user'=>$this->auth->user()] as $key => $value) {
          //  $appointment->{$key} = $value;
        //}

        $appointment->fill([
            'reservation' => $reservation,
            'location' => $location,
            'user' => $this->auth->user(),
        ]);

        $this->db->persist($appointment);
        $this->db->flush();
        return $appointment;
    }
}