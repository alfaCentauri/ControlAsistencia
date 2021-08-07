<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Form\AsistenciaType;
use App\Form\AsistenciaSalidaType;
use App\Repository\AsistenciaRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/asistencia")
 */
class AsistenciaController extends AbstractController
{
    /**
     * @var Asistencia
     */
    private $asistencia;

    /**
     * @var Asistencia[]
     */
    private $listadoAsistencias;

    /**
     * @var array
     */
    private $listaEmpleados;

    /**
     * @Route("/{pag}", name="asistencia_index", requirements={"pag"="\d+"})
     *
     * @param Request $request
     * @param int $pag Indica el numero de página
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(Request $request, int $pag = 1, AsistenciaRepository $asistenciaRepository): Response
    {
        $this->clearSesion($request->getSession());
        $this->listaEmpleados = null;
        $inicio = ($pag-1)*10;
        $paginas = 1;
        $total = $asistenciaRepository->contarTodas();
        $this->listadoAsistencias = $asistenciaRepository->paginarAsistencias($inicio, 10);
        $paginas = $this->calcularPaginasTotalesAMostrar($total);

        return $this->render('asistencia/index.html.twig', [
            'asistencias' => $this->listadoAsistencias,
            'paginaActual' => $pag,
            'total' => $paginas,
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
     * @Route("/new", name="asistencia_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->asistencia = new Asistencia();
        $this->listaEmpleados = array();
        $operation = $request->query->get('operation', 'ui');
        $entityManager = $this->getDoctrine()->getManager();
        if ($operation == 'do') {
            $id = intval($request->request->get('selectEmpleados',0));
            $horaEntrada = $request->request->get('hora', 0);
            $minutosEntrada = $request->request->get('minutos', 0);
            if($id > 0){
                $empleado = $entityManager->getRepository('App:Empleado')->find($id);
                if ($empleado){
                    $this->asistencia->setUserId(1);//debug
                    $this->asistencia->setEmpleado($empleado);
                    date_default_timezone_set("America/Caracas");
                    $this->asistencia->setFecha(new \DateTime());
                    $this->asistencia->setHoraEntrada(new \DateTime($horaEntrada.':'.$minutosEntrada));
                    $this->asistencia->setHoraSalida(new \DateTime($horaEntrada.':'.$minutosEntrada));
                    $entityManager->persist($this->asistencia);
                    $entityManager->flush();
                    $this->addFlash('success','La asistencia del empleado fue agregada exitosamente.!');
                }
                else{
                    $this->addFlash('warning','La asistencia del empleado no pudo ser agregada. ');
                }
                return $this->redirectToRoute('asistencia_index', ['pag' => 1]);
            }
        }
        else{
            $this->listaEmpleados = $entityManager->getRepository('App:Empleado')->listarTodos();
        }
        return $this->renderForm('asistencia/new.html.twig', [
            'listaEmpleados' => $this->listaEmpleados,
        ]);
    }

    /**
     * @Route("/{id}/show", name="asistencia_show")
     *
     * @param Asistencia $asistencia
     * @return Response
     */
    public function show(Asistencia $asistencia): Response
    {
        return $this->render('asistencia/show.html.twig', [
            'asistencia' => $asistencia,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="asistencia_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Asistencia $asistencia
     * @return Response
     */
    public function edit(Request $request, Asistencia $asistencia): Response
    {
        $form = $this->createForm(AsistenciaType::class, $asistencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','La asistencia del empleado fue actualizada exitosamente.!');
            return $this->redirectToRoute('asistencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('asistencia/edit.html.twig', [
            'asistencia' => $asistencia,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="asistencia_delete", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $this->asistencia = $entityManager->getRepository(Asistencia::class)->find($id);
        if($this->asistencia){
            $entityManager->remove($this->asistencia);
            $entityManager->flush();
            $this->addFlash('success','La asistencia fue borrada con exito. ');
        }
        else{
            $this->addFlash('danger','La asistencia no pudo ser borrada. ');
        }
        return $this->redirectToRoute('asistencia_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/salida", name="asistencia_out", methods={"GET","POST"})
     * @param Request $request
     * @param Asistencia $asistencia
     * @return Response
     */
    public function out(Request $request, Asistencia $asistencia): Response
    {
        $form = $this->createForm(AsistenciaSalidaType::class, $asistencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','La hora de salida de la asistencia del empleado fue agregada exitosamente.!');
            return $this->redirectToRoute('asistencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('asistencia/salida.html.twig', [
            'asistencia' => $asistencia,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{pag}/busqueda", name="asistencia_find", requirements={"pag"="\d+"})
     *
     * @param Request $request
     * @param int $pag Indica el numero de página
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function buscarFecha(Request $request, int $pag = 1, AsistenciaRepository $asistenciaRepository): Response
    {
        $this->listaEmpleados = null;
        $inicio = ($pag-1)*10;
        $paginas = 1;
        $total = 0;
        $mesBuscado = $request->request->get('buscar', null);
        if($mesBuscado){
            $this->addFlash('info', 'Buscando: '.$mesBuscado);
            $this->listadoAsistencias = $asistenciaRepository->buscar($mesBuscado, $inicio, 10);
            $total = $asistenciaRepository->contarTodasAsistenciasMes($mesBuscado);
        }
        $paginas = $this->calcularPaginasTotalesAMostrar($total);
        return $this->render('asistencia/index.html.twig', [
            'asistencias' => $this->listadoAsistencias,
            'paginaActual' => $pag,
            'total' => $paginas,
        ]);
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
}
