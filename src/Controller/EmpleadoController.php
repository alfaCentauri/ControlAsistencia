<?php

namespace App\Controller;

use App\Entity\Empleado;
use App\Form\EmpleadoType;
use App\Repository\EmpleadoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/empleado")
 */
class EmpleadoController extends AbstractController
{
    /**
     * @var array
     */
    private $listaEmpleados;

    /**
     * @Route("/{pag}", name="empleado_index", methods={"GET","POST"}, requirements={"pag"="\d+"})
     * @param Request $request
     * @param EmpleadoRepository $empleadoRepository
     * @return Response
     */
    public function index(Request $request, int $pag = 1, EmpleadoRepository $empleadoRepository): Response
    {
        $this->clearSesion($request->getSession());
        $this->listaEmpleados = null;
        $palabra = $request->request->get('buscar', null);
        $inicio = ($pag-1)*10;
        $paginas = 1;
        if(!$palabra){
            $total = $empleadoRepository->contarTodos();
            $this->listaEmpleados = $empleadoRepository->paginarEmpleados($inicio, 10);
        }
        else {
            $this->addFlash('info', 'Buscando: '.$palabra);
            $this->listaEmpleados = $empleadoRepository->buscar($palabra, $inicio, 10);
            $total = $empleadoRepository->contarEmpleadosBuscados($palabra);
        }
        $paginas = $this->calcularPaginasTotalesAMostrar($total);
        return $this->render('empleado/index.html.twig', [
            'empleados' => $this->listaEmpleados,
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
     * @Route("/new", name="empleado_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $empleado = new Empleado();
        $form = $this->createForm(EmpleadoType::class, $empleado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($empleado);
            $entityManager->flush();
            $this->addFlash('success','El empleado fue agregado exitosamente.!');
            return $this->redirectToRoute('empleado_index', ['pag' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('empleado/new.html.twig', [
            'empleado' => $empleado,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/show", name="empleado_show", methods={"GET"})
     */
    public function show(Empleado $empleado): Response
    {
        return $this->render('empleado/show.html.twig', [
            'empleado' => $empleado,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="empleado_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Empleado $empleado): Response
    {
        $form = $this->createForm(EmpleadoType::class, $empleado);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','El empleado fue editado exitosamente.!');
            return $this->redirectToRoute('empleado_index', ['pag' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('empleado/edit.html.twig', [
            'empleado' => $empleado,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="empleado_delete", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $empleado = $entityManager->getRepository('App:Empleado')->find($id);
        if($empleado) {
            $entityManager->remove($empleado);
            $entityManager->flush();
            $this->addFlash('success','El empleado fue borrado con exito. ');
        }
        else{
            $this->addFlash('danger','El empleado no pudo ser borrado. ');
        }
        return $this->redirectToRoute('empleado_index', ['pag' => 1], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/buscar", name="buscar_empleado", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function buscar(Request $request): Response
    {
        $empleado = null;
        $entityManager = $this->getDoctrine()->getManager();
        $cedula = $request->request->get('buscar', 0);
        if($cedula > 0) {
            $empleado = $entityManager->getRepository('App:Empleado')->findOneByCedula($cedula);
            return $this->renderForm('asistencia/formBuscar.html.twig', array( 'empleado' => $empleado ));
        }
        else{
            $this->addFlash('warning','La información del empleado no pudo ser encontrada.');
        }
        return $this->renderForm('asistencia/formBuscar.html.twig', array( 'empleado' => $empleado ));
    }
}
