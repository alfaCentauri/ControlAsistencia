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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @var string
     */
    private $fecha;

    /**
     * @Route("/{pag}", name="reporte_index", methods={"GET","POST"}, requirements={"pag"="\d+"})
     * @param Request $request
     * @param int $pag
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(Request $request, int $pag = 1, AsistenciaRepository $asistenciaRepository): Response
    {
        //Inicializa los parámetros
        $sesion = $request->getSession();
        $this->listadoAsistencias = array();
        //Procesa la petición del formulario
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
            $sesion->set('mes', $mes);
            $sesion->set('anio', $anio);
        }
        else{  //Procesa la sesion
            $mes = $sesion->get("mes", "01");
            $anio = $sesion->get("anio", "2021");
        }
        //Formato de fecha para el reporte
        $this->fecha = $anio."-".$mes;
        //Calcula el inicio de los items para mostrar
        $inicio = ($pag-1)*10;
        $total = $asistenciaRepository->contarTodasAsistenciasMes($this->fecha);
        $this->listaAsistenciasEncontradas = $asistenciaRepository->listarAsistencias($this->fecha, $inicio, 10);
        $paginas = $this->calcularPaginasTotalesAMostrar($total);
        //
        $this->prepararListadoParaVista();
        //Renderiza la vista
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'paginaActual' => $pag,
            'total' => $paginas,
            'mes' => $mes,
            'anio' => $anio,
        ]);
    }

    /**
     * @param int $total
     * @return int Regresa la cantidad de páginas a mostrar en el paginador.
     */
    private function calcularPaginasTotalesAMostrar(int $total): int
    {
        $paginasTotales = 0;
        if($total > 10){
            $paginasTotales = ceil( $total/10 );
        }
        return $paginasTotales;
    }

    /**
     * Limpia la sesion.
     * @param SessionInterface $sesion The session
     */
    private function clearSesion(SessionInterface $sesion): void
    {
        $sesion->remove('fecha');
        $sesion->remove('paginasTotales');
        $sesion->remove('mes');
        $sesion->remove('anio');
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

    /**
     * @Route("/buscar", name="buscar_reporte", methods={"GET","POST"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function buscarUnReporte(Request $request, AsistenciaRepository $asistenciaRepository): Response
    {
        //
        $this->listaEmpleados = array();
        $this->listaAsistencias = array();
        $operation = $request->query->get('operation', 'ui');
        $entityManager = $this->getDoctrine()->getManager();
        $this->listaEmpleados = $entityManager->getRepository('App:Empleado')->findAll();
        if ($operation == 'do') {
            //Lee los datos del formulario
            $id = intval($request->request->get('selectEmpleados',0));
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            //Formato de fecha para el reporte
            $this->fecha = $anio."-".$mes;
            //Busca las asistencias del empleado
            if($id > 0) {
                $arregloAsistencia = $asistenciaRepository->buscarReporteDeUnEmpleado($this->fecha, $id);
                $this->prepararAsistenciaEmpleadoParaVista($id, $arregloAsistencia);
            }
            //Renderiza la vista con el resultado
            return $this->render('reporte/buscar.html.twig', [
                'listaEmpleados' => $this->listaEmpleados,
                'reporte' => $this->listaAsistencias[0],
                'mes' => $mes,
                'anio' => $anio,
            ]);
        }
        //Renderiza la vista
        return $this->render('reporte/buscar.html.twig', [
            'listaEmpleados' => $this->listaEmpleados,
            'reporte' => null,
            'mes' => '01',
            'anio' => '2021',
        ]);
    }

    /**
     * Recupera los datos del empleado, sus asistecias y las prepara los datos para ser mostrados en la vista.
     * @param int $idEmpleado
     * @param array $arregloAsistencia
     */
    private function prepararAsistenciaEmpleadoParaVista(int $idEmpleado, array $arregloAsistencia): void
    {
        $entityManager = $this->getDoctrine()->getManager();
        $this->empleado = $entityManager->getRepository(Empleado::class)->find($idEmpleado);
        $this->addItemToList($arregloAsistencia);
    }

}
