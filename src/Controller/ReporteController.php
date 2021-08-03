<?php

namespace App\Controller;

use App\Entity\Empleado;
use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Symfony\Component\HttpFoundation\Request;
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
     * @var Asistencia
     */
    private $asistencia;

    /**
     * @var Empleado
     */
    private $empleado;

    /**
     * @Route("/", name="reporte_actual")
     * @param Request $request
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(Request $request, AsistenciaRepository $asistenciaRepository): Response
    {
        $this->listadoAsistencias = array();
        $mesActual = "2021-01"; //Debug
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $mesActual = $anio."-".$mes;
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
        }
        //Busca los datos en la base de datos
        $totalAsistenciasMes = $asistenciaRepository->contarTodasAsistenciasMes($mesActual);
        $asistenciasEncontradas = $asistenciaRepository->listarAsistencias($mesActual);
        $this->prepararListadoParaVista($asistenciasEncontradas);
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'totalAsistenciasMes' => $totalAsistenciasMes,
            'fecha' => $mesActual,
        ]);
    }

    /**
     * Prepara los datos para ser mostrados en la vista como una tabla.
     * @param $asistenciasEncontradas
     */
    private function prepararListadoParaVista($asistenciasEncontradas): void
    {
        $cantidadAsistencias = sizeof($asistenciasEncontradas);
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $nodo = array();
            //Recupera una asistencia
            $this->asistencia = $asistenciasEncontradas[$i];
            $this->empleado = $this->asistencia->getEmpleado();
            $nodo['cedula'] = $this->empleado->getCedula();
            $nodo['nombre'] = $this->empleado->getNombre();
            $nodo['apellido'] = $this->empleado->getApellido();
            $intervaloTiempo = $this->asistencia->getHoraSalida()->diff($this->asistencia->getHoraEntrada());
            $nodo['horasTrabajadas'] = $intervaloTiempo->format("%h horas con %i minutos");
            $this->listaAsistencias []= $nodo;
        }
    }
}
