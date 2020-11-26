<?php

namespace App\Controller;

use App\Entity\Comentario;
use App\Entity\Publicacion;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ComentarioController
 * @package App\Controller
 *
 * @Route("/api")
 */
class ComentarioController extends AbstractController
{
    /**
     * @return JsonResponse
     * @throws \Exception
     * @Route("/comentarios", name="getAllComentarios", methods={"GET"})
     */
    public function getComentarios(): JsonResponse
    {
        $comentarios= $this->getDoctrine()->getRepository(Comentario::class)->findAll();
        $data = [];

        foreach ($comentarios as $comentario) {
            $data []= [
                'id' => $comentario->getId(),
                'texto' => $comentario->getTexto(),
                'titulo_publicacion' => $comentario->getPublicacion()->getTitulo()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/comentarios/{id}", name="getAllComentariosUsuario", methods={"POST"})
     */
    public function getComentariosUsuario($id): JsonResponse
    {

        $comentarios = $this->getDoctrine()->getRepository(Comentario::class)->findBy(array('usuario' => $id), array('fecha_creacion' => 'DESC'));
        $data = [];

        foreach ($comentarios as $comentario) {
            $data []= [
                'id' => $comentario->getId(),
                'texto' => $comentario->getTexto(),
                'fecha_creacion' => $comentario->getFechaCreacion()->format("Y-m-d H:m:s"),
                'usuario' => $comentario->getUsuario()->getNombre(),
                'id_publicacion' => $comentario->getPublicacion()->getId()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/comentario/{id}", name="getComentario", methods={"GET"})
     */
    public function getComentario($id): JsonResponse
    {
        $comentario = $this->getDoctrine()->getRepository(Comentario::class)->findOneBy(array('id' => $id));

        if(!$comentario){
            $data=[
                'status' => 404,
                'error' => "Publicacion no encontrada"
            ];
            return $this->response($data,404);
        }else {
            $data = [
                'id' => $comentario->getId(),
                'email' => $comentario->getTitulo(),
                'autor' => $comentario->getUsuario()->getNombre()
            ];

            return $this->response($data, 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/anadir-comentario", name="addComentario", methods={"POST"})
     */
    public function addComentario(Request $request): JsonResponse
    {

        try {
            $request = $this->transformJsonBody($request);
            $publicacion = $this->getDoctrine()->getRepository(Publicacion::class)->findOneBy(array('id' => $request->get('publicacion')));
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('usuario' => $request->get('usuario')));

            if (!$request){
                throw new \Exception();
            }

            if (!$usuario){
                throw new \Exception("El usuario no existe");
            }

            if (!$publicacion){
                throw new \Exception("La publicación no existe");
            }

            $comentario = new Comentario();
            empty($request->get('texto')) ? true : $comentario->setTexto($request->get('texto'));
//            $comentario->setCreatedAt(new \DateTime(date("Y-m-d H:i:s")));
//            $comentario->setUpdatedAt(new \DateTime(date("Y-m-d H:i:s")));
            $comentario->setPublicacion($publicacion);
            $comentario->setUsuario($usuario);

            $this->getDoctrine()->getRepository(Comentario::class)->addComentario($comentario);
            $data = [
                'id' => $comentario->getId(),
                'texto' => $comentario->getTexto(),
                'titulo_publicacion' => $comentario->getPublicacion()->getTitulo(),
                'usuario' => $comentario->getUsuario()->getNombre()
            ];

            return $this->response($data, 200);

        }catch(\Exception $e){
            $data = [
                'status' => 422,
                'error' => "Datos invalidos",
                'mensaje' => $e->getMessage()
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/comentario/{id}", name="updateComentario", methods={"PUT"})
     */
    public function updatePublicacion(Request $request, $id): JsonResponse
    {
        try {
            $comentario = $this->getDoctrine()->getRepository(Comentario::class)->find($id);
            if(!$comentario){
                $data = [
                    'status' => 404,
                    'error' => "Comentario no encontrado",
                ];
                return $this->response($data, 404);
            }
            $request = $this->transformJsonBody($request);

            if (!$request ){
                throw new \Exception();
            }
            empty($request->get('texto')) ? true : $comentario->setTexto($request->get('texto'));
//            $publicacion->setUpdatedAt(new \DateTime(date("Y-m-d H:i:s")));

            $this->getDoctrine()->getRepository(Comentario::class)->updateComentario($comentario);

            $data = [
                'status' => 200,
                'ok' => "Comentario actualizado correctamente",
            ];
            return $this->response($data, 200);


        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'error' => "Datos no validos"
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/comentario/{id}", name="deleteComentario", methods={"DELETE"})
     */
    public function deletePublicacion($id)
    {
        $comentario = $this->getDoctrine()->getRepository(Comentario::class)->find($id);

        if(!$comentario){
            $data = [
                'status' => 404,
                'error' => "Comentario no encontrado",
            ];
            return $this->response($data, 404);
        }

        $this->getDoctrine()->getRepository(Comentario::class)->deleteComentario($comentario);

        $data = [
            'status' => 200,
            'error' => "Comentario borrado",
        ];
        return $this->response($data, 200);

    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
