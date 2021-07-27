<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Asistencia;
use App\Form\AsistenciaType;
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
        $form = $this->createForm(AsistenciaType::class, $this->asistencia);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $this->asistencia->setFecha(new \DateTime());
            $entityManager->persist($this->asistencia);
            $entityManager->flush();
            $this->addFlash('success','La asistencia del empleado fue agregada exitosamente.!');
            return $this->redirectToRoute('asistencia_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('asistencia/new.html.twig', [
            'asistencia' => $this->asistencia,
            'form' => $form,
        ]);
    }
}
