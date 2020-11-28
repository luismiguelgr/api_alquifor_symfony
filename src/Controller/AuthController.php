<?php
namespace App\Controller;
use App\Controller\ApiController;
use App\Entity\Usuario;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $password = $request->get('password');
        $email = $request->get('email');

        if (empty($email) || empty($password)){
            return $this->respondValidationError("Invalid Username or Password or Email");
        }


        $user = new Usuario();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();
        // return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));

        //Cambios para registrar usuarios
        $usuarioCreado = $em->getRepository(Usuario::class)->findOneBy(['email'=>$email]);

        return $this->response($usuarioCreado->getId());
    }
    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     * @Route("/login_check", name="api_login_check", methods={"POST"})
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email'=>$user->getUsername()]);
        $data []= [
            'token' => $JWTManager->create($user),
            'usuario' => $usuario->getUsername()
        ];
//        return new JsonResponse(['token' => $JWTManager->create($user), ]);
        return new JsonResponse($data, Response::HTTP_OK);
    }

}
