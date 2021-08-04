<?php

namespace App\Controller;

use App\Entity\Empleado;
use App\Entity\Asistencia;
use App\Repository\AsistenciaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
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
        $sesion = $request->getSession();
        $this->listadoAsistencias = array();
        $fecha = $sesion->get("fecha", "2021-01");
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $fecha = $anio."-".$mes;
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
            $sesion->set('fecha', $fecha);
        }
        $palabra = $request->request->get('buscar', null);
        $inicio = ($pag-1)*10;
        $paginas = 1;
        if(!$palabra){
            $total = $asistenciaRepository->contarTodasAsistenciasMes($fecha);
            if($total>10){
                $paginas = ceil( $total/10 );
            }
            $this->listaAsistenciasEncontradas = $asistenciaRepository->listarAsistencias($fecha, $inicio, 10);
        }
        else{
            $this->addFlash('info', 'Buscando: '.$palabra);
            $this->listaAsistenciasEncontradas = $asistenciaRepository->buscar($fecha, $palabra);
            $total = sizeof($this->listaAsistenciasEncontradas);
        }
        $this->prepararListadoParaVista();
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'paginaActual' => $pag,
            'total' => $paginas,
        ]);
    }

    /**
     * Recupera los datos del empleado, sus asistecias y las prepara los datos para ser mostrados en la vista como una
     * tabla.
     */
    private function prepararListadoParaVista(): void
    {
        $cantidadAsistencias = sizeof($this->listaAsistenciasEncontradas);
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $this->asistencia = $this->listaAsistenciasEncontradas[$i];
            $this->empleado = $this->asistencia->getEmpleado();
            $this->addingDataToList();
        }
    }

    /**
     * Agregando datos al listado
     */
    private function addingDataToList(): void
    {
        $cantidadListado = $this->calcularItemsListado();
        if($cantidadListado > 0){ //Existen items en la lista
            for ($j = 0; $j < $cantidadListado; $j++){
                $currentNode = $this->listaAsistencias[$j];
                if($currentNode['cedula'] == $this->empleado->getCedula()){
                    $timeInterval = $this->asistencia->getHoraSalida()->diff($this->asistencia->getHoraEntrada());
                    date_default_timezone_set("America/Caracas");
                    $fecha = new \DateTime("now");
                    $fechaInicial = new \DateTime("now");
                    $fecha->add( $currentNode['intervaloTiempo'] );
                    $fecha->add($timeInterval);
                    //Now get diff
                    $newTimeInterval = $fecha->diff($fechaInicial);
                    $currentNode['intervaloTiempo'] = $newTimeInterval;
                    $currentNode['horasTrabajadas'] = $newTimeInterval->format("%h horas con %i minutos");
                }
                else{
                    $this->addItemToList();
                }
            }
        }
        else{
            $this->addItemToList();
        }
    }

    /**
     * Calcular cantidad de items en el listado.
     * @return int Regresa un entero.
     */
    private function calcularItemsListado(): int
    {
        $cantidadItemsListado = 0;
        if(isset($this->listaAsistencias))
            $cantidadItemsListado = sizeof($this->listaAsistencias);

        return $cantidadItemsListado;
    }

    /**
     * Agrega un item a la lista.
     */
    private function addItemToList(): void
    {
        $nodo = array();
        $nodo['cedula'] = $this->empleado->getCedula();
        $nodo['nombre'] = $this->empleado->getNombre();
        $nodo['apellido'] = $this->empleado->getApellido();
        $timeInterval = $this->asistencia->getHoraSalida()->diff($this->asistencia->getHoraEntrada());
        $nodo['intervaloTiempo'] = $timeInterval;
        $nodo['horasTrabajadas'] = $timeInterval->format("%h horas con %i minutos");
        $this->listaAsistencias []= $nodo;
    }
}
