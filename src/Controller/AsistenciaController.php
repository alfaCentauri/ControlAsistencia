<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Empleado;
use App\Entity\Asistencia;
use App\Form\AsistenciaType;
use App\Repository\EmpleadoRepository;
use App\Repository\AsistenciaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
}
