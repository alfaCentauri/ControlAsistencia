<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/reportes")
 */
class ReporteController extends AbstractController
{
    /**
     * @var array
     */
    private $listaAsistencias;

    /**
     * @Route("/", name="reporte_actual")
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(AsistenciaRepository $asistenciaRepository): Response
    {
        $mesActual = "2021-01"; //Debug
        $totalAsistenciasMes = $asistenciaRepository->contarTodasAsistenciasMes($mesActual);
        $this->listadoAsistencias = $asistenciaRepository->listarAsistencias($mesActual);
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'totalAsistenciasMes' => $totalAsistenciasMes[1],
        ]);
    }

    /**
     * @Route("/{mesAnterior}/{anio}", name="reporte_anterior")
     * @param AsistenciaRepository $asistenciaRepository
     * @param string $mesAnterior
     * @return Response
     */
    public function anterior(AsistenciaRepository $asistenciaRepository, string $mesAnterior, string $anio): Response
    {
        $totalAsistenciasMes = $asistenciaRepository->contarTodasAsistenciasMes($mesAnterior);
        $this->listadoAsistencias = $asistenciaRepository->listarAsistencias($mesAnterior);
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'totalAsistenciasMes' => $totalAsistenciasMes,
        ]);
    }
}
