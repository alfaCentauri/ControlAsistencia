<?php

namespace App\Controller;

use App\Entity\Asistencia;
use App\Form\AsistenciaType;
use App\Repository\AsistenciaRepository;
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
     * @var array
     */
    private $listaEmpleados;

    /**
     * @Route("/", name="asistencia_index")
     *
     * @param AsistenciaRepository $asistenciaRepository
     * @return Response
     */
    public function index(AsistenciaRepository $asistenciaRepository): Response
    {
        $total = $asistenciaRepository->contarTodas();
        $listadoAsistencias = $asistenciaRepository->findAll();
        return $this->render('asistencia/index.html.twig', [
            'totalAsistencias' => $total,
            'asistencias' => $listadoAsistencias,
        ]);
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
                return $this->redirectToRoute('asistencia_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        else{
            $this->listaEmpleados = $entityManager->getRepository('App:Empleado')->findAll();
        }
        return $this->renderForm('asistencia/new.html.twig', [
            'listaEmpleados' => $this->listaEmpleados,
        ]);
    }

    /**
     * @Route("/{id}", name="asistencia_show")
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
}
