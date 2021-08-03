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
        $mesActual = "2021-01"; //Debug
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $mesActual = $anio."-".$mes;
//            $sesion = $request->getSession(); //Debug
//            $sesion->set('fecha', $mesActual);
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
        }
        //Busca los datos en la base de datos
        $totalAsistenciasMes = $asistenciaRepository->contarTodasAsistenciasMes($mesActual);
        $asistenciasEncontradas = $asistenciaRepository->listarAsistencias($mesActual);
        //Prepara los datos para ser mostrados
        $cantidadAsistencias = sizeof($asistenciasEncontradas);
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $nodo = array();
            //Recupera una asistencia
            $this->asistencia = $asistenciasEncontradas[$i];
            if ($this->asistencia != null){ //Valida el objeto
                $this->empleado = $this->asistencia->getEmpleado();
                $nodo['cedula'] = $this->empleado->getCedula();
                $nodo['nombre'] = $this->empleado->getNombre();
                $nodo['apellido'] = $this->empleado->getApellido();
                $nodo['horasTrabajadas'] = 0;
                $this->listadoAsistencias [] = $nodo;
            }
        }
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'totalAsistenciasMes' => $totalAsistenciasMes,
            'fecha' => $mesActual,
        ]);
    }

    /**
     * @Route("/{mesAnterior}/{anio}", name="reporte_generate")
     * @param Request $request
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function anterior(Request $request, AsistenciaRepository $asistenciaRepository): Response
    {
        $mesActual = "2021-01"; //Debug
        if ($request->isMethod('POST')){
            $mes = $request->request->get('mes', "01");
            $anio = $request->request->get('anio', "2021");
            $mesActual = $anio."-".$mes;
            $sesion = $request->getSession();
            $this->addFlash('success','Usted ha seleccionado el reporte de fecha: '.$mes."-".$anio);
        }
        $totalAsistenciasMes = $asistenciaRepository->contarTodasAsistenciasMes($mesActual);
        $asistenciasEncontradas = $asistenciaRepository->listarAsistencias($mesActual);
        $cantidadAsistencias = sizeof($asistenciasEncontradas);
        for($i = 0; $i < $cantidadAsistencias; $i++){
            $nodo = array();
            $this->asistencia = $asistenciasEncontradas[$i];
            $this->empleado = $this->asistencia.getEmpleado();
            $nodo['cedula'] = $this->empleado.getCedula();
            $nodo['nombre'] = $this->empleado.getNombre();
            $nodo['apellido'] = $this->empleado.getApellido();
            $nodo['horasTrabajadas'] = 0;
            $this->listadoAsistencias [] = $nodo;
        }
        return $this->render('reporte/index.html.twig', [
            'asistencias' => $this->listaAsistencias,
            'totalAsistenciasMes' => $totalAsistenciasMes[1],
            'fecha' => $mesActual,
        ]);
    }
}
