<?php

namespace App\Controller;

use App\Entity\Usuario;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsuarioController
 * @package App\Controller
 *
 */

class UsuarioController extends AbstractController
{
    /**
     * @return JsonResponse
     * @throws \Exception
     * @Route("/api/usuarios", name="getAllUsuarios", methods={"GET"})
     */
    public function getUsuarios(): JsonResponse
    {
        $usuarios= $this->getDoctrine()->getRepository(Usuario::class)->findAll();
        $data = [];

        foreach ($usuarios as $usuario) {
            $data []= [
                'id' => $usuario->getId(),
                'email' => $usuario->getEmail(),
                'nombre' => $usuario->getNombre()
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);

    }


    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @Route("/api/usuario/{id}", name="getUsuario", methods={"GET"})
     */
    public function getUsuario($id): JsonResponse
    {
        $usuario = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('id' => $id));
        if(!$usuario){
            $data=[
                'status' => 404,
                'error' => "Usuario no encontrado"
            ];
            return $this->response($data,404);
        }else {
            $data = [
                'id' => $usuario->getId(),
                'email' => $usuario->getEmail(),
                'nombre' => $usuario->getNombre(),
                'primer_apellido' => $usuario->getPrimerApellido()
            ];

            return $this->response($data, 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/anadir-usuario", name="addUsuario", methods={"POST"})
     */
    public function addUsuario(Request $request): JsonResponse
    {

        try {

            $request = $this->transformJsonBody($request);
            $usuarioExistente = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('usuario' => $request->get('usuario')));

            if($usuarioExistente){
                $data = [
                    'status' => 200,
                    'error' => "El usuario ya existe",
                ];
                return $this->response($data, 200);
            }

            $usuarioEmail = $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('email' => $request->get('email')));

            if($usuarioEmail){
                $data = [
                    'status' => 200,
                    'error' => "El usuario ya existe",
                ];
                return $this->response($data, 200);
            }

            if (!$request){
                throw new \Exception();
            }

            $usuario = new Usuario();
            empty($request->get('usuario')) ? "" : $usuario->setUsuario($request->get('usuario'));
            empty($request->get('password')) ? "" : $usuario->setPassword($request->get('password'));
            empty($request->get('email')) ? "" : $usuario->setEmail($request->get('email'));
            empty($request->get('nombre')) ? "" : $usuario->setNombre($request->get('nombre'));
            empty($request->get('primer_apellido')) ? "" : $usuario->setPrimerApellido($request->get('primer_apellido'));
            empty($request->get('segundo_apellido')) ? "" : $usuario->setSegundoApellido($request->get('segundo_apellido'));
            empty($request->get('fecha_nacimiento')) ? "" : $usuario->setFechaNacimiento(new \DateTime($request->get('fecha_nacimiento')));
            empty($request->get('direccion')) ? "" : $usuario->setDireccion($request->get('direccion'));
            empty($request->get('ciudad')) ? "" : $usuario->setCiudad($request->get('ciudad'));
            empty($request->get('provincia')) ? "" : $usuario->setProvincia($request->get('provincia'));
            empty($request->get('codigo_postal')) ? 0 : $usuario->setCodigoPostal($request->get('codigo_postal'));
            empty($request->get('telefono')) ? 0 : $usuario->setTelefono($request->get('telefono'));
//            empty($request->get('imagen')) ? true : $usuario->setImagen($request->get('imagen'));
            $usuario->setImagen("");
            $usuario->setTipoPerfil(1);

            $this->getDoctrine()->getRepository(Usuario::class)->addUsuario($usuario);
            $data = [
                'id' => $usuario->getId(),
                'email' => $usuario->getEmail(),
                'nombre' => $usuario->getNombre(),
                'primer_apellido' => $usuario->getPrimerApellido()
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
     * @Route("/api/usuario/{id}", name="updateUsuario", methods={"PUT"})
     */
    public function updateUsuario(Request $request, $id): JsonResponse
    {
        try {
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($id);
            if(!$usuario){
                $data = [
                    'status' => 404,
                    'error' => "Usuario no encontrado",
                ];
                return $this->response($data, 404);
            }
            $request = $this->transformJsonBody($request);

            if (!$request ){
                throw new \Exception();
            }
            empty($request->get('usuario')) ? true : $usuario->setUsuario($request->get('usuario'));
            empty($request->get('password')) ? true : $usuario->setPassword($request->get('password'));
            empty($request->get('email')) ? true : $usuario->setEmail($request->get('email'));
            empty($request->get('nombre')) ? true : $usuario->setNombre($request->get('nombre'));
            empty($request->get('primer_apellido')) ? true : $usuario->setPrimerApellido($request->get('primer_apellido'));
            empty($request->get('segundo_apellido')) ? true : $usuario->setSegundoApellido($request->get('segundo_apellido'));
            empty($request->get('fecha_nacimiento')) ? true : $usuario->setFechaNacimiento($request->get('fecha_nacimiento'));
            empty($request->get('direccion')) ? true : $usuario->setDireccion($request->get('direccion'));
            empty($request->get('ciudad')) ? true : $usuario->setCiudad($request->get('ciudad'));
            empty($request->get('provincia')) ? true : $usuario->setProvincia($request->get('provincia'));
            empty($request->get('codigo_postal')) ? true : $usuario->setCodigoPostal($request->get('codigo_postal'));
            empty($request->get('telefono')) ? true : $usuario->setTelefono($request->get('telefono'));
//            empty($request->get('imagen')) ? true : $usuario->setImagen($request->get('imagen'));
            $usuario->setImagen("");
            $this->getDoctrine()->getRepository(Usuario::class)->updateUsuario($usuario);

            $data = [
                'status' => 200,
                'ok' => "Usuario actualizado correctamente",
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
     * @Route("/api/usuario/{id}", name="deleteUsuario", methods={"DELETE"})
     */
    public function deleteUsuario($id)
    {
        $usuario= $this->getDoctrine()->getRepository(Usuario::class)->find($id);

        if(!$usuario){
            $data = [
                'status' => 404,
                'error' => "Usuario no encontrado",
            ];
            return $this->response($data, 404);
        }

        $this->getDoctrine()->getRepository(Usuario::class)->deleteUsuario($usuario);

        $data = [
            'status' => 200,
            'error' => "Usuario borrado",
        ];
        return $this->response($data, 200);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/usuario/login", name="loginUsuario", methods={"POST"})
     */
    public function login(Request $request)
    {
        $request = $this->transformJsonBody($request);

        if (!$request){
            throw new \Exception();
        }
        $usuario= $this->getDoctrine()->getRepository(Usuario::class)->findOneBy(array('usuario' => $request->get('usuario')));

        if(!$usuario){
            $data = [
                'status' => 404,
                'error' => "Usuario no encontrado",
            ];
            return $this->response($data, 404);
        }

        if($usuario->getEmail() == $request->get('email')){

            $data = [
                'id' => $usuario->getId(),
                'nombre' => $usuario->getNombre(),
                'usuario' => $usuario->getUsuario(),
                'email' => $usuario->getEmail(),
            ];

            return $this->response($data, 200);
        }else{
            $data = [
                'status' => 401,
                'error' => "Usuario no correcto",
            ];
            return $this->response($data, 401);
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
