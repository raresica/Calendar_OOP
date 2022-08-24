<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Entities\Location;
use App\Entities\Appointment;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use League\Route\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservationController extends Controller
{
    public function __construct(protected View $view,protected Router $router, protected EntityManager $db, protected Auth $auth)
    {

    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $locations = $this->db->getRepository(Location::class)->findAll();
        //dd($_SERVER['REQUEST_URI']);
        //dd($this->db->getRepository(Appointment::class)->findAll());

//        $urldate= substr($_SERVER['HTTP_REFERER'],36);
//        dd($request->getQueryParams());
        $urldate= $request->getQueryParams()["date"];
        $urldate = \DateTime::createFromFormat('Y-m-d', $urldate);

        $urldate = $urldate->format('Y-m-d');
        return $this->view->render(new Response, 'templates/reservation.twig',['locations'=> $locations, 'urldate'=> $urldate]);

    }
//----Create appointment----$request->getQueryParams()

     public function store(ServerRequestInterface $request): ResponseInterface

    {
//        dd($request->getParsedBody()['date']);
        $data = $this->validateAppointment($request);

        $this->createAppointment($data);
         //dd($request->getQueryParams());
        //return $this->view->render(new Response, 'templates/calendar.twig');
         return redirect($this->router->getNamedRoute('calendar')->getPath());
    }

    protected function createAppointment(array $data): Appointment
    {
        $appointment = new Appointment();
        $location= $this->db->getRepository(Location::class)->find($data['location']);
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
    private function validateAppointment(ServerRequestInterface $request): array
    {

        return $this->validate($request, [
            'date' => ['required'],
            'location' => ['required'],


        ]);

    }

}