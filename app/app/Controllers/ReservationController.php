<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Location;
use App\Views\View;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReservationController
{
    public function __construct(protected View $view, protected EntityManager $db)
    {

    }




    public function index(ServerRequestInterface $request): ResponseInterface
    {
//        dd($request->getQueryParams());
        $locations = $this->db->getRepository(Location::class)->findAll();

//        dd($locations);

        return $this->view->render(new Response, 'templates/reservation.twig',['locations'=> $locations]);
    }

}