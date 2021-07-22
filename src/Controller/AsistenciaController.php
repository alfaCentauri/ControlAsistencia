<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/asistencia")
 */
class AsistenciaController extends AbstractController
{
    /**
     * @Route("/", name="asistencia")
     */
    public function index(): Response
    {
        $total = $this->getDoctrine()->getRepository('Asistencia')
            ->ContarTodas();
        return $this->render('asistencia/index.html.twig', [
            'totalAsistencias' => $total,
        ]);
    }
}
