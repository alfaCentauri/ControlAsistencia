<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
    /**
     * @var Usuario
     */
    private $usuario;

    /**
     * @var array
     */
    private $listaUsuarios;

    /**
     * @Route("/{pag}", name="usuario_index", methods={"GET","POST"}, requirements={"pag"="\d+"})
     * @param Request $request
     * @param int $pag
     * @param UsuarioRepository $usuarioRepository
     * @return Response
     */
    public function index(Request $request, int $pag = 1, UsuarioRepository $usuarioRepository): Response
    {
        $palabra = $request->request->get('buscar', null);
        $inicio = ($pag-1)*10;
        $paginas = 1;
        if(!$palabra){
            $total = $usuarioRepository->contarTodos();
            if($total>10){
                $paginas = ceil( $total/10 );
            }
            $this->listaUsuarios = $usuarioRepository->paginarUsuarios($inicio, 10);
        }
        else{
            $this->addFlash('info', 'Buscando: '.$palabra);
            $this->listaUsuarios = $usuarioRepository->buscarUsuarios($palabra);
            $total = sizeof($this->listaUsuarios);
        }
        return $this->render('usuario/index.html.twig', [
            'usuarios' => $this->listaUsuarios,
            'paginaActual' => $pag,
            'total' => $paginas,
        ]);
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuario);
            $entityManager->flush();
            $this->addFlash('success','El usuario fue agregado exitosamente.!');
            return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/show", name="usuario_show", methods={"GET"})
     * @param Usuario $usuario
     * @return Response
     */
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @return Response
     */
    public function edit(Request $request, Usuario $usuario): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','El usuario fue actualizado exitosamente.!');
            return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="usuario_delete", methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, int $id): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $this->usuario = $entityManager->getRepository(Usuario::class)->find($id);
            if($this->usuario){
                $entityManager->remove($this->usuario);
                $entityManager->flush();
                $this->addFlash('success','El usuario fue borrado con exito. ');
            }
            else{
                $this->addFlash('danger','El usuario no pudo ser borrado. ');
            }
        return $this->redirectToRoute('usuario_index', ['pag' => 1], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/activar/{id}", name="usuario_activar")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function activar(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $this->usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if($this->usuario)
        {
            $this->usuario->setIsActive(true);
            $entityManager->flush();
            $this->addFlash('success','El usuario fue activado exitosamente.!');
        }
        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/desactivar/{id}", name="usuario_desactivar")
     */
    public function desactivar(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $this->usuario = $entityManager->getRepository(Usuario::class)->find($id);
        if($this->usuario)
        {
            $this->usuario->setIsActive(false);
            $entityManager->flush();
            $this->addFlash('warning','El usuario fue desactivado.!');
        }
        return $this->redirectToRoute('usuario_index', [], Response::HTTP_SEE_OTHER);
    }
}
