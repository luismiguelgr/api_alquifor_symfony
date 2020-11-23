<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\Usuario;
use App\Entity\Comentario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class PublicacionController
 * @package App\Controller
 *
 * @Route("/api")
 */

class PublicacionController extends AbstractController
{
    /**
     * @return JsonResponse
     * @throws \Exception
     * @Route("/publicaciones", name="getAllPublicaciones", methods={"GET"})
     */
    public function getPublicaciones(): JsonResponse
    {
        $publicaciones= $this->getDoctrine()->getRepository(Publicacion::class)->findBy(array(), array('updated_at' => 'DESC'));
        $data = [];

        foreach ($publicaciones as $publicacion) {
            $data []= [
                'id' => $publicacion->getId(),
                'titulo' => $publicacion->getTitulo(),
                'autor' => $publicacion->getUsuario()->getNombre(),
                'foto' => $publicacion->getFoto(),
                'descripcion' => $publicacion->getDescripcion(),
                'pros' => $publicacion->getPros(),
                'contras' => $publicacion->getContras(),
                'visitas' => $publicacion->getVisitas(),
                'created_at' => $publicacion->getCreatedAt()->format("Y-m-d H:m:s"),
                'updated_at' => $publicacion->getUpdatedAt()->format("Y-m-d H:m:s")
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/publicacion/{id}", name="getPublicacion", methods={"GET"})
     */
    public function getPublicacion($id): JsonResponse
    {
        $publicacion = $this->getDoctrine()->getRepository(Publicacion::class)->findOneBy(array('id' => $id));

        if(!$publicacion){
            $data=[
                'status' => 404,
                'error' => "Publicacion no encontrada"
            ];
            return $this->response($data,404);
        }else {
            $publicacion->setVisitas($publicacion->getVisitas()+1);
            $this->getDoctrine()->getRepository(Publicacion::class)->updatePublicacion($publicacion);
            $data = [
                'id' => $publicacion->getId(),
                'titulo' => $publicacion->getTitulo(),
                'autor' => $publicacion->getUsuario()->getNombre(),
                'foto' => $publicacion->getFoto(),
                'descripcion' => $publicacion->getDescripcion(),
                'pros' => $publicacion->getPros(),
                'contras' => $publicacion->getContras(),
                'visitas' => $publicacion->getVisitas(),
                'created_at' => $publicacion->getCreatedAt()->format("Y-m-d H:m:s"),
                'updated_at' => $publicacion->getUpdatedAt()->format("Y-m-d H:m:s"),
            ];

            return $this->response($data, 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/anadir-publicacion", name="addPublicacion", methods={"POST"})
     */
    public function addPublicacion(Request $request): JsonResponse
    {

        try {
            $request = $this->transformJsonBody($request);
            $files = $request->files->all();
            if(isset($files['imagen'])){
                $originalFilename = pathinfo($files['imagen']->getClientOriginalName(), PATHINFO_FILENAME);
//                    $newFilename = $originalFilename.'-'.uniqid().'.'.$files['foto']->getClientOriginalExtension();
                $directorioDescarga = $this->getParameter('files_directory');

                $files['imagen']->move($directorioDescarga, $originalFilename);
            }
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('id' => $request->get('id_usuario')));
            if (!$request){
                throw new \Exception();
            }

            $publicacion = new Publicacion();
            empty($request->get('titulo')) ? "" : $publicacion->setTitulo($request->get('titulo'));
            empty($request->get('descripcion')) ? "" : $publicacion->setDescripcion($request->get('descripcion'));
//            empty($request->get('pros')) ? "" : $publicacion->setPros($request->get('pros'));
//            empty($request->get('contras')) ? "" : $publicacion->setContras($request->get('contras'));
            empty($request->get('foto')) ? "" : $publicacion->setFoto($request->get('foto'));
            $publicacion->setCreatedAt(new \DateTime(date("Y-m-d H:i:s")));
            $publicacion->setUpdatedAt(new \DateTime(date("Y-m-d H:i:s")));
            $publicacion->setVisitas(0);
            $publicacion->setUsuario($usuario);
            $this->getDoctrine()->getRepository(Publicacion::class)->addPublicacion($publicacion);
            $data = [
                'id' => $publicacion->getId(),
                'titulo' => $publicacion->getTitulo()
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
     * @Route("/publicacion/{id}", name="updatePublicacion", methods={"PUT"})
     */
    public function updatePublicacion(Request $request, $id): JsonResponse
    {
        try {
            $publicacion = $this->getDoctrine()->getRepository(Publicacion::class)->find($id);
            if(!$publicacion){
                $data = [
                    'status' => 404,
                    'error' => "Publicacion no encontrado",
                ];
                return $this->response($data, 404);
            }
            $request = $this->transformJsonBody($request);

            if (!$request ){
                throw new \Exception();
            }
            empty($request->get('titulo')) ? true : $publicacion->setTitulo($request->get('titulo'));
            empty($request->get('descripcion')) ? true : $publicacion->setDescripcion($request->get('descripcion'));
            $publicacion->setUpdatedAt(new \DateTime(date("Y-m-d H:i:s")));
            $publicacion->setFoto("");

            $this->getDoctrine()->getRepository(Publicacion::class)->updatePublicacion($publicacion);

            $data = [
                'status' => 200,
                'ok' => "Publicacion actualizado correctamente",
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
     * @Route("/publicacion/{id}", name="deletePublicacion", methods={"DELETE"})
     */
    public function deletePublicacion($id)
    {
        $publicacion = $this->getDoctrine()->getRepository(Publicacion::class)->find($id);

        if(!$publicacion){
            $data = [
                'status' => 404,
                'error' => "Publicacion no encontrada",
            ];
            return $this->response($data, 404);
        }

        $this->getDoctrine()->getRepository(Publicacion::class)->deletePublicacion($publicacion);

        $data = [
            'status' => 200,
            'error' => "Publicacion borrado",
        ];
        return $this->response($data, 200);

    }

    /**
     * @return JsonResponse
     * @throws \Exception
     * @Route("/publicacion/{id}/comentarios", name="getComentariosPublicacion", methods={"GET"})
     */
    public function getComentariosPublicacion($id): JsonResponse
    {
//        $publicacion = $this->getDoctrine()->getRepository(Publicacion::class)->findOneBy(array('id' => $id));

        $comentarios = $this->getDoctrine()->getRepository(Comentario::class)->findBy(array('publicacion' => $id), array('id' => 'asc'));

        if(!$comentarios){
            $data=[
                'status' => 404,
                'error' => "Comentarios no encontrados"
            ];
            return $this->response($data,404);
        }else {
            $data = [];
            foreach ($comentarios as $comentario) {
                $data []= [
                    'id' => $comentario->getId(),
                    'texto' => $comentario->getTexto(),
//                    'usuario' => $comentario->getUsuario()->getUsuario(),
                    'fecha_creacion' => $comentario->getFechaCreacion()->format("Y-m-d H:m:s")
                ];
            }

            return new JsonResponse($data, Response::HTTP_OK);
        }
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
