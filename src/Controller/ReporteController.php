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
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $fecha = $anio."-".$mes;
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
            $sesion->set('fecha', $fecha);
        }
        else{
            $fecha = $sesion->get("fecha", "2021-01");
        }
        $palabra = $request->request->get('buscar', null);
        $inicio = ($pag-1)*10;
        $paginas = $sesion->get("paginasTotales", 1);
        if($pag == $paginas){ //Si es la ultima página limpiar la sesion
            $sesion->remove('fecha');
            $sesion->remove('paginasTotales');
        }
        if(!$palabra){
            $total = $asistenciaRepository->contarTodasAsistenciasMes($fecha);
            if($total>10){
                $paginas = ceil( $total/10 );
                $sesion->set('paginasTotales', $paginas);
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
        $entityManager = $this->getDoctrine()->getManager();
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $nodoActual = $this->listaAsistenciasEncontradas[$i];
            $this->empleado = $entityManager->getRepository(Empleado::class)->find($nodoActual['empleado_id']);
            $this->addItemToList($nodoActual);
        }
    }

    /**
     * Agrega un item a la lista.
     * @param array $currentNode
     */
    private function addItemToList(array $currentNode): void
    {
        $nodo = array();
        $nodo['cedula'] = $this->empleado->getCedula();
        $nodo['nombre'] = $this->empleado->getNombre();
        $nodo['apellido'] = $this->empleado->getApellido();
        $cantidadLetras = strlen($currentNode['horasTrabajadas']);
        $nodo['horasTrabajadas'] = $this->getHoursToWork($currentNode['horasTrabajadas'], $cantidadLetras);
        $this->listaAsistencias []= $nodo;
    }

    /**
     * Genera el string con las horas de trabajo.
     * @param $hoursToWork
     * @param $cantidadLetras
     * @return string Cadena de caracteres con la cantidad de horas y minutos trabajadas por empleado.
     */
    private function getHoursToWork($hoursToWork, $cantidadLetras): string
    {
        $horasTrabajadas = "";
        if($cantidadLetras >= 5){
            $digitosHora = $cantidadLetras - 4;
            $horasTrabajadas = substr($hoursToWork, 0, $digitosHora) . " horas con "
                . substr($hoursToWork, -4, 2)." minutos";
        }
        elseif($cantidadLetras >= 3 && $cantidadLetras < 5 ) {
            $digitosMinutos = $cantidadLetras - 2;
            $horasTrabajadas = "0 horas con ".substr($hoursToWork,-4,$digitosMinutos)." minutos";
        }
        else{
            $horasTrabajadas = "0 horas con 0 minutos";
        }
        return $horasTrabajadas;
    }
}
