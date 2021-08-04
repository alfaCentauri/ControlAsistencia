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
     * @var Asistencia[]
     */
    private $listaAsistenciasEncontradas;

    /**
     * @var Asistencia
     */
    private $asistencia;

    /**
     * @var Empleado
     */
    private $empleado;

    /**
     * @Route("/{pag}", name="reporte_actual", methods={"GET","POST"}, requirements={"pag"="\d+"})
     * @param Request $request
     * @param int $pag
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(Request $request, int $pag = 1, AsistenciaRepository $asistenciaRepository): Response
    {
        $this->listadoAsistencias = array();
        $mesActual = "2021-01";
        $palabra = $request->request->get('buscar', null);
        $inicio = ($pag-1)*10;
        $paginas = 1;
        if(!$palabra){
            $total = $asistenciaRepository->contarTodasAsistenciasMes($mesActual);
            if($total>10){
                $paginas = ceil( $total/10 );
            }
            $this->listaAsistenciasEncontradas = $asistenciaRepository->listarAsistencias($mesActual, $inicio, 10);
        }
        else{
            $this->addFlash('info', 'Buscando: '.$palabra);
            $this->listaAsistenciasEncontradas = $asistenciaRepository->buscar($mesActual, $palabra);
            $total = sizeof($this->listaAsistenciasEncontradas);
        }
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $mesActual = $anio."-".$mes;
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
        }
        $this->prepararListadoParaVista();
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'paginaActual' => $pag,
            'total' => $paginas,
        ]);
    }

    /**
     * Prepara los datos para ser mostrados en la vista como una tabla.
     * @param $asistenciasEncontradas
     */
    private function prepararListadoParaVista(): void
    {
        $cantidadAsistencias = sizeof($this->listaAsistenciasEncontradas);
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $nodo = array();
            //Recupera una asistencia
            $this->asistencia = $this->listaAsistenciasEncontradas[$i];
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
