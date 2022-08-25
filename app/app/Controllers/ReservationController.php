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
        //dd($_SERVER['REQUEST_URI']);
        //dd($this->db->getRepository(Appointment::class)->findAll());

//        $urldate= substr($_SERVER['HTTP_REFERER'],36);
//        dd($request->getQueryParams());
        $urldate = $request->getQueryParams()["date"];
        $urldate = \DateTime::createFromFormat('Y-m-d', $urldate);

        $urldate = $urldate->format('Y-m-d');
        return $this->view->render(new Response, 'templates/reservation.twig', ['locations' => $locations, 'urldate' => $urldate]);

    }

//----Create appointment----$request->getQueryParams()

    public function store(ServerRequestInterface $request): ResponseInterface

    {
//        dd($request);
        $data = $this->validateAppointment($request);
        if ($this->validateTodayReservation($data) && $this->validatePreviousReservation($request->getParsedBody()['date'])) {
            $this->createAppointment($data);
            return redirect($this->router->getNamedRoute('calendar')->getPath());
        } else {
            $this->flash->now('error', 'You can not make an appointment on the same day');
        }

        return redirect($this->router->getNamedRoute('calendar')->getPath());


    }

//        dd($request->getParsedBody()['date']);


//        $this->createAppointment($data);
//         return redirect($this->router->getNamedRoute('calendar')->getPath());

    private function validateAppointment(ServerRequestInterface $request): array

    {

        return $this->validate($request, [
            'date' => ['required'],
            'location' => ['required'],

        ]);

    }

    private function validateTodayReservation(array $data): bool
    {
        $reservationDate = $this->db->getRepository(Appointment::class)->count([
            'reservation' => \DateTime::createFromFormat('Y-m-d', $data['date']),
            'user' => $this->auth->user()
        ]);
        if (($reservationDate != 0))
            return false;
        else {
            return true;
        }
    }

    private function validatePreviousReservation(string $date): bool
    {
        $selectedDate = date('Y-m-d', strtotime($date));
        $todayDate = date('Y-m-d');


        if ($selectedDate < $todayDate)
            return false;
        else {
            return true;
        }
    }

    protected function createAppointment(array $data): Appointment
    {

        $appointment = new Appointment();
        $location = $this->db->getRepository(Location::class)->find($data['location']);
        $reservation = \DateTime::createFromFormat('Y-m-d', $data['date']);


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